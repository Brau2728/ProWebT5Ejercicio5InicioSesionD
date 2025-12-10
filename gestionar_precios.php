<?php
// 1. CONEXIÓN A LA BASE DE DATOS
$host = 'localhost';
$db   = 'equipo'; 
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) { die("Error de conexión: " . $e->getMessage()); }

$mensaje = "";

// 2. GUARDAR PRECIOS (Cuando se presiona el botón "Guardar Tarifas")
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_mueble = $_POST['id_mueble'];
    
    // Recibimos los precios (si la caja está vacía, guardamos 0)
    $p_armado    = !empty($_POST['p_armado']) ? $_POST['p_armado'] : 0;
    $p_barnizado = !empty($_POST['p_barnizado']) ? $_POST['p_barnizado'] : 0;
    $p_pintado   = !empty($_POST['p_pintado']) ? $_POST['p_pintado'] : 0;
    $p_adornado  = !empty($_POST['p_adornado']) ? $_POST['p_adornado'] : 0;
    $p_maquila   = !empty($_POST['p_maquila']) ? $_POST['p_maquila'] : 0;

    // A) Primero verificamos si ya existen precios para este mueble
    // (Esto es más seguro que el ON DUPLICATE KEY si no tenemos la llave única configurada)
    $stmtCheck = $pdo->prepare("SELECT id_precios FROM precios_empleados WHERE id_muebles = ?");
    $stmtCheck->execute([$id_mueble]);
    $existe = $stmtCheck->fetch();

    if ($existe) {
        // ACTUALIZAR (UPDATE)
        $sql = "UPDATE precios_empleados SET 
                mue_precio_maquila = ?, mue_precio_armado = ?, 
                mue_precio_barnizado = ?, mue_precio_pintado = ?, mue_precio_adornado = ?
                WHERE id_muebles = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$p_maquila, $p_armado, $p_barnizado, $p_pintado, $p_adornado, $id_mueble]);
    } else {
        // INSERTAR NUEVO (INSERT)
        $sql = "INSERT INTO precios_empleados 
                (id_muebles, mue_precio_maquila, mue_precio_armado, mue_precio_barnizado, mue_precio_pintado, mue_precio_adornado)
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_mueble, $p_maquila, $p_armado, $p_barnizado, $p_pintado, $p_adornado]);
    }
    
    $mensaje = "¡Tarifas actualizadas correctamente!";
}

// 3. CONSULTA INTELIGENTE (Corregida sin el alias 'mod')
// Traemos todos los muebles y sus precios actuales (si los tienen)
$sqlMuebles = "SELECT 
                m.id_muebles, 
                mdl.modelos_nombre,   -- Usamos alias 'mdl' para evitar error
                mdl.modelos_imagen,
                m.mue_color, 
                m.mue_herraje,
                p.mue_precio_armado,
                p.mue_precio_barnizado,
                p.mue_precio_pintado,
                p.mue_precio_adornado,
                p.mue_precio_maquila
               FROM muebles m
               JOIN modelos mdl ON m.id_modelos = mdl.id_modelos
               LEFT JOIN precios_empleados p ON m.id_muebles = p.id_muebles";

$stmt = $pdo->query($sqlMuebles);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configurar Precios</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    
    <style>
        :root {
            --primary: #2E7D32; /* Verde Taller */
            --surface: #FFFFFF; 
            --bg: #F5F5F5;
            --text: #333;
        }
        body { font-family: 'Roboto', sans-serif; background: var(--bg); color: var(--text); margin: 0; padding: 20px; }
        
        h1 { font-weight: 300; text-align: center; color: #444; }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }

        /* TARJETA MATERIAL */
        .card {
            background: var(--surface);
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            overflow: hidden;
            transition: transform 0.2s;
            display: flex;
            flex-direction: column;
        }
        .card:hover { transform: translateY(-3px); box-shadow: 0 5px 15px rgba(0,0,0,0.15); }

        .card-header {
            padding: 15px;
            background: #fff;
            border-bottom: 1px solid #eee;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .thumb {
            width: 50px; height: 50px; 
            background: #eee; border-radius: 4px; object-fit: cover;
        }

        .card-body { padding: 20px; flex: 1; }

        /* INPUTS ESTILIZADOS */
        .input-row {
            display: flex;
            align-items: center;
            margin-bottom: 12px;
            border-bottom: 1px solid #e0e0e0;
            padding-bottom: 5px;
        }
        .input-row label { flex: 1; font-size: 0.9rem; color: #666; display: flex; align-items: center; gap: 8px;}
        .input-row input {
            width: 80px;
            border: none;
            background: transparent;
            text-align: right;
            font-size: 1rem;
            font-weight: 500;
            color: var(--primary);
            outline: none;
        }
        .input-row input:focus { background: #e8f5e9; }

        .btn-save {
            width: 100%;
            padding: 12px;
            background: var(--primary);
            color: white;
            border: none;
            font-weight: 500;
            cursor: pointer;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: background 0.3s;
        }
        .btn-save:hover { background: #1B5E20; }

        .alert { 
            position: fixed; top: 20px; right: 20px; 
            background: #43A047; color: white; 
            padding: 15px 25px; border-radius: 4px; 
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
            animation: slideIn 0.5s ease-out;
            z-index: 100;
        }
        @keyframes slideIn { from { transform: translateX(100%); } to { transform: translateX(0); } }
    </style>
</head>
<body>

    <?php if($mensaje): ?>
        <div class="alert">
            <span class="material-icons" style="vertical-align: middle; margin-right: 8px;">check_circle</span>
            <?= $mensaje ?>
        </div>
        <script>setTimeout(() => { document.querySelector('.alert').style.display = 'none'; }, 3000);</script>
    <?php endif; ?>

    <h1>Configuración de Tarifas</h1>

    <div class="grid">
        <?php while($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
            <div class="card">
                <form method="POST">
                    <input type="hidden" name="id_mueble" value="<?= $row['id_muebles'] ?>">
                    
                    <div class="card-header">
                        <?php if(!empty($row['modelos_imagen'])): ?>
                            <img src="<?= htmlspecialchars($row['modelos_imagen']) ?>" class="thumb" alt="Foto">
                        <?php else: ?>
                            <div class="thumb" style="display:flex;align-items:center;justify-content:center;color:#999;">
                                <span class="material-icons">image</span>
                            </div>
                        <?php endif; ?>
                        
                        <div>
                            <div style="font-weight:500; font-size:1.1rem;">
                                <?= htmlspecialchars($row['modelos_nombre']) ?>
                            </div>
                            <div style="font-size:0.85rem; color:#666;">
                                <?= htmlspecialchars($row['mue_color']) ?> • <?= htmlspecialchars($row['mue_herraje']) ?>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="input-row">
                            <label><span class="material-icons" style="font-size:16px;">handyman</span> Armado</label>
                            $ <input type="number" step="0.50" name="p_armado" value="<?= $row['mue_precio_armado'] ?>" placeholder="0">
                        </div>
                        <div class="input-row">
                            <label><span class="material-icons" style="font-size:16px;">brush</span> Barnizado</label>
                            $ <input type="number" step="0.50" name="p_barnizado" value="<?= $row['mue_precio_barnizado'] ?>" placeholder="0">
                        </div>
                        <div class="input-row">
                            <label><span class="material-icons" style="font-size:16px;">format_paint</span> Pintado</label>
                            $ <input type="number" step="0.50" name="p_pintado" value="<?= $row['mue_precio_pintado'] ?>" placeholder="0">
                        </div>
                        <div class="input-row">
                            <label><span class="material-icons" style="font-size:16px;">star</span> Adornado</label>
                            $ <input type="number" step="0.50" name="p_adornado" value="<?= $row['mue_precio_adornado'] ?>" placeholder="0">
                        </div>
                        </div>

                    <button type="submit" class="btn-save">Guardar Precio</button>
                </form>
            </div>
        <?php endwhile; ?>
    </div>

</body>
</html>
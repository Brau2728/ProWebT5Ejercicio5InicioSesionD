<?php
// crear_orden.php - Módulo del Administrador (VERSIÓN VERDE)
$host = 'localhost'; $db = 'equipo'; $user = 'root'; $pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) { die("Error: " . $e->getMessage()); }

$mensaje = "";

// 1. PROCESAR FORMULARIO
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recibimos los datos
    $id_mueble = $_POST['id_mueble']; // ID específico (Alacena Roma - Nogal)
    $cantidad  = $_POST['cantidad'];
    $herraje   = $_POST['herraje_orden'];
    $color     = $_POST['color_orden'];
    $prioridad = $_POST['prioridad'];

    // Buscamos el id_modelo asociado a este mueble (para llenar ambos campos y evitar errores)
    $stmtModelo = $pdo->prepare("SELECT id_modelos FROM muebles WHERE id_muebles = ?");
    $stmtModelo->execute([$id_mueble]);
    $rowModelo = $stmtModelo->fetch();
    $id_modelo = $rowModelo['id_modelos'];

    // INSERTAMOS LA ORDEN
    // Nota: Guardamos id_muebles (para precios) Y id_modelo (para referencias generales)
    $sql = "INSERT INTO ordenes_produccion 
            (id_muebles, id_modelo, cantidad_total, especif_herraje, especif_color, prioridad, estado) 
            VALUES (?, ?, ?, ?, ?, ?, 'Activa')";
    
    $stmt = $pdo->prepare($sql);
    
    // El orden de las variables debe coincidir con los ? de arriba
    if ($stmt->execute([$id_mueble, $id_modelo, $cantidad, $herraje, $color, $prioridad])) {
        $id_nuevo = $pdo->lastInsertId();
        $mensaje = "¡Orden #$id_nuevo lanzada al taller exitosamente!";
    }
}

// 2. OBTENER LISTA DE MUEBLES (Uniendo tabla muebles y modelos)
// Usamos el alias 'mdl' para evitar conflictos, igual que en el archivo anterior
$sqlMuebles = "SELECT m.id_muebles, mdl.modelos_nombre, m.mue_color, m.mue_herraje 
               FROM muebles m 
               JOIN modelos mdl ON m.id_modelos = mdl.id_modelos";
$stmt = $pdo->query($sqlMuebles);
$muebles = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Orden de Producción</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    
    <style>
        /* ESTILO UNIFICADO (VERDE TALLER) */
        :root { 
            --primary: #2E7D32; /* El mismo verde de gestionar_precios.php */
            --primary-dark: #1B5E20;
            --surface: #FFFFFF; 
            --bg: #F5F5F5; 
            --text: #333;
        }
        
        body { font-family: 'Roboto', sans-serif; background: var(--bg); margin: 0; padding: 20px; color: var(--text); }
        
        .container { max-width: 700px; margin: 0 auto; }
        
        /* TARJETA */
        .card {
            background: var(--surface);
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            padding: 30px;
            border-top: 5px solid var(--primary); /* Detalle visual */
        }

        h2 { 
            margin-top: 0; 
            color: #444; 
            display: flex; 
            align-items: center; 
            gap: 12px;
            font-weight: 400;
        }
        
        .form-group { margin-bottom: 25px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 500; color: #555; font-size: 0.95rem; }
        
        /* INPUTS & SELECTS */
        select, input, textarea {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 1rem;
            box-sizing: border-box;
            transition: border 0.3s;
            background-color: #FAFAFA;
        }
        select:focus, input:focus { 
            border-color: var(--primary); 
            background-color: #FFF; 
            outline: none; 
            box-shadow: 0 0 0 3px rgba(46, 125, 50, 0.1);
        }

        .row { display: flex; gap: 20px; }
        .col { flex: 1; }

        /* BOTÓN PRINCIPAL */
        .btn-submit {
            width: 100%;
            background: var(--primary);
            color: white;
            padding: 15px;
            border: none;
            border-radius: 6px;
            font-size: 1.1rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            cursor: pointer;
            box-shadow: 0 4px 6px rgba(0,0,0,0.15);
            transition: all 0.3s;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
        }
        .btn-submit:hover { 
            background: var(--primary-dark); 
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.2);
        }

        /* ALERTA DE ÉXITO */
        .alert-success {
            background: #43A047; color: white; padding: 15px; border-radius: 6px; margin-bottom: 25px;
            display: flex; align-items: center; gap: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            animation: fadeIn 0.5s;
        }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
        
        /* ÁREA DE ESPECIFICACIONES */
        .specs-box {
            background: #F1F8E9; /* Verde muy clarito */
            padding: 20px; 
            border-radius: 6px; 
            margin-bottom: 25px; 
            border: 1px dashed #A5D6A7;
        }
        
        .info-original {
            font-size: 0.85rem; color: var(--primary); margin-top: 5px; display: none; font-weight: 500;
        }
    </style>
</head>
<body>

<div class="container">
    <?php if($mensaje): ?>
        <div class="alert-success">
            <span class="material-icons">check_circle</span> 
            <div><?= $mensaje ?></div>
        </div>
    <?php endif; ?>

    <div class="card">
        <h2><span class="material-icons" style="color:var(--primary); font-size:32px;">post_add</span> Lanzar Orden de Producción</h2>
        <p style="color:#777; margin-bottom:30px; margin-top:-10px;">Define el lote que entrará al taller hoy.</p>

        <form method="POST">
            
            <div class="form-group">
                <label>1. ¿Qué modelo vamos a fabricar?</label>
                <select name="id_mueble" id="selectMueble" required onchange="cargarDatos()">
                    <option value="">-- Selecciona del catálogo --</option>
                    <?php foreach ($muebles as $m): ?>
                        <option value="<?= $m['id_muebles'] ?>" 
                                data-color="<?= htmlspecialchars($m['mue_color']) ?>"
                                data-herraje="<?= htmlspecialchars($m['mue_herraje']) ?>">
                            <?= htmlspecialchars($m['modelos_nombre']) ?> 
                            (Estándar: <?= htmlspecialchars($m['mue_color']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="row">
                <div class="col form-group">
                    <label>2. Cantidad (Piezas)</label>
                    <input type="number" name="cantidad" placeholder="Ej. 50" min="1" required>
                </div>
                <div class="col form-group">
                    <label>Prioridad</label>
                    <select name="prioridad">
                        <option value="Normal">Normal</option>
                        <option value="Urgente">🔥 Urgente</option>
                    </select>
                </div>
            </div>

            <div class="specs-box">
                <label style="margin-bottom:15px; display:block; color:#2E7D32; font-weight:bold;">
                    <span class="material-icons" style="font-size:18px; vertical-align:text-bottom;">tune</span> 
                    Especificaciones del Lote
                </label>
                
                <div class="row">
                    <div class="col form-group" style="margin-bottom:0;">
                        <label style="font-size:0.85rem;">Color</label>
                        <input type="text" name="color_orden" id="inputColor" required>
                    </div>
                    <div class="col form-group" style="margin-bottom:0;">
                        <label style="font-size:0.85rem;">Herraje / Material</label>
                        <input type="text" name="herraje_orden" id="inputHerraje" required>
                    </div>
                </div>
                <div id="infoOriginal" class="info-original">
                    ℹ️ Cargado desde configuración estándar del mueble.
                </div>
            </div>

            <button type="submit" class="btn-submit">
                Crear Orden
                <span class="material-icons">arrow_forward</span>
            </button>
        </form>
    </div>
</div>

<script>
    // Script para autocompletar los campos al elegir el mueble
    function cargarDatos() {
        var select = document.getElementById("selectMueble");
        var selectedOption = select.options[select.selectedIndex];
        
        if (selectedOption.value !== "") {
            var color = selectedOption.getAttribute("data-color");
            var herraje = selectedOption.getAttribute("data-herraje");
            
            // Llenar inputs
            document.getElementById("inputColor").value = color;
            document.getElementById("inputHerraje").value = herraje;
            
            // Mostrar mensajito
            document.getElementById("infoOriginal").style.display = "block";
        } else {
            document.getElementById("infoOriginal").style.display = "none";
            document.getElementById("inputColor").value = "";
            document.getElementById("inputHerraje").value = "";
        }
    }
</script>

</body>
</html>
<?php
$page = 'precios';
session_start();

if (!isset($_SESSION['usuario']) || !isset($_SESSION['tipo'])) {
    header("Location: login.php");
    exit();
}

include("php/conexion.php");

// 1. CONEXIÓN PUENTE
$db_host = 'localhost'; $db_user = 'root'; $db_pass = ''; $db_name = 'equipo'; $db_port = '3306';
$link = mysqli_connect($db_host, $db_user, $db_pass, $db_name, $db_port);

// 2. GUARDAR PRECIOS
if (isset($_POST['btn_guardar'])) {
    $id_mueble = mysqli_real_escape_string($link, $_POST['id_mueble']);
    
    $p_maquila   = (float)$_POST['p_maquila'];
    $p_armado    = (float)$_POST['p_armado'];
    $p_barnizado = (float)$_POST['p_barnizado'];
    $p_pintado   = (float)$_POST['p_pintado'];
    $p_adornado  = (float)$_POST['p_adornado'];

    $check = mysqli_query($link, "SELECT id_precios FROM precios_empleados WHERE id_muebles = '$id_mueble'");
    
    if (mysqli_num_rows($check) > 0) {
        $sql = "UPDATE precios_empleados SET 
                mue_precio_maquila='$p_maquila', mue_precio_armado='$p_armado', mue_precio_barnizado='$p_barnizado',
                mue_precio_pintado='$p_pintado', mue_precio_adornado='$p_adornado'
                WHERE id_muebles = '$id_mueble'";
    } else {
        $sql = "INSERT INTO precios_empleados 
                (id_muebles, mue_precio_maquila, mue_precio_armado, mue_precio_barnizado, mue_precio_pintado, mue_precio_adornado)
                VALUES ('$id_mueble', '$p_maquila', '$p_armado', '$p_barnizado', '$p_pintado', '$p_adornado')";
    }

    if(mysqli_query($link, $sql)) {
        echo "<script>alert('Tarifas actualizadas.'); window.location='gestionar_precios.php';</script>";
    } else {
        echo "<script>alert('Error al guardar.');</script>";
    }
}

// 3. CONSULTA
$sqlMuebles = "SELECT m.id_muebles, mdl.modelos_nombre, mdl.modelos_imagen, m.mue_color, m.mue_herraje, m.mue_cantidad,
               p.mue_precio_maquila, p.mue_precio_armado, p.mue_precio_barnizado, p.mue_precio_pintado, p.mue_precio_adornado
               FROM muebles m
               JOIN modelos mdl ON m.id_modelos = mdl.id_modelos
               LEFT JOIN precios_empleados p ON m.id_muebles = p.id_muebles
               WHERE m.id_estatus_mueble < 7
               ORDER BY m.id_muebles DESC";
$res = mysqli_query($link, $sqlMuebles);

$datos = [];
while($row = mysqli_fetch_assoc($res)) { $datos[] = $row; }
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Configurar Precios - Idealiza</title>
    <link rel="stylesheet" href="estilos/Wave2.css">
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <style>
        /* PALETA: #144c3c, #94745c, #cedfcd, #5d6b62 */
        body { font-family: 'Quicksand', sans-serif; background-color: #F0F2F5; padding-bottom: 100px; }
        .container-prices { max-width: 1200px; margin: 0 auto; padding: 40px 20px; }

        /* BARRA CONTROL */
        .control-bar { 
            display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; 
            background: white; padding: 15px 25px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        }
        .search-box {
            display: flex; align-items: center; background: #F5F5F5; border-radius: 30px; 
            padding: 8px 15px; border: 1px solid #cedfcd; flex: 1; max-width: 400px;
        }
        .search-box input { border: none; background: transparent; outline: none; width: 100%; font-family: inherit; color: #144c3c; font-weight: bold; }
        .view-toggles { display: flex; gap: 10px; }
        .btn-view {
            background: #fff; border: 1px solid #94745c; color: #94745c; border-radius: 8px; 
            padding: 8px; cursor: pointer; transition: 0.3s;
        }
        .btn-view.active { background: #144c3c; color: white; border-color: #144c3c; }

        /* GRID */
        .grid-view { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 25px; }
        
        .price-card {
            background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.08); 
            border-top: 5px solid #94745c; transition: transform 0.2s;
        }
        .price-card:hover { transform: translateY(-3px); }
        .card-header { padding: 15px; border-bottom: 1px solid #eee; display: flex; gap: 15px; align-items: center; background: #FAFAFA; }
        .thumb { width: 60px; height: 60px; background: #cedfcd; border-radius: 8px; object-fit: cover; }
        .card-body { padding: 20px; }
        .input-row { display: flex; align-items: center; margin-bottom: 10px; border-bottom: 1px dashed #eee; padding-bottom: 5px; }
        .lbl-stage { flex: 1; font-weight: bold; color: #5d6b62; font-size: 0.9rem; display: flex; gap: 8px; }
        
        /* INPUT MONEY */
        .input-money {
            width: 80px; text-align: right; padding: 5px; border: 1px solid #cedfcd; border-radius: 6px;
            font-weight: bold; color: #144c3c; outline: none;
        }
        .input-money:focus { border-color: #94745c; background: #FFF3E0; }

        /* BADGE TOTAL (NUEVO) */
        .total-badge {
            background: #cedfcd; color: #144c3c; padding: 10px; border-radius: 8px; margin-top: 15px; margin-bottom: 15px;
            text-align: center; font-weight: bold; display: flex; justify-content: space-between; align-items: center;
        }
        .total-badge span { font-size: 1.1rem; }

        .btn-save-mini {
            background: #144c3c; color: white; border: none; padding: 10px; border-radius: 20px; 
            cursor: pointer; font-size: 0.9rem; font-weight: bold; width: 100%;
        }
        .btn-save-mini:hover { background: #0f382c; }

        .hidden { display: none !important; }
        
        /* LIST VIEW */
        .list-view { display: none; background: white; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        table { width: 100%; border-collapse: collapse; }
        th { background: #144c3c; color: white; padding: 15px; text-align: left; }
        td { padding: 12px 15px; border-bottom: 1px solid #eee; }
    </style>
</head>
<body>

    <?php include("php/encabezado_madera.php"); ?>
    <?php include("php/barra_navegacion.php"); ?>

    <div class="container-prices">
        
        <div class="control-bar">
            <h2 style="margin:0; color:#144c3c; margin-right:20px;">Tarifas</h2>
            
            <div class="search-box">
                <span class="material-icons" style="color:#94745c;">search</span>
                <input type="text" id="searchInput" onkeyup="filtrarContenido()" placeholder="Buscar modelo, color...">
            </div>

            <div class="view-toggles">
                <button class="btn-view active" onclick="cambiarVista('grid')"><span class="material-icons">grid_view</span></button>
                <button class="btn-view" onclick="cambiarVista('list')"><span class="material-icons">view_list</span></button>
            </div>
        </div>

        <div id="view-grid" class="grid-view">
            <?php foreach($datos as $row) { 
                $img = !empty($row['modelos_imagen']) ? $row['modelos_imagen'] : 'https://dummyimage.com/100x100/cedfcd/144c3c&text=Mueble';
                $busqueda = strtolower($row['modelos_nombre'] . ' ' . $row['mue_color']);
                
                // Calculamos total inicial
                $total = $row['mue_precio_armado'] + $row['mue_precio_barnizado'] + $row['mue_precio_pintado'] + $row['mue_precio_adornado'];
            ?>
            <div class="price-card item-filtro" data-texto="<?php echo $busqueda; ?>" id="card-<?php echo $row['id_muebles']; ?>">
                <form method="POST">
                    <input type="hidden" name="id_mueble" value="<?php echo $row['id_muebles']; ?>">
                    <div class="card-header">
                        <img src="<?php echo $img; ?>" class="thumb">
                        <div>
                            <h3 style="margin:0; font-size:1.1rem; color:#144c3c;"><?php echo $row['modelos_nombre']; ?></h3>
                            <div style="font-size:0.8rem; color:#94745c;"><?php echo $row['mue_color']; ?></div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="input-row"><span class="lbl-stage"><span class="material-icons" style="font-size:16px">handyman</span> Armado</span> $ <input type="number" step="0.50" name="p_armado" class="input-money calc-<?php echo $row['id_muebles']; ?>" value="<?php echo $row['mue_precio_armado']; ?>" onkeyup="calcularTotal(<?php echo $row['id_muebles']; ?>)"></div>
                        <div class="input-row"><span class="lbl-stage"><span class="material-icons" style="font-size:16px">brush</span> Barniz</span> $ <input type="number" step="0.50" name="p_barnizado" class="input-money calc-<?php echo $row['id_muebles']; ?>" value="<?php echo $row['mue_precio_barnizado']; ?>" onkeyup="calcularTotal(<?php echo $row['id_muebles']; ?>)"></div>
                        <div class="input-row"><span class="lbl-stage"><span class="material-icons" style="font-size:16px">format_paint</span> Pintura</span> $ <input type="number" step="0.50" name="p_pintado" class="input-money calc-<?php echo $row['id_muebles']; ?>" value="<?php echo $row['mue_precio_pintado']; ?>" onkeyup="calcularTotal(<?php echo $row['id_muebles']; ?>)"></div>
                        <div class="input-row"><span class="lbl-stage"><span class="material-icons" style="font-size:16px">star</span> Adorno</span> $ <input type="number" step="0.50" name="p_adornado" class="input-money calc-<?php echo $row['id_muebles']; ?>" value="<?php echo $row['mue_precio_adornado']; ?>" onkeyup="calcularTotal(<?php echo $row['id_muebles']; ?>)"></div>
                        <input type="hidden" name="p_maquila" value="<?php echo $row['mue_precio_maquila']; ?>">
                        
                        <div class="total-badge">
                            <small>COSTO M.O.</small>
                            <span id="total-display-<?php echo $row['id_muebles']; ?>">$<?php echo number_format($total, 2); ?></span>
                        </div>

                        <button type="submit" name="btn_guardar" class="btn-save-mini">GUARDAR</button>
                    </div>
                </form>
            </div>
            <?php } ?>
        </div>

        <div id="view-list" class="list-view">
            </div>

    </div>

    <?php include("php/olas.php"); ?>

    <script>
        function cambiarVista(vista) {
            const grid = document.getElementById('view-grid');
            // Implementación simple para solo Grid por ahora
            grid.style.display = 'grid';
        }

        function filtrarContenido() {
            const texto = document.getElementById('searchInput').value.toLowerCase();
            const items = document.querySelectorAll('.item-filtro');
            items.forEach(item => {
                const contenido = item.getAttribute('data-texto');
                item.classList.toggle('hidden', !contenido.includes(texto));
            });
        }

        // FUNCION MAGICA PARA SUMAR
        function calcularTotal(id) {
            let total = 0;
            // Busca todos los inputs de la tarjeta específica
            const inputs = document.querySelectorAll(`.calc-${id}`);
            
            inputs.forEach(input => {
                let val = parseFloat(input.value);
                if(isNaN(val)) val = 0;
                total += val;
            });

            // Actualiza el texto
            document.getElementById(`total-display-${id}`).innerText = "$" + total.toFixed(2);
        }
    </script>

</body>
</html>
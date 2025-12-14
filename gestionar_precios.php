<?php
// 1. CONFIGURACIÓN
$page = 'precios';
session_start();

// 2. SEGURIDAD
if (!isset($_SESSION['usuario']) || !isset($_SESSION['tipo'])) {
    header("Location: login.php");
    exit();
}

// 3. CONEXIÓN (Robusta)
include("php/conexion.php");

// Si el include no define $link, lo creamos manualmente para evitar fallos
if (!isset($link)) {
    $db_host = 'localhost'; 
    $db_user = 'root'; 
    $db_pass = ''; 
    $db_name = 'equipo'; 
    $db_port = '3306';
    $link = mysqli_connect($db_host, $db_user, $db_pass, $db_name, $db_port);
}

// 4. GUARDAR PRECIOS (Lógica corregida)
if (isset($_POST['btn_guardar'])) {
    $id_mueble = mysqli_real_escape_string($link, $_POST['id_mueble']);
    
    // Convertir a float (0 si está vacío)
    $p_maquila   = floatval($_POST['p_maquila']);
    $p_armado    = floatval($_POST['p_armado']);
    $p_barnizado = floatval($_POST['p_barnizado']);
    $p_pintado   = floatval($_POST['p_pintado']);
    $p_adornado  = floatval($_POST['p_adornado']);

    // Verificar existencia
    $check = mysqli_query($link, "SELECT id_precios FROM precios_empleados WHERE id_muebles = '$id_mueble'");
    
    if (mysqli_num_rows($check) > 0) {
        // UPDATE
        $sql = "UPDATE precios_empleados SET 
                mue_precio_maquila='$p_maquila', 
                mue_precio_armado='$p_armado', 
                mue_precio_barnizado='$p_barnizado',
                mue_precio_pintado='$p_pintado', 
                mue_precio_adornado='$p_adornado'
                WHERE id_muebles = '$id_mueble'";
    } else {
        // INSERT
        $sql = "INSERT INTO precios_empleados 
                (id_muebles, mue_precio_maquila, mue_precio_armado, mue_precio_barnizado, mue_precio_pintado, mue_precio_adornado)
                VALUES ('$id_mueble', '$p_maquila', '$p_armado', '$p_barnizado', '$p_pintado', '$p_adornado')";
    }

    if(mysqli_query($link, $sql)) {
        // Usamos JS para la alerta y redirección fluida
        echo "<script>alert('¡Tarifas guardadas correctamente!'); window.location='gestionar_precios.php';</script>";
    } else {
        echo "<script>alert('Error al guardar en la base de datos.');</script>";
    }
}

// 5. CONSULTA DE DATOS
$sqlMuebles = "SELECT m.id_muebles, mdl.modelos_nombre, mdl.modelos_imagen, m.mue_color, m.mue_herraje, m.mue_cantidad,
               p.mue_precio_maquila, p.mue_precio_armado, p.mue_precio_barnizado, p.mue_precio_pintado, p.mue_precio_adornado
               FROM muebles m
               JOIN modelos mdl ON m.id_modelos = mdl.id_modelos
               LEFT JOIN precios_empleados p ON m.id_muebles = p.id_muebles
               WHERE m.id_estatus_mueble < 7
               ORDER BY m.id_muebles DESC";
$res = mysqli_query($link, $sqlMuebles);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Configurar Tarifas | Idealisa</title>
    
    <!-- Estilos y Fuentes -->
    <link rel="stylesheet" href="estilos/Wave2.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&family=Quicksand:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">

    <style>
        /* === PALETA OFICIAL IDEALISA === */
        :root {
            --primary: #144c3c; /* Verde */
            --accent: #94745c;  /* Café */
            --bg-page: #F0F2F5;
            --white: #ffffff;
            --text-dark: #2c3e50;
            --text-light: #7f8c8d;
        }

        body { font-family: 'Quicksand', sans-serif; background-color: var(--bg-page); margin: 0; padding-bottom: 80px; }
        
        .container-prices { max-width: 1400px; margin: 0 auto; padding: 30px 20px; }

        /* BARRA DE CONTROL SUPERIOR */
        .control-bar { 
            display: flex; justify-content: space-between; align-items: center; 
            background: var(--white); padding: 15px 25px; border-radius: 12px; 
            box-shadow: 0 4px 15px rgba(0,0,0,0.05); margin-bottom: 30px;
            flex-wrap: wrap; gap: 15px;
        }
        
        .page-title { 
            margin: 0; color: var(--primary); font-family: 'Outfit'; font-weight: 700; 
            font-size: 1.5rem; display: flex; align-items: center; gap: 10px;
        }

        .search-box {
            display: flex; align-items: center; background: #F8F9FA; border-radius: 30px; 
            padding: 8px 15px; border: 1px solid #e0e0e0; flex: 1; max-width: 400px;
            transition: 0.3s;
        }
        .search-box:focus-within { border-color: var(--accent); background: white; }
        .search-box input { border: none; background: transparent; outline: none; width: 100%; font-family: 'Quicksand'; font-weight: 600; color: var(--primary); }

        .view-toggles button {
            background: white; border: 1px solid #ccc; color: #666; border-radius: 8px; 
            padding: 8px; cursor: pointer; transition: 0.2s;
        }
        .view-toggles button.active { background: var(--primary); color: white; border-color: var(--primary); }

        /* GRID DE TARJETAS */
        .grid-view { 
            display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 25px; 
        }
        
        /* TARJETA DE PRECIO INDIVIDUAL */
        .price-card {
            background: var(--white); border-radius: 16px; overflow: hidden; 
            box-shadow: 0 4px 10px rgba(0,0,0,0.05); transition: transform 0.2s;
            border-top: 5px solid var(--accent); /* Detalle café */
            position: relative;
        }
        .price-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1); }

        .card-header { 
            padding: 15px; border-bottom: 1px solid #f0f0f0; 
            display: flex; gap: 15px; align-items: center; background: #fff; 
        }
        .thumb { width: 60px; height: 60px; background: #f0f0f0; border-radius: 8px; object-fit: cover; }
        
        .card-body { padding: 20px; }
        
        /* Filas de Inputs */
        .input-row { 
            display: flex; align-items: center; justify-content: space-between; 
            margin-bottom: 10px; border-bottom: 1px dashed #eee; padding-bottom: 8px; 
        }
        .input-row:last-child { border-bottom: none; }
        
        .lbl-stage { 
            font-weight: 700; color: var(--text-dark); font-size: 0.9rem; 
            display: flex; align-items: center; gap: 8px; 
        }
        
        .input-money {
            width: 80px; text-align: right; padding: 6px; border: 1px solid #e0e0e0; 
            border-radius: 6px; font-weight: 700; color: var(--primary); outline: none; font-family: 'Quicksand';
        }
        .input-money:focus { border-color: var(--primary); background: #f0fdf4; }

        /* Footer con Total */
        .total-badge {
            background: #eefdf6; color: var(--primary); padding: 12px; border-radius: 10px; 
            margin-top: 15px; margin-bottom: 15px; text-align: center; font-weight: 800; 
            display: flex; justify-content: space-between; align-items: center; border: 1px solid #ccece6;
        }
        
        .btn-save-mini {
            background: var(--primary); color: white; border: none; padding: 12px; 
            border-radius: 10px; cursor: pointer; font-size: 0.95rem; font-weight: 700; 
            width: 100%; transition: 0.2s; display: flex; align-items: center; justify-content: center; gap: 5px;
        }
        .btn-save-mini:hover { background: #0e362b; transform: scale(1.02); }

        .hidden { display: none !important; }

        /* Estilo Lista (Oculto por defecto) */
        .list-view { display: none; flex-direction: column; gap: 10px; }
    </style>
</head>
<body>

    <?php include("php/encabezado_madera.php"); ?>
    <?php include("php/barra_navegacion.php"); ?>

    <div class="container-prices">
        
        <!-- BARRA DE HERRAMIENTAS -->
        <div class="control-bar">
            <h2 class="page-title">
                <span class="material-icons-round">payments</span> Gestión de Tarifas
            </h2>
            
            <div class="search-box">
                <span class="material-icons-round" style="color:var(--accent); margin-right:5px;">search</span>
                <input type="text" id="searchInput" onkeyup="filtrarContenido()" placeholder="Buscar modelo, color o ID...">
            </div>

            <div class="view-toggles">
                <button class="active" onclick="cambiarVista('grid')" title="Vista Tarjetas"><span class="material-icons-round">grid_view</span></button>
                <!-- <button onclick="cambiarVista('list')" title="Vista Lista"><span class="material-icons-round">view_list</span></button> -->
            </div>
        </div>

        <!-- CONTENEDOR GRID -->
        <div id="view-grid" class="grid-view">
            <?php 
            if(mysqli_num_rows($res) > 0) {
                while($row = mysqli_fetch_assoc($res)) { 
                    $id = $row['id_muebles'];
                    $img = !empty($row['modelos_imagen']) ? $row['modelos_imagen'] : 'img/no-image.png';
                    // Crear string de búsqueda
                    $busqueda = strtolower($row['modelos_nombre'] . ' ' . $row['mue_color'] . ' ' . $id);
                    
                    // Calcular total inicial (con validación de nulls)
                    $v_maq = floatval($row['mue_precio_maquila']);
                    $v_arm = floatval($row['mue_precio_armado']);
                    $v_bar = floatval($row['mue_precio_barnizado']);
                    $v_pin = floatval($row['mue_precio_pintado']);
                    $v_ado = floatval($row['mue_precio_adornado']);
                    $total = $v_maq + $v_arm + $v_bar + $v_pin + $v_ado;
            ?>
            
            <!-- TARJETA -->
            <div class="price-card item-filtro" data-texto="<?php echo $busqueda; ?>" id="card-<?php echo $id; ?>">
                <form method="POST">
                    <input type="hidden" name="id_mueble" value="<?php echo $id; ?>">
                    
                    <div class="card-header">
                        <img src="<?php echo $img; ?>" class="thumb" alt="Foto">
                        <div>
                            <h3 style="margin:0; font-size:1.1rem; color:var(--text-dark); font-family:'Outfit';"><?php echo $row['modelos_nombre']; ?></h3>
                            <div style="font-size:0.8rem; color:var(--accent); font-weight:600;"><?php echo $row['mue_color']; ?></div>
                            <small style="color:#aaa;">Lote #<?php echo $id; ?></small>
                        </div>
                    </div>

                    <div class="card-body">
                        <!-- Inputs de Precios -->
                        <div class="input-row">
                            <span class="lbl-stage"><span class="material-icons-round" style="font-size:16px; color:#5d6b62;">handyman</span> Maquila</span> 
                            <div>$ <input type="number" step="0.50" name="p_maquila" class="input-money calc-<?php echo $id; ?>" value="<?php echo $v_maq; ?>" onkeyup="calcularTotal(<?php echo $id; ?>)"></div>
                        </div>

                        <div class="input-row">
                            <span class="lbl-stage"><span class="material-icons-round" style="font-size:16px; color:#94745c;">build</span> Armado</span> 
                            <div>$ <input type="number" step="0.50" name="p_armado" class="input-money calc-<?php echo $id; ?>" value="<?php echo $v_arm; ?>" onkeyup="calcularTotal(<?php echo $id; ?>)"></div>
                        </div>

                        <div class="input-row">
                            <span class="lbl-stage"><span class="material-icons-round" style="font-size:16px; color:#b45309;">format_paint</span> Barniz</span> 
                            <div>$ <input type="number" step="0.50" name="p_barnizado" class="input-money calc-<?php echo $id; ?>" value="<?php echo $v_bar; ?>" onkeyup="calcularTotal(<?php echo $id; ?>)"></div>
                        </div>

                        <div class="input-row">
                            <span class="lbl-stage"><span class="material-icons-round" style="font-size:16px; color:#0f766e;">brush</span> Pintado</span> 
                            <div>$ <input type="number" step="0.50" name="p_pintado" class="input-money calc-<?php echo $id; ?>" value="<?php echo $v_pin; ?>" onkeyup="calcularTotal(<?php echo $id; ?>)"></div>
                        </div>

                        <div class="input-row" style="border:none;">
                            <span class="lbl-stage"><span class="material-icons-round" style="font-size:16px; color:#7c3aed;">auto_awesome</span> Adorno</span> 
                            <div>$ <input type="number" step="0.50" name="p_adornado" class="input-money calc-<?php echo $id; ?>" value="<?php echo $v_ado; ?>" onkeyup="calcularTotal(<?php echo $id; ?>)"></div>
                        </div>
                        
                        <!-- Total -->
                        <div class="total-badge">
                            <small style="color:var(--text-light); text-transform:uppercase;">Costo Total M.O.</small>
                            <span id="total-display-<?php echo $id; ?>">$<?php echo number_format($total, 2); ?></span>
                        </div>

                        <button type="submit" name="btn_guardar" class="btn-save-mini">
                            <span class="material-icons-round">save</span> GUARDAR TARIFAS
                        </button>
                    </div>
                </form>
            </div>
            <?php 
                } // Fin While
            } else {
                echo "<p style='width:100%; text-align:center; color:#aaa; font-size:1.2rem; margin-top:50px;'>No hay muebles pendientes.</p>";
            } 
            ?>
        </div>

    </div>

    <!-- SCRIPTS -->
    <script>
        // FILTRADO
        function filtrarContenido() {
            const texto = document.getElementById('searchInput').value.toLowerCase();
            const items = document.querySelectorAll('.item-filtro');
            
            items.forEach(item => {
                const contenido = item.getAttribute('data-texto');
                if(contenido.includes(texto)) {
                    item.classList.remove('hidden');
                } else {
                    item.classList.add('hidden');
                }
            });
        }

        // CALCULO DE TOTALES EN VIVO
        function calcularTotal(id) {
            let total = 0;
            const inputs = document.querySelectorAll(`.calc-${id}`);
            
            inputs.forEach(input => {
                let val = parseFloat(input.value);
                if(isNaN(val)) val = 0;
                total += val;
            });

            const display = document.getElementById(`total-display-${id}`);
            display.innerText = "$" + total.toFixed(2);
            display.style.color = "#d35400"; // Cambio visual temporal
            setTimeout(() => { display.style.color = "var(--primary)"; }, 500);
        }

        function cambiarVista(v) {
            // Futura implementación de lista si se requiere
            console.log("Vista: " + v);
        }
    </script>

</body>
</html>
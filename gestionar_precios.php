<?php
// gestionar_precios.php - CORREGIDO: Gestión por MODELO (Catálogo), no por Lote
$page = 'precios';
session_start();

if (!isset($_SESSION['usuario']) || !isset($_SESSION['tipo'])) { header("Location: login.php"); exit(); }

include("php/conexion.php");
if (!isset($link)) { $link = mysqli_connect('localhost', 'root', '', 'equipo', 3306); }

// GUARDAR
if (isset($_POST['btn_guardar'])) {
    $id_modelo = intval($_POST['id_modelo']); // Ahora guardamos por MODELO

    // Precios
    $p_maq = floatval($_POST['p_maquila']); $p_arm = floatval($_POST['p_armado']);
    $p_bar = floatval($_POST['p_barnizado']); $p_pin = floatval($_POST['p_pintado']); $p_ado = floatval($_POST['p_adornado']);

    // Verificar si ya existe precio para este modelo
    $check = mysqli_query($link, "SELECT id_precios FROM precios_empleados WHERE id_modelos = '$id_modelo'");
    
    if (mysqli_num_rows($check) > 0) {
        $sql = "UPDATE precios_empleados SET mue_precio_maquila='$p_maq', mue_precio_armado='$p_arm', mue_precio_barnizado='$p_bar', mue_precio_pintado='$p_pin', mue_precio_adornado='$p_ado' WHERE id_modelos = '$id_modelo'";
    } else {
        $sql = "INSERT INTO precios_empleados (id_modelos, mue_precio_maquila, mue_precio_armado, mue_precio_barnizado, mue_precio_pintado, mue_precio_adornado) VALUES ('$id_modelo', '$p_maq', '$p_arm', '$p_bar', '$p_pin', '$p_ado')";
    }
    mysqli_query($link, $sql);

    // Tiempos
    $etapas = [2 => intval($_POST['t_maquila']), 3 => intval($_POST['t_armado']), 4 => intval($_POST['t_barnizado']), 5 => intval($_POST['t_pintado']), 6 => intval($_POST['t_adornado'])];
    foreach ($etapas as $id_etapa => $dias) {
        $checkT = mysqli_query($link, "SELECT id_tiempo FROM modelo_tiempos WHERE id_modelos=$id_modelo AND id_estatus_mueble=$id_etapa");
        if (mysqli_num_rows($checkT) > 0) {
            mysqli_query($link, "UPDATE modelo_tiempos SET dias_estimados=$dias WHERE id_modelos=$id_modelo AND id_estatus_mueble=$id_etapa");
        } else {
            mysqli_query($link, "INSERT INTO modelo_tiempos (id_modelos, id_estatus_mueble, dias_estimados) VALUES ($id_modelo, $id_etapa, $dias)");
        }
    }
    echo "<script>alert('¡Configuración del MODELO guardada!'); window.location='gestionar_precios.php';</script>";
}

// CONSULTA: Ahora traemos TODOS los modelos del catálogo, tengan o no producción activa
$sqlModelos = "SELECT m.id_modelos, m.modelos_nombre, m.modelos_imagen,
               p.mue_precio_maquila, p.mue_precio_armado, p.mue_precio_barnizado, p.mue_precio_pintado, p.mue_precio_adornado
               FROM modelos m
               LEFT JOIN precios_empleados p ON m.id_modelos = p.id_modelos
               ORDER BY m.modelos_nombre ASC";
$res = mysqli_query($link, $sqlModelos);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Configurar Tarifas Maestras | Idealisa</title>
    <link rel="stylesheet" href="estilos/Wave2.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&family=Quicksand:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
    <style>
        :root { --primary: #144c3c; --accent: #94745c; --bg-page: #F0F2F5; --white: #ffffff; --text-dark: #2c3e50; }
        body { font-family: 'Quicksand', sans-serif; background-color: var(--bg-page); margin: 0; padding-bottom: 80px; }
        .container-prices { max-width: 1400px; margin: 0 auto; padding: 30px 20px; }
        .control-bar { display: flex; justify-content: space-between; align-items: center; background: var(--white); padding: 15px 25px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); margin-bottom: 30px; flex-wrap: wrap; gap: 15px; }
        .page-title { margin: 0; color: var(--primary); font-family: 'Outfit'; font-weight: 700; font-size: 1.5rem; display: flex; align-items: center; gap: 10px; }
        .search-box { display: flex; align-items: center; background: #F8F9FA; border-radius: 30px; padding: 8px 15px; border: 1px solid #e0e0e0; flex: 1; max-width: 400px; transition: 0.3s; }
        .search-box:focus-within { border-color: var(--accent); background: white; }
        .search-box input { border: none; background: transparent; outline: none; width: 100%; font-family: 'Quicksand'; font-weight: 600; color: var(--primary); }
        .grid-view { display: grid; grid-template-columns: repeat(auto-fill, minmax(340px, 1fr)); gap: 25px; }
        .price-card { background: var(--white); border-radius: 16px; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.05); transition: transform 0.2s; border-top: 5px solid var(--accent); position: relative; }
        .price-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1); }
        .card-header { padding: 15px; border-bottom: 1px solid #f0f0f0; display: flex; gap: 15px; align-items: center; background: #fff; }
        .thumb { width: 60px; height: 60px; background: #f0f0f0; border-radius: 8px; object-fit: cover; }
        .card-body { padding: 20px; }
        .input-row { display: grid; grid-template-columns: 1fr 1fr 1fr; align-items: center; gap: 10px; margin-bottom: 10px; border-bottom: 1px dashed #eee; padding-bottom: 8px; }
        .input-row:last-child { border-bottom: none; }
        .lbl-stage { font-weight: 700; color: var(--text-dark); font-size: 0.85rem; display: flex; align-items: center; gap: 6px; }
        .money-wrap { display: flex; align-items: center; background: #f9f9f9; border-radius: 6px; padding: 0 8px; border: 1px solid #eee; }
        .time-wrap { display: flex; align-items: center; background: #fff8e1; border-radius: 6px; padding: 0 8px; border: 1px solid #ffe0b2; position: relative; }
        .input-mini { width: 50px; text-align: right; padding: 6px 2px; border: none; background: transparent; font-weight: 700; color: var(--primary); outline: none; font-family: 'Quicksand'; font-size: 0.9rem; }
        .total-badge { background: #eefdf6; color: var(--primary); padding: 12px; border-radius: 10px; margin-top: 15px; margin-bottom: 15px; text-align: center; font-weight: 800; display: flex; justify-content: space-between; align-items: center; border: 1px solid #ccece6; }
        .btn-save-mini { background: var(--primary); color: white; border: none; padding: 12px; border-radius: 10px; cursor: pointer; font-size: 0.95rem; font-weight: 700; width: 100%; transition: 0.2s; display: flex; align-items: center; justify-content: center; gap: 5px; }
        .btn-save-mini:hover { background: #0e362b; transform: scale(1.02); }
        .hidden { display: none !important; }
        .base-label { position: absolute; top: -8px; right: -5px; background: #FFB74D; color: white; font-size: 9px; padding: 2px 4px; border-radius: 4px; font-weight: 800; }
    </style>
</head>
<body>

    <?php include("php/encabezado_madera.php"); ?>
    <?php include("php/barra_navegacion.php"); ?>

    <div class="container-prices">
        <div class="control-bar">
            <h2 class="page-title"><span class="material-icons-round">settings_suggest</span> Catálogo Maestro de Tarifas</h2>
            <div class="search-box">
                <span class="material-icons-round" style="color:var(--accent); margin-right:5px;">search</span>
                <input type="text" id="searchInput" onkeyup="filtrarContenido()" placeholder="Buscar modelo...">
            </div>
        </div>

        <div id="view-grid" class="grid-view">
            <?php 
            if(mysqli_num_rows($res) > 0) {
                while($row = mysqli_fetch_assoc($res)) { 
                    $id_modelo = $row['id_modelos'];
                    $img = !empty($row['modelos_imagen']) ? $row['modelos_imagen'] : 'img/no-image.png';
                    $busqueda = strtolower($row['modelos_nombre']);
                    
                    // Precios
                    $v_maq = floatval($row['mue_precio_maquila']); $v_arm = floatval($row['mue_precio_armado']); $v_bar = floatval($row['mue_precio_barnizado']);
                    $v_pin = floatval($row['mue_precio_pintado']); $v_ado = floatval($row['mue_precio_adornado']);
                    $total = $v_maq + $v_arm + $v_bar + $v_pin + $v_ado;

                    $t_dias = [2=>0, 3=>0, 4=>0, 5=>0, 6=>0];
                    $qTiempos = mysqli_query($link, "SELECT id_estatus_mueble, dias_estimados FROM modelo_tiempos WHERE id_modelos = $id_modelo");
                    while($rt = mysqli_fetch_assoc($qTiempos)) { $t_dias[$rt['id_estatus_mueble']] = $rt['dias_estimados']; }
            ?>
            <div class="price-card item-filtro" data-texto="<?php echo $busqueda; ?>">
                <form method="POST">
                    <input type="hidden" name="id_modelo" value="<?php echo $id_modelo; ?>">
                    <div class="card-header">
                        <img src="<?php echo $img; ?>" class="thumb" alt="Foto">
                        <div>
                            <h3 style="margin:0; font-size:1rem; color:var(--text-dark); font-family:'Outfit';"><?php echo $row['modelos_nombre']; ?></h3>
                            <div style="font-size:0.7rem; color:#aaa;">ID Modelo: <?php echo $id_modelo; ?></div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div style="display:flex; justify-content:space-between; font-size:0.7rem; color:#aaa; margin-bottom:5px; padding-right:5px;">
                            <span>Etapa</span> <span style="margin-left:auto; margin-right:25px;">Pago Unitario ($)</span> <span>Días (x10 pzas)</span>
                        </div>

                        <?php 
                        $stages = [
                            ['lbl'=>'Maquila', 'icon'=>'handyman', 'col'=>'#5d6b62', 'n_p'=>'p_maquila', 'v_p'=>$v_maq, 'n_t'=>'t_maquila', 'v_t'=>$t_dias[2]],
                            ['lbl'=>'Armado', 'icon'=>'build', 'col'=>'#94745c', 'n_p'=>'p_armado', 'v_p'=>$v_arm, 'n_t'=>'t_armado', 'v_t'=>$t_dias[3]],
                            ['lbl'=>'Barniz', 'icon'=>'format_paint', 'col'=>'#b45309', 'n_p'=>'p_barnizado', 'v_p'=>$v_bar, 'n_t'=>'t_barnizado', 'v_t'=>$t_dias[4]],
                            ['lbl'=>'Pintado', 'icon'=>'brush', 'col'=>'#0f766e', 'n_p'=>'p_pintado', 'v_p'=>$v_pin, 'n_t'=>'t_pintado', 'v_t'=>$t_dias[5]],
                            ['lbl'=>'Adorno', 'icon'=>'auto_awesome', 'col'=>'#7c3aed', 'n_p'=>'p_adornado', 'v_p'=>$v_ado, 'n_t'=>'t_adornado', 'v_t'=>$t_dias[6]]
                        ];
                        foreach($stages as $s){ ?>
                        <div class="input-row">
                            <span class="lbl-stage"><span class="material-icons-round" style="font-size:16px; color:<?php echo $s['col']; ?>;"><?php echo $s['icon']; ?></span> <?php echo $s['lbl']; ?></span> 
                            <div class="money-wrap">$<input type="number" step="0.50" name="<?php echo $s['n_p']; ?>" class="input-mini calc-<?php echo $id_modelo; ?>" value="<?php echo $s['v_p']; ?>" onkeyup="calcularTotal(<?php echo $id_modelo; ?>)"></div>
                            <div class="time-wrap"><input type="number" name="<?php echo $s['n_t']; ?>" class="input-mini" value="<?php echo $s['v_t']; ?>" placeholder="0">d <span class="base-label">10p</span></div>
                        </div>
                        <?php } ?>
                        
                        <div class="total-badge">
                            <small style="color:var(--text-light); text-transform:uppercase;">Costo Total M.O.</small>
                            <span id="total-display-<?php echo $id_modelo; ?>">$<?php echo number_format($total, 2); ?></span>
                        </div>
                        <button type="submit" name="btn_guardar" class="btn-save-mini"><span class="material-icons-round">save</span> GUARDAR TARIFAS MAESTRAS</button>
                    </div>
                </form>
            </div>
            <?php } } else { echo "<p style='width:100%; text-align:center; color:#aaa; font-size:1.2rem; margin-top:50px;'>No hay modelos registrados.</p>"; } ?>
        </div>
    </div>

    <script>
        function filtrarContenido() {
            const texto = document.getElementById('searchInput').value.toLowerCase();
            const items = document.querySelectorAll('.item-filtro');
            items.forEach(item => {
                const contenido = item.getAttribute('data-texto');
                if(contenido.includes(texto)) item.classList.remove('hidden'); else item.classList.add('hidden');
            });
        }
        function calcularTotal(id) {
            let total = 0;
            document.querySelectorAll(`.calc-${id}`).forEach(input => {
                let val = parseFloat(input.value);
                if(isNaN(val)) val = 0;
                total += val;
            });
            const display = document.getElementById(`total-display-${id}`);
            display.innerText = "$" + total.toFixed(2);
            display.style.color = "#d35400";
            setTimeout(() => { display.style.color = "var(--primary)"; }, 500);
        }
    </script>
</body>
</html>
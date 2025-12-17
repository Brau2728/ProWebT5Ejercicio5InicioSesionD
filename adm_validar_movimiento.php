<?php
session_start();
// 1. AJUSTE DE ZONA HORARIA (Crucial para que salga en la nómina de hoy)
date_default_timezone_set('America/Mexico_City');

// 2. SEGURIDAD Y CONEXIÓN
if (!isset($_SESSION['usuario'])) { header('Location: login.php'); exit(); }

if (file_exists("php/conexion.php")) {
    include("php/conexion.php");
} elseif (file_exists("conexion.php")) {
    include("conexion.php");
} else {
    die("Error Crítico: No se encuentra conexion.php");
}

if (!isset($link) || !$link) {
    $link = mysqli_connect('localhost', 'root', '', 'equipo', '3306');
}

$id_mueble = mysqli_real_escape_string($link, $_GET['id']);

// 3. DATOS DEL MUEBLE Y MODELO
$sql = "SELECT m.*, mo.modelos_nombre, mo.modelos_imagen, mo.id_modelos 
        FROM muebles m 
        INNER JOIN modelos mo ON m.id_modelos = mo.id_modelos 
        WHERE id_muebles = '$id_mueble'";
$res = mysqli_query($link, $sql);
$mueble = mysqli_fetch_assoc($res);

$etapa_actual = $mueble['id_estatus_mueble'];
$nombre_operario = trim($mueble['asignado_a']); // Limpiamos espacios extra
$cantidad_piezas = (int)$mueble['mue_cantidad'];
$id_modelo_actual = $mueble['id_modelos'];

// 4. LÓGICA DE PRECIOS
$precio_unitario = 0;
$nombre_etapa_cobro = "Trabajo General";

$mapa_precios = [
    2 => ['col' => 'mue_precio_maquila',   'nom' => 'Maquila'],
    3 => ['col' => 'mue_precio_armado',    'nom' => 'Armado'],
    4 => ['col' => 'mue_precio_barnizado', 'nom' => 'Barnizado'],
    5 => ['col' => 'mue_precio_pintado',   'nom' => 'Pintado'],
    6 => ['col' => 'mue_precio_adornado',  'nom' => 'Adornado']
];

if (isset($mapa_precios[$etapa_actual])) {
    $columna = $mapa_precios[$etapa_actual]['col'];
    $nombre_etapa_cobro = $mapa_precios[$etapa_actual]['nom'];
    
    // Buscar precio por MODELO
    $sqlP = "SELECT $columna FROM precios_empleados WHERE id_modelos = '$id_modelo_actual'";
    $resP = mysqli_query($link, $sqlP);
    if ($resP && mysqli_num_rows($resP) > 0) {
        $filaP = mysqli_fetch_assoc($resP);
        $precio_unitario = (float)$filaP[$columna];
    }
}

$total_a_pagar = $precio_unitario * $cantidad_piezas;

// DATOS SIGUIENTE ETAPA
$etapa_siguiente = $etapa_actual + 1;
$nombresEtapas = [2=>'Maquila', 3=>'Armado', 4=>'Barnizado', 5=>'Pintado', 6=>'Adornado', 7=>'Almacén Terminado'];
$nombreSig = $nombresEtapas[$etapa_siguiente] ?? 'Siguiente Etapa';


// 5. PROCESAR VALIDACIÓN
if(isset($_POST['aprobar'])) {
    
    // A) VALIDACIÓN ESTRICTA DEL EMPLEADO
    // Buscamos el ID del usuario. Si no existe, DETENEMOS TODO.
    $sqlUser = "SELECT id_usuario FROM usuarios WHERE CONCAT(usu_nom, ' ', usu_ap_pat) LIKE '%".$nombre_operario."%' LIMIT 1";
    $resUser = mysqli_query($link, $sqlUser);
    
    if($resUser && mysqli_num_rows($resUser) > 0) {
        $rowUser = mysqli_fetch_assoc($resUser);
        $id_empleado = $rowUser['id_usuario'];
        
        // Insertamos el pago
        $fecha_hoy = date('Y-m-d H:i:s'); // Hora corregida a México
        $id_validador = $_SESSION['id_usuario'] ?? 1;
        
        $sqlBitacora = "INSERT INTO bitacora_produccion 
        (id_usuario_empleado, id_etapa, cantidad_reportada, fecha_reporte, id_usuario_validador, estado_validacion, monto_pago, id_muebles)
        VALUES 
        ('$id_empleado', '$etapa_actual', '$cantidad_piezas', '$fecha_hoy', '$id_validador', 'Aprobado', '$total_a_pagar', '$id_mueble')";
        
        if(!mysqli_query($link, $sqlBitacora)) {
            die("Error al guardar nómina: " . mysqli_error($link));
        }

    } else {
        // ERROR CRÍTICO: SI NO ENCUENTRA AL EMPLEADO, AVISA Y NO AVANZA
        echo "<script>
            alert('¡ERROR! No se puede pagar porque no encuentro al usuario \"$nombre_operario\" en la base de datos.\\n\\nVerifica que el nombre asignado coincida con un usuario registrado.');
            window.history.back();
        </script>";
        exit(); // Detiene la ejecución para no mover el mueble
    }

    // B) MOVER MUEBLE (Solo si pasó la validación de empleado)
    $nuevo_asignado = "NULL"; 
    $nuevo_sub = "cola"; 

    if(!empty($_POST['nuevo_asignado'])) {
        $asignado = mysqli_real_escape_string($link, $_POST['nuevo_asignado']);
        $nuevo_asignado = "'$asignado'";
    }

    $update = "UPDATE muebles 
               SET id_estatus_mueble = '$etapa_siguiente', 
                   sub_estatus = '$nuevo_sub', 
                   asignado_a = $nuevo_asignado 
               WHERE id_muebles = '$id_mueble'";
               
    if(mysqli_query($link, $update)) {
        echo "<script>window.location.href = 'adm_registros.php';</script>";
    } else {
        echo "<script>alert('Error SQL al mover: ".mysqli_error($link)."');</script>";
    }
}

// Cargar personal siguiente
$resPer = mysqli_query($link, "SELECT usu_nom, usu_ap_pat, usu_puesto FROM usuarios WHERE usu_puesto IS NOT NULL");
$listaSiguiente = [];
while($p = mysqli_fetch_assoc($resPer)) {
    $nom = $p['usu_nom'].' '.$p['usu_ap_pat'];
    $puesto = strtolower($p['usu_puesto']);
    if($etapa_siguiente==3 && strpos($puesto,'armad')!==false) $listaSiguiente[] = $nom;
    if($etapa_siguiente==4 && strpos($puesto,'barniz')!==false) $listaSiguiente[] = $nom;
    if($etapa_siguiente==5 && strpos($puesto,'pint')!==false) $listaSiguiente[] = $nom;
    if($etapa_siguiente==6 && strpos($puesto,'adorn')!==false) $listaSiguiente[] = $nom;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Validar - Idealiza</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&family=Roboto+Mono:wght@500&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        :root { --primary: #144c3c; --bg: #F0F2F5; --surface: #FFFFFF; }
        body { font-family: 'Roboto', sans-serif; background: var(--bg); display: flex; justify-content: center; padding: 20px; }
        .card { background: var(--surface); padding: 30px; border-radius: 16px; width: 100%; max-width: 420px; border-top: 5px solid var(--primary); box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        h2 { color: var(--primary); text-align: center; margin-top:0; display:flex; align-items:center; justify-content:center; gap:8px;}
        .info-row { display: flex; justify-content: space-between; margin-bottom: 8px; color: #555; border-bottom: 1px dashed #eee; padding-bottom:4px;}
        .math-card { background: #e8f5e9; border: 1px solid #c8e6c9; padding: 15px; border-radius: 8px; margin: 20px 0; text-align: center; }
        .math-equation { font-family: 'Roboto Mono', monospace; font-size: 1.2rem; color: #0f382c; font-weight: bold; display: block; }
        .btn-ok { background: var(--primary); color: white; width: 100%; padding: 15px; border: none; border-radius: 50px; font-weight: bold; cursor: pointer; margin-top:15px;}
        .btn-cancel { display: block; text-align: center; margin-top: 15px; color: #888; text-decoration: none; }
        select, input[type=radio] { margin-top: 5px; }
        select { width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #ccc; }
    </style>
</head>
<body>
    <div class="card">
        <h2><span class="material-icons">verified</span> Validar Calidad</h2>
        
        <div class="info-row"><span>Modelo:</span> <strong><?php echo $mueble['modelos_nombre']; ?></strong></div>
        <div class="info-row"><span>Operario:</span> <strong><?php echo $nombre_operario ?: 'N/A'; ?></strong></div>

        <form method="POST">
            <div class="math-card">
                <span style="font-size:0.8rem; letter-spacing:1px; display:block; margin-bottom:5px;">PAGO A REGISTRAR</span>
                <span class="math-equation">
                    $<?php echo number_format($total_a_pagar, 2); ?>
                </span>
                <?php if($precio_unitario == 0): ?>
                    <div style="color:red; font-size:0.8rem; margin-top:5px; font-weight:bold;">
                        ⚠ ALERTA: Se registrarán $0.00
                    </div>
                <?php endif; ?>
            </div>

            <p style="text-align:center; color:#666;">Destino: <strong><?php echo $nombreSig; ?></strong></p>
            
            <label style="display:block; margin-bottom:10px;">
                <input type="radio" name="modo" checked onclick="document.getElementById('sel').disabled=true;"> A Cola General
            </label>
            <label style="display:block; margin-bottom:10px;">
                <input type="radio" name="modo" onclick="document.getElementById('sel').disabled=false;"> Asignar a:
            </label>
            <select name="nuevo_asignado" id="sel" disabled>
                <option value="">-- Seleccionar --</option>
                <?php foreach($listaSiguiente as $p) { echo "<option value='$p'>$p</option>"; } ?>
            </select>

            <button type="submit" name="aprobar" class="btn-ok">CONFIRMAR Y PAGAR</button>
            <a href="adm_registros.php" class="btn-cancel">Cancelar</a>
        </form>
    </div>
</body>
</html>
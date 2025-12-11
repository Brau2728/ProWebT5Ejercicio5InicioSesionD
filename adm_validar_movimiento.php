<?php
session_start();
if (!isset($_SESSION['usuario'])) { header('Location: login.php'); exit(); }
include("php/conexion.php");

// 1. CONEXIÓN PUENTE
$link = mysqli_connect('localhost', 'root', '', 'equipo', '3306');

$id_mueble = mysqli_real_escape_string($link, $_GET['id']);

// OBTENER DATOS DEL MUEBLE
$sql = "SELECT m.*, mo.modelos_nombre, mo.modelos_imagen FROM muebles m 
        INNER JOIN modelos mo ON m.id_modelos = mo.id_modelos 
        WHERE id_muebles = '$id_mueble'";
$res = mysqli_query($link, $sql);
$mueble = mysqli_fetch_assoc($res);

// DATOS ACTUALES
$etapa_actual = $mueble['id_estatus_mueble'];
$nombre_operario = $mueble['asignado_a'];
$cantidad_piezas = (int)$mueble['mue_cantidad'];

// =========================================================================
// 2. BUSCAR PRECIO AUTOMÁTICO (DEL CATÁLOGO DE TARIFAS)
// =========================================================================
$precio_unitario = 0;
$nombre_etapa_cobro = "Trabajo General";

// Mapeo: ID Etapa -> Columna en Base de Datos
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
    
    // Consultamos si hay precio configurado para este mueble
    $sqlP = "SELECT $columna FROM precios_empleados WHERE id_muebles = '$id_mueble'";
    $resP = mysqli_query($link, $sqlP);
    if ($resP && mysqli_num_rows($resP) > 0) {
        $filaP = mysqli_fetch_assoc($resP);
        $precio_unitario = (float)$filaP[$columna];
    }
}

// Calculamos el total a pagar
$total_a_pagar = $precio_unitario * $cantidad_piezas;
// =========================================================================


// DATOS SIGUIENTES
$etapa_siguiente = $etapa_actual + 1;
// Si ya es 6 (Adornado), el siguiente es 7 (Almacén)
$nombresEtapas = [2=>'Maquila', 3=>'Armado', 4=>'Barnizado', 5=>'Pintado', 6=>'Adornado', 7=>'Almacén Terminado'];
$nombreSig = $nombresEtapas[$etapa_siguiente] ?? 'Siguiente Etapa';


// 3. PROCESAR VALIDACIÓN (AL DAR CLIC EN APROBAR)
if(isset($_POST['aprobar'])) {
    
    // A) GUARDAR EN BITÁCORA (NÓMINA)
    // Buscamos ID del empleado por nombre (para ligarlo a la nómina)
    $sqlUser = "SELECT id_usuario FROM usuarios WHERE CONCAT(usu_nom, ' ', usu_ap_pat) LIKE '%".trim($nombre_operario)."%' LIMIT 1";
    $resUser = mysqli_query($link, $sqlUser);
    $rowUser = mysqli_fetch_assoc($resUser);
    $id_empleado = $rowUser ? $rowUser['id_usuario'] : 'NULL';

    if($id_empleado != 'NULL') {
        $fecha_hoy = date('Y-m-d H:i:s');
        $id_validador = 1; // Aquí podrías usar $_SESSION['id_usuario'] si lo tienes en sesión
        
        $sqlBitacora = "INSERT INTO bitacora_produccion 
        (id_usuario_empleado, id_etapa, cantidad_reportada, fecha_reporte, id_usuario_validador, estado_validacion, monto_pago, id_muebles)
        VALUES 
        ('$id_empleado', '$etapa_actual', '$cantidad_piezas', '$fecha_hoy', '$id_validador', 'Aprobado', '$total_a_pagar', '$id_mueble')";
        
        mysqli_query($link, $sqlBitacora);
    }

    // B) MOVER MUEBLE
    $nuevo_asignado = "NULL"; 
    $nuevo_sub = "cola"; // Por defecto lo mandamos a la cola general de la siguiente área

    // Si seleccionó asignar directo a alguien
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
        // Redirigir al monitor
        echo "<script>window.location='adm_registros.php';</script>";
    }
}

// Cargar personal siguiente para el select
$resPer = mysqli_query($link, "SELECT usu_nom, usu_ap_pat, usu_puesto FROM usuarios WHERE usu_puesto IS NOT NULL");
$listaSiguiente = [];
while($p = mysqli_fetch_assoc($resPer)) {
    $nom = $p['usu_nom'].' '.$p['usu_ap_pat'];
    $puesto = strtolower($p['usu_puesto']);
    // Filtramos empleados según la etapa a la que va el mueble
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
    <title>Validar Paso - Idealiza</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        /* PALETA: #144c3c, #94745c, #cedfcd, #5d6b62 */
        body { font-family: 'Quicksand', sans-serif; background: #F0F2F5; display: flex; justify-content: center; padding-top: 40px; }
        
        .card { 
            background: white; padding: 40px; border-radius: 16px; width: 90%; max-width: 450px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.1); 
            border-top: 6px solid #144c3c; /* Verde Corporativo */
        }
        
        h2 { 
            color: #144c3c; text-align: center; margin: 0 0 20px 0; font-weight: 700; 
            display: flex; align-items: center; justify-content: center; gap: 10px;
        }
        
        .info-row { 
            display: flex; justify-content: space-between; margin-bottom: 12px; color: #5d6b62; border-bottom: 1px dashed #eee; padding-bottom: 5px;
        }
        .info-row strong { color: #144c3c; }

        /* SECCIÓN DE PAGO AUTOMÁTICO */
        .price-display {
            background: #cedfcd; border: 1px solid #a8c6a6; padding: 20px; border-radius: 12px; 
            margin: 25px 0; text-align: center;
        }
        .price-label { font-size: 0.85rem; color: #144c3c; font-weight: bold; display: block; margin-bottom: 5px; text-transform: uppercase; }
        .price-value { font-size: 2rem; color: #144c3c; font-weight: 800; display: block; }
        .price-calc { font-size: 0.85rem; color: #5d6b62; margin-top: 5px; display: block; }

        .price-warning {
            background: #FFF3E0; border: 1px solid #FFE0B2; color: #E65100; padding: 15px; 
            border-radius: 8px; margin: 20px 0; text-align: center; font-size: 0.9rem; font-weight: bold;
        }

        /* OPCIONES */
        label { display: block; cursor: pointer; padding: 10px 0; transition: 0.2s; color: #333; }
        input[type="radio"] { accent-color: #144c3c; transform: scale(1.2); margin-right: 8px; }
        
        select { 
            width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #94745c; 
            margin-bottom: 20px; font-family: inherit; color: #333; outline: none; background: white;
        }

        /* BOTONES */
        .btn-ok { 
            background: #144c3c; color: white; width: 100%; padding: 15px; border: none; border-radius: 30px; 
            font-weight: bold; cursor: pointer; font-size: 1rem; transition: 0.3s;
            box-shadow: 0 4px 10px rgba(20, 76, 60, 0.3);
        }
        .btn-ok:hover { background: #0f382c; transform: translateY(-2px); }
        
        .btn-cancel { display: block; text-align: center; margin-top: 15px; color: #94745c; text-decoration: none; font-weight: bold; }
        .btn-cancel:hover { color: #7a604c; text-decoration: underline; }
    </style>
</head>
<body>
    <div class="card">
        <h2><span class="material-icons">verified</span> Validar Calidad</h2>
        
        <div class="info-row">
            <span>Modelo:</span> <strong><?php echo $mueble['modelos_nombre']; ?></strong>
        </div>
        <div class="info-row">
            <span>Color:</span> <strong><?php echo $mueble['mue_color']; ?></strong>
        </div>
        <div class="info-row">
            <span>Operario:</span> <strong><?php echo $nombre_operario ?: 'Sin asignar'; ?></strong>
        </div>
        <div class="info-row" style="border-bottom:none;">
            <span>Cantidad Lote:</span> <strong><?php echo $cantidad_piezas; ?> pzas</strong>
        </div>

        <form method="POST">
            
            <?php if($precio_unitario > 0) { ?>
                <div class="price-display">
                    <span class="price-label">PAGO POR <?php echo strtoupper($nombre_etapa_cobro); ?></span>
                    <span class="price-value">$<?php echo number_format($total_a_pagar, 2); ?></span>
                    <span class="price-calc">($<?php echo $precio_unitario; ?> x <?php echo $cantidad_piezas; ?> piezas)</span>
                </div>
            <?php } else { ?>
                <div class="price-warning">
                    <span class="material-icons">warning</span> Sin tarifa configurada.<br>
                    Se registrará pago en $0.00
                </div>
            <?php } ?>

            <p style="font-weight:bold; color:#5d6b62; margin-bottom:5px;">Destino: <?php echo $nombreSig; ?></p>
            
            <label>
                <input type="radio" name="modo" checked onclick="document.getElementById('sel').disabled=true;">
                Enviar a Cola General (Sin dueño)
            </label>
            
            <label>
                <input type="radio" name="modo" onclick="document.getElementById('sel').disabled=false;">
                Asignar siguiente paso a:
            </label>
            
            <select name="nuevo_asignado" id="sel" disabled>
                <option value="">-- Seleccionar Personal --</option>
                <?php foreach($listaSiguiente as $p) { echo "<option value='$p'>$p</option>"; } ?>
            </select>

            <button type="submit" name="aprobar" class="btn-ok">APROBAR Y REGISTRAR</button>
            <a href="adm_registros.php" class="btn-cancel">Cancelar</a>
        </form>
    </div>
</body>
</html>
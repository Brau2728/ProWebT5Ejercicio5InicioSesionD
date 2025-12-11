<?php
session_start();
include("conexion.php");

// 1. CONEXIÓN MANUAL SEGURA
$link = mysqli_connect('localhost', 'root', '', 'equipo', '3306');
if (!$link) die("Error conexión");

$id = mysqli_real_escape_string($link, $_POST['id_mueble']);
$persona = mysqli_real_escape_string($link, $_POST['persona_asignada']);
$cantidadAsignar = (int)$_POST['cantidad_asignar'];

if(!empty($id) && !empty($persona) && $cantidadAsignar > 0) {
    
    // Obtener datos del lote original (que está en cola general)
    $sqlInfo = "SELECT * FROM muebles WHERE id_muebles = '$id'";
    $resInfo = mysqli_query($link, $sqlInfo);
    $mueble = mysqli_fetch_assoc($resInfo);
    
    $cantidadTotal = (int)$mueble['mue_cantidad'];

    // CASO A: Asigna TODO el lote
    if ($cantidadAsignar >= $cantidadTotal) {
        $sql = "UPDATE muebles SET asignado_a = '$persona', sub_estatus = 'cola' WHERE id_muebles = '$id'";
        mysqli_query($link, $sql);
    }
    // CASO B: Asignación PARCIAL (División)
    else {
        // 1. Restamos al lote original (se queda en cola general sin dueño)
        $nuevaCantidadRestante = $cantidadTotal - $cantidadAsignar;
        $sqlReduce = "UPDATE muebles SET mue_cantidad = '$nuevaCantidadRestante' WHERE id_muebles = '$id'";
        mysqli_query($link, $sqlReduce);

        // 2. Creamos el nuevo lote PARA LA PERSONA
        $sqlClon = "INSERT INTO muebles 
                    (id_modelos, id_estatus_mueble, mue_cantidad, mue_color, mue_herraje, mue_precio, asignado_a, sub_estatus)
                    VALUES 
                    ('{$mueble['id_modelos']}', '{$mueble['id_estatus_mueble']}', '$cantidadAsignar', '{$mueble['mue_color']}', '{$mueble['mue_herraje']}', '{$mueble['mue_precio']}', '$persona', 'cola')";
        mysqli_query($link, $sqlClon);
    }
}

header("Location: ../adm_registros.php");
?>
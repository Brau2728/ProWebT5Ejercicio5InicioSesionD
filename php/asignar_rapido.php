<?php
// php/asignar_rapido.php - CORREGIDO: Deja el mueble en 'cola' para que el usuario le de 'Iniciar'
session_start();
include("conexion.php");

// Blindaje conexión
if (!isset($link) || $link === null) {
    $link = mysqli_connect('localhost', 'root', '', 'equipo', 3306);
}

if (!isset($_SESSION['usuario'])) { exit(); }

$id_mueble = intval($_POST['id_mueble']); 
$cantidad_asignar = intval($_POST['cantidad_asignar']);
$persona = mysqli_real_escape_string($link, $_POST['persona_asignada']);

$qPadre = mysqli_query($link, "SELECT * FROM muebles WHERE id_muebles = $id_mueble");
$padre = mysqli_fetch_assoc($qPadre);

if ($padre && $cantidad_asignar > 0) {
    $cant_actual = intval($padre['mue_cantidad']);
    
    // CASO A: TOTAL
    if ($cantidad_asignar >= $cant_actual) {
        // CAMBIO AQUÍ: sub_estatus se queda en 'cola'
        $sql = "UPDATE muebles SET asignado_a = '$persona', sub_estatus = 'cola' WHERE id_muebles = $id_mueble";
        mysqli_query($link, $sql);
    } 
    // CASO B: SPLIT
    else {
        $nueva_cant_padre = $cant_actual - $cantidad_asignar;
        mysqli_query($link, "UPDATE muebles SET mue_cantidad = $nueva_cant_padre WHERE id_muebles = $id_mueble");
        
        $id_modelo = $padre['id_modelos'];
        $id_estatus = $padre['id_estatus_mueble'];
        $color = $padre['mue_color'];
        $herraje = $padre['mue_herraje'];
        $id_pedido_sql = ($padre['id_pedido']) ? "'".$padre['id_pedido']."'" : "NULL";
        $comentario_sql = mysqli_real_escape_string($link, $padre['mue_comentario']);

        // CAMBIO AQUÍ: sub_estatus = 'cola'
        $sqlInsert = "INSERT INTO muebles 
                      (id_modelos, id_estatus_mueble, mue_cantidad, mue_color, mue_herraje, mue_comentario, asignado_a, sub_estatus, id_pedido) 
                      VALUES 
                      ('$id_modelo', '$id_estatus', '$cantidad_asignar', '$color', '$herraje', '$comentario_sql', '$persona', 'cola', $id_pedido_sql)";
                      
        mysqli_query($link, $sqlInsert);
    }
}
header("Location: ../adm_registros.php");
?>
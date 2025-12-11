<?php
session_start();
include("conexion.php");

$link = mysqli_connect('localhost', 'root', '', 'equipo', '3306');

$id_modelo = mysqli_real_escape_string($link, $_GET['mod']);
$color     = mysqli_real_escape_string($link, trim(urldecode($_GET['col']))); // Limpieza extra
$herraje   = mysqli_real_escape_string($link, trim(urldecode($_GET['herr'])));
$cantidad_salir = (int)$_GET['cant'];

if($cantidad_salir > 0) {
    // Usamos LIKE para que sea flexible con espacios
    $sql = "SELECT id_muebles, mue_cantidad FROM muebles 
            WHERE id_estatus_mueble = 7 
            AND id_modelos = '$id_modelo' 
            AND mue_color LIKE '$color%' 
            AND mue_herraje LIKE '$herraje%' 
            ORDER BY id_muebles ASC";
            
    $res = mysqli_query($link, $sql);

    while($cantidad_salir > 0 && $row = mysqli_fetch_assoc($res)) {
        $lote_id = $row['id_muebles'];
        $lote_cant = (int)$row['mue_cantidad'];

        if($lote_cant <= $cantidad_salir) {
            mysqli_query($link, "DELETE FROM muebles WHERE id_muebles = '$lote_id'");
            $cantidad_salir -= $lote_cant;
        } else {
            $nueva_cant = $lote_cant - $cantidad_salir;
            mysqli_query($link, "UPDATE muebles SET mue_cantidad = '$nueva_cant' WHERE id_muebles = '$lote_id'");
            $cantidad_salir = 0;
        }
    }
}

header("Location: ../adm_registros.php");
exit();
?>
<?php
session_start();

if (!isset($_SESSION['usuario']) || !isset($_SESSION['tipo'])) {
    echo "Usuario no Logueado";
    header('Location: login.php');
    exit();
}

include("php/conexion.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener los datos del formulario
    $id_mueble = $_POST['id_mueble'];
    $id_modelo = $_POST['id_modelo'];
    $estatus = $_POST['estatus'];
    $precio = $_POST['precio'];
    $cantidad = $_POST['cantidad'];
    $color = $_POST['color'];
    $herraje = $_POST['herraje'];

    // Query para actualizar el mueble en la base de datos
    $query = "UPDATE muebles 
              SET id_modelos = '$id_modelo', 
                  id_estatus_mueble = '$estatus', 
                  mue_precio = '$precio', 
                  mue_cantidad = '$cantidad', 
                  mue_color = '$color', 
                  mue_herraje = '$herraje' 
              WHERE id_muebles = '$id_mueble'";

    // Ejecutar la consulta
    $result = db_query($query);

    if ($result) {
        // Actualización exitosa
        echo "Mueble actualizado correctamente.";
    } else {
        // Error en la actualización
        echo "Hubo un error al actualizar el mueble.";
    }
}
?><meta http-equiv="refresh" content="0;URL=adm_registros.php">
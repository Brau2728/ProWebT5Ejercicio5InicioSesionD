<?php
include("php/conexion.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener los datos del formulario
    $id_modelo = $_POST['id_modelo'];
    $estatus = $_POST['estatus'];
    $precio = $_POST['precio'];
    $cantidad = $_POST['cantidad'];
    $color = $_POST['color'];
    $herraje = $_POST['herraje'];

    // Query para insertar el mueble en la base de datos
    $query = "INSERT INTO muebles (id_modelos, id_estatus_mueble, mue_precio, mue_cantidad, mue_color, mue_herraje) 
              VALUES ('$id_modelo', '$estatus', '$precio', '$cantidad', '$color', '$herraje')";

    // Ejecutar la consulta
    $result = db_query($query);

    if ($result) {
        // La inserción fue exitosa
        echo "Mueble insertado correctamente.";
    } else {
        // Error en la inserción
        echo "Hubo un error al insertar el mueble.";
    }
}
?>
<meta http-equiv="refresh" content="0; URL=adm_registros.php">
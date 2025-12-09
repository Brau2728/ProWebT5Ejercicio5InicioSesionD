<?php
session_start();

if (!isset($_SESSION['usuario']) || !isset($_SESSION['tipo'])) {
    echo "Usuario no logueado";
    header('Location: login.php');
    exit();
}

include("php/conexion.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener valores del formulario
    $modelo_nombre = $_POST['modelos_nombre'];
    $modelo_descripcion = $_POST['modelos_descripcion'];
    $modelo_imagen = $_POST['modelos_imagen'];

    // Validar los datos, por ejemplo, asegurarse de que los campos no estén vacíos

    // Insertar los datos en la base de datos
    $cons = insert(
        'modelos',
        "NULL, '$modelo_nombre', '$modelo_descripcion', '$modelo_imagen'"
    );

    if ($cons) {
        echo "<script languaje='javascript'>alert('El modelo se añadió correctamente a la base de datos');</script>";
        header('Location: adm_modelos.php'); // Redireccionar después de la inserción
        exit(); // Asegurar que no se ejecute más código después de la redirección
    } else {
        echo "<script languaje='javascript'>alert('El modelo no pudo ser insertado en la base de datos.');</script>";
        header('Location: adm_modelos.php'); // Redireccionar en caso de error
        exit(); // Asegurar que no se ejecute más código después de la redirección
    }
}
?><meta http-equiv="refresh" content="0; URL=adm_modelos_registrar_modelos.php">
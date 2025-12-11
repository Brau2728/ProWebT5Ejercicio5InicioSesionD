<?php
session_start();
// 1. Seguridad
if (!isset($_SESSION['usuario'])) {
    header("Location: ../login.php");
    exit();
}

// 2. Conexión Puente (Para que no falle el DELETE)
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'equipo';
$db_port = '3306';

$link = mysqli_connect($db_host, $db_user, $db_pass, $db_name, $db_port);
if (!$link) { die("Error de conexión: " . mysqli_connect_error()); }

// 3. Borrar
if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($link, $_GET['id']);

    // Intentamos borrar
    $sql = "DELETE FROM usuarios WHERE id_usuario = '$id'";

    if (mysqli_query($link, $sql)) {
        // Si borra bien
        echo "<script>alert('Usuario eliminado correctamente.'); window.location.href='../adm_usuarios.php';</script>";
    } else {
        // Si falla por historial (Foreign Key)
        if (mysqli_errno($link) == 1451) {
            echo "<script>alert('ALERTA: No se puede borrar este usuario porque tiene historial en Nómina, Bitácora o Producción.\\n\\nEl sistema protege estos datos para no perder reportes.'); window.location.href='../adm_usuarios.php';</script>";
        } else {
            echo "<script>alert('Error técnico al borrar: " . mysqli_error($link) . "'); window.location.href='../adm_usuarios.php';</script>";
        }
    }
}
?>
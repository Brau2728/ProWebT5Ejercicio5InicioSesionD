<?php
session_start();
include("conexion.php");
$link = mysqli_connect('localhost', 'root', '', 'equipo', '3306');
$id = $_GET['id'];
mysqli_query($link, "DELETE FROM muebles WHERE id_muebles = '$id'");
header("Location: ../adm_registros.php");
?>
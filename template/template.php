<?php session_start();
//validamos si se ha hecho o no el inicio de sesion correctamente
//si no se ha hecho la sesion nos regresará a login.php
    if(!isset($_SESSION['usuario']) || !isset($_SESSION['tipo']) ){
        echo "Usuario no Logueado";
        header('Location: login.php'); 
        exit();
    }
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <meta name="description" content="Sistemas computacionales">
    <meta name="keywords" content="MySql, conexión, Wamp">
    <meta name="author" content="Ramirez Erik, Sistemas">

  <title></title>
  <link rel="stylesheet" href="css/estilos.css">
  <link rel="stylesheet" href="css/menu.css">
</head>

<body>
<div id="container">
    <div id="header">
        <!-- ************  MENU  *************** -->
        <?php include('php/menu.php');?>
    </div>
    <div id="content">
        <div id="section">
            <!-- ************  CONTENIDO  *************** -->
            
        </div>            
    </div>
    <!-- ************  FOOTER  *************** -->
    <?php include("php/footer.php"); ?>
</div>
</body>
</html>
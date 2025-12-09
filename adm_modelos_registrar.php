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
    <link rel="stylesheet" href="css/estilos_admin.css">

  
  <link rel="stylesheet" href="css/menu1.css">
    <link rel="icon" href="Img/Icons/logo-idealisa.ico" type="image/png">

    <!-- --------------------------------------------------------- -->
    <link rel="stylesheet" href="estilos/Wave2.css">
    <!-- <link rel="stylesheet" href="estilos/btnIdiom.css"> -->
    <!-- Fuente Personalizada -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300&display=swap" rel="stylesheet">

    <?php include("php/conexion.php"); ?>
  <title>resgistrar-admin</title>
  <link rel="stylesheet" href="css/estilos_admin.css">
  <link rel="stylesheet" href="css/menu.css">
</head>

<body>
<body>
    <article>
        <?php include('php/header_admin.php');?>
        <?php include('php/menu_admin.php');?>
        <div id="container">
            <div id="">
                <h1>Registro de modelo</h1>
                <form id="form1" name="form1" method="post" action="adm_modelos_registrar_modelos.php" style="text-align:center;" onsubmit="return validarForm(this);">
                    <!-- Campos del formulario para registrar modelo -->
                    <p><label for="modelos_nombre">Nombre del modelo</label></p><br>
                    <input type="text" name="modelos_nombre" id="modelos_nombre" />

                    <!-- Otros campos para descripción, imagen, etc. -->
                    <p><label for="modelos_descripcion">Descripción</label><br>
                    <input type="text" name="modelos_descripcion" id="modelos_descripcion" /></p>

                    <p><label for="modelos_imagen">URL de la imagen</label><br>
                    <input name="modelos_imagen" type="text" /></p>

                    <p><button name="btn_guardar" id="btn_guardar" class="button">Guardar</button></p>
                </form>
            </div>            
        </div>
        <!-- Resto del contenido o footer -->
        <div class="wave wave1"> </div>
    <div class="wave wave2"> </div>
    <div class="wave wave3"> </div>
    <div class="wave wave4"> </div>
     </article>
<script src="js/validacion.js"></script>
 <!-- ************  FOOTER  *************** -->
 <?php include("php/footer.php"); ?>
 </div>
<!-- Extencion para los icnos de redes sociales-->
<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
     <!-- Extencion para los icnos de redes sociales-->


</body>
</html>
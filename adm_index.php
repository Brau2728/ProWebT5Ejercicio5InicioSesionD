
<!-- ``-------------------------------------------------- -->
<!-- ----------Inicio de Administador------------------------- -->
<!-- ---------------idioma español---------------------------------- -->

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
 

  <title> Amin-Inicio - Idealiza</title>
 
    



  
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/5.0.0/normalize.min.css">
  <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css'>
  <link rel="stylesheet" href="css/estilos_admin.css">
  <link rel="stylesheet" href="css/menu1.css">
  <link rel="stylesheet" href="estilos/Wave2.css">
  <link rel="icon" href="Img/Icons/logo-idealisa.ico" type="image/png">
    <!-- Fuente Personalizada -->
   <link rel="stylesheet" href="css/menu.css">
</head>


<body>

<article>

        <!-- ************  HEADER *************** -->
        <?php include('php/header_admin.php');?>

        
       

        <!-- ************  MENU  *************** -->
        <?php  include('php/menu_admin.php');
        ?>
    
    <div id="content2">
        <div id="section">

        <div colo id="clock"></div>
            <!-- ************  CONTENIDO  *************** -->
            <h1>Bienvenido al inicio de amdinistrador</h1>
           <br>
            <p>En este apartado podras relizar varias acciones las cuales te ayudara a administrar de una mejor manera.</p>
            <br>
            <p>Dar de alta, baja y modificar usuarios los que tienen acceso al sistema </p>
            <p> <a href="adm_usuario.php" class="button2">Usuarios</a></p>
            
            <br>
            <p>control de productos</p>
            <p> <a href="adm_modelos.php" class="button2">Productos</a></p>
            <br>
            <p>Consultar inventario</p>
            <p><button>Inventario</button></p>
            <br>
            <p>Control de empleados</p>
            <p><button>Empleado</button></p>
            <br>
           

            


        </div>
    </div>
    <div class="wave wave1"> </div>
    <div class="wave wave2"> </div>
    <div class="wave wave3"> </div>
    <div class="wave wave4"> </div>
     </article>
    <!-- ************  FOOTER  *************** -->
    <?php include("php/footer.php"); ?>
</div>
   <!-- Extencion para los icnos de redes sociales-->
   <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
     <!-- Extencion para los icnos de redes sociales-->

</body>
</html>
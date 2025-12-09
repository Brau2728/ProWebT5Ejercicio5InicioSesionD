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


  <title> Amin-Usuarios_Contraseñas - Idealiza</title>
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
    <?php include("php/conexion.php"); ?>
</head>

<body>
  
<article>
<?php include('php/header_admin.php');?>
        <!-- ************  MENU  *************** -->
        <?php include('php/menu_admin.php');?>
    
    <div id="content">
        <div id="section">
            <!-- ************  CONTENIDO  *************** -->
            <h1>Menu de Usuarios-Contrsaeñas</h1>
            <br>
  
            
            <!-- <p>Buscar Usuario <input type="text"><button>Buscar</button></p> -->
            <!-- <p><a href="adm_usuario_registrar.php" class="button2">Crear usuario</a></p>
            <br> -->
            <br>
            <p><a href="adm_usuario.php" class="button2">Regresar a los registros</a></p>
            <br>
            <h2>Usuarios Existentes y Contrseñas</h2>
            <?php
            //invocar la funcion select y la tabla
            $result = select("usuarios");
            // Realizamos un bucle que muestre el resultado
            ?>
        <table border=1 align="center">
          <thead>
            <td>Id</td>
            <td>Nombre</td>
            <td>Apellido</td>
            <td>Contraseña</td>
            <td>Acciones</td>
          </thead>
          <?php
          if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_object($result)) {
          ?>
              <tr>
                
                <td><?php echo $row->id_usuario; ?></td>
                <td><?php echo $row->usu_nom; ?></td>
                <td><?php echo $row->usu_ap_pat; ?></td>
                <td><?php echo $row->usu_password; ?></td>
                <td>
                  <a href="adm_usuario_modificar.php?id=<?php echo $row->id_usuario; ?>" class="button">Modificar</a> /                  
                  <a href="adm_usuario_eliminar.php?id=<?php echo $row->id_usuario; ?>" onclick="return confirmarEliminacion()" class="buttonDelete">Eliminar</a> 

                </td>
              </tr>
          <?php

            }
          } else {
            echo "no hay ningun registro";
          }
          ?>
        </table>
            
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
<script>
  function confirmarEliminacion() {
    if (confirm("¿Realmente desea eliminar el registro?")) {
      return true;
    }
    return false;
  }
</script>

 <!-- Extencion para los icnos de redes sociales-->
 <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
     <!-- Extencion para los icnos de redes sociales-->
     
</body>
</html>
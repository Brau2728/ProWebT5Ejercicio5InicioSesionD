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


  <title> Amin-Usuarios - Idealiza</title>
  <link rel="stylesheet" href="css/estilos_admin.css">
  <link rel="stylesheet" href="css/perfiles.css">
  <link rel="stylesheet" href="css/menu1.css">
    <link rel="icon" href="Img/Icons/logo-idealisa.ico" type="image/png">

    <!-- --------------------------------------------------------- -->
    <link rel="stylesheet" href="estilos/Wave2.css">
    <!-- <link rel="stylesheet" href="estilos/btnIdiom.css"> -->
    <!-- Fuente Personalizada -->
   

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/5.0.0/normalize.min.css">
  <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css'>
  <link rel="stylesheet" href="css/estilos_admin.css">
  <link rel="stylesheet" href="css/menu1.css">
  <link rel="stylesheet" href="css/perfiles.css">
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
            <h1>Menu de Usuarios</h1>
            <br>
  
            
            <!-- <p>Buscar Usuario <input type="text"><button>Buscar</button></p> -->
            <p><a href="adm_usuario_registrar.php" class="button2">Crear usuario</a></p>
            <br>
            <p><a href="adm_usuario_contra.php" class="button2">Ver contraseñas</a></p>
            
            <h2>Usuarios Existentes</h2>
            <div class="wrapper">
        <div class="container">
            <!-- Inicio de la sección para mostrar perfiles -->
            <div class="row row--flex">
            <?php
$result = select("usuarios");
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_object($result)) {
        $imagen_url = $row->usu_img ? $row->usu_img : 'https://icon-library.com/images/user-png-icon/user-png-icon-16.jpg';
?>
        <div class="col-md-4">
            <div class="card card--profile card--grid">
                <div class="card__content">
                    <!-- Contenedor de la imagen -->
                    <div class="card__media">
                        <!-- Imagen del perfil -->
                        <img class="card__img" src="<?php echo $imagen_url; ?>" alt="Imagen de perfil" />
                    </div>
                    <p class="card__name"><?php echo $row->usu_nom . ' ' . $row->usu_ap_pat . ' ' . $row->usu_ap_mat; ?></p>
                    <!-- Resto de la información del perfil -->
                    <address class="card__contact">
                        <p class="card__contact-item">
                            <strong>Email:</strong> <?php echo $row->usu_email; ?>
                        </p>
                        <p class="card__contact-item">
                            <strong>Tipo:</strong> <?php echo $row->usu_tipo; ?>
                        </p>
                        <p class="card__contact-item">
                            <strong>Puesto:</strong> <?php echo $row->usu_puesto; ?>
                        </p>
                        <p class="card__contact-item">
                            <strong>Sexo:</strong> <?php echo $row->usu_sexo; ?>
                        </p>
                        <p class="card__contact-item">
                            <strong>Fecha de Nacimiento:</strong> <?php echo $row->usu_fecha_nacimiento; ?>
                        </p>
                    </address>
                    <!-- Botones de Modificar y Eliminar -->
                    <div class="card__buttons">
                        <a href="adm_usuario_modificar.php?id=<?php echo $row->id_usuario; ?>" class="button">Modificar</a>
                        <br><br>
                        <a href="adm_usuario_eliminar.php?id=<?php echo $row->id_usuario; ?>" onclick="return confirmarEliminacion()" class="buttonDelete">Eliminar</a>
                    </div>
                </div>
            </div>
        </div>
<?php
    }
} else {
    echo "No hay ningún registro de usuarios";
}
?>
<!-- Fin de la sección para mostrar perfiles -->
</div>
    </div>
            
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
     <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js'></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/handlebars.js/4.0.11/handlebars.min.js'></script>
    

</body>
</html>
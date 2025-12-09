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
  <link rel="stylesheet" href="css/perfiles.css">
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
        <!-- Incluir el header y el menú -->
        <?php include('php/header_admin.php'); ?>
        <?php include('php/menu_admin.php'); ?>
    
        <div id="content">
    <div id="section">
        <h1>Menú de Modelos</h1>
        <br>
        <p><a href="adm_modelos_registrar.php" class="button2">Crear modelo</a></p>
        <h2>Modelos Existentes</h2>
<div class="container">
    <div class="row">
        <?php
        $result = select("modelos");
        if ($result && mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_object($result)) {
                $imagen_url = $row->modelos_imagen ? $row->modelos_imagen : 'img/logo.jpg'; // URL por defecto si no hay imagen
        ?>
               <div class="col-md-4">
    <div class="card card--profile card--grid d-flex flex-column align-items-stretch h-100">
        <div class="card__content d-flex flex-column">
            <div class="card__media">
                <!-- Imagen del modelo -->
                <img class="card__img" src="<?php echo $imagen_url; ?>" alt="Imagen del modelo" />
            </div>
            <p class="card__name text-truncate mb-0"><?php echo $row->modelos_nombre; ?></p>
            <p class="card__contact-item text-truncate"><?php echo $row->modelos_descripcion; ?></p>
            <!-- Nuevos datos -->
            
            
            
           
            <!-- Resto de los datos anteriores -->
            <div class="mt-auto">
                <a href="adm_modelos_modificar.php?id=<?php echo $row->id_modelos; ?>" class="button btn-block">Modificar</a>
                <a href="adm_modelos_eliminar.php?id=<?php echo $row->id_modelos; ?>" onclick="return confirmarEliminacion()" class="buttonDelete btn-block">Eliminar</a>
            </div>
        </div>
    </div>
</div>
        <?php
            }
        } else {
            echo "No hay ningún registro de modelos";
        }
        ?>
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

    <script> $(document).ready(function() {
    function setCardSizes() {
        $('.row--flex').each(function() {
            var cards = $(this).find('.card--profile');
            var maxHeight = 0;

            cards.css('height', 'auto'); // Restablecer la altura a 'auto'

            // Obtener la altura máxima de las tarjetas en la fila
            cards.each(function() {
                maxHeight = Math.max(maxHeight, $(this).outerHeight());
            });

            // Establecer la altura máxima para todas las tarjetas en esa fila
            cards.css('height', maxHeight);
        });
    }

    // Llama a la función para establecer el tamaño al cargar la página y al cambiar el tamaño de la ventana
    setCardSizes();
    $(window).resize(setCardSizes);
});</script>
   
</body>
</html>
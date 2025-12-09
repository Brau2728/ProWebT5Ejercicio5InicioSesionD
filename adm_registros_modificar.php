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


  <title> Amin-Registros-Modificar-Idealiza</title>
  
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

 
 
</head>

<body>
  
<article>
<?php include('php/header_admin.php');?>
        <!-- ************  MENU  *************** -->
        <?php include('php/menu_admin.php');?>
    
    <div id="">
        
<h1>Modificar Mueble</h1>

<?php
// Recuperar el ID del mueble a modificar desde la URL
if (isset($_GET['id'])) {
    $id_mueble = $_GET['id'];
    
    // Realizar la consulta para obtener los detalles del mueble a partir de su ID
    include("php/conexion.php");
    $query_mueble = "SELECT * FROM muebles WHERE id_muebles = $id_mueble";
    $result_mueble = db_query($query_mueble);
    
    if ($result_mueble && mysqli_num_rows($result_mueble) > 0) {
        $mueble = mysqli_fetch_assoc($result_mueble);
?>
    <!-- Formulario para modificar el mueble -->
    <form method="post" action="actualizar_mueble.php">
        <input type="hidden" name="id_mueble" value="<?php echo $mueble['id_muebles']; ?>">
        
        <label for="modelo">Modelo:</label>
        <select id="modelo" name="id_modelo">
            <!-- Mostrar opciones de modelos -->
            <?php
            $query_modelos = "SELECT id_modelos, modelos_nombre FROM modelos";
            $result_modelos = db_query($query_modelos);
            
            while ($row = mysqli_fetch_assoc($result_modelos)) {
                $selected = ($row['id_modelos'] == $mueble['id_modelos']) ? 'selected' : '';
                echo "<option value='" . $row['id_modelos'] . "' $selected>" . $row['modelos_nombre'] . "</option>";
            }
            ?>
        </select>
        <!-- Formulario para modificar el mueble -->
        <form method="post" action="actualizar_mueble.php">
                <input type="hidden" name="id_mueble" value="<?php echo $mueble['id_muebles']; ?>">

                <label for="estatus">Estatus:</label>
                <select id="estatus" name="estatus">
                    <option value="1" <?php if ($mueble['id_estatus_mueble'] == 1) echo "selected"; ?>>Sin estado</option>
                    <option value="2" <?php if ($mueble['id_estatus_mueble'] == 2) echo "selected"; ?>>Maquilado</option>
                    <option value="3" <?php if ($mueble['id_estatus_mueble'] == 3) echo "selected"; ?>>Armado</option>
                    <option value="4" <?php if ($mueble['id_estatus_mueble'] == 4) echo "selected"; ?>>Barnizado</option>
                    <option value="5" <?php if ($mueble['id_estatus_mueble'] == 5) echo "selected"; ?>>Pintado</option>
                    <option value="6" <?php if ($mueble['id_estatus_mueble'] == 6) echo "selected"; ?>>Adornado</option>
                    <option value="7" <?php if ($mueble['id_estatus_mueble'] == 7) echo "selected"; ?>>Terminado</option>
                </select>

                <label for="precio">Precio:</label>
                <input type="text" id="precio" name="precio" value="<?php echo $mueble['mue_precio']; ?>">

                <label for="cantidad">Cantidad:</label>
                <input type="number" id="cantidad" name="cantidad" min="1" max="100" value="<?php echo $mueble['mue_cantidad']; ?>">

                <label for="color">Color:</label>
                <select id="color" name="color">
                    <option value="Chocolate" <?php if ($mueble['mue_color'] == 'Chocolate') echo "selected"; ?>>Chocolate</option>
                    <option value="Vino" <?php if ($mueble['mue_color'] == 'Vino') echo "selected"; ?>>Vino</option>
                    <option value="Nogal" <?php if ($mueble['mue_color'] == 'Nogal') echo "selected"; ?>>Nogal</option>
                     <option value="Negro"<?php if ($mueble['mue_color'] == 'Negro') echo "selected"; ?>>Negro</option>
                     <option value="Rojo"<?php if ($mueble['mue_color'] == 'Rojo') echo "selected"; ?>>Rojo</option>
                     <option value="Conbinado"<?php if ($mueble['mue_color'] == 'Conbinado') echo "selected"; ?>>Conbinado</option>
                     <option value="Otro"<?php if ($mueble['mue_color'] == 'Otro') echo "selected"; ?>>Otro</option>
                </select>

                <label for="herraje">Herraje:</label>
                <select id="herraje" name="herraje">
                    <option value="Metal" <?php if ($mueble['mue_herraje'] == 'Metal') echo "selected"; ?>>Metal</option>
                    <option value="Plástico" <?php if ($mueble['mue_herraje'] == 'Plástico') echo "selected"; ?>>Plástico</option>
                </select>
                <br>

                <input type="submit" value="Actualizar">
            </form>

    <?php
        } else {
            echo "No se encontró el mueble.";
        }
    } else {
        echo "No se proporcionó un ID de mueble válido.";
    }
    ?>
        <!-- Otras etiquetas para los campos a modificar -->
        
        
    


            
                 
    </div>
    <div class="wave wave1"> </div>
    <div class="wave wave2"> </div>
    <div class="wave wave3"> </div>
    <div class="wave wave4"> </div>
     </article>
    <!-- ************  FOOTER  *************** -->
    <?php include("php/footer.php"); ?>

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
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

 
  <title> Amin-Usuarios-Modificar - Idealiza</title>
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
    <?php include('php/header_admin.php');?>
    <?php include('php/menu_admin.php');?>
    
    <div id="">
        <div id="">
            <?php
            $var_id = $_GET['id'];
            echo "Registro a modificar: $var_id";
            $result = select_where("modelos", "id_modelos = $var_id");

            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_object($result)) {
            ?>
                    <!-- Contenido para modificar el modelo -->
                    <h1>Modificando modelo</h1>
                    <form id="form1" name="form1" method="post" action="adm_modelos_modificar_modelos.php" style="text-align:center;" onsubmit="return validarForm(this);">
                        <input name="txt_id" type="hidden" value="<?php echo $row->id_modelos; ?>" />
                        
                        <!-- Campos para modificar nombre, descripción, imagen, estatus y color del modelo -->
                        <p><label for="txt_Nombre">Nombre</label></p><br>
                        <input type="text" name="txt_Nombre" id="txt_Nombre" value="<?php echo $row->modelos_nombre; ?>" />
                        <br><br>
                        <p><label for="txt_Descripcion">Descripción</label></p><br>
                        <textarea name="txt_Descripcion" id="txt_Descripcion"><?php echo $row->modelos_descripcion; ?></textarea>
                        <br><br>
                        <!-- <p><label for="modelos_precio">Precio</label></p><br>
<p><input type="text" name="modelos_precio" id="modelos_precio" value="<?php echo $row->modelos_precio; ?>" /></p>

<br> -->
                        <p><label for="url_imagen">URL de la imagen</label></p><br>
                        <input name="url_imagen" type="text" value="<?php echo $row->modelos_imagen; ?>" />
                        <br><br>
                        
                        <br>
                        <br>
                        
                        <!-- Botón de Actualizar -->
                        <p><button name="btn_actualizar" id="btn_actualizar" class="button">Actualizar</button></p>
                    </form>
            <?php
                }
            } else {
                echo "No hay ningún registro de modelos";
            }
            ?>
        </div>
    </div>
    <!-- ************  FOOTER  *************** -->
   
    <div class="wave wave1"> </div>
    <div class="wave wave2"> </div>
    <div class="wave wave3"> </div>
    <div class="wave wave4"> </div>
     </article> <?php include("php/footer.php"); ?>
<script src="js/validacion.js"></script>

<!-- Extencion para los icnos de redes sociales-->
<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
     <!-- Extencion para los icnos de redes sociales-->


     <script>
function mostrarPuesto(select) {
    var campoPuesto = document.getElementById("campoPuesto");
    if (select.value === "empleado") {
        campoPuesto.style.display = "block";
    } else {
        campoPuesto.style.display = "none";
        document.getElementById("emp_puesto").value = ""; // Establecer el valor como vacío si no es empleado
    }
}
</script>
<!-- Extencion para los icnos de redes sociales-->
<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
     <!-- Extencion para los icnos de redes sociales-->

</body>
</html>
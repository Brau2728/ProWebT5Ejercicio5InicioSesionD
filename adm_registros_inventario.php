<?php
session_start();

// Verificar si se ha iniciado sesión correctamente
if (!isset($_SESSION['usuario']) || !isset($_SESSION['tipo'])) {
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

    <title>Administración de Muebles - Idealiza</title>
    <link rel="stylesheet" href="css/estilos_admin.css">
    <link rel="stylesheet" href="css/menu1.css">
    <link rel="icon" href="Img/Icons/logo-idealisa.ico" type="image/png">

    <!-- --------------------------------------------------------- -->
    <link rel="stylesheet" href="estilos/Wave2.css">
    <!-- Fuente Personalizada -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300&display=swap" rel="stylesheet">
   
</head>
<body>
  
<article>
<?php include('php/header_admin.php');?>
        <!-- ************  MENU  *************** -->
        <?php include('php/menu_admin.php');?>
    
    <div id="">
        <div id="section">
        
    <!-- ... (código previo) ... -->

    <!-- Formulario para añadir un mueble -->
    <h2>Añadir Mueble</h2>
<form method="post" action="insertar_mueble.php">
    <label for="modelo">Modelo:</label>
    <select id="modelo" name="id_modelo">
        <?php
        include("php/conexion.php");

        // Realizar una consulta para obtener los nombres de los modelos
        $query_modelos = "SELECT id_modelos, modelos_nombre FROM modelos";
        $result_modelos = db_query($query_modelos);

        // Mostrar los nombres de los modelos en un menú desplegable
        while ($row = mysqli_fetch_assoc($result_modelos)) {
            echo "<option value='" . $row['id_modelos'] . "'>" . $row['modelos_nombre'] . "</option>";
        }
        ?>
    </select>



        <label for="estatus">Estatus:</label>
        <select id="estatus" name="estatus">
            <option value="1">Sin estado</option>
            <option value="2">Maquilado</option>
            <option value="3">Armado</option>
            <option value="4">Barnizado</option>
            <option value="5">Pintado</option>
            <option value="6">Adornado</option>
            <option value="7">Terminado</option>
        </select>

        <label for="precio">Precio:</label>
<input type="text" id="precio" name="precio">

<label for="cantidad">Cantidad:</label>
<input type="number" id="cantidad" name="cantidad" min="1" max="100" value="1">

<label for="color">Color:</label>
<select id="color" name="color">
    <option value="Chocolate">Chocolate</option>
    <option value="Vino">Vino</option>
    <option value="Nogal">Nogal</option>
    <option value="Negro">Negro</option>
    <option value="Rojo">Rojo</option>
    <option value="Conbinado">Conbinado</option>
</select>

<label for="herraje">Herraje:</label>
<select id="herraje" name="herraje">
    <option value="Metal">Metal</option>
    <option value="Plástico">Plástico</option>
    <option value="Otro">Otro</option>
</select>
<br>
<input type="submit" value="Añadir Mueble">
</form>
    </div>            
    
    <div class="wave wave1"> </div>
    <div class="wave wave2"> </div>
    <div class="wave wave3"> </div>
    <div class="wave wave4"> </div>
</article>
<?php include("php/footer.php"); ?>
</div>
<!-- Extencion para los icnos de redes sociales-->
<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
     <!-- Extencion para los icnos de redes sociales-->
     <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js'></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/handlebars.js/4.0.11/handlebars.min.js'></script>
    


</body>
</html>

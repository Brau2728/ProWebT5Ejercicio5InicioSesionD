<?php session_start();
//validamos si se ha hecho o no el inicio de sesion correctamente
//si no se ha hecho la sesion nos regresará a login.php
    if(!isset($_SESSION['usuario']) || !isset($_SESSION['tipo']) ){
        echo "Usuario no Logueado";
        header('Location: login.php'); 
        exit();
    }
?>
<?php include("php/conexion.php"); 

// Asignación de valores recibidos del formulario a las variables
$var_email = $_POST['ema_email'];
$var_pass = $_POST['pas_password'];
$var_tipo = $_POST['lst_Tipo'];
$var_apPat = $_POST['txt_ApPat'];
$var_apMat = $_POST['txt_ApMat'];
$var_nombre = $_POST['txt_Nombre'];
$var_fecha_nacimiento = $_POST['cal_fecha_nacimiento'];
$var_sexo = $_POST['lst_Sexo'];
$var_url_imagen = $_POST['url_imagen'];

// Verificar si es empleado para obtener el puesto
if ($var_tipo === 'empleado') {
    $var_puesto = $_POST['lst_puesto'];
} else {
    $var_puesto = null; // Establecer como NULL si no es empleado
}

// Mostrar los datos recibidos
echo "<h2>Datos recibidos</h2>
      <hr>
      <p>Usted ingresó los siguientes datos:</p>
      <h3>Nombre: $var_nombre</h3>
      <h3>Apellido Paterno: $var_apPat</h3>
      <h3>Apellido Materno: $var_apMat</h3>
      <h3>Tipo de usuario: $var_tipo</h3>
      <h3>Email: $var_email</h3>
      <h3>Puesto: $var_puesto</h3>
      <h3>Password: $var_pass</h3>
      <h3>Fecha de Nacimiento: $var_fecha_nacimiento</h3>
      <h3>Sexo: $var_sexo</h3>
      <h3>URL de la imagen: $var_url_imagen</h3>";

// Realizar la inserción de datos en la tabla con la siguiente sentencia SQL
$cons = insert(
    'usuarios',
    "NULL, '$var_nombre', '$var_apPat', '$var_apMat', '$var_fecha_nacimiento', '$var_sexo', '$var_tipo', '$var_puesto', '$var_url_imagen', '$var_pass', '$var_email'"
);
      

if($cons) {
?>
<script languaje="javascript" >alert("El Usuario SE AÑADIO CORRECTAMENTE a la base de datos");</script> 
<?php
} else {
?>
<script languaje="javascript" >alert("El usuario no pudo ser insertada en la base de datos.");</script>
<?php
}
?>
<meta http-equiv="refresh" content="0;URL=adm_usuario.php" > 

<?php
include("php/conexion.php");

// Obtener las variables
$var_email = $_POST['ema_email'];
$var_pass = $_POST['pas_password'];
$var_apPat = $_POST['txt_ApPat'];
$var_apMat = $_POST['txt_ApMat'];
$var_nombre = $_POST['txt_Nombre'];
$var_fecha_nacimiento = $_POST['cal_fecha_nacimiento'];
$var_sexo = $_POST['lst_Sexo'];

// Mostrar los datos recibidos
echo "<h2>Datos recibidos</h2>
      <hr>
      <p>Usted ingresó los siguientes datos:</p>
      <h3>Nombre: $var_nombre</h3>
      <h3>Apellido Paterno: $var_apPat</h3>
      <h3>Apellido Materno: $var_apMat</h3>
      <h3>Email: $var_email</h3>
      <h3>Password: $var_pass</h3>
      <h3>Fecha de Nacimiento: $var_fecha_nacimiento</h3>
      <h3>Sexo: $var_sexo</h3>";

// Realizar la inserción de datos en la tabla
$estado_por_defecto = 'en_espera'; // Definir el estado por defecto

$cons = insert(
    'usuarios',
    "NULL, '$var_nombre', '$var_apPat', '$var_apMat', '$var_fecha_nacimiento', '$var_sexo', '$estado_por_defecto', NULL, NULL, '$var_pass', '$var_email'"
);

// Verificar si la inserción fue exitosa
if ($cons) {
?>
    <script language="javascript">alert("El Usuario SE AÑADIÓ CORRECTAMENTE a la base de datos");</script>
<?php
} else {
?>
    <script language="javascript">alert("El usuario no pudo ser insertado en la base de datos.");</script>
<?php
}
?>
<meta http-equiv="refresh" content="0;URL=login.php">

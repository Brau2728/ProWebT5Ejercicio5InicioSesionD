<?php
session_start();

// Verificar si se ha iniciado sesión correctamente
if (!isset($_SESSION['usuario']) || !isset($_SESSION['tipo'])) {
    echo "Usuario no Logueado";
    header('Location: login.php');
    exit();
}

include("php/conexion.php");

// Obtener las variables del formulario
$var_id = $_POST['txt_id'];
$var_nombre = $_POST['txt_Nombre'];
$var_descripcion = $_POST['txt_Descripcion'];
$var_imagen = $_POST['url_imagen'];

?>

<h2>Datos recibidos</h2>
<hr>
<p>Usted ingresó los siguientes datos:</p>
<?php
// Mostrar los datos recibidos
echo "<h3>$var_id</h3>
      <h3>$var_nombre</h3>
      <h3>$var_descripcion</h3>
      <h3>$var_imagen</h3>";

// Realizar la actualización de datos en la tabla utilizando la siguiente sentencia SQL
// Actualización de solo tres campos: nombre, descripción e imagen
$cons = update(
    "modelos",
    "modelos_nombre='$var_nombre',
    modelos_descripcion='$var_descripcion',
    modelos_imagen='$var_imagen'",
    "id_modelos=$var_id"
);

if ($cons) {
?>
    <script language="javascript">alert("El modelo se modificó correctamente en la base de datos");</script>
<?php
} else {
?>
    <script language="javascript">alert("El modelo no pudo ser modificado en la base de datos.");</script>
<?php
}
?>
<meta http-equiv="refresh" content="0;URL=adm_modelos.php">

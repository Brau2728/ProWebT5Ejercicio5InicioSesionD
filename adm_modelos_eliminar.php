<?php
session_start();
// Verificar si se ha iniciado sesión correctamente
if (!isset($_SESSION['usuario']) || !isset($_SESSION['tipo'])) {
    echo "Usuario no Logueado";
    header('Location: login.php');
    exit();
}

include("php/conexion.php");

// Obtener el ID del modelo a eliminar desde el parámetro GET
$var_id = $_GET['id'];
echo $var_id;

// Invocar la función delete
$result = delete("modelos", "id_modelos = $var_id");

// Realizar la eliminación del modelo
if ($result) {
?>
    <script language="javascript">alert("El modelo se borró correctamente de la base de datos");</script>
<?php
} else {
?>
    <script language="javascript">alert("El modelo no pudo ser eliminado de la base de datos.");</script>
<?php
}
?>
<meta http-equiv="refresh" content="0;URL=adm_modelos.php">

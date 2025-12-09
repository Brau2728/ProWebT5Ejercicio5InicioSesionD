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

//obtener las variables
$var_id = $_POST['txt_id'];
$var_email = $_POST['ema_email'];
$var_pass = $_POST['pas_password'];
$var_tipo = $_POST['lst_Tipo'];
$var_apPat = $_POST['txt_ApPat'];
$var_apMat = $_POST['txt_ApMat'];
$var_nombre = $_POST['txt_Nombre'];
$var_fecha = $_POST['cal_fecha_nacimiento'];
$var_sexo = $_POST['lst_Sexo'];
$var_imagen = $_POST['url_imagen'];
$var_puesto = $_POST['lst_puesto'];

?>

<h2>Datos recibidos</h2>
<hr>
<p>Usted ingreso los siguientes datos:</p>
<?php
//mostrar los datos recibidos
echo "<h3>$var_id</h3>
      <h3>$var_nombre</h3>
      <h3>$var_apPat</h3>
      <h3>$var_apMat</h3>
      <h3>$var_fecha</h3>
      <h3>$var_tipo</h3>
      <h3>$var_email</h3>
      <h3>$var_sexo</h3>
      <h3>$var_puesto</h3>
      <h3>$var_imagen</h3>
      <h3>$var_pass</h3>";


//realizar la inserción de datos en la tabla con la siguiente sentencia SQL
//insert into t_usuario values( "NULL" , "" , "" , "" ,   , "" )
//considere que la insercion de la primary Key es nula ya que es autoincrmentable


$cons = update(
    "usuarios",
    "usu_nom='$var_nombre', usu_ap_pat='$var_apPat', usu_ap_mat='$var_apMat', usu_fecha_nacimiento='$var_fecha', usu_sexo='$var_sexo', usu_tipo='$var_tipo', usu_puesto='$var_puesto', usu_img='$var_imagen', usu_password='$var_pass', usu_email='$var_email'",
    "id_usuario=$var_id"
);

if ($cons) {
?>
    <script language="javascript">alert("El Usuario SE MODIFICÓ CORRECTAMENTE en la base de datos");</script>
<?php
} else {
?>
    <script language="javascript">alert("El usuario no pudo ser modificado en la base de datos.");</script>
<?php
}
?>
<meta http-equiv="refresh" content="0; URL=adm_usuario.php">

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
$var_id=$_GET['id'];
echo $var_id;
//invocar la funcion select y la tabla
$result = delete("usuarios", "id_usuario = $var_id");
// Realizamos un bucle que muestre el resultado
?>
<script languaje="javascript" >alert("El Usuario SE BORRO CORRECTAMENTE a la base de datos");</script> 

<meta http-equiv="refresh" content="0;URL=adm_usuario.php" > 

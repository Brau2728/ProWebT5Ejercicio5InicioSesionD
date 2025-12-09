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
   
  <title>resgistrar-admin</title>
  <title> Amin-Usuarios-Modificar - Idealiza</title>
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
    <?php include("php/conexion.php"); ?></head>

<body>
  <article>
<?php include('php/header_admin.php');?>
        <!-- ************  MENU  *************** -->
        <?php include('php/menu_admin.php');?>
<div id="container">
    <div id="header">
        
    
            <!-- ************  CONTENIDO  *************** -->
            <h1>Registro de usuario</h1>
            <form id="form1" name="form1" method="post" action="adm_usuario_registrar_usuario.php" style="text-align:center;" onsubmit="return validarForm(this);">
    <p><label for="ema_email">Email</label></p><br>
    <input name="ema_email" type="email" required onkeyup="javascript:this.value=this.value.toLowerCase();" />

    <p><label for="txt_Nombre">Nombre</label><br>
    <input type="text" name="txt_Nombre" id="txt_Nombre" onkeyup="javascript:this.value=this.value.toUpperCase();" />
    </p>

    <p><label for="txt_ApPat">Apellido Paterno</label><br>
    <input type="text" name="txt_ApPat" id="txt_ApPat" onkeyup="javascript:this.value=this.value.toUpperCase();" />
    </p>

    <p><label for="txt_ApMat">Apellido Materno</label><br>
    <input type="text" name="txt_ApMat" id="txt_ApMat" onkeyup="javascript:this.value=this.value.toUpperCase();" />
    </p>

    <p>
        <label for="cal_fecha_nacimiento">Fecha de Nacimiento</label><br>
        <input type="date" name="cal_fecha_nacimiento" id="cal_fecha_nacimiento">
    </p>

    <p>
        <label for="lst_Sexo">Sexo</label><br>
        <select name="lst_Sexo" id="lst_Sexo">
            <option value="masculino">Masculino</option>
            <option value="femenino">Femenino</option>
        </select>
    </p>

    <p><label for="url_imagen">URL de la imagen</label><br>
    <input name="url_imagen" type="text" />
    </p>

    <p><label for="lst_Tipo">Tipo de usuario</label><br>
    <select name="lst_Tipo" id="lst_Tipo" onchange="mostrarPuesto(this)">
        <option value="admin">Admin</option>
        <option value="gerente">Gerente</option> 
        <option value="cliente">Cliente</option>
        <option value="empleado">Empleado</option>
    </select>
    </p>

    <div id="campoPuesto" style="display: none;">
        <label style="color: #2C4D32;" for="lst_puesto">Puesto</label>
        <select name="lst_puesto" id="lst_puesto">
            <option value="maquilador">Maquilador</option>
            <option value="armador">Armador</option>
            <option value="barnizador">Barnizador</option>
            <option value="pintor">Pintor</option>
            <option value="adornador">Adornador</option>
        </select>
    </div>

  <br>
    <input name="pas_password" type="password" required />
    <p><label for="pas_password">Password</label><br>
    </p>
<br>
  <input name="pas_password2" type="password" required />
  <p><label for="pas_password2">Confirmar Password</label><br>
    
    </p>
    
    <p><button name="btn_guardar" id="btn_guardar" class="button">Guardar</button> boton que nos envía a crear_usuario.php</p>
</form>
        </div>            
    </div>
    <!-- ************  FOOTER  *************** -->
  
</div>
<div class="wave wave1"> </div>
    <div class="wave wave2"> </div>
    <div class="wave wave3"> </div>
    <div class="wave wave4"> </div>
     </article>
<script src="js/validacion.js"></script>
 <!-- ************  FOOTER  *************** -->
 <?php include("php/footer.php"); ?>
 </div>

<script>
    function mostrarPuesto(select) {
        var selectedOption = select.value;
        var campoPuesto = document.getElementById("campoPuesto");
        if (selectedOption === "empleado") {
            campoPuesto.style.display = "block";
        } else {
            campoPuesto.style.display = "none";
        }
    }
</script>
</body>
</html>
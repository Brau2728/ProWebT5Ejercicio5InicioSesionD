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
        <!-- ************  MENU  *************** -->
        <?php include('php/menu_admin.php');?>
        <!-- ************  MENU  *************** -->
        
    <div id="content">
        <div id="section">
        <?php
        $var_id = $_GET['id'];
        echo "Registro a modifica:  $var_id";
        //invocar la funcion select y la tabla
        $result = select_where("usuarios", "id_usuario = $var_id");
        

        if (mysqli_num_rows($result) > 0) {
          while ($row = mysqli_fetch_object($result)) {
        ?>
 
            <!-- ************  CONTENIDO  *************** -->
            <h1>Modifcando usuario</h1>
            <form id="form1" name="form1" method="post" action="adm_usuario_modificar_usuario.php" style="text-align:center;" onsubmit="return validarForm(this);" >
            <input name="txt_id" type="hidden" value="<?php echo $row->id_usuario; ?>" />

            <p><label for="ema_email">Email </label></p><br>
            <input name="ema_email" type="email" required onkeyup="javascript:this.value=this.value.toLowerCase();" value="<?php echo $row->usu_email; ?>"/>

          <p><label for="txt_Nombre">Nombre </label><br>
            <input type="text" name="txt_Nombre" id="txt_Nombre" onkeyup="javascript:this.value=this.value.toUpperCase();" value="<?php echo $row->usu_nom; ?>"/>
          </p>
          <p><label for="txt_ApPat">Apellido Paterno </label><br>
            <input type="text" name="txt_ApPat" id="txt_ApPat" onkeyup="javascript:this.value=this.value.toUpperCase();" value="<?php echo $row->usu_ap_pat; ?>"/>
          </p>
          <p><label for="txt_ApMat">Apellido Materno </label><br>
            <input type="text" name="txt_ApMat" id="txt_ApMat" onkeyup="javascript:this.value=this.value.toUpperCase();" value="<?php echo $row->usu_ap_mat; ?>"/>
          </p>
          <p>
            <label for="cal_fecha_nacimiento">Fecha de Nacimiento</label>
            <input type="date" name="cal_fecha_nacimiento" id="cal_fecha_nacimiento" value="<?php echo $row->usu_fecha_nacimiento; ?>">
            </p>
            <!-- <p><label for="lst_Sexo">Sexo</label><br>
           <input type="radio" id="sexo_femenino" name="lst_Sexo" value="F">
           <label for="sexo_femenino">Femenino</label><br>
           <input type="radio" id="sexo_masculino" name="lst_Sexo" value="M">
           <label for="sexo_masculino">Masculino</label><br>
           </p> -->

           <br><p>
    <label for="lst_Sexo">Sexo</label>
    <select name="lst_Sexo" id="lst_Sexo">
        <option value="masculino" <?php if ($row->usu_sexo === 'masculino') echo 'selected'; ?>>masculino</option>
        <option value="femenino" <?php if ($row->usu_sexo === 'femenino') echo 'selected'; ?>>femenino</option>
    </select>
</p>
<br>

          <p><label for="lst_Tipo">Tipo de usuario</label>
            <select name="lst_Tipo" id="lst_Tipo" onchange="mostrarPuesto(this)">
              
              <option value="admin" <?php if ($row->usu_tipo === 'admin') echo 'selected'; ?>>admin</option>
              <option value="supervisor" <?php if ($row->usu_tipo === 'supervisor') echo 'selected'; ?>>supervisor</option>
              <option value="empleado" <?php if ($row->usu_tipo === 'empleado') echo 'selected'; ?>>empleado</option>

            </select>
          </p>
          

          <div id="campoPuesto" style="display: none;">
          <br><label style="color: #2C4D32;" for="lst_puesto">Puesto</label>
          <select name="lst_puesto" id="lst_puesto">
            <option><?php echo $row->usu_puesto; ?></option>
            <option value="maquilador">maquilador</option>
            <option value="armador">armador</option>
            <option value="barnizador">barnizador</option>
            <option value="pintor">pintor</option>
            <option value="adornador">adornador</option>
        </select>
    </div>
    
    <div>
    <label for="url_imagen">URL de la imagen</label><br>
            <input name="url_imagen" type="text" value="<?php echo $row->usu_img; ?>" />

         
    </div>

          <br><p><label for="pas_password">Password </label><br>
          <input name="pas_password" type="password" required value="<?php echo $row->usu_password; ?>"/>
          </p>
          <p><label for="pas_password2">confirmar Password </label><br>
          <input name="pas_password2" type="password" required value="<?php echo $row->usu_password; ?>"/>
          </p>
          
          <p><button name="btn_actualizar" id="btn_actualizar" class="button">Actualizar</button></p>

          <?php

          }
        } else {
          echo "no hay ningun registro";
        }
        ?>
        </form>
        </div>            
    </div>
    <div class="wave wave1"> </div>
    <div class="wave wave2"> </div>
    <div class="wave wave3"> </div>
    <div class="wave wave4"> </div>
     </article>
    <!-- ************  FOOTER  *************** -->
    <?php include("php/footer.php"); ?>
</div>
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
</body>
</html>
<?php
$page = 'usuarios';
session_start();

if (!isset($_SESSION['usuario']) || !isset($_SESSION['tipo'])) {
    header("Location: login.php");
    exit();
}

include("php/conexion.php");

// 1. CONEXIÓN SEGURA (Puente)
$db_host_local = 'localhost';
$db_user_local = 'root';
$db_pass_local = '';
$db_name_local = 'equipo';
$db_port_local = '3306';

$link_seguridad = mysqli_connect($db_host_local, $db_user_local, $db_pass_local, $db_name_local, $db_port_local);
if (!$link_seguridad) { die("Error de conexión local."); }

// 2. OBTENER DATOS DEL USUARIO
if (isset($_GET['id'])) {
    $id_usuario = mysqli_real_escape_string($link_seguridad, $_GET['id']);
    $sql = "SELECT * FROM usuarios WHERE id_usuario = '$id_usuario'";
    $result = mysqli_query($link_seguridad, $sql);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_object($result);
    } else {
        echo "<script>alert('Usuario no encontrado'); window.location='adm_usuarios.php';</script>";
        exit();
    }
} else {
    header("Location: adm_usuarios.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modificar Usuario - Idealiza</title>
    
    <link rel="stylesheet" href="estilos/Wave2.css">
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <style>
        body { font-family: 'Quicksand', sans-serif; background-color: #F0F2F5; padding-bottom: 100px; }
        
        .form-wrapper {
            background: white; max-width: 800px; margin: 40px auto; padding: 40px;
            border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            /* Color Naranja de la imagen */
            border-top: 5px solid #EF6C00; 
        }

        h2 { color: #EF6C00; text-align: center; margin-bottom: 30px; font-weight: 700; }
        
        /* Grid */
        .input-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 15px; }
        
        .input-group { margin-bottom: 5px; }
        .input-group label { display: block; font-weight: bold; color: #555; margin-bottom: 8px; font-size: 0.9rem; }
        
        .form-control {
            width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px;
            font-family: inherit; font-size: 1rem; box-sizing: border-box; transition: 0.3s;
        }
        /* Foco Naranja */
        .form-control:focus { border-color: #EF6C00; outline: none; box-shadow: 0 0 0 3px rgba(239, 108, 0, 0.1); }

        /* Sección Puesto - Estilo de Alerta Naranja */
        .highlight-box {
            background: #FFF3E0; /* Fondo naranja muy claro */
            padding: 20px; border-radius: 12px; margin: 25px 0;
            border-left: 5px solid #EF6C00; /* Borde naranja fuerte */
            display: none; /* Se controla con JS */
        }
        .highlight-box label { color: #E65100; /* Texto naranja oscuro */ }

        /* Botones */
        .btn-submit {
            background: #EF6C00; /* Botón Naranja */
            color: white; border: none; width: 100%; padding: 15px;
            border-radius: 30px; font-size: 1.1rem; font-weight: bold; cursor: pointer;
            transition: 0.3s; margin-top: 10px; display: flex; justify-content: center; align-items: center; gap: 10px;
        }
        .btn-submit:hover { background: #E65100; transform: translateY(-2px); box-shadow: 0 5px 15px rgba(239, 108, 0, 0.3); }

        .btn-back {
            display: inline-flex; align-items: center; gap: 5px; text-decoration: none;
            color: #757575; font-weight: bold; margin-bottom: 20px; transition: 0.3s;
        }
        .btn-back:hover { color: #EF6C00; transform: translateX(-5px); }

        @media (max-width: 600px) {
            .input-row { grid-template-columns: 1fr; gap: 10px; }
            .form-wrapper { padding: 20px; }
        }
    </style>
</head>
<body>

    <?php include("php/encabezado_madera.php"); ?>
    <?php include("php/barra_navegacion.php"); ?>

    <div class="form-wrapper">
        <a href="adm_usuarios.php" class="btn-back">
            <span class="material-icons">arrow_back</span> Volver a Lista
        </a>

        <h2>Modificar Usuario</h2>

        <form action="adm_usuario_modificar_usuario.php" method="POST">
            
            <input type="hidden" name="txt_id" value="<?php echo $row->id_usuario; ?>">

            <div class="input-row">
                <div class="input-group">
                    <label>Nombre(s):</label>
                    <input type="text" name="txt_Nombre" class="form-control" value="<?php echo $row->usu_nom; ?>" onkeyup="this.value=this.value.toUpperCase();" required>
                </div>
                <div class="input-group">
                    <label>Apellido Paterno:</label>
                    <input type="text" name="txt_ApPat" class="form-control" value="<?php echo $row->usu_ap_pat; ?>" onkeyup="this.value=this.value.toUpperCase();" required>
                </div>
            </div>

            <div class="input-row">
                <div class="input-group">
                    <label>Apellido Materno:</label>
                    <input type="text" name="txt_ApMat" class="form-control" value="<?php echo $row->usu_ap_mat; ?>" onkeyup="this.value=this.value.toUpperCase();">
                </div>
                <div class="input-group">
                    <label>Fecha de Nacimiento:</label>
                    <input type="date" name="cal_fecha_nacimiento" class="form-control" value="<?php echo $row->usu_fecha_nacimiento; ?>" required>
                </div>
            </div>

            <div class="input-row">
                <div class="input-group">
                    <label>Sexo:</label>
                    <select name="lst_Sexo" class="form-control" required>
                        <option value="masculino" <?php if ($row->usu_sexo === 'masculino') echo 'selected'; ?>>Masculino</option>
                        <option value="femenino" <?php if ($row->usu_sexo === 'femenino') echo 'selected'; ?>>Femenino</option>
                    </select>
                </div>
                <div class="input-group">
                    <label>Tipo de Usuario:</label>
                    <select name="lst_Tipo" id="lst_Tipo" class="form-control" onchange="mostrarPuesto()" required>
                        <option value="admin" <?php if ($row->usu_tipo === 'admin') echo 'selected'; ?>>Admin</option>
                        <option value="supervisor" <?php if ($row->usu_tipo === 'supervisor') echo 'selected'; ?>>Supervisor</option>
                        <option value="empleado" <?php if ($row->usu_tipo === 'empleado') echo 'selected'; ?>>Empleado</option>
                        <option value="gerente" <?php if ($row->usu_tipo === 'gerente') echo 'selected'; ?>>Gerente</option>
                    </select>
                </div>
            </div>

            <div id="campoPuesto" class="highlight-box">
                <div class="input-group">
                    <label style="font-size:1.1rem;">Puesto de Trabajo:</label>
                    <select name="lst_puesto" class="form-control" style="font-weight:bold; color:#333;">
                        <option value="<?php echo $row->usu_puesto; ?>" selected><?php echo $row->usu_puesto; ?> (Actual)</option>
                        
                        <optgroup label="Producción">
                            <option value="maquilador">Maquilador</option>
                            <option value="armador">Armador / Carpintero</option>
                            <option value="barnizador">Barnizador</option>
                            <option value="pintor">Pintor</option>
                            <option value="adornador">Adornador</option>
                        </optgroup>
                        
                        <optgroup label="Otros">
                            <option value="terminado">Almacén</option>
                            <option value="supervisor">Supervisor</option>
                            <option value="administrador">Administrador</option>
                        </optgroup>
                    </select>
                    <small style="color:#666;">Define en qué lista aparecerá la persona en el Monitor.</small>
                </div>
            </div>

            <div class="input-row">
                <div class="input-group">
                    <label>Correo Electrónico:</label>
                    <input type="email" name="ema_email" class="form-control" value="<?php echo $row->usu_email; ?>" required>
                </div>
                <div class="input-group">
                    <label>URL Foto:</label>
                    <input type="text" name="url_imagen" class="form-control" value="<?php echo $row->usu_img; ?>">
                </div>
            </div>

            <div class="input-row">
                <div class="input-group">
                    <label>Contraseña:</label>
                    <input type="text" name="pas_password" class="form-control" value="<?php echo $row->usu_password; ?>" required>
                </div>
                <div class="input-group">
                    <label>Confirmar Contraseña:</label>
                    <input type="text" name="pas_password2" class="form-control" value="<?php echo $row->usu_password; ?>" required>
                </div>
            </div>

            <button type="submit" name="btn_actualizar" class="btn-submit">
                <span class="material-icons">save</span> Guardar Cambios
            </button>

        </form>
    </div>

    <?php include("php/olas.php"); ?>

    <script>
        function mostrarPuesto() {
            var tipo = document.getElementById("lst_Tipo").value;
            var caja = document.getElementById("campoPuesto");
            
            // Mostrar si es empleado, supervisor o admin (para que aparezcan en el monitor)
            if (tipo === "empleado" || tipo === "supervisor" || tipo === "admin") {
                caja.style.display = "block";
            } else {
                caja.style.display = "none";
            }
        }

        // Ejecutar al cargar para ver el estado inicial
        window.onload = mostrarPuesto;
    </script>

</body>
</html>
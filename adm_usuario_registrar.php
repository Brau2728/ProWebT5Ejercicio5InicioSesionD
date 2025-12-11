<?php
$page = 'usuarios'; // Para que la barra sepa que estamos en la sección de Personal
session_start();

if (!isset($_SESSION['usuario']) || !isset($_SESSION['tipo'])) {
    header("Location: login.php");
    exit();
}

include("php/conexion.php");

// ==========================================================
// PUENTE DE CONEXIÓN PARA GUARDAR DATOS
// ==========================================================
$db_host_local = 'localhost';
$db_user_local = 'root';
$db_pass_local = '';
$db_name_local = 'equipo';
$db_port_local = '3306';

$link_seguridad = mysqli_connect($db_host_local, $db_user_local, $db_pass_local, $db_name_local, $db_port_local);
if (!$link_seguridad) { die("Error de conexión local."); }

// --- PROCESAR FORMULARIO ---
if (isset($_POST['registrar'])) {
    
    // Recibir y limpiar datos
    $nombre    = mysqli_real_escape_string($link_seguridad, $_POST['nombre']);
    $ap_pat    = mysqli_real_escape_string($link_seguridad, $_POST['ap_pat']);
    $ap_mat    = mysqli_real_escape_string($link_seguridad, $_POST['ap_mat']);
    $fecha_nac = mysqli_real_escape_string($link_seguridad, $_POST['fecha_nac']);
    $sexo      = mysqli_real_escape_string($link_seguridad, $_POST['sexo']);
    
    $tipo      = mysqli_real_escape_string($link_seguridad, $_POST['tipo']);   
    $puesto    = mysqli_real_escape_string($link_seguridad, $_POST['puesto']); 
    
    $email     = mysqli_real_escape_string($link_seguridad, $_POST['email']);
    $password  = mysqli_real_escape_string($link_seguridad, $_POST['password']);
    
    // --- CORRECCIÓN DE IMAGEN POR DEFECTO ---
    // Usamos una URL de una silueta genérica (el "monito")
    $default_img = 'https://cdn.pixabay.com/photo/2015/10/05/22/37/blank-profile-picture-973460_960_720.png';
    // Si el campo está vacío, usamos la genérica. Si no, usamos la que pusieron.
    $img = isset($_POST['img']) && !empty($_POST['img']) ? mysqli_real_escape_string($link_seguridad, $_POST['img']) : $default_img;
    
    $id_role = 3; 

    $sql = "INSERT INTO usuarios 
            (usu_nom, usu_ap_pat, usu_ap_mat, usu_fecha_nacimiento, usu_sexo, usu_tipo, usu_puesto, usu_img, usu_password, usu_email, id_role)
            VALUES 
            ('$nombre', '$ap_pat', '$ap_mat', '$fecha_nac', '$sexo', '$tipo', '$puesto', '$img', '$password', '$email', '$id_role')";

    // Guardar
    if (db_query($sql)) {
        echo "<script>alert('¡Colaborador $nombre registrado correctamente!'); window.location='adm_usuarios.php';</script>";
    } else {
        echo "<script>alert('Error al registrar usuario.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Personal - Idealiza</title>
    
    <link rel="stylesheet" href="estilos/Wave2.css">
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <style>
        body { font-family: 'Quicksand', sans-serif; background-color: #F0F2F5; padding-bottom: 100px; }
        
        .form-wrapper {
            background: white; max-width: 800px; margin: 40px auto; padding: 40px;
            border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            border-top: 5px solid #1565C0;
        }

        h2 { color: #1565C0; text-align: center; margin-bottom: 30px; font-weight: 700; }
        
        .input-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 15px; }
        
        .input-group { margin-bottom: 5px; }
        .input-group label { display: block; font-weight: bold; color: #555; margin-bottom: 8px; font-size: 0.9rem; }
        
        .form-control {
            width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px;
            font-family: inherit; font-size: 1rem; box-sizing: border-box; transition: 0.3s;
        }
        .form-control:focus { border-color: #1565C0; outline: none; box-shadow: 0 0 0 3px rgba(21, 101, 192, 0.1); }

        .highlight-box {
            background: #E3F2FD; padding: 20px; border-radius: 12px; margin: 25px 0;
            border-left: 5px solid #1565C0;
        }
        .highlight-box label { color: #1565C0; }

        .btn-submit {
            background: #2E7D32; color: white; border: none; width: 100%; padding: 15px;
            border-radius: 30px; font-size: 1.1rem; font-weight: bold; cursor: pointer;
            transition: 0.3s; margin-top: 10px; display: flex; justify-content: center; align-items: center; gap: 10px;
        }
        .btn-submit:hover { background: #1B5E20; transform: translateY(-2px); box-shadow: 0 5px 15px rgba(46, 125, 50, 0.3); }

        .btn-back {
            display: inline-flex; align-items: center; gap: 5px; text-decoration: none;
            color: #757575; font-weight: bold; margin-bottom: 20px; transition: 0.3s;
        }
        .btn-back:hover { color: #1565C0; transform: translateX(-5px); }

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
            <span class="material-icons">arrow_back</span> Volver a Lista de Personal
        </a>

        <h2>Registrar Nuevo Colaborador</h2>

        <form method="POST" action="">
            
            <div class="input-row">
                <div class="input-group">
                    <label>Nombre(s):</label>
                    <input type="text" name="nombre" class="form-control" placeholder="Ej. Mary" required>
                </div>
                <div class="input-group">
                    <label>Apellido Paterno:</label>
                    <input type="text" name="ap_pat" class="form-control" placeholder="Ej. Pérez" required>
                </div>
            </div>

            <div class="input-row">
                <div class="input-group">
                    <label>Apellido Materno:</label>
                    <input type="text" name="ap_mat" class="form-control" placeholder="Ej. López">
                </div>
                <div class="input-group">
                    <label>Fecha de Nacimiento:</label>
                    <input type="date" name="fecha_nac" class="form-control" required>
                </div>
            </div>

            <div class="input-row">
                <div class="input-group">
                    <label>Sexo:</label>
                    <select name="sexo" class="form-control" required>
                        <option value="femenino">Femenino</option>
                        <option value="masculino">Masculino</option>
                    </select>
                </div>
                <div class="input-group">
                    <label>Nivel de Acceso (Sistema):</label>
                    <select name="tipo" class="form-control" required>
                        <option value="empleado">Empleado (Solo ver)</option>
                        <option value="admin">Administrador (Control Total)</option>
                        <option value="supervisor">Supervisor</option>
                        <option value="gerente">Gerente</option>
                    </select>
                </div>
            </div>

            <div class="highlight-box">
                <div class="input-group">
                    <label style="font-size:1.1rem;">Puesto de Trabajo (Define el área en el Monitor):</label>
                    <select name="puesto" class="form-control" required style="font-weight:bold; color:#333;">
                        <option value="">-- Selecciona el puesto --</option>
                        <optgroup label="Áreas de Producción">
                            <option value="maquilador">Maquilador</option>
                            <option value="armador">Armador / Carpintero</option>
                            <option value="barnizador">Barnizador</option>
                            <option value="pintor">Pintor</option>
                            <option value="adornador">Adornador</option>
                        </optgroup>
                        <optgroup label="Administrativo / Otros">
                            <option value="terminado">Almacén / Terminado</option>
                            <option value="supervisor">Supervisor</option>
                            <option value="administrador">Administrador</option>
                        </optgroup>
                    </select>
                    <small style="color:#666; display:block; margin-top:5px;">El sistema usará este dato para asignar tareas automáticamente.</small>
                </div>
            </div>

            <div class="input-row">
                <div class="input-group">
                    <label>Correo Electrónico:</label>
                    <input type="email" name="email" class="form-control" placeholder="nombre@correo.com" required>
                </div>
                <div class="input-group">
                    <label>Contraseña:</label>
                    <input type="password" name="password" class="form-control" placeholder="*******" required>
                </div>
            </div>

            <div class="input-group">
                <label>URL Foto de Perfil (Opcional):</label>
                <input type="text" name="img" class="form-control" placeholder="Si se deja vacío, se usará una imagen genérica.">
            </div>

            <button type="submit" name="registrar" class="btn-submit">
                <span class="material-icons">person_add</span> Registrar Colaborador
            </button>

        </form>
    </div>

    <?php include("php/olas.php"); ?>

</body>
</html>
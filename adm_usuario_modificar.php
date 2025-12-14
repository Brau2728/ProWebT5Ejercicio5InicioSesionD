<?php
$page = 'usuarios'; 
session_start();

if (!isset($_SESSION['usuario']) || !isset($_SESSION['tipo'])) {
    header("Location: login.php");
    exit();
}

// =======================================================================
// 1. CONEXIÓN BLINDADA (Tu red de seguridad)
// =======================================================================
$conexion = null;

// Intentamos cargar tu archivo de conexión original
$ruta_conexion = __DIR__ . '/php/conexion.php';
if (file_exists($ruta_conexion)) {
    include($ruta_conexion);
    // Buscamos las variables comunes que sueles usar
    if (isset($link)) $conexion = $link;
    if (isset($conn)) $conexion = $conn;
}

// SI FALLA LO ANTERIOR, CONECTAMOS MANUALMENTE (Plan B)
if (!$conexion) {
    try {
        $conexion = mysqli_connect('localhost', 'root', '', 'equipo', 3306);
    } catch (Exception $e) {
        // Si falla aquí, el error se muestra abajo
    }
}

// Si después de todo no hay conexión, detenemos el script
if (!$conexion) {
    die("<div style='color:red; text-align:center; padding:20px;'>Error Crítico: No se pudo conectar a la base de datos. Verifica que XAMPP esté encendido.</div>");
}
// =======================================================================

// 2. OBTENER DATOS DEL USUARIO (GET)
if (isset($_GET['id'])) {
    $id_usuario = mysqli_real_escape_string($conexion, $_GET['id']);
    $sql = "SELECT * FROM usuarios WHERE id_usuario = '$id_usuario'";
    $result = mysqli_query($conexion, $sql);
    
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

// 3. PROCESAR ACTUALIZACIÓN (POST)
if (isset($_POST['btn_actualizar'])) {
    
    // Recibir datos
    $nombre    = mysqli_real_escape_string($conexion, $_POST['txt_Nombre']);
    $ap_pat    = mysqli_real_escape_string($conexion, $_POST['txt_ApPat']);
    $ap_mat    = mysqli_real_escape_string($conexion, $_POST['txt_ApMat']);
    $fecha_nac = mysqli_real_escape_string($conexion, $_POST['cal_fecha_nacimiento']);
    $sexo      = mysqli_real_escape_string($conexion, $_POST['lst_Sexo']);
    $tipo      = mysqli_real_escape_string($conexion, $_POST['lst_Tipo']);
    $puesto    = mysqli_real_escape_string($conexion, $_POST['lst_puesto']);
    $email     = mysqli_real_escape_string($conexion, $_POST['ema_email']);
    $password  = mysqli_real_escape_string($conexion, $_POST['pas_password']);
    
    // --- LÓGICA DE FOTO ---
    $ruta_img = $row->usu_img; // Mantener la actual por defecto

    // 1. Si subieron archivo nuevo
    if (isset($_FILES['foto_archivo']) && $_FILES['foto_archivo']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['foto_archivo']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
            $dir = "img/uploads/";
            if (!file_exists($dir)) mkdir($dir, 0777, true);
            
            $nuevo_nombre = 'user_' . time() . '.' . $ext;
            if (move_uploaded_file($_FILES['foto_archivo']['tmp_name'], $dir . $nuevo_nombre)) {
                $ruta_img = $dir . $nuevo_nombre;
            }
        }
    } 
    // 2. Si pusieron URL manual (opcional)
    elseif (!empty($_POST['url_imagen_manual']) && $_POST['url_imagen_manual'] != $row->usu_img) {
        $ruta_img = mysqli_real_escape_string($conexion, $_POST['url_imagen_manual']);
    }

    // Actualizar BD
    $updateSQL = "UPDATE usuarios SET 
                  usu_nom='$nombre', usu_ap_pat='$ap_pat', usu_ap_mat='$ap_mat', 
                  usu_fecha_nacimiento='$fecha_nac', usu_sexo='$sexo', usu_tipo='$tipo', 
                  usu_puesto='$puesto', usu_email='$email', usu_password='$password', 
                  usu_img='$ruta_img' 
                  WHERE id_usuario='$id_usuario'";

    if (mysqli_query($conexion, $updateSQL)) {
        
        // -------------------------------------------------------------
        // [ESPACIO RESERVADO PARA FIREBASE]
        // Aquí agregarás el código de sincronización en el futuro.
        // -------------------------------------------------------------

        echo "<script>alert('Datos actualizados correctamente.'); window.location='adm_usuarios.php';</script>";
    } else {
        echo "<script>alert('Error al actualizar: " . mysqli_error($conexion) . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modificar Usuario - Idealiza</title>
    
    <link rel="stylesheet" href="estilos/Wave2.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;500;700&family=Quicksand:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">

    <style>
        /* ESTILO PREMIUM IDEALISA */
        body { font-family: 'Outfit', sans-serif; background-color: #F0F2F5; padding-bottom: 100px; margin: 0; }
        
        .form-wrapper {
            background: white; max-width: 850px; margin: 40px auto; padding: 0;
            border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            border-top: 6px solid #144c3c; 
            overflow: hidden;
        }

        .form-header {
            background: #fdfdfd; padding: 30px; text-align: center; border-bottom: 1px solid #eee;
        }
        .form-header h2 { margin: 0; color: #144c3c; font-weight: 700; font-size: 1.8rem; }
        
        .form-body { padding: 40px; }

        /* Grid */
        .input-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px; }
        
        .input-group { margin-bottom: 5px; }
        .input-group label { display: block; font-weight: 600; color: #5d6b62; margin-bottom: 8px; font-size: 0.9rem; }
        
        .form-control {
            width: 100%; padding: 12px; border: 1px solid #cedfcd; border-radius: 10px;
            font-family: inherit; font-size: 1rem; box-sizing: border-box; transition: 0.3s;
            background-color: #FAFAFA;
        }
        /* Foco Verde */
        .form-control:focus { border-color: #144c3c; outline: none; box-shadow: 0 0 0 3px rgba(20, 76, 60, 0.1); background: white; }

        /* Caja de Puesto destacada */
        .highlight-box {
            background: #e8f5e9; /* Verde muy claro */
            padding: 25px; border-radius: 12px; margin: 25px 0;
            border-left: 5px solid #144c3c;
        }
        .highlight-box label { color: #144c3c; font-size: 1.1rem; }

        /* SECCIÓN FOTO */
        .photo-area {
            text-align: center; margin-bottom: 30px; padding: 20px;
            background: #fff8f0; border-radius: 12px; border: 1px dashed #94745c;
        }
        .current-img {
            width: 100px; height: 100px; border-radius: 50%; object-fit: cover;
            border: 3px solid white; box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            margin-bottom: 10px;
        }
        .upload-btn {
            display: inline-block; background: #94745c; color: white; padding: 8px 20px;
            border-radius: 20px; font-size: 0.9rem; font-weight: bold; cursor: pointer; transition: 0.2s;
        }
        .upload-btn:hover { background: #7a604c; }

        /* Botones */
        .btn-submit {
            background: #144c3c; color: white; border: none; width: 100%; padding: 15px;
            border-radius: 30px; font-size: 1.1rem; font-weight: bold; cursor: pointer;
            transition: 0.3s; margin-top: 10px; display: flex; justify-content: center; align-items: center; gap: 10px;
            box-shadow: 0 4px 15px rgba(20, 76, 60, 0.2);
        }
        .btn-submit:hover { background: #0f382c; transform: translateY(-2px); }

        .btn-back {
            display: inline-flex; align-items: center; gap: 5px; text-decoration: none;
            color: #748579; font-weight: bold; margin: 20px 0; transition: 0.3s;
        }
        .btn-back:hover { color: #144c3c; transform: translateX(-5px); }

        @media (max-width: 600px) {
            .input-row { grid-template-columns: 1fr; gap: 15px; }
            .form-body { padding: 20px; }
        }
    </style>
</head>
<body>

    <?php include("php/encabezado_madera.php"); ?>
    <?php include("php/barra_navegacion.php"); ?>

    <div style="max-width: 850px; margin: 0 auto;">
        <a href="adm_usuarios.php" class="btn-back">
            <span class="material-icons-round">arrow_back</span> Cancelar y Volver
        </a>
    </div>

    <div class="form-wrapper">
        <div class="form-header">
            <h2>Modificar Usuario</h2>
            <p>Actualiza los datos del colaborador</p>
        </div>

        <!-- enctype obligatorio para subir foto -->
        <form action="" method="POST" class="form-body" enctype="multipart/form-data">
            
            <input type="hidden" name="txt_id" value="<?php echo $row->id_usuario; ?>">

            <div class="input-row">
                <div class="input-group">
                    <label>Nombre(s):</label>
                    <input type="text" name="txt_Nombre" class="form-control" value="<?php echo $row->usu_nom; ?>" required>
                </div>
                <div class="input-group">
                    <label>Apellido Paterno:</label>
                    <input type="text" name="txt_ApPat" class="form-control" value="<?php echo $row->usu_ap_pat; ?>" required>
                </div>
            </div>

            <div class="input-row">
                <div class="input-group">
                    <label>Apellido Materno:</label>
                    <input type="text" name="txt_ApMat" class="form-control" value="<?php echo $row->usu_ap_mat; ?>">
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
                    <select name="lst_Tipo" id="lst_Tipo" class="form-control" required>
                        <option value="admin" <?php if ($row->usu_tipo === 'admin') echo 'selected'; ?>>Admin</option>
                        <option value="supervisor" <?php if ($row->usu_tipo === 'supervisor') echo 'selected'; ?>>Supervisor</option>
                        <option value="empleado" <?php if ($row->usu_tipo === 'empleado') echo 'selected'; ?>>Empleado</option>
                        <option value="gerente" <?php if ($row->usu_tipo === 'gerente') echo 'selected'; ?>>Gerente</option>
                    </select>
                </div>
            </div>

            <!-- FOTO DE PERFIL -->
            <div class="photo-area">
                <label style="display:block; margin-bottom:10px; font-weight:bold; color:#94745c;">Foto de Perfil</label>
                
                <!-- Previsualización -->
                <?php 
                    $img_display = !empty($row->usu_img) ? $row->usu_img : 'https://cdn.pixabay.com/photo/2015/10/05/22/37/blank-profile-picture-973460_960_720.png';
                ?>
                <img id="preview" src="<?php echo $img_display; ?>" class="current-img" onerror="this.src='https://cdn.pixabay.com/photo/2015/10/05/22/37/blank-profile-picture-973460_960_720.png'">
                <br>
                
                <label class="upload-btn">
                    <span class="material-icons-round" style="vertical-align:middle; font-size:18px;">cloud_upload</span> Cambiar Foto
                    <input type="file" name="foto_archivo" style="display:none;" accept="image/*" onchange="verPreview(this)">
                </label>
                
                <!-- Campo oculto por si no suben archivo pero quieren editar la URL manual -->
                <input type="hidden" name="url_imagen_manual" value="<?php echo $row->usu_img; ?>">
            </div>

            <div class="highlight-box">
                <div class="input-group">
                    <label>Puesto de Trabajo (Monitor):</label>
                    <select name="lst_puesto" class="form-control" style="font-weight:600; color:#333;">
                        <option value="<?php echo $row->usu_puesto; ?>" selected><?php echo ucfirst($row->usu_puesto); ?> (Actual)</option>
                        
                        <optgroup label="Producción">
                            <option value="maquilador">Maquilador</option>
                            <option value="armador">Armador</option>
                            <option value="barnizador">Barnizador</option>
                            <option value="pintor">Pintor</option>
                            <option value="adornador">Adornador</option>
                        </optgroup>
                        <optgroup label="Administrativo">
                            <option value="terminado">Almacén</option>
                            <option value="supervisor">Supervisor</option>
                            <option value="administrador">Administrador</option>
                        </optgroup>
                    </select>
                </div>
            </div>

            <div class="input-row">
                <div class="input-group">
                    <label>Correo Electrónico:</label>
                    <input type="email" name="ema_email" class="form-control" value="<?php echo $row->usu_email; ?>" required>
                </div>
                <div class="input-group">
                    <label>Contraseña:</label>
                    <input type="text" name="pas_password" class="form-control" value="<?php echo $row->usu_password; ?>" required>
                </div>
            </div>

            <button type="submit" name="btn_actualizar" class="btn-submit">
                <span class="material-icons-round">save</span> Guardar Cambios
            </button>

        </form>
    </div>

    <?php include("php/olas.php"); ?>

    <script>
        function verPreview(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('preview').src = e.target.result;
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>

</body>
</html>
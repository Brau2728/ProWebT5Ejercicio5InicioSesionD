<?php
$page = 'usuarios'; 
session_start();

if (!isset($_SESSION['usuario']) || !isset($_SESSION['tipo'])) {
    header("Location: login.php");
    exit();
}

include("php/conexion.php");

// --- PUENTE DE CONEXIÓN BLINDADO ---
$conexion = null;
if (isset($link)) $conexion = $link;
if (isset($conn)) $conexion = $conn;
if (!$conexion) { try { $conexion = mysqli_connect('localhost', 'root', '', 'equipo', 3306); } catch(Exception $e){} }
if (!$conexion) { die("Error crítico de conexión."); }

// --- PROCESAR FORMULARIO ---
if (isset($_POST['registrar'])) {
    
    // Recibir datos de texto
    $nombre    = mysqli_real_escape_string($conexion, $_POST['nombre']);
    $ap_pat    = mysqli_real_escape_string($conexion, $_POST['ap_pat']);
    $ap_mat    = mysqli_real_escape_string($conexion, $_POST['ap_mat']);
    $fecha_nac = mysqli_real_escape_string($conexion, $_POST['fecha_nac']);
    $sexo      = mysqli_real_escape_string($conexion, $_POST['sexo']);
    $tipo      = mysqli_real_escape_string($conexion, $_POST['tipo']);   
    $puesto    = mysqli_real_escape_string($conexion, $_POST['puesto']); 
    $email     = mysqli_real_escape_string($conexion, $_POST['email']);
    $password  = mysqli_real_escape_string($conexion, $_POST['password']);
    
    // --- LÓGICA DE SUBIDA DE IMAGEN ---
    $img_final = ''; // Valor por defecto vacío (la BD o el front pondrán la genérica si está vacío)
    
    // Verificamos si se subió un archivo
    if (isset($_FILES['foto_archivo']) && $_FILES['foto_archivo']['error'] === UPLOAD_ERR_OK) {
        $nombre_archivo = $_FILES['foto_archivo']['name'];
        $tmp_archivo    = $_FILES['foto_archivo']['tmp_name'];
        $ext            = strtolower(pathinfo($nombre_archivo, PATHINFO_EXTENSION));
        
        // Validar extensión
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
            $directorio = "img/uploads/";
            if (!file_exists($directorio)) { mkdir($directorio, 0777, true); }
            
            // Nombre único: user_timestamp.jpg
            $nuevo_nombre = 'user_' . time() . '.' . $ext;
            $destino      = $directorio . $nuevo_nombre;
            
            if (move_uploaded_file($tmp_archivo, $destino)) {
                $img_final = $destino; // Guardamos la ruta relativa
            }
        }
    } 
    // Si no subió archivo pero puso URL (opcional legacy)
    elseif (!empty($_POST['img_url'])) {
        $img_final = mysqli_real_escape_string($conexion, $_POST['img_url']);
    }

    $id_role = 3; // Rol base por defecto

    // Insertar
    $sql = "INSERT INTO usuarios 
            (usu_nom, usu_ap_pat, usu_ap_mat, usu_fecha_nacimiento, usu_sexo, usu_tipo, usu_puesto, usu_img, usu_password, usu_email, id_role)
            VALUES 
            ('$nombre', '$ap_pat', '$ap_mat', '$fecha_nac', '$sexo', '$tipo', '$puesto', '$img_final', '$password', '$email', '$id_role')";

    if (mysqli_query($conexion, $sql)) {
        echo "<script>alert('¡Colaborador $nombre registrado correctamente!'); window.location='adm_usuarios.php';</script>";
    } else {
        echo "<script>alert('Error al registrar: " . mysqli_error($conexion) . "');</script>";
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
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;500;700&family=Quicksand:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">

    <style>
        /* PALETA IDEALISA: #144c3c (Verde), #94745c (Marrón) */
        body { font-family: 'Outfit', sans-serif; background-color: #F0F2F5; padding-bottom: 100px; margin: 0; }
        
        .form-wrapper {
            background: white; max-width: 850px; margin: 40px auto; padding: 0;
            border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            border-top: 6px solid #144c3c; /* Verde Institucional */
            overflow: hidden;
        }

        .form-header {
            padding: 30px; text-align: center; border-bottom: 1px solid #eee; background: #fdfdfd;
        }
        .form-header h2 { margin: 0; color: #144c3c; font-weight: 700; font-size: 1.8rem; }
        .form-header p { margin: 5px 0 0; color: #777; font-size: 0.9rem; }

        .form-body { padding: 40px; }
        
        .input-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px; }
        
        .input-group { margin-bottom: 5px; }
        .input-group label { display: block; font-weight: 600; color: #5d6b62; margin-bottom: 8px; font-size: 0.9rem; }
        
        .form-control {
            width: 100%; padding: 12px; border: 1px solid #cedfcd; border-radius: 10px;
            font-family: inherit; font-size: 1rem; box-sizing: border-box; transition: 0.3s;
            background-color: #FAFAFA;
        }
        .form-control:focus { 
            border-color: #144c3c; outline: none; background: white;
            box-shadow: 0 0 0 3px rgba(20, 76, 60, 0.1); 
        }

        /* CAJA DESTACADA (PUESTO) */
        .highlight-box {
            background: #e8f5e9; padding: 25px; border-radius: 12px; margin: 25px 0;
            border-left: 5px solid #144c3c;
        }
        .highlight-box label { color: #144c3c; font-size: 1.1rem; }

        /* ÁREA DE FOTO */
        .photo-upload-area {
            text-align: center; margin-bottom: 30px;
            background: #fff8f0; padding: 20px; border-radius: 12px; border: 1px dashed #94745c;
        }
        .preview-img {
            width: 120px; height: 120px; border-radius: 50%; object-fit: cover;
            border: 4px solid white; box-shadow: 0 4px 10px rgba(0,0,0,0.1); margin-bottom: 10px;
        }
        .file-label {
            display: inline-block; background: #94745c; color: white; padding: 8px 20px;
            border-radius: 20px; font-size: 0.9rem; font-weight: bold; cursor: pointer;
            transition: 0.2s;
        }
        .file-label:hover { background: #7a604c; }

        /* BOTONES */
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

        @media (max-width: 700px) {
            .input-row { grid-template-columns: 1fr; gap: 15px; }
            .form-body { padding: 25px; }
        }
    </style>
</head>
<body>

    <?php include("php/encabezado_madera.php"); ?>
    <?php include("php/barra_navegacion.php"); ?>

    <div style="max-width: 850px; margin: 0 auto;">
        <a href="adm_usuarios.php" class="btn-back">
            <span class="material-icons-round">arrow_back</span> Volver a Lista de Personal
        </a>
    </div>

    <div class="form-wrapper">
        <div class="form-header">
            <h2>Registrar Nuevo Colaborador</h2>
            <p>Ingresa los datos personales y asigna un rol en el sistema</p>
        </div>

        <!-- IMPORTANTE: enctype="multipart/form-data" para subir archivos -->
        <form method="POST" action="" class="form-body" enctype="multipart/form-data">
            
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
                    <label>Puesto de Trabajo (Define área en Monitor):</label>
                    <select name="puesto" class="form-control" required style="font-weight:600; color:#333;">
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

            <!-- SECCIÓN DE FOTO -->
            <div class="photo-upload-area">
                <label style="display:block; margin-bottom:10px; font-weight:bold; color:#94745c;">Fotografía de Perfil</label>
                
                <!-- Imagen Previa -->
                <img id="preview" src="https://cdn.pixabay.com/photo/2015/10/05/22/37/blank-profile-picture-973460_960_720.png" class="preview-img">
                <br>
                
                <label class="file-label">
                    <span class="material-icons-round" style="vertical-align:middle; font-size:18px;">cloud_upload</span> Seleccionar Archivo
                    <input type="file" name="foto_archivo" style="display:none;" accept="image/*" onchange="verPreview(this)">
                </label>
                <br>
                <small style="color:#888;">Formatos: JPG, PNG, WEBP</small>
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

            <button type="submit" name="registrar" class="btn-submit">
                <span class="material-icons-round">person_add</span> Registrar Colaborador
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
<?php
// login.php - Versión Final (Conexión Directa Integrada)
session_start();

// 1. Omitir login si ya estás dentro
if (isset($_SESSION['usuario'])) {
    header("Location: " . ($_SESSION['tipo'] == 'admin' ? 'adm_index.php' : 'index.php'));
    exit();
}

// 2. CONEXIÓN MANUAL (Para asegurar que la variable $connection exista)
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'equipo';
$db_port = '3306';

$connection = mysqli_connect($db_host, $db_user, $db_pass, $db_name, $db_port);

// Verificar si falló la conexión
if (!$connection) {
    die("Error de conexión: " . mysqli_connect_error());
}

$errorMsg = "";

// 3. PROCESAR EL FORMULARIO
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Recibimos datos
    $correo_usuario = $_POST['email_login']; // Puede ser nombre o correo
    $pass = $_POST['pass_login'];

    // Limpieza de seguridad (Ahora sí funciona porque $connection existe)
    $correo_usuario = mysqli_real_escape_string($connection, $correo_usuario);
    $pass = mysqli_real_escape_string($connection, $pass);

    // CONSULTA HÍBRIDA: Busca por Correo O por Nombre de Usuario
    // Nota: Usamos 'usu_password' como vimos en tu base de datos
    $sql = "SELECT * FROM usuarios 
            WHERE (usu_email = '$correo_usuario' OR usu_nom = '$correo_usuario') 
            AND usu_password = '$pass'";
            
    $query = mysqli_query($connection, $sql);

    if ($query) {
        if (mysqli_num_rows($query) > 0) {
            $row = mysqli_fetch_assoc($query);
            
            // ¡ÉXITO! Guardamos sesión
            $_SESSION['usuario']    = $row['usu_nom'];
            $_SESSION['email']      = $row['usu_email'];
            $_SESSION['tipo']       = $row['usu_tipo']; // admin, empleado, etc.
            $_SESSION['id_usuario'] = $row['id_usuario'];

            // Redirección
            if($row['usu_tipo'] == 'admin') {
                header("Location: adm_index.php");
            } else {
                header("Location: index.php");
            }
            exit();

        } else {
            $errorMsg = "Usuario o contraseña incorrectos.";
        }
    } else {
        $errorMsg = "Error en la consulta: " . mysqli_error($connection);
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso | Idealisa</title>

    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;700&family=Quicksand:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        /* Estilos Modernos Idealisa */
        body {
            margin: 0; padding: 0;
            font-family: 'Quicksand', sans-serif;
            background: #F0F2F5;
            height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
        }

        .main-container {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10;
            padding: 20px;
        }

        .card-login {
            background: rgba(255, 255, 255, 0.95);
            width: 100%;
            max-width: 380px;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(20, 76, 60, 0.1);
            text-align: center;
            border: 1px solid #fff;
            animation: fadeIn 0.8s ease;
            position: relative;
        }
        @keyframes fadeIn { from {opacity:0; transform:translateY(20px);} to {opacity:1; transform:translateY(0);} }

        /* Barra decorativa superior */
        .card-login::before {
            content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 5px;
            background: linear-gradient(90deg, #144c3c, #94745c);
            border-top-left-radius: 20px; border-top-right-radius: 20px;
        }

        .logo-circle {
            width: 90px; height: 90px;
            background: white;
            border-radius: 50%;
            margin: 0 auto 20px auto;
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            overflow: hidden;
        }
        .logo-circle img { width: 100%; height: auto; }

        h1 { font-family: 'Outfit'; color: #144c3c; margin: 0; font-size: 1.8rem; }
        p { color: #94745c; font-size: 0.9rem; margin: 5px 0 30px 0; }

        .field-group { position: relative; margin-bottom: 20px; text-align: left; }
        .field-label { font-size: 0.8rem; font-weight: 700; color: #144c3c; margin-left: 10px; margin-bottom: 5px; display: block; }
        
        .input-box {
            width: 100%;
            padding: 12px 15px 12px 40px;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            font-size: 1rem;
            box-sizing: border-box;
            font-family: 'Quicksand';
            transition: 0.3s;
            background: #fafafa;
        }
        .input-box:focus { border-color: #144c3c; background: #fff; outline: none; }
        
        .field-icon {
            position: absolute;
            left: 12px;
            top: 36px;
            color: #ccc;
        }
        .input-box:focus + .field-icon { color: #144c3c; }

        .btn-submit {
            background: #144c3c;
            color: white;
            border: none;
            width: 100%;
            padding: 14px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 1rem;
            cursor: pointer;
            transition: 0.3s;
            margin-top: 10px;
        }
        .btn-submit:hover { background: #0f382c; transform: translateY(-2px); box-shadow: 0 10px 20px rgba(0,0,0,0.1); }

        .footer-waves {
            position: relative;
            width: 100%;
            height: 150px;
            margin-top: auto;
            z-index: 1;
        }
    </style>
</head>
<body>

    <div class="main-container">
        <div class="card-login">
            <div class="logo-circle">
                <img src="Img/logo.jpg" alt="Logo" onerror="this.src='https://cdn-icons-png.flaticon.com/512/295/295128.png'">
            </div>
            <h1>Bienvenido</h1>
            <p>Accede a tu cuenta</p>

            <form action="login.php" method="POST">
                
                <div class="field-group">
                    <label class="field-label">USUARIO O CORREO</label>
                    <input type="text" name="email_login" class="input-box" placeholder="ej. usuario@idealisa.com" required>
                    <span class="material-icons-round field-icon">person</span>
                </div>

                <div class="field-group">
                    <label class="field-label">CONTRASEÑA</label>
                    <input type="password" name="pass_login" class="input-box" placeholder="••••••••" required>
                    <span class="material-icons-round field-icon">lock</span>
                </div>

                <button type="submit" class="btn-submit">ENTRAR</button>
            </form>
            
            <br>
            <a href="registrar.php" style="font-size:0.85rem; color:#94745c; text-decoration:none;">Crear cuenta nueva</a>
        </div>
    </div>

    <div class="footer-waves">
        <?php include("php/olas.php"); ?>
    </div>

    <?php if(!empty($errorMsg)): ?>
    <script>
        Swal.fire({
            icon: 'error',
            title: '¡Error!',
            text: '<?php echo $errorMsg; ?>',
            confirmButtonColor: '#144c3c'
        });
    </script>
    <?php endif; ?>

</body>
</html>
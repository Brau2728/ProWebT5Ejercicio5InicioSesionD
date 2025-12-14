<?php
// 1. CONFIGURACIÓN DE PÁGINA
$page = 'personal'; // Ilumina la pestaña "Personal" en el menú
session_start();

// 2. SEGURIDAD
if(!isset($_SESSION['usuario']) || !isset($_SESSION['tipo'])){
    header('Location: login.php');
    exit();
}

include("php/conexion.php");

// 3. CONSULTA DE USUARIOS
$sql = "SELECT * FROM usuarios ORDER BY usu_nom ASC";
$result = db_query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Personal - Idealisa</title>
    
    <!-- Estilos Base -->
    <link rel="stylesheet" href="estilos/Wave2.css">
    
    <!-- Fuentes y Iconos -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&family=Quicksand:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">

    <style>
        /* === PALETA INSTITUCIONAL === */
        :root {
            --primary: #144c3c;   /* Verde Oscuro */
            --accent: #94745c;    /* Marrón */
            --light-green: #cedfcd;
            --bg-page: #F4F7FE;   /* Fondo suave */
            --white: #ffffff;
            --text-dark: #2b3674;
            --text-grey: #a3aed0;
        }

        body { 
            font-family: 'Quicksand', sans-serif; 
            background-color: var(--bg-page); 
            margin: 0; 
            padding-bottom: 80px; 
        }

        /* Contenedor Principal (Ajustado para navbar) */
        .main-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 30px;
        }

        /* HEADER DE PÁGINA */
        .page-header {
            display: flex; justify-content: space-between; align-items: center; 
            background: var(--white); padding: 20px 30px; border-radius: 20px; 
            box-shadow: 0 5px 20px rgba(0,0,0,0.02); margin-bottom: 40px;
            flex-wrap: wrap; gap: 20px;
        }

        .ph-title { 
            margin: 0; color: var(--primary); font-family: 'Outfit'; font-weight: 700; 
            font-size: 1.8rem; display: flex; align-items: center; gap: 12px; 
        }

        .ph-actions { display: flex; gap: 15px; }

        /* BOTONES PERSONALIZADOS */
        .btn-action {
            padding: 12px 24px; border-radius: 12px; text-decoration: none; 
            font-weight: 700; font-family: 'Outfit'; font-size: 0.95rem;
            display: flex; align-items: center; gap: 8px; transition: 0.3s;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        .btn-green { 
            background: var(--primary); color: white; 
            box-shadow: 0 10px 20px rgba(20, 76, 60, 0.2);
        }
        .btn-green:hover { transform: translateY(-3px); box-shadow: 0 15px 30px rgba(20, 76, 60, 0.3); }

        .btn-brown { 
            background: var(--accent); color: white; 
            box-shadow: 0 10px 20px rgba(148, 116, 92, 0.2);
        }
        .btn-brown:hover { transform: translateY(-3px); background: #7a604c; }

        /* GRID DE USUARIOS */
        .users-grid {
            display: grid; 
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); 
            gap: 30px;
        }

        /* TARJETA DE USUARIO */
        .user-card {
            background: var(--white); border-radius: 20px; overflow: visible; /* IMPORTANTE: Para que la foto salga del banner */
            box-shadow: 0 5px 15px rgba(0,0,0,0.03); border: 1px solid rgba(0,0,0,0.02);
            transition: all 0.3s; position: relative; display: flex; flex-direction: column;
            margin-top: 30px; /* Espacio superior para compensar efectos hover */
        }
        .user-card:hover { transform: translateY(-5px); box-shadow: 0 20px 40px rgba(0,0,0,0.08); }

        /* Banner Superior (Verde) */
        .card-banner {
            height: 90px; background: var(--primary); 
            border-top-left-radius: 20px; border-top-right-radius: 20px;
            background-image: linear-gradient(45deg, #144c3c 0%, #1d6652 100%);
            position: relative;
        }

        /* Avatar Flotante (CORREGIDO) */
        .card-avatar-container {
            position: absolute;
            top: 45px; /* Mitad del banner (90px / 2) */
            left: 25px;
            width: 90px; height: 90px;
        }

        .card-avatar {
            width: 90px; height: 90px; 
            border-radius: 50%; 
            border: 5px solid var(--white);
            object-fit: cover; 
            background: var(--white); 
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            display: block;
        }

        /* Cuerpo Tarjeta */
        .card-body { 
            /* Padding top compensa la altura del avatar que sobresale (45px) + espacio extra */
            padding: 55px 25px 25px 25px; 
            flex: 1; display: flex; flex-direction: column; 
        }

        .user-name { 
            margin: 0; font-family: 'Outfit'; font-weight: 700; font-size: 1.3rem; color: var(--text-dark); 
        }
        
        /* Badge de Rol */
        .role-badge {
            display: inline-block; background: var(--light-green); color: var(--primary);
            padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 800;
            text-transform: uppercase; margin-top: 5px; margin-bottom: 20px; width: fit-content;
        }

        /* Lista de Info */
        .info-list { list-style: none; padding: 0; margin: 0; color: var(--text-grey); font-size: 0.9rem; }
        .info-item { 
            display: flex; align-items: center; gap: 10px; margin-bottom: 12px; 
            border-bottom: 1px dashed #f0f0f0; padding-bottom: 8px;
        }
        .info-item:last-child { border-bottom: none; }
        .info-icon { color: var(--accent); font-size: 18px; }

        /* Footer Acciones */
        .card-footer {
            padding: 15px 25px; background: #fafbfc; border-top: 1px solid #f1f5f9;
            display: flex; justify-content: flex-end; gap: 10px;
            border-bottom-left-radius: 20px; border-bottom-right-radius: 20px;
        }

        .btn-icon {
            width: 38px; height: 38px; border-radius: 12px; display: flex; align-items: center; justify-content: center;
            text-decoration: none; transition: 0.2s; border: 1px solid transparent;
        }
        
        .btn-edit { background: var(--light-green); color: var(--primary); }
        .btn-edit:hover { background: #b8d6b6; transform: scale(1.05); }

        .btn-del { background: #FFEBEE; color: #D32F2F; }
        .btn-del:hover { background: #ffcdd2; transform: scale(1.05); }

    </style>
</head>
<body>

    <!-- Incluimos la barra correcta -->
    <?php include("php/encabezado_madera.php"); ?>
    <?php include("php/barra_navegacion.php"); ?>

    <div class="main-container">
        
        <!-- CABECERA -->
        <div class="page-header">
            <h1 class="ph-title">
                <span class="material-icons-round" style="font-size:32px;">groups</span> 
                Directorio de Personal
            </h1>
            
            <div class="ph-actions">
                <a href="adm_usuario_contra.php" class="btn-action btn-brown">
                    <span class="material-icons-round">vpn_key</span> Contraseñas
                </a>
                <a href="adm_usuario_registrar.php" class="btn-action btn-green">
                    <span class="material-icons-round">person_add</span> Nuevo Usuario
                </a>
            </div>
        </div>

        <!-- GRID TARJETAS -->
        <div class="users-grid">
            <?php
            if ($result && mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_object($result)) {
                    // LÓGICA DE IMAGEN MEJORADA:
                    $nombre_completo = $row->usu_nom . '+' . $row->usu_ap_pat;
                    // Generador de avatar si no hay imagen (Opcional, si tienes internet)
                    $default_img = "https://ui-avatars.com/api/?name=$nombre_completo&background=144c3c&color=fff&size=128&bold=true";
                    
                    // Si prefieres usar una imagen local por defecto si no hay internet o si falla la carga:
                    // $default_img = "Img/default-user.png"; 
                    
                    $img_src = !empty($row->usu_img) ? $row->usu_img : $default_img;
            ?>
            
            <div class="user-card">
                <!-- Banner -->
                <div class="card-banner">
                    <!-- Avatar posicionado absolutamente respecto al banner -->
                    <div class="card-avatar-container">
                        <img src="<?php echo $img_src; ?>" 
                             alt="Foto de <?php echo $row->usu_nom; ?>" 
                             class="card-avatar"
                             onerror="this.onerror=null; this.src='https://via.placeholder.com/150/CCCCCC/FFFFFF?text=USER';">
                    </div>
                </div>
                
                <div class="card-body">
                    <h3 class="user-name"><?php echo $row->usu_nom . ' ' . $row->usu_ap_pat; ?></h3>
                    <div class="role-badge"><?php echo $row->usu_tipo; ?></div>
                    
                    <ul class="info-list">
                        <li class="info-item">
                            <span class="material-icons-round info-icon">work</span>
                            <?php echo $row->usu_puesto ? ucfirst($row->usu_puesto) : 'No especificado'; ?>
                        </li>
                        <li class="info-item">
                            <span class="material-icons-round info-icon">email</span>
                            <?php echo $row->usu_email; ?>
                        </li>
                        <li class="info-item">
                            <span class="material-icons-round info-icon">cake</span>
                            <?php echo $row->usu_fecha_nacimiento ? date('d/m/Y', strtotime($row->usu_fecha_nacimiento)) : '--/--/----'; ?>
                        </li>
                    </ul>
                </div>

                <div class="card-footer">
                    <a href="adm_usuario_modificar.php?id=<?php echo $row->id_usuario; ?>" class="btn-icon btn-edit" title="Editar">
                        <span class="material-icons-round" style="font-size:20px;">edit</span>
                    </a>
                    <a href="#" onclick="borrar(<?php echo $row->id_usuario; ?>)" class="btn-icon btn-del" title="Eliminar">
                        <span class="material-icons-round" style="font-size:20px;">delete</span>
                    </a>
                </div>
            </div>

            <?php 
                } 
            } else {
                echo "<div style='grid-column:1/-1; text-align:center; padding:50px; color:#a3aed0;'>
                        <span class='material-icons-round' style='font-size:60px; opacity:0.5;'>no_accounts</span>
                        <h3>No hay personal registrado</h3>
                      </div>";
            }
            ?>
        </div>

    </div>

    <!-- JS ELIMINAR -->
    <script>
        function borrar(id) {
            if(confirm("⚠️ ATENCIÓN:\n¿Estás seguro de eliminar este usuario permanentemente?")) {
                window.location.href = "php/eliminar_usuario.php?id=" + id;
            }
        }
    </script>

</body>
</html>
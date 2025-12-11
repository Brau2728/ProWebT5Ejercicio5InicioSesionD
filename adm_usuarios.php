<?php
$page = 'usuarios'; // Mantiene iluminado el menú "Personal"
session_start();

// Validación de seguridad
if(!isset($_SESSION['usuario']) || !isset($_SESSION['tipo'])){
    header('Location: login.php'); 
    exit();
}

include("php/conexion.php");

// Consulta de usuarios
$sql = "SELECT * FROM usuarios ORDER BY usu_nom ASC";
$result = db_query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuarios - Idealiza</title>
    
    <link rel="stylesheet" href="estilos/Wave2.css">
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <style>
        /* PALETA: #144c3c (Verde Oscuro), #94745c (Marrón), #cedfcd (Verde Claro) */

        body { font-family: 'Quicksand', sans-serif; background-color: #F0F2F5; padding-bottom: 100px; }
        .container-users { max-width: 1200px; margin: 0 auto; padding: 40px 20px; }

        /* ENCABEZADO DE PÁGINA */
        .header-actions { 
            display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; 
            background: white; padding: 20px 30px; border-radius: 12px; 
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            border-left: 6px solid #144c3c; /* Borde Verde Oscuro */
        }
        .page-title { margin: 0; color: #144c3c; font-weight: 700; display: flex; align-items: center; gap: 10px; font-size: 1.8rem; }
        
        /* BOTÓN PRINCIPAL (VERDE) */
        .btn-main { 
            background: #144c3c; color: white; padding: 12px 25px; border-radius: 30px; 
            text-decoration: none; font-weight: bold; display: flex; align-items: center; gap: 5px; 
            box-shadow: 0 4px 10px rgba(20, 76, 60, 0.3); transition: 0.2s;
        }
        .btn-main:hover { background: #0f382c; transform: translateY(-2px); }

        /* BOTÓN SECUNDARIO (MARRÓN) */
        .btn-sec { 
            background: #94745c; color: white; padding: 12px 25px; border-radius: 30px; 
            text-decoration: none; font-weight: bold; display: flex; align-items: center; gap: 5px; margin-right: 10px;
            box-shadow: 0 4px 10px rgba(148, 116, 92, 0.3); transition: 0.2s;
        }
        .btn-sec:hover { background: #7a604c; transform: translateY(-2px); }

        /* GRID DE TARJETAS */
        .users-grid { 
            display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 30px; 
        }

        /* TARJETA DE USUARIO */
        .user-card {
            background: white; border-radius: 16px; overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05); transition: 0.3s;
            border: 1px solid #eee; display: flex; flex-direction: column;
        }
        .user-card:hover { transform: translateY(-5px); box-shadow: 0 12px 25px rgba(0,0,0,0.1); }

        /* PORTADA / FONDO SUPERIOR */
        .card-cover {
            height: 90px; 
            background: #144c3c; /* Verde Oscuro */
            position: relative;
        }
        .user-avatar {
            width: 80px; height: 80px; border-radius: 50%; border: 4px solid white;
            object-fit: cover; position: absolute; bottom: -40px; left: 20px;
            background: #fff; box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        /* CONTENIDO TARJETA */
        .card-body { padding: 50px 20px 20px 20px; flex: 1; }
        
        .user-name { margin: 0; color: #144c3c; font-size: 1.2rem; font-weight: 700; }
        
        .user-role { 
            display: inline-block; 
            background: #cedfcd; color: #144c3c; /* Verde Claro fondo, Verde Oscuro texto */
            padding: 4px 12px; border-radius: 15px; font-size: 0.75rem; 
            font-weight: bold; text-transform: uppercase; margin-bottom: 15px; margin-top: 5px;
        }
        
        .info-list { list-style: none; padding: 0; margin: 0; font-size: 0.9rem; color: #5d6b62; }
        .info-list li { margin-bottom: 10px; display: flex; align-items: center; gap: 10px; }
        .info-list li .material-icons { font-size: 18px; color: #94745c; /* Iconos Marrones */ }

        /* BOTONES ACCIÓN EN TARJETA */
        .card-footer {
            padding: 15px 20px; border-top: 1px solid #f0f0f0; background: #fafafa;
            display: flex; justify-content: flex-end; gap: 10px;
        }
        .btn-icon {
            width: 35px; height: 35px; border-radius: 50%; display: flex; align-items: center; justify-content: center;
            text-decoration: none; transition: 0.2s;
        }
        
        /* Editar: Verde Claro */
        .btn-edit { background: #cedfcd; color: #144c3c; }
        .btn-edit:hover { background: #b8d6b6; }
        
        /* Eliminar: Rojo sutil (Mantener por semántica de peligro) */
        .btn-del { background: #FFEBEE; color: #D32F2F; border: 1px solid #FFCDD2; }
        .btn-del:hover { background: #EF5350; color: white; border-color: #EF5350; }

    </style>
</head>
<body>

    <?php include("php/encabezado_madera.php"); ?>
    <?php include("php/barra_navegacion.php"); ?>

    <div class="container-users">
        
        <div class="header-actions">
            <h1 class="page-title"><span class="material-icons">people</span> Directorio de Personal</h1>
            <div style="display:flex;">
                <a href="adm_usuario_contra.php" class="btn-sec">
                    <span class="material-icons">vpn_key</span> Contraseñas
                </a>
                <a href="adm_usuario_registrar.php" class="btn-main">
                    <span class="material-icons">person_add</span> Nuevo Usuario
                </a>
            </div>
        </div>

        <div class="users-grid">
            <?php
            if ($result && mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_object($result)) {
                    // Imagen por defecto si no tiene (Usamos silueta gris si está vacío)
                    $default_img = 'https://cdn.pixabay.com/photo/2015/10/05/22/37/blank-profile-picture-973460_960_720.png';
                    $img = $row->usu_img ? $row->usu_img : $default_img;
            ?>
            
            <div class="user-card">
                <div class="card-cover">
                    <img src="<?php echo $img; ?>" alt="Foto" class="user-avatar">
                </div>
                
                <div class="card-body">
                    <h3 class="user-name"><?php echo $row->usu_nom . ' ' . $row->usu_ap_pat; ?></h3>
                    <span class="user-role"><?php echo $row->usu_tipo; ?></span>
                    
                    <ul class="info-list">
                        <li>
                            <span class="material-icons">work</span> 
                            <?php echo $row->usu_puesto ? ucfirst($row->usu_puesto) : 'Sin puesto'; ?>
                        </li>
                        <li>
                            <span class="material-icons">email</span> 
                            <?php echo $row->usu_email; ?>
                        </li>
                        <li>
                            <span class="material-icons">cake</span> 
                            <?php echo $row->usu_fecha_nacimiento; ?>
                        </li>
                    </ul>
                </div>

                <div class="card-footer">
                    <a href="adm_usuario_modificar.php?id=<?php echo $row->id_usuario; ?>" class="btn-icon btn-edit" title="Editar">
                        <span class="material-icons" style="font-size:18px;">edit</span>
                    </a>
                    <a href="#" onclick="confirmarBorrado(<?php echo $row->id_usuario; ?>)" class="btn-icon btn-del" title="Eliminar">
                        <span class="material-icons" style="font-size:18px;">delete</span>
                    </a>
                </div>
            </div>

            <?php 
                } 
            } else {
                echo "<p style='text-align:center; width:100%; color:#748579; font-size:1.2rem; grid-column: 1 / -1;'>
                        <span class='material-icons' style='font-size:40px; display:block; margin-bottom:10px;'>no_accounts</span>
                        No hay personal registrado aún.
                      </p>";
            } 
            ?>
        </div>

    </div>

    <?php include("php/olas.php"); ?>

    <script>
        function confirmarBorrado(id) {
            if(confirm("¿Estás seguro de eliminar este usuario? Esta acción es irreversible.")) {
                window.location.href = "php/eliminar_usuario.php?id=" + id;
            }
        }
    </script>

</body>
</html>
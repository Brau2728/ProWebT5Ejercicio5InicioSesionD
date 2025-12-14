<?php
// 1. CONFIGURACIÓN
$page = 'personal'; // Se mantiene en la sección de Personal
session_start();

// 2. SEGURIDAD
if (!isset($_SESSION['usuario']) || !isset($_SESSION['tipo'])) {
    header('Location: login.php');
    exit();
}

include("php/conexion.php");

// 3. CONSULTA (ID, Nombre, Tipo, Password)
$sql = "SELECT id_usuario, usu_nom, usu_ap_pat, usu_tipo, usu_password FROM usuarios ORDER BY usu_nom ASC";
$result = db_query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Contraseñas - Idealisa</title>
    
    <!-- Estilos Base -->
    <link rel="stylesheet" href="estilos/Wave2.css">
    
    <!-- Fuentes y Iconos -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&family=Quicksand:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
    <!-- Fuente Monoespaciada para passwords -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Mono:wght@500&display=swap" rel="stylesheet">

    <style>
        /* === PALETA INSTITUCIONAL === */
        :root {
            --primary: #144c3c;   /* Verde Oscuro */
            --accent: #94745c;    /* Marrón */
            --light-green: #cedfcd;
            --bg-page: #F4F7FE;
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

        /* Contenedor Principal Ajustado */
        .main-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 30px;
        }

        /* HEADER DE PÁGINA */
        .page-header {
            display: flex; justify-content: space-between; align-items: center; 
            background: var(--white); padding: 20px 30px; border-radius: 20px; 
            box-shadow: 0 5px 20px rgba(0,0,0,0.02); margin-bottom: 30px;
            flex-wrap: wrap; gap: 20px;
        }

        .ph-title { 
            margin: 0; color: var(--primary); font-family: 'Outfit'; font-weight: 700; 
            font-size: 1.6rem; display: flex; align-items: center; gap: 12px; 
        }

        /* Botón Regresar */
        .btn-back {
            background: #f4f7fe; color: var(--text-grey); padding: 10px 20px; border-radius: 12px;
            text-decoration: none; font-weight: 700; display: flex; align-items: center; gap: 8px; 
            transition: 0.2s; border: 1px solid transparent; font-family: 'Outfit';
        }
        .btn-back:hover { background: #e2e8f0; color: var(--text-dark); }

        /* TABLA MODERNA */
        .table-card {
            background: var(--white); border-radius: 20px; overflow: hidden;
            box-shadow: 0 5px 20px rgba(0,0,0,0.03); border: 1px solid rgba(0,0,0,0.02);
        }

        table { width: 100%; border-collapse: collapse; }
        
        thead { background: var(--primary); color: white; }
        
        th { 
            padding: 18px 25px; text-align: left; font-family: 'Outfit'; font-weight: 600; 
            text-transform: uppercase; font-size: 0.85rem; letter-spacing: 0.5px; 
        }
        
        td { 
            padding: 18px 25px; border-bottom: 1px solid #f0f0f0; color: var(--text-dark); 
            vertical-align: middle; font-size: 0.95rem; 
        }
        
        tr:last-child td { border-bottom: none; }
        tr:hover { background-color: #f8fafc; }

        /* Estilos de Celda */
        .user-cell { display: flex; flex-direction: column; }
        .u-name { font-weight: 700; color: var(--text-dark); }
        .u-role { font-size: 0.8rem; color: var(--accent); font-weight: 600; text-transform: uppercase; margin-top: 2px; }

        .pass-badge {
            font-family: 'Roboto Mono', monospace; background: #fff8e1; color: #f57f17;
            padding: 6px 12px; border-radius: 8px; border: 1px solid #ffecb3; font-weight: 600;
            display: inline-block; letter-spacing: 1px;
        }

        /* Botones Acción */
        .actions-cell { display: flex; gap: 10px; justify-content: flex-end; }
        
        .btn-icon {
            width: 36px; height: 36px; border-radius: 10px; display: flex; align-items: center; justify-content: center;
            text-decoration: none; transition: 0.2s; border: 1px solid transparent;
        }
        
        .btn-edit { background: var(--light-green); color: var(--primary); }
        .btn-edit:hover { background: #b8d6b6; transform: scale(1.05); }
        
        .btn-del { background: #FFEBEE; color: #D32F2F; }
        .btn-del:hover { background: #ffcdd2; transform: scale(1.05); }

        /* Responsive Table */
        @media (max-width: 768px) {
            .table-card { overflow-x: auto; }
            th, td { padding: 15px; white-space: nowrap; }
        }

    </style>
</head>
<body>

    <!-- Incluimos Header y Barra -->
    <?php include("php/encabezado_madera.php"); ?>
    <?php include("php/barra_navegacion.php"); ?>

    <div class="main-container">
        
        <!-- HEADER -->
        <div class="page-header">
            <h1 class="ph-title">
                <span class="material-icons-round" style="color:var(--primary); font-size:32px;">vpn_key</span> 
                Accesos y Seguridad
            </h1>
            <a href="adm_usuarios.php" class="btn-back">
                <span class="material-icons-round">arrow_back</span> Volver al Directorio
            </a>
        </div>

        <!-- TABLA -->
        <div class="table-card">
            <table>
                <thead>
                    <tr>
                        <th width="10%">ID</th>
                        <th width="35%">Usuario</th>
                        <th width="35%">Contraseña Actual</th>
                        <th width="20%" style="text-align: right;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result && mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                    ?>
                    <tr>
                        <td style="color:var(--text-grey); font-weight:600;">#<?php echo $row['id_usuario']; ?></td>
                        <td>
                            <div class="user-cell">
                                <span class="u-name"><?php echo $row['usu_nom'] . ' ' . $row['usu_ap_pat']; ?></span>
                                <span class="u-role"><?php echo $row['usu_tipo']; ?></span>
                            </div>
                        </td>
                        <td>
                            <span class="pass-badge"><?php echo $row['usu_password']; ?></span>
                        </td>
                        <td>
                            <div class="actions-cell">
                                <a href="adm_usuario_modificar.php?id=<?php echo $row['id_usuario']; ?>" class="btn-icon btn-edit" title="Cambiar Contraseña">
                                    <span class="material-icons-round" style="font-size:18px;">edit</span>
                                </a>
                                <a href="#" onclick="borrar(<?php echo $row['id_usuario']; ?>)" class="btn-icon btn-del" title="Eliminar Usuario">
                                    <span class="material-icons-round" style="font-size:18px;">delete</span>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php
                        }
                    } else {
                        echo "<tr><td colspan='4' style='text-align:center; padding:40px; color:#a3aed0;'>
                                <span class='material-icons-round' style='font-size:40px; opacity:0.5; display:block; margin-bottom:10px;'>no_accounts</span>
                                No hay usuarios registrados
                              </td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

    </div>

    <!-- JS ELIMINAR -->
    <script>
        function borrar(id) {
            if(confirm("⚠️ ¿Estás seguro de eliminar este usuario?")) {
                window.location.href = "php/eliminar_usuario.php?id=" + id;
            }
        }
    </script>

</body>
</html>
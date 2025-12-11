<?php
$page = 'usuarios'; // Para mantener activa la pestaña de Usuarios
session_start();

// Validación de sesión
if (!isset($_SESSION['usuario']) || !isset($_SESSION['tipo'])) {
    header('Location: login.php');
    exit();
}

include("php/conexion.php");

// Consulta de usuarios
$sql = "SELECT * FROM usuarios ORDER BY id_usuario ASC";
// Usamos la función db_query (asegúrate que esté en conexion.php, si no usa mysqli_query)
$result = db_query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contraseñas - Idealiza</title>
    
    <link rel="stylesheet" href="estilos/Wave2.css">
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Mono:wght@400;500&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Quicksand', sans-serif; background-color: #F0F2F5; padding-bottom: 100px; }
        
        .container-pass { max-width: 1000px; margin: 40px auto; padding: 20px; }

        /* ENCABEZADO */
        .header-actions {
            display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;
            background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .page-title { margin: 0; color: #1565C0; font-weight: 700; display: flex; align-items: center; gap: 10px; }
        
        .btn-back {
            background: #ECEFF1; color: #546E7A; padding: 10px 20px; border-radius: 30px;
            text-decoration: none; font-weight: bold; display: flex; align-items: center; gap: 5px; transition: 0.2s;
        }
        .btn-back:hover { background: #CFD8DC; color: #37474F; }

        /* TABLA MODERNA */
        .table-container {
            background: white; border-radius: 16px; overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05); border: 1px solid #eee;
        }

        table { width: 100%; border-collapse: collapse; }
        
        thead { background: #1565C0; color: white; }
        th { padding: 15px; text-align: left; font-weight: 600; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 0.5px; }
        
        td { padding: 15px; border-bottom: 1px solid #eee; color: #444; vertical-align: middle; }
        tr:last-child td { border-bottom: none; }
        tr:hover { background-color: #F5F9FF; }

        /* ESTILOS DE CELDA */
        .password-text {
            font-family: 'Roboto Mono', monospace; background: #FFF3E0; color: #E65100;
            padding: 4px 8px; border-radius: 4px; border: 1px solid #FFE0B2; font-weight: 500;
        }
        
        .user-name { font-weight: bold; color: #333; }
        .user-role { font-size: 0.8rem; color: #888; }

        /* BOTONES DE ACCIÓN */
        .actions-cell { display: flex; gap: 8px; }
        .btn-icon {
            width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center;
            text-decoration: none; transition: 0.2s;
        }
        .btn-edit { background: #E3F2FD; color: #1565C0; }
        .btn-edit:hover { background: #2196F3; color: white; }
        
        .btn-del { background: #FFEBEE; color: #D32F2F; }
        .btn-del:hover { background: #EF5350; color: white; }

        /* RESPONSIVE */
        @media (max-width: 768px) {
            .table-container { overflow-x: auto; }
        }
    </style>
</head>
<body>

    <?php include("php/encabezado_madera.php"); ?>
    <?php include("php/barra_navegacion.php"); ?>

    <div class="container-pass">
        
        <div class="header-actions">
            <h1 class="page-title"><span class="material-icons">vpn_key</span> Accesos y Contraseñas</h1>
            <a href="adm_usuarios.php" class="btn-back">
                <span class="material-icons">arrow_back</span> Volver a Usuarios
            </a>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Usuario</th>
                        <th>Contraseña Actual</th>
                        <th style="text-align: right;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result && mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_object($result)) {
                    ?>
                    <tr>
                        <td>#<?php echo $row->id_usuario; ?></td>
                        <td>
                            <div class="user-name"><?php echo $row->usu_nom . ' ' . $row->usu_ap_pat; ?></div>
                            <div class="user-role"><?php echo $row->usu_tipo; ?></div>
                        </td>
                        <td>
                            <span class="password-text"><?php echo $row->usu_password; ?></span>
                        </td>
                        <td>
                            <div class="actions-cell" style="justify-content: flex-end;">
                                <a href="adm_usuario_modificar.php?id=<?php echo $row->id_usuario; ?>" class="btn-icon btn-edit" title="Modificar">
                                    <span class="material-icons" style="font-size:18px;">edit</span>
                                </a>
                                <a href="#" onclick="confirmarBorrado(<?php echo $row->id_usuario; ?>)" class="btn-icon btn-del" title="Eliminar">
                                    <span class="material-icons" style="font-size:18px;">delete</span>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php
                        }
                    } else {
                        echo "<tr><td colspan='4' style='text-align:center; padding:20px;'>No hay usuarios registrados</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

    </div>

    <?php include("php/olas.php"); ?>

    <script>
        function confirmarBorrado(id) {
            if(confirm("¿Estás seguro de eliminar este usuario?")) {
                window.location.href = "adm_usuario_eliminar.php?id=" + id;
            }
        }
    </script>

</body>
</html>
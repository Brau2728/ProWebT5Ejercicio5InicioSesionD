<?php
$page = 'inicio'; // Ilumina el menú "Inicio"
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

include("php/conexion.php");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Principal - Idealiza</title>
    
    <link rel="stylesheet" href="estilos/Wave2.css">
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <style>
        /* PALETA DE COLORES PERSONALIZADA */
        body {
            font-family: 'Quicksand', sans-serif;
            background-color: #F0F2F5;
            margin: 0;
            padding-bottom: 100px;
        }

        .container-admin {
            max-width: 1100px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        /* --- LOGO CENTRAL (Sustituye a la cápsula de bienvenida) --- */
        .logo-header {
            text-align: center;
            margin-bottom: 50px;
            padding: 20px;
            /* Opcional: fondo blanco suave detrás del logo */
            /* background: white; border-radius: 16px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); */
        }

        .img-logo-principal {
            max-width: 450px; /* Tamaño del logo */
            width: 100%;
            height: auto;
            display: inline-block;
            filter: drop-shadow(0 4px 6px rgba(0,0,0,0.1)); 
        }

        /* GRID DE MENÚ PRINCIPAL */
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
        }

        /* TARJETAS DE ACCESO */
        .menu-card {
            background: white;
            border-radius: 16px;
            padding: 30px;
            text-decoration: none;
            color: inherit;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            transition: transform 0.3s, box-shadow 0.3s;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            border-top: 5px solid transparent;
        }
        .menu-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.15);
        }

        .icon-circle {
            width: 70px; height: 70px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 20px;
        }
        .icon-circle .material-icons { font-size: 36px; }

        /* ESTILOS DE COLOR (Paleta Madera) */
        
        /* Verde Oscuro */
        .card-green { border-color: #144c3c; }
        .card-green .icon-circle { background: #cedfcd; color: #144c3c; }
        
        /* Marrón */
        .card-brown { border-color: #94745c; }
        .card-brown .icon-circle { background: #efebe9; color: #94745c; }

        .menu-title { font-size: 1.3rem; font-weight: 700; margin: 0 0 10px 0; color: #144c3c; }
        .menu-desc { font-size: 0.9rem; color: #5d6b62; margin: 0; line-height: 1.5; }

    </style>
</head>
<body>

    <?php include("php/encabezado_madera.php"); ?>
    <?php include("php/barra_navegacion.php"); ?>

    <div class="container-admin">
        
        <div class="logo-header">
        <img src="Img/img-logo-idealisa" alt="Logo Idealiza" 

style="max-width: 200px; width: 100%; height: auto; filter: drop-shadow(0 5px 5px rgba(0,0,0,0.5));"> </div>

        <div class="dashboard-grid">
            
            <a href="adm_registros.php" class="menu-card card-green">
                <div class="icon-circle"><span class="material-icons">query_stats</span></div>
                <h3 class="menu-title">Monitor de Producción</h3>
                <p class="menu-desc">Visualiza el flujo de trabajo, valida avances y gestiona el almacén en tiempo real.</p>
            </a>

            <a href="adm_modelos.php" class="menu-card card-brown">
                <div class="icon-circle"><span class="material-icons">chair</span></div>
                <h3 class="menu-title">Catálogo de Modelos</h3>
                <p class="menu-desc">Administra los diseños, fotos y descripciones de tus muebles.</p>
            </a>

            <a href="adm_usuarios.php" class="menu-card card-green">
                <div class="icon-circle"><span class="material-icons">people</span></div>
                <h3 class="menu-title">Personal y Usuarios</h3>
                <p class="menu-desc">Gestiona a tus colaboradores, accesos y roles del sistema.</p>
            </a>

            <a href="adm_registros_registrar.php" class="menu-card card-brown">
                <div class="icon-circle"><span class="material-icons">add_circle</span></div>
                <h3 class="menu-title">Registrar Nuevo Lote</h3>
                <p class="menu-desc">Inicia una nueva orden de producción rápidamente.</p>
            </a>

        </div>
    </div>

    <?php include("php/olas.php"); ?>

</body>
</html>
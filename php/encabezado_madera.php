<?php
// 1. LÓGICA DE DETECCIÓN DE PÁGINA
// Obtenemos el nombre del archivo actual (ej: adm_pedidos.php)
$archivo_actual = basename($_SERVER['PHP_SELF']);

// Valores por defecto
$titulo_header = "Bienvenido a Idealisa";
$desc_header = "Sistema de Gestión y Manufactura";
$icono_header = "store";

// Diccionario de páginas (Configura aquí tus títulos)
switch ($archivo_actual) {
    case 'adm_index.php':
        $titulo_header = "Panel General";
        $desc_header = "Resumen de actividad y métricas clave";
        $icono_header = "dashboard";
        break;
        
    case 'adm_pedidos.php':
        $titulo_header = "Control de Pedidos";
        $desc_header = "Gestión de órdenes, entregas y tiempos";
        $icono_header = "receipt_long";
        break;

    case 'adm_registros.php':
        $titulo_header = "Monitor de Planta";
        $desc_header = "Seguimiento de producción en tiempo real";
        $icono_header = "precision_manufacturing";
        break;

    case 'adm_modelos.php':
        $titulo_header = "Catálogo de Productos";
        $desc_header = "Administración de modelos y diseños";
        $icono_header = "chair";
        break;

    case 'adm_usuarios.php':
        $titulo_header = "Gestión de Personal";
        $desc_header = "Administradores, empleados y accesos";
        $icono_header = "groups";
        break;

    case 'adm_registros_registrar.php':
        $titulo_header = "Nuevo Registro";
        $desc_header = "Ingreso de lotes a producción";
        $icono_header = "add_circle";
        break;
}
?>

<style>
    :root {
        --header-bg-start: #144c3c;  /* Verde Idealisa Oscuro */
        --header-bg-end: #1e5c4b;    /* Un tono un poco más claro */
        --accent-wood: #94745c;      /* El color madera como acento */
        --text-white: #ffffff;
    }

    .hero-header {
        /* Fondo degradado elegante en lugar de imagen pixelada */
        background: linear-gradient(135deg, var(--header-bg-start) 0%, var(--header-bg-end) 100%);
        padding: 30px 40px;
        border-radius: 0 0 20px 20px; /* Redondeado abajo si está suelto */
        color: var(--text-white);
        position: relative;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(20, 76, 60, 0.15);
        margin-bottom: 25px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    /* Patrón sutil de fondo (Opcional, para dar textura sin usar imagen pesada) */
    .hero-header::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background-image: radial-gradient(rgba(255, 255, 255, 0.1) 1px, transparent 1px);
        background-size: 20px 20px;
        opacity: 0.3;
        pointer-events: none;
    }

    /* Contenido Izquierdo */
    .hero-content {
        position: relative;
        z-index: 2;
    }

    .hero-breadcrumb {
        font-family: 'Quicksand', sans-serif;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: rgba(255, 255, 255, 0.7);
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .hero-title {
        font-family: 'Outfit', sans-serif;
        font-weight: 700;
        font-size: 2rem;
        margin: 0;
        line-height: 1.1;
    }

    .hero-desc {
        font-family: 'Quicksand', sans-serif;
        font-size: 1rem;
        margin-top: 8px;
        opacity: 0.9;
        font-weight: 400;
    }

    /* Icono Grande Decorativo a la derecha */
    .hero-icon-decoration {
        font-size: 80px;
        color: rgba(255, 255, 255, 0.1);
        transform: rotate(-15deg) translateY(10px);
        position: absolute;
        right: 30px;
        bottom: -20px;
        pointer-events: none;
    }
    
    /* Círculo decorativo */
    .decoration-circle {
        position: absolute;
        width: 150px; height: 150px;
        background: var(--accent-wood);
        border-radius: 50%;
        top: -60px; right: -40px;
        opacity: 0.2;
        filter: blur(40px);
    }
</style>

<div class="hero-header">
    
    <div class="decoration-circle"></div>
    <span class="material-icons-round hero-icon-decoration"><?php echo $icono_header; ?></span>

    <div class="hero-content">
        <div class="hero-breadcrumb">
            <span class="material-icons-round" style="font-size:14px">home</span> INICIO
            <span class="material-icons-round" style="font-size:12px">chevron_right</span>
            <?php echo strtoupper(str_replace('adm_', '', str_replace('.php', '', $archivo_actual))); ?>
        </div>
        
        <h1 class="hero-title"><?php echo $titulo_header; ?></h1>
        <div class="hero-desc"><?php echo $desc_header; ?></div>
    </div>

    <div style="position:relative; z-index:2; text-align:right; display:none;">
        <div style="font-size:0.9rem; opacity:0.8;"><?php echo date("d/m/Y"); ?></div>
    </div>
</div>
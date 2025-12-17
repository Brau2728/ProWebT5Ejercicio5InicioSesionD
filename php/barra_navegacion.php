<?php
// Aseguramos que $page esté definido para evitar errores
if (!isset($page)) { $page = ''; }
?>

<style>
    /* === ESTILOS DE BARRA DE NAVEGACIÓN IDEALISA === */
    :root {
        --nav-bg: #ffffff;
        --nav-text: #2b3674;
        --nav-hover: #F4F7FE;
        --primary-green: #144c3c;
        --accent-brown: #94745c;
        --logout-red: #d32f2f;
        --logout-bg: #ffebee;
    }

    /* Contenedor Principal */
    .idealisa-navbar {
        background-color: var(--nav-bg);
        box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        height: 80px;
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 30px;
        position: sticky;
        top: 0;
        z-index: 1000;
        box-sizing: border-box;
        font-family: 'Quicksand', sans-serif;
        border-bottom: 1px solid rgba(0,0,0,0.05);
    }

    /* 1. ÁREA DE MARCA (LOGO) */
    .nav-brand {
        display: flex;
        align-items: center;
        gap: 15px;
        text-decoration: none;
    }

    .nav-logo-img {
        height: 50px; /* Ajusta según tu logo */
        width: auto;
        object-fit: contain;
    }

    .nav-brand-text {
        font-family: 'Outfit', sans-serif;
        font-weight: 800;
        font-size: 1.4rem;
        color: var(--primary-green);
        letter-spacing: 0.5px;
        text-transform: uppercase;
        display: flex;
        flex-direction: column;
        line-height: 1;
    }
    
    .nav-brand-sub {
        font-size: 0.75rem;
        color: var(--accent-brown);
        font-weight: 600;
        letter-spacing: 2px;
        margin-top: 2px;
    }

    /* 2. ENLACES DE NAVEGACIÓN */
    .nav-links-container {
        display: flex;
        gap: 10px;
        height: 100%;
        align-items: center;
    }

    .nav-link {
        text-decoration: none;
        color: var(--nav-text);
        font-weight: 600;
        font-size: 0.95rem;
        padding: 10px 18px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
    }

    /* ANIMACIÓN: Hover General */
    .nav-link:hover {
        background-color: var(--nav-hover);
        color: var(--primary-green);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(20, 76, 60, 0.1);
    }

    /* Estado Activo (Página Actual) */
    .nav-link.active {
        background-color: #E8F5E9; /* Verde muy claro */
        color: var(--primary-green);
        font-weight: 700;
    }

    /* ANIMACIÓN: Icono Zoom */
    .nav-link span.material-icons-round {
        font-size: 22px;
        transition: transform 0.3s ease;
    }
    .nav-link:hover span.material-icons-round {
        transform: scale(1.2);
    }

    /* 3. ÁREA DE USUARIO Y SALIR */
    .nav-user-area {
        display: flex;
        align-items: center;
        gap: 20px;
        padding-left: 20px;
        border-left: 1px solid #eee;
        height: 40px;
    }

    .user-info {
        text-align: right;
        line-height: 1.2;
    }
    .user-name { font-weight: 700; font-size: 0.9rem; color: #333; }
    .user-role { font-size: 0.75rem; color: var(--accent-brown); font-weight: 600; text-transform: uppercase; }

    /* BOTÓN SALIR (Logout) */
    .btn-logout {
        color: #74777F;
        text-decoration: none;
        padding: 10px;
        border-radius: 50%;
        transition: all 0.3s ease;
        display: flex; align-items: center; justify-content: center;
    }

    /* ANIMACIÓN SALIR: Rojo + Shake + Rotación */
    .btn-logout:hover {
        background-color: var(--logout-bg);
        color: var(--logout-red);
        box-shadow: 0 4px 15px rgba(211, 47, 47, 0.2);
        animation: shake 0.5s ease-in-out;
    }
    
    .btn-logout:hover span.material-icons-round {
        transform: rotate(90deg);
    }

    @keyframes shake {
        0% { transform: translateX(0); }
        25% { transform: translateX(-3px); }
        50% { transform: translateX(3px); }
        75% { transform: translateX(-3px); }
        100% { transform: translateX(0); }
    }

    /* Responsive (Ocultar textos en pantallas pequeñas) */
    @media (max-width: 1100px) {
        .nav-link span:not(.material-icons-round) { display: none; }
        .nav-link { padding: 10px; }
        .nav-brand-text { display: none; }
    }
</style>

<nav class="idealisa-navbar">
    
    <a href="adm_index.php" class="nav-brand">
        <img src="Img/img-logo-idealisa.png" alt="Idealisa" class="nav-logo-img" onerror="this.style.display='none'">
        <div class="nav-brand-text">
            IDEALISA
            <span class="nav-brand-sub">ADMIN</span>
        </div>
    </a>

    <div class="nav-links-container">
        
        <a href="adm_index.php" class="nav-link <?php echo ($page=='inicio') ? 'active' : ''; ?>" title="Inicio">
            <span class="material-icons-round">dashboard</span>
            <span>Inicio</span>
        </a>

        <a href="adm_modelos.php" class="nav-link <?php echo ($page=='modelos') ? 'active' : ''; ?>" title="Catálogo de Productos">
            <span class="material-icons-round">chair</span>
            <span>Catálogo</span>
        </a>

        <a href="adm_registros.php" class="nav-link <?php echo ($page=='monitor') ? 'active' : ''; ?>" title="Monitor de Producción">
            <span class="material-icons-round">analytics</span>
            <span>Monitoreo Planta</span>
        </a>

        <a href="adm_pedidos.php" class="nav-link <?php echo ($page=='pedidos') ? 'active' : ''; ?>" title="Gestión de Pedidos">
            <span class="material-icons-round">receipt_long</span>
            <span>Pedidos</span>
        </a>

        <a href="adm_nomina.php" class="nav-link <?php echo ($page=='nomina') ? 'active' : ''; ?>" title="Nómina y Pagos">
            <span class="material-icons-round">payments</span>
            <span>Nómina</span>
        </a>

        <?php if(isset($_SESSION['tipo']) && $_SESSION['tipo'] == 'admin'): ?>
        <a href="adm_usuarios.php" class="nav-link <?php echo ($page=='personal') ? 'active' : ''; ?>" title="Gestión de Personal">
            <span class="material-icons-round">groups</span>
            <span>Personal</span>
        </a>
        <?php endif; ?>

    </div>

    <div class="nav-user-area">
        <div class="user-info">
            <div class="user-name"><?php echo isset($_SESSION['usuario']) ? htmlspecialchars($_SESSION['usuario']) : 'Usuario'; ?></div>
            <div class="user-role"><?php echo isset($_SESSION['tipo']) ? htmlspecialchars($_SESSION['tipo']) : 'Staff'; ?></div>
        </div>
        
        <a href="logout.php" class="btn-logout" title="Cerrar Sesión">
            <span class="material-icons-round" style="font-size:24px;">logout</span>
        </a>
    </div>

</nav>
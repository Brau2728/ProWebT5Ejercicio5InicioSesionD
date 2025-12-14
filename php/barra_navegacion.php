<?php
// Aseguramos que $page esté definido para evitar errores de "Undefined variable"
if (!isset($page)) { $page = ''; }
?>

<!-- Estilos Específicos de la Barra (Inline para evitar caché) -->
<style>
    /* Variables de color basadas en tu identidad */
    :root {
        --nav-bg-color: #ffffff;
        --nav-text-color: #333333;
        --nav-hover-bg: #f4f6f8;
        --nav-active-color: #144c3c; /* Verde Idealisa */
        --nav-border-color: #e0e0e0;
        --nav-accent: #94745c; /* Marrón */
    }

    /* Contenedor Principal */
    .idealisa-navbar {
        background-color: var(--nav-bg-color);
        height: 70px;
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        border-bottom: 1px solid var(--nav-border-color);
        position: sticky;
        top: 0;
        z-index: 1000;
        box-sizing: border-box;
        font-family: 'Quicksand', sans-serif;
    }

    /* Marca / Logo */
    .nav-brand-area {
        display: flex;
        align-items: center;
        gap: 12px;
        text-decoration: none;
    }

    .nav-logo-img {
        height: 45px;
        width: auto;
        object-fit: contain;
    }

    .nav-brand-text {
        font-family: 'Outfit', sans-serif;
        font-weight: 700;
        font-size: 1.3rem;
        color: var(--nav-active-color);
        letter-spacing: 0.5px;
        text-transform: uppercase;
    }

    /* Enlaces de Navegación (Desktop) */
    .nav-links-area {
        display: flex;
        gap: 5px;
        height: 100%;
        align-items: center;
    }

    .nav-item-link {
        text-decoration: none;
        color: var(--nav-text-color);
        padding: 10px 18px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.95rem;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        gap: 8px;
        position: relative;
    }

    /* Efecto Hover */
    .nav-item-link:hover {
        background-color: var(--nav-hover-bg);
        color: var(--nav-active-color);
    }

    /* Estado Activo */
    .nav-item-link.active {
        background-color: #e8f5e9; /* Verde muy claro */
        color: var(--nav-active-color);
        font-weight: 700;
    }
    
    /* Iconos Material */
    .nav-icon {
        font-size: 20px;
        /* Ajuste vertical */
        display: flex; 
        align-items: center;
    }

    /* Área de Usuario */
    .nav-user-area {
        display: flex;
        align-items: center;
        gap: 15px;
        padding-left: 20px;
        border-left: 1px solid var(--nav-border-color);
        height: 40px;
    }

    .user-info {
        text-align: right;
        line-height: 1.2;
    }
    .user-name {
        font-weight: 700;
        font-size: 0.9rem;
        color: #333;
    }
    .user-role {
        font-size: 0.75rem;
        color: var(--nav-accent);
        font-weight: 600;
    }

    .btn-logout-nav {
        color: #d32f2f;
        text-decoration: none;
        padding: 8px;
        border-radius: 50%;
        transition: 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .btn-logout-nav:hover {
        background-color: #ffebee;
    }

    /* RESPONSIVE (Móvil) */
    .mobile-menu-btn {
        display: none;
        background: none;
        border: none;
        font-size: 24px;
        color: var(--nav-active-color);
        cursor: pointer;
    }

    @media (max-width: 992px) {
        .nav-links-area {
            display: none; /* Ocultar menú normal */
            position: absolute;
            top: 70px;
            left: 0;
            width: 100%;
            background: white;
            flex-direction: column;
            padding: 20px;
            box-shadow: 0 5px 10px rgba(0,0,0,0.1);
            height: auto;
            border-bottom: 1px solid #eee;
        }
        
        .nav-links-area.show-mobile {
            display: flex; /* Mostrar al activar JS */
        }

        .nav-item-link {
            width: 100%;
            padding: 15px;
            justify-content: flex-start;
        }

        .nav-user-area {
            display: none; /* Simplificar header en móvil */
        }
        
        .mobile-menu-btn {
            display: block;
        }
        
        .nav-brand-text {
            font-size: 1.1rem;
        }
    }
</style>

<!-- HTML DE LA BARRA -->
<nav class="idealisa-navbar">
    
    <!-- 1. LOGO -->
    <a href="adm_index.php" class="nav-brand-area">
        <img src="Img/img-logo-idealisa.png" alt="Logo" class="nav-logo-img" onerror="this.style.display='none'">
        <span class="nav-brand-text">IDEALISA ERP</span>
    </a>

    <!-- Botón Móvil -->
    <button class="mobile-menu-btn" onclick="toggleMobileMenu()">
        <span class="material-icons-round">menu</span>
    </button>

    <!-- 2. ENLACES (Menú Central) -->
    <div class="nav-links-area" id="navLinks">
        
        <a href="adm_index.php" class="nav-item-link <?php echo ($page=='inicio') ? 'active' : ''; ?>">
            <span class="material-icons-round nav-icon">dashboard</span>
            <span>Inicio</span>
        </a>

        <a href="adm_modelos.php" class="nav-item-link <?php echo ($page=='modelos') ? 'active' : ''; ?>">
            <span class="material-icons-round nav-icon">chair</span>
            <span>Catálogo</span>
        </a>

        <a href="adm_registros.php" class="nav-item-link <?php echo ($page=='monitor') ? 'active' : ''; ?>">
            <span class="material-icons-round nav-icon">analytics</span>
            <span>Monitor Planta</span>
        </a>

        <a href="adm_pedidos.php" class="nav-item-link <?php echo ($page=='pedidos') ? 'active' : ''; ?>">
            <span class="material-icons-round nav-icon">receipt_long</span>
            <span>Pedidos</span>
        </a>

        <a href="adm_usuarios.php" class="nav-item-link <?php echo ($page=='personal') ? 'active' : ''; ?>">
            <span class="material-icons-round nav-icon">groups</span>
            <span>Personal</span>
        </a>

        <!-- En móvil mostramos salir aquí -->
        <a href="logout.php" class="nav-item-link" style="color:#d32f2f; margin-top:10px; border-top:1px solid #eee;" id="mobileLogout">
            <span class="material-icons-round nav-icon">logout</span>
            <span>Cerrar Sesión</span>
        </a>
    </div>

    <!-- 3. USUARIO (Derecha) -->
    <div class="nav-user-area">
        <div class="user-info">
            <div class="user-name"><?php echo isset($_SESSION['usuario']) ? $_SESSION['usuario'] : 'Usuario'; ?></div>
            <div class="user-role"><?php echo isset($_SESSION['tipo']) ? ucfirst($_SESSION['tipo']) : 'Staff'; ?></div>
        </div>
        <a href="logout.php" class="btn-logout-nav" title="Salir">
            <span class="material-icons-round">logout</span>
        </a>
    </div>

</nav>

<!-- Script simple para menú móvil -->
<script>
    function toggleMobileMenu() {
        var menu = document.getElementById('navLinks');
        menu.classList.toggle('show-mobile');
    }
    
    // Ocultar botón salir duplicado en desktop
    if(window.innerWidth > 992) {
        document.getElementById('mobileLogout').style.display = 'none';
    }
</script>
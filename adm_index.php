<?php include("php/bienvenida.php"); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Control - Idealisa</title>
    
    <link rel="stylesheet" href="estilos/Wave2.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;500;700;800&family=Quicksand:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">

    <style>
        :root {
            --primary: #144c3c;
            --accent: #94745c;
            --bg-body: #F7F9FC; 
            --shadow-glow: 0 15px 30px -5px rgba(20, 76, 60, 0.15);
        }

        body { 
            font-family: 'Outfit', sans-serif; 
            background-color: var(--bg-body); 
            min-height: 100vh; 
            margin: 0;
            padding-bottom: 200px;
            color: #2D3436;
        }

        .main-container { max-width: 1200px; margin: 0 auto; padding: 40px 25px; }

        /* HERO */
        .hero-banner {
            background: linear-gradient(120deg, var(--primary) 0%, #0f382c 100%);
            border-radius: 30px; padding: 40px; position: relative; overflow: hidden;
            color: white; box-shadow: var(--shadow-glow); margin-bottom: 50px;
            display: flex; align-items: center; justify-content: space-between;
        }
        .hero-banner::before {
            content: ''; position: absolute; top: -50px; right: -50px; width: 300px; height: 300px; 
            background: rgba(148, 116, 92, 0.25); border-radius: 50%;
        }
        .hero-banner::after {
            content: ''; position: absolute; bottom: -80px; left: 100px; width: 200px; height: 200px; 
            background: rgba(148, 116, 92, 0.15); border-radius: 50%;
        }

        .user-welcome h2 { font-weight: 300; font-size: 1.5rem; margin: 0; opacity: 0.9; }
        .user-welcome h1 { font-weight: 800; font-size: 3rem; margin: 5px 0 0 0; letter-spacing: -1px; }
        .date-badge {
            display: inline-block; background: rgba(255,255,255,0.2);
            padding: 5px 15px; border-radius: 20px; font-size: 0.85rem;
            margin-top: 15px; backdrop-filter: blur(5px);
        }
        .hero-logo img { height: 80px; position: relative; z-index: 2; transition: transform 0.5s; }
        .hero-logo img:hover { transform: scale(1.05) rotate(-2deg); }

        /* STATS */
        .stats-container { display: flex; gap: 30px; margin-bottom: 60px; flex-wrap: wrap; }
        .stat-widget {
            flex: 1; min-width: 200px; background: white; border-radius: 24px; padding: 25px;
            box-shadow: 0 10px 40px -10px rgba(0,0,0,0.08); position: relative; overflow: hidden;
            transition: transform 0.3s;
        }
        .stat-widget:hover { transform: translateY(-5px); }
        .stat-number { font-size: 3.5rem; font-weight: 800; color: var(--primary); line-height: 1; margin-bottom: 5px; }
        .stat-label { font-size: 0.9rem; font-weight: 600; color: #888; text-transform: uppercase; letter-spacing: 1px; }
        .stat-bg-icon {
            position: absolute; right: -20px; bottom: -20px; font-size: 120px; opacity: 0.05; color: var(--primary);
        }

        /* APPS GRID */
        .grid-header { margin-bottom: 25px; }
        .section-heading { font-size: 1.5rem; font-weight: 700; color: #2D3436; }

        .apps-grid {
            display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 30px;
            position: relative; z-index: 10;
        }
        .app-card {
            background: white; border-radius: 28px; padding: 35px 30px;
            text-decoration: none; color: inherit; box-shadow: 0 10px 40px -10px rgba(0,0,0,0.05);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            display: flex; flex-direction: column; align-items: center; text-align: center;
            border: 1px solid rgba(0,0,0,0.02);
        }
        .app-card:hover { transform: translateY(-10px); box-shadow: 0 25px 50px -12px rgba(20, 76, 60, 0.25); }

        .icon-container {
            width: 80px; height: 80px; border-radius: 24px;
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 25px; font-size: 36px;
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); color: var(--primary);
            transition: 0.4s ease;
        }
        .ac-brown .icon-container { background: linear-gradient(135deg, #fff7ed 0%, #ffedd5 100%); color: var(--accent); }
        .ac-blue .icon-container { background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%); color: #2563eb; }

        .app-card:hover .icon-container { transform: scale(1.1) rotate(5deg); border-radius: 30px; }
        .app-title { font-size: 1.25rem; font-weight: 700; margin-bottom: 8px; color: #111; }
        .app-desc { font-size: 0.9rem; color: #888; line-height: 1.5; font-weight: 500; }
        .action-arrow {
            margin-top: 20px; opacity: 0; transform: translateX(-10px); transition: 0.3s;
            color: var(--primary); font-weight: bold; font-size: 1.2rem;
        }
        .app-card:hover .action-arrow { opacity: 1; transform: translateX(0); }

        @media (max-width: 768px) {
            .hero-banner { flex-direction: column; text-align: center; gap: 20px; }
            .user-welcome h1 { font-size: 2.2rem; }
        }
    </style>
</head>
<body>

    <?php include("php/encabezado_madera.php"); ?>
    <?php include("php/barra_navegacion.php"); ?>

    <div class="main-container">
        
        <!-- HERO -->
        <div class="hero-banner">
            <div class="user-welcome">
                <h2>Hola, <?php echo $_SESSION['usuario']; ?></h2>
                <h1><?php echo $saludo_genero; ?> a Idealisa</h1>
                <div class="date-badge">
                    <?php echo $fecha_actual; ?> • Panel Admin
                </div>
            </div>
            <div class="hero-logo">
                <img src="img/img-logo-idealisa.png" alt="Logo" onerror="this.style.display='none'">
            </div>
        </div>

        <!-- KPI WIDGETS -->
        <div class="stats-container">
            <div class="stat-widget">
                <div class="stat-number"><?php echo $total_pedidos; ?></div>
                <div class="stat-label">En Producción</div>
                <span class="material-icons-round stat-bg-icon">inventory_2</span>
            </div>
            <div class="stat-widget">
                <div class="stat-number" style="color:var(--accent)"><?php echo $total_modelos; ?></div>
                <div class="stat-label">Modelos Activos</div>
                <span class="material-icons-round stat-bg-icon" style="color:var(--accent)">chair</span>
            </div>
            <div class="stat-widget">
                <div class="stat-number" style="color:#2563eb"><?php echo $total_staff; ?></div>
                <div class="stat-label">Equipo</div>
                <span class="material-icons-round stat-bg-icon" style="color:#2563eb">groups</span>
            </div>
        </div>

        <!-- APPS -->
        <div class="grid-header">
            <div class="section-heading">Tus Herramientas</div>
        </div>

        <div class="apps-grid">
            <!-- 1. Monitor -->
            <a href="adm_registros.php" class="app-card">
                <div class="icon-container"><span class="material-icons-round">query_stats</span></div>
                <div class="app-title">Monitor de Planta</div>
                <div class="app-desc">Control visual del flujo de trabajo y validaciones.</div>
                <div class="action-arrow">→</div>
            </a>
            <!-- 2. Nuevo Lote -->
            <a href="adm_registros_registrar.php" class="app-card ac-brown">
                <div class="icon-container"><span class="material-icons-round">add_shopping_cart</span></div>
                <div class="app-title">Nuevo Lote</div>
                <div class="app-desc">Ingresa nuevos pedidos para iniciar producción.</div>
                <div class="action-arrow" style="color:var(--accent)">→</div>
            </a>
            <!-- 3. Catálogo -->
            <a href="adm_modelos.php" class="app-card ac-brown">
                <div class="icon-container"><span class="material-icons-round">design_services</span></div>
                <div class="app-title">Catálogo</div>
                <div class="app-desc">Administra modelos, fotos y materiales.</div>
                <div class="action-arrow" style="color:var(--accent)">→</div>
            </a>
            <!-- 4. Personal -->
            <a href="adm_usuarios.php" class="app-card ac-blue">
                <div class="icon-container"><span class="material-icons-round">badge</span></div>
                <div class="app-title">Recursos Humanos</div>
                <div class="app-desc">Gestión de usuarios y roles del personal.</div>
                <div class="action-arrow" style="color:#2563eb">→</div>
            </a>
        </div>
    </div>

    <?php include("php/olas.php"); ?>

    

</body>
</html>
<?php
// adm_index.php - VERSIÓN CORREGIDA: Incluye Stock Libre (Sin Pedido)
session_start();
if (!isset($_SESSION['usuario'])) { header('Location: login.php'); exit(); }

$page = 'inicio';
include("php/conexion.php");

// --- 1. CONFIGURACIÓN ---
date_default_timezone_set('America/Mexico_City');
$hora = date('G');
$saludo = ($hora < 12) ? "Buenos días" : (($hora < 19) ? "Buenas tardes" : "Buenas noches");

$dias = ["Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado"];
$meses = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
$fecha_hoy = $dias[date('w')] . ", " . date('j') . " de " . $meses[date('n')-1] . " de " . date('Y');

// --- 2. CONSULTAS GENERALES ---

// Pedidos Activos (Fábrica)
$rPed = mysqli_fetch_assoc(db_query("SELECT COUNT(*) as t FROM pedidos WHERE estatus_pedido IN ('pendiente', 'produccion')"));

// Pedidos EN RUTA
$rRuta = mysqli_fetch_assoc(db_query("SELECT COUNT(*) as t FROM pedidos WHERE estatus_pedido = 'ruta'"));

// Producción (Incluye muebles con pedido activo O muebles sin pedido)
$rProd = mysqli_fetch_assoc(db_query("
    SELECT SUM(m.mue_cantidad) as t 
    FROM muebles m 
    LEFT JOIN pedidos p ON m.id_pedido = p.id_pedido
    WHERE m.id_estatus_mueble BETWEEN 2 AND 6 
    AND (p.estatus_pedido IS NULL OR p.estatus_pedido NOT IN ('cancelado', 'entregado'))
"));

// Stock (Terminados en Almacén)
// CORRECCIÓN: Ahora incluye muebles sin pedido (Stock Libre)
$rStock = mysqli_fetch_assoc(db_query("
    SELECT SUM(m.mue_cantidad) as t 
    FROM muebles m 
    LEFT JOIN pedidos p ON m.id_pedido = p.id_pedido
    WHERE m.id_estatus_mueble >= 7 
    AND (
        p.id_pedido IS NULL                -- Caso 1: Mueble sin pedido (Stock libre)
        OR 
        (p.estatus_pedido != 'ruta' AND p.estatus_pedido != 'entregado') -- Caso 2: Con pedido, pero aún en bodega
    )
")); 

$rUsers = mysqli_fetch_assoc(db_query("SELECT COUNT(*) as t FROM usuarios"));

// --- 3. DESGLOSE POR ÁREA ---
$areas = ['Maquila'=>0, 'Armado'=>0, 'Barniz'=>0, 'Pintado'=>0, 'Adornado'=>0];
$qDes = db_query("SELECT id_estatus_mueble, SUM(mue_cantidad) as cant FROM muebles WHERE id_estatus_mueble BETWEEN 2 AND 6 GROUP BY id_estatus_mueble");
while($d = mysqli_fetch_assoc($qDes)) {
    if($d['id_estatus_mueble']==2) $areas['Maquila'] = $d['cant'];
    if($d['id_estatus_mueble']==3) $areas['Armado'] = $d['cant'];
    if($d['id_estatus_mueble']==4) $areas['Barniz'] = $d['cant'];
    if($d['id_estatus_mueble']==5) $areas['Pintado'] = $d['cant'];
    if($d['id_estatus_mueble']==6) $areas['Adornado'] = $d['cant'];
}

$resRecientes = db_query("SELECT * FROM pedidos WHERE estatus_pedido != 'entregado' ORDER BY id_pedido DESC LIMIT 5");

// --- 4. ALERTAS ---
$alertas = [];

// A. Urgentes
$sqlUrg = "SELECT COUNT(*) as t FROM pedidos WHERE fecha_entrega BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 3 DAY) AND estatus_pedido != 'entregado'";
$rUrg = mysqli_fetch_assoc(db_query($sqlUrg));
if($rUrg['t'] > 0) {
    $alertas[] = ['tipo'=>'danger', 'icono'=>'timer', 'titulo'=>'Entregas Próximas', 'msg'=>"<b>{$rUrg['t']} pedidos</b> vencen en < 3 días."];
}

// B. Cuellos de Botella
foreach($areas as $nom => $cant) {
    if($cant < 5) {
        $alertas[] = ['tipo'=>'warning', 'icono'=>'low_priority', 'titulo'=>"Baja Carga en $nom", 'msg'=>"Solo <b>$cant piezas</b> en proceso."];
    }
}

// C. Stock Bajo
$stockVal = $rStock['t'] ? $rStock['t'] : 0;
if($stockVal < 10) {
    $alertas[] = ['tipo'=>'info', 'icono'=>'inventory', 'titulo'=>'Stock Bajo', 'msg'=>"Pocas existencias en bodega ({$stockVal} pzas)."];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Principal | Idealisa</title>
    <link rel="stylesheet" href="estilos/Wave2.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;700&family=Quicksand:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">

    <style>
        /* === ESTILOS CORPORATIVOS === */
        :root {
            --primary: #144c3c;
            --accent: #94745c;
            --bg-body: #F4F7FE;
            --card-bg: #ffffff;
            --text-dark: #2b3674;
            --text-light: #a3aed0;
            --alert-red-bg: #FFF5F5; --alert-red-text: #C53030;
            --alert-orange-bg: #FFFAF0; --alert-orange-text: #C05621;
            --alert-blue-bg: #EBF8FF; --alert-blue-text: #2B6CB0;
        }

        body { font-family: 'Quicksand', sans-serif; background-color: var(--bg-body); margin: 0; padding-bottom: 120px; }
        .main-container { max-width: 1600px; margin: 0 auto; padding: 20px 30px; position: relative; z-index: 10; }

        .dashboard-header-text { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 25px; }
        .dht-left h2 { font-family: 'Outfit'; color: var(--text-dark); margin: 0; font-size: 1.8rem; }
        .dht-left p { margin: 5px 0 0 0; color: var(--text-light); }
        .dht-date { background: white; padding: 8px 15px; border-radius: 30px; color: var(--primary); font-weight: 700; font-size: 0.9rem; box-shadow: 0 3px 10px rgba(0,0,0,0.03); display: flex; align-items: center; gap: 8px; }

        .kpi-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 35px; }
        .kpi-card { background: var(--card-bg); border-radius: 20px; padding: 20px; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 5px 15px rgba(0,0,0,0.03); transition: all 0.3s ease; text-decoration: none; position: relative; overflow: hidden; color: var(--text-dark); }
        .kpi-card:hover { transform: translateY(-5px); box-shadow: 0 15px 30px rgba(148, 116, 92, 0.1); }
        .kpi-card::before { content: ''; position: absolute; left: 0; top: 0; bottom: 0; width: 5px; }
        
        .kpi-info h3 { margin: 0; font-size: 0.8rem; color: var(--text-light); font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }
        .kpi-info .number { font-family: 'Outfit'; font-size: 2rem; font-weight: 700; margin-top: 5px; }
        .kpi-icon { width: 50px; height: 50px; border-radius: 15px; display: flex; align-items: center; justify-content: center; font-size: 24px; transition: 0.3s; }

        .theme-green::before { background: var(--primary); } .theme-green .number { color: var(--primary); } .theme-green .kpi-icon { background: #E8F5E9; color: var(--primary); } .theme-green:hover .kpi-icon { background: var(--primary); color: white; }
        .theme-brown::before { background: var(--accent); } .theme-brown .number { color: var(--accent); } .theme-brown .kpi-icon { background: #F9F3F0; color: var(--accent); } .theme-brown:hover .kpi-icon { background: var(--accent); color: white; }
        .theme-route::before { background: #607d8b; } .theme-route .number { color: #607d8b; } .theme-route .kpi-icon { background: #eceff1; color: #607d8b; } .theme-route:hover .kpi-icon { background: #607d8b; color: white; }

        .dashboard-layout { display: grid; grid-template-columns: 2fr 1fr; gap: 30px; }
        @media (max-width: 1000px) { .dashboard-layout { grid-template-columns: 1fr; } }

        .card-panel { background: var(--card-bg); border-radius: 20px; padding: 25px; box-shadow: 0 5px 20px rgba(0,0,0,0.03); margin-bottom: 30px; }
        .panel-title { font-family: 'Outfit'; font-size: 1.2rem; font-weight: 700; color: var(--text-dark); margin-bottom: 20px; display: flex; align-items: center; gap: 10px; }

        .simple-table { width: 100%; border-collapse: collapse; }
        .simple-table th { text-align: left; color: var(--text-light); font-weight: 600; padding: 10px; font-size: 0.85rem; border-bottom: 1px solid #eee; }
        .simple-table td { padding: 15px 10px; border-bottom: 1px solid #f0f0f0; color: var(--text-dark); font-weight: 600; font-size: 0.95rem; }
        .status-pill { padding: 5px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; }
        .st-pendiente { background: #FFF8E1; color: #FFA000; } .st-produccion { background: #E3F2FD; color: #1E88E5; } .st-ruta { background: #eceff1; color: #607d8b; } .st-entregado { background: #E8F5E9; color: #43A047; }

        .progress-row { margin-bottom: 15px; }
        .pr-labels { display: flex; justify-content: space-between; font-size: 0.85rem; font-weight: 700; color: var(--text-dark); margin-bottom: 6px; }
        .pr-track { width: 100%; height: 8px; background: #F4F7FE; border-radius: 10px; overflow: hidden; }
        .pr-bar { height: 100%; border-radius: 10px; }

        .quick-btn { display: flex; align-items: center; gap: 15px; background: #F9F9F9; padding: 15px; border-radius: 15px; text-decoration: none; color: var(--text-dark); margin-bottom: 15px; transition: 0.2s; border: 1px solid transparent; }
        .quick-btn:hover { background: #FFFFFF; border-color: var(--primary); color: var(--primary); transform: translateX(5px); box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
        .qb-icon { width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; background: var(--primary); color: white; }

        .alerts-wrapper { display: flex; flex-direction: column; gap: 15px; margin-bottom: 25px; }
        .alert-item { padding: 15px; border-radius: 15px; display: flex; gap: 15px; align-items: flex-start; animation: slideIn 0.3s ease; }
        @keyframes slideIn { from{opacity:0; transform:translateX(10px);} to{opacity:1; transform:translateX(0);} }
        .alert-danger { background: var(--alert-red-bg); color: var(--alert-red-text); border-left: 4px solid var(--alert-red-text); }
        .alert-warning { background: var(--alert-orange-bg); color: var(--alert-orange-text); border-left: 4px solid var(--alert-orange-text); }
        .alert-info { background: var(--alert-blue-bg); color: var(--alert-blue-text); border-left: 4px solid var(--alert-blue-text); }
        .al-title { font-weight: 700; font-size: 0.9rem; display: block; margin-bottom: 2px; }
        .al-desc { font-size: 0.85rem; opacity: 0.9; }

        .quote-box { background: linear-gradient(135deg, var(--primary) 0%, #1e5c4b 100%); color: white; padding: 25px; border-radius: 20px; position: relative; overflow: hidden; min-height: 120px; box-shadow: 0 10px 20px rgba(20, 76, 60, 0.2); display: flex; flex-direction: column; justify-content: center; }
        .quote-icon-bg { position: absolute; right: -10px; bottom: -20px; font-size: 90px; color: white; opacity: 0.1; }
        .quote-txt { font-family: 'Outfit'; font-size: 1rem; font-style: italic; position: relative; z-index: 2; line-height: 1.5; margin-bottom: 10px; }
        .quote-author { font-size: 0.8rem; opacity: 0.8; font-weight: 600; text-align: right; }
        .quote-lbl { font-size: 0.7rem; text-transform: uppercase; letter-spacing: 1px; opacity: 0.7; margin-bottom: 10px; display: block; font-weight: 700; }

        .waves-container { position: fixed; bottom: 0; left: 0; width: 100%; height: 150px; z-index: 1; pointer-events: none; }
    </style>
</head>
<body>

    <?php include("php/barra_navegacion.php"); ?>

    <div class="main-container">
        <?php include("php/encabezado_madera.php"); ?>

        <div class="dashboard-header-text">
            <div class="dht-left">
                <h2><?php echo $saludo; ?>, <?php echo htmlspecialchars($_SESSION['usuario']); ?></h2>
                <p>Resumen general de operaciones.</p>
            </div>
            <div class="dht-date">
                <span class="material-icons-round" style="font-size:18px">calendar_today</span>
                <?php echo $fecha_hoy; ?>
            </div>
        </div>

        <div class="kpi-grid">
            <a href="adm_pedidos.php" class="kpi-card theme-green">
                <div class="kpi-info"><h3>Pedidos Activos</h3><div class="number"><?php echo $rPed['t']??0; ?></div></div>
                <div class="kpi-icon"><span class="material-icons-round">shopping_cart</span></div>
            </a>
            
            <a href="adm_registros.php" class="kpi-card theme-brown">
                <div class="kpi-info"><h3>En Producción</h3><div class="number"><?php echo $rProd['t']??0; ?></div></div>
                <div class="kpi-icon"><span class="material-icons-round">handyman</span></div>
            </a>
            
            <a href="adm_registros.php" class="kpi-card theme-green">
                <div class="kpi-info"><h3>Almacén</h3><div class="number"><?php echo $stockVal; ?></div></div>
                <div class="kpi-icon"><span class="material-icons-round">inventory_2</span></div>
            </a>

            <a href="adm_pedidos.php" class="kpi-card theme-route">
                <div class="kpi-info"><h3>En Ruta</h3><div class="number"><?php echo $rRuta['t']??0; ?></div></div>
                <div class="kpi-icon"><span class="material-icons-round">local_shipping</span></div>
            </a>

            <a href="adm_usuarios.php" class="kpi-card theme-brown">
                <div class="kpi-info"><h3>Equipo</h3><div class="number"><?php echo $rUsers['t']??0; ?></div></div>
                <div class="kpi-icon"><span class="material-icons-round">groups</span></div>
            </a>
        </div>

        <div class="dashboard-layout">
            
            <div>
                <div class="card-panel">
                    <div class="panel-title"><span class="material-icons-round" style="color:var(--accent)">analytics</span> Estado de la Línea</div>
                    <?php 
                        $max = max(1, array_sum($areas));
                        $colores = ['Maquila'=>'#455a64', 'Armado'=>'#94745c', 'Barniz'=>'#d97706', 'Pintado'=>'#059669', 'Adornado'=>'#7c3aed'];
                        foreach($areas as $nom => $cant) { $pct = ($cant/$max)*100;
                    ?>
                    <div class="progress-row">
                        <div class="pr-labels"><span><?php echo $nom; ?></span><span><?php echo $cant; ?> pzas</span></div>
                        <div class="pr-track"><div class="pr-bar" style="width:<?php echo $pct; ?>%; background:<?php echo $colores[$nom]; ?>"></div></div>
                    </div>
                    <?php } ?>
                </div>

                <div class="card-panel">
                    <div class="panel-title"><span class="material-icons-round" style="color:var(--text-light)">history</span> Últimos Pedidos</div>
                    <table class="simple-table">
                        <thead><tr><th>ID</th><th>Cliente</th><th>Entrega</th><th>Estado</th></tr></thead>
                        <tbody>
                            <?php while($p = mysqli_fetch_assoc($resRecientes)) { $stClass = 'st-' . strtolower($p['estatus_pedido']); ?>
                            <tr>
                                <td style="color:var(--primary); font-weight:700;">#<?php echo $p['id_pedido']; ?></td>
                                <td><?php echo $p['cliente_nombre']; ?></td>
                                <td><?php echo date('d M', strtotime($p['fecha_entrega'])); ?></td>
                                <td><span class="status-pill <?php echo $stClass; ?>"><?php echo ucfirst($p['estatus_pedido']); ?></span></td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div>
                <?php if(!empty($alertas)): ?>
                <div class="alerts-wrapper">
                    <?php foreach($alertas as $alert): ?>
                    <div class="alert-item alert-<?php echo $alert['tipo']; ?>">
                        <span class="material-icons-round"><?php echo $alert['icono']; ?></span>
                        <div><span class="al-title"><?php echo $alert['titulo']; ?></span><span class="al-desc"><?php echo $alert['msg']; ?></span></div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                    <div class="card-panel" style="text-align:center; color:var(--primary); padding:15px;">
                        <span class="material-icons-round" style="font-size:30px">check_circle</span>
                        <div style="font-weight:700; margin-top:5px;">Todo en orden</div>
                    </div>
                <?php endif; ?>

                <div class="card-panel">
                    <div class="panel-title"><span class="material-icons-round" style="color:var(--primary)">bolt</span> Accesos Rápidos</div>
                    <a href="adm_pedidos.php" class="quick-btn">
                        <div class="qb-icon"><span class="material-icons-round">add</span></div>
                        <div><div style="font-weight:700;">Nuevo Pedido</div><div style="font-size:0.8rem; color:var(--text-light);">Registrar venta</div></div>
                    </a>
                    <a href="adm_modelos.php" class="quick-btn">
                        <div class="qb-icon" style="background:var(--accent)"><span class="material-icons-round">chair</span></div>
                        <div><div style="font-weight:700;">Catálogo</div><div style="font-size:0.8rem; color:var(--text-light);">Ver modelos</div></div>
                    </a>
                    <?php if($_SESSION['tipo']=='admin'): ?>
                    <a href="adm_usuarios.php" class="quick-btn">
                        <div class="qb-icon" style="background:#2b3674"><span class="material-icons-round">person_add</span></div>
                        <div><div style="font-weight:700;">Usuarios</div><div style="font-size:0.8rem; color:var(--text-light);">Gestionar staff</div></div>
                    </a>
                    <?php endif; ?>
                </div>

                <div class="quote-box" id="quoteContainer">
                    <span class="quote-lbl">✨ MENTALIDAD DEL DÍA</span>
                    <div id="quoteContent"></div>
                    <span class="material-icons-round quote-icon-bg">format_quote</span>
                </div>
            </div>

        </div>
    </div>

    <div class="waves-container"><?php include("php/olas.php"); ?></div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const inicios = ["La calidad", "El éxito", "La disciplina", "La constancia", "El esfuerzo", "La dedicación", "La excelencia", "La perseverancia", "El trabajo bien hecho"];
            const medios = ["se construye con", "nace de", "es resultado de", "crece gracias a", "depende de"];
            const finales = ["hábitos diarios.", "pequeños esfuerzos constantes.", "atención a los detalles.", "compromiso y responsabilidad.", "trabajo honesto.", "pasión por mejorar.", "decisiones inteligentes."];

            const bancoFrases = [];
            for (let i = 0; i < 365; i++) {
                const frase = inicios[Math.floor(Math.random() * inicios.length)] + " " + medios[Math.floor(Math.random() * medios.length)] + " " + finales[Math.floor(Math.random() * finales.length)];
                bancoFrases.push({ q: frase, a: "Mentalidad Idealisa" });
            }

            const quoteContent = document.getElementById('quoteContent');
            const todayKey = 'idealisa_quote_' + new Date().toDateString();
            const savedData = localStorage.getItem(todayKey);
            if (savedData) {
                const data = JSON.parse(savedData);
                renderQuote(data.quote, data.author);
            } else {
                const rand = bancoFrases[Math.floor(Math.random() * bancoFrases.length)];
                localStorage.setItem(todayKey, JSON.stringify({ quote: rand.q, author: rand.a }));
                renderQuote(rand.q, rand.a);
            }
            function renderQuote(text, author) {
                quoteContent.innerHTML = `<div class="quote-txt">“${text}”</div><div class="quote-author">— ${author}</div>`;
            }
        });
    </script>
</body>
</html>
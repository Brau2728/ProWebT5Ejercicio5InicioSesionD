<?php
// adm_registros.php - Versión ESTABLE y CORREGIDA
session_start();
if (!isset($_SESSION['usuario']) || !isset($_SESSION['tipo'])) { header('Location: login.php'); exit(); }
include("php/conexion.php");

// ==========================================
// 1. LÓGICA DE DATOS
// ==========================================

// Inicializar variables
$monitorData = [];
$stockTotal = 0;
$listaMuebles = [];

// Inicializamos etapas 2 a 6
for($i=2; $i<=6; $i++) {
    $monitorData[$i] = ['cola' => 0, 'proceso' => 0, 'revision' => 0, 'total' => 0, 'workers' => []];
    $listaMuebles[$i] = [];
}

// Diccionario de Áreas (ESTO FALTABA y causaba el error)
$kw = [
    2 => 'maquila', 
    3 => 'armado', 
    4 => 'barniz', 
    5 => 'pintado', 
    6 => 'adornado'
];

// Definición visual de Áreas
$areasDef = [
    2 => ['n'=>'MAQUILA', 'c'=>'#144c3c', 'i'=>'handyman'],
    3 => ['n'=>'ARMADO', 'c'=>'#94745c', 'i'=>'build'],
    4 => ['n'=>'BARNIZADO', 'c'=>'#b45309', 'i'=>'format_paint'],
    5 => ['n'=>'PINTADO', 'c'=>'#0f766e', 'i'=>'brush'],
    6 => ['n'=>'ADORNADO', 'c'=>'#7c3aed', 'i'=>'auto_awesome']
];

// FILTRO SQL LIMPIO (Oculta lo que ya se fue)
$condicion_limpieza = " AND (p.estatus_pedido IS NULL OR p.estatus_pedido NOT IN ('ruta', 'entregado', 'cancelado')) ";

// A) MONITOR
$sqlMonitor = "SELECT m.id_estatus_mueble, m.asignado_a, m.sub_estatus, SUM(m.mue_cantidad) as total_piezas 
               FROM muebles m LEFT JOIN pedidos p ON m.id_pedido = p.id_pedido
               WHERE m.id_estatus_mueble >= 2 AND m.id_estatus_mueble < 7 $condicion_limpieza
               GROUP BY m.id_estatus_mueble, m.asignado_a, m.sub_estatus";
$resMonitor = db_query($sqlMonitor);

if($resMonitor) {
    while($r = mysqli_fetch_assoc($resMonitor)) {
        $et = $r['id_estatus_mueble'];
        $st = $r['sub_estatus'];
        $cant = (int)$r['total_piezas'];
        $asig = $r['asignado_a'];

        if(isset($monitorData[$et])) {
            $monitorData[$et][$st] += $cant;
            $monitorData[$et]['total'] += $cant;
            if(!empty($asig) && ($st == 'proceso' || $st == 'revision')) {
                if(!isset($monitorData[$et]['workers'][$asig])) $monitorData[$et]['workers'][$asig] = 0;
                $monitorData[$et]['workers'][$asig] += $cant;
            }
        }
    }
}

// B) STOCK
$qStock = db_query("SELECT SUM(m.mue_cantidad) as t FROM muebles m LEFT JOIN pedidos p ON m.id_pedido = p.id_pedido WHERE m.id_estatus_mueble = 7 $condicion_limpieza");
if($rowS = mysqli_fetch_assoc($qStock)) { $stockTotal = $rowS['t'] ? $rowS['t'] : 0; }

// C) MUEBLES DETALLE
$sqlM = "SELECT m.*, mo.modelos_nombre, mo.modelos_imagen, p.id_pedido 
         FROM muebles m INNER JOIN modelos mo ON m.id_modelos = mo.id_modelos LEFT JOIN pedidos p ON m.id_pedido = p.id_pedido
         WHERE m.id_estatus_mueble >= 2 AND m.id_estatus_mueble < 7 $condicion_limpieza
         ORDER BY FIELD(m.sub_estatus, 'revision', 'proceso', 'cola') ASC, m.id_muebles DESC";
$resM = db_query($sqlM);

if($resM) { 
    while($row = mysqli_fetch_assoc($resM)) { 
        $listaMuebles[$row['id_estatus_mueble']][] = $row; 
    } 
}

// D) ALMACÉN
$resTerm = db_query("SELECT m.id_modelos, mo.modelos_nombre, m.mue_color, SUM(m.mue_cantidad) as total_stock FROM muebles m JOIN modelos mo ON m.id_modelos = mo.id_modelos LEFT JOIN pedidos p ON m.id_pedido = p.id_pedido WHERE m.id_estatus_mueble = 7 $condicion_limpieza GROUP BY m.id_modelos, m.mue_color");

// E) EMPLEADOS
$resEmp = db_query("SELECT usu_nom, usu_ap_pat, usu_puesto FROM usuarios WHERE usu_puesto IS NOT NULL ORDER BY usu_puesto, usu_nom");
$empleadosPorArea = ['maquila'=>[], 'armado'=>[], 'barniz'=>[], 'pintado'=>[], 'adornado'=>[], 'almacen'=>[], 'otros'=>[]];

while($e = mysqli_fetch_assoc($resEmp)) {
    $nombre = $e['usu_nom'].' '.$e['usu_ap_pat'];
    $puesto = strtolower($e['usu_puesto']);
    
    if(strpos($puesto, 'maquil') !== false) $empleadosPorArea['maquila'][] = $nombre;
    else if(strpos($puesto, 'armad') !== false) $empleadosPorArea['armado'][] = $nombre;
    else if(strpos($puesto, 'barniz') !== false) $empleadosPorArea['barniz'][] = $nombre;
    else if(strpos($puesto, 'pint') !== false) $empleadosPorArea['pintado'][] = $nombre;
    else if(strpos($puesto, 'adorn') !== false) $empleadosPorArea['adornado'][] = $nombre;
    else if(strpos($puesto, 'almac') !== false) $empleadosPorArea['almacen'][] = $nombre;
    else $empleadosPorArea['otros'][] = ['n'=>$nombre, 'p'=>$e['usu_puesto']];
}
$jsonEmpleados = json_encode($empleadosPorArea);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Monitor Planta | Idealisa</title>
    <link rel="stylesheet" href="estilos/Wave2.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;700&family=Quicksand:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">

    <style>
        :root { --primary: #144c3c; --primary-light: #e0f2f1; --accent: #94745c; --bg-body: #F4F7FE; --white: #ffffff; --text-dark: #2b3674; --text-grey: #a3aed0; --alert-red: #e31a1a; --alert-orange: #ffb547; }
        body { font-family: 'Quicksand', sans-serif; background-color: var(--bg-body); margin: 0; padding-bottom: 60px; color: var(--text-dark); }
        
        .dashboard-wrapper { display: flex; gap: 30px; padding: 30px; max-width: 1800px; margin: 0 auto; align-items: flex-start; }
        .sidebar-monitor { width: 350px; min-width: 350px; display: flex; flex-direction: column; gap: 20px; position: sticky; top: 20px; max-height: 90vh; overflow-y: auto; }
        
        .btn-premium-orders { background: var(--white); color: var(--primary); padding: 15px; border-radius: 15px; text-decoration: none; display: flex; align-items: center; justify-content: center; gap: 10px; font-weight: 700; box-shadow: 0 5px 15px rgba(0,0,0,0.03); border: 1px solid rgba(0,0,0,0.05); }
        .btn-premium-orders:hover { background: var(--primary-light); }

        .widget-card { background: var(--white); border-radius: 20px; padding: 20px; box-shadow: 0 5px 20px rgba(0,0,0,0.02); cursor: pointer; transition: all 0.3s ease; position: relative; overflow: hidden; border: 1px solid transparent; }
        .widget-card:hover { transform: translateY(-5px); box-shadow: 0 15px 30px rgba(0,0,0,0.08); }
        .widget-card.active { border: 2px solid var(--primary); background: #f2fcf5; }
        
        .wc-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; }
        .wc-icon { width: 45px; height: 45px; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; box-shadow: 0 5px 10px rgba(0,0,0,0.1); }
        .wc-title { font-family: 'Outfit'; font-weight: 700; font-size: 1.1rem; color: var(--text-dark); margin-left: 15px; flex: 1; }
        .wc-total { font-size: 1.2rem; font-weight: 800; color: var(--text-dark); }
        
        .wc-stats { display: flex; justify-content: space-between; margin-bottom: 10px; }
        .stat-item { text-align: center; } .stat-lbl { font-size: 0.7rem; color: var(--text-grey); font-weight: 600; text-transform: uppercase; } .stat-val { font-size: 1rem; font-weight: 700; }
        
        .progress-container { width: 100%; height: 8px; background: #E9EDF7; border-radius: 10px; overflow: hidden; display: flex; }
        .bar-segment { height: 100%; transition: width 0.5s ease; }
        
        .alert-pill { margin-top: 15px; padding: 8px 12px; border-radius: 10px; font-size: 0.75rem; font-weight: 700; display: flex; align-items: center; gap: 8px; }
        .ap-red { background: #ffebeb; color: var(--alert-red); } .ap-gray { background: #f4f7fe; color: var(--text-grey); } .ap-orange { background: #fff8e6; color: var(--alert-orange); }
        
        .worker-panel { display: none; margin-top: 15px; padding-top: 15px; border-top: 1px solid #f4f7fe; }
        .widget-card.active .worker-panel { display: block; }
        .wp-row { display: flex; justify-content: space-between; font-size: 0.85rem; padding: 5px 0; }
        
        .main-content { flex: 1; min-width: 0; }
        .top-toolbar { background: var(--white); padding: 15px 25px; border-radius: 20px; margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.02); }
        
        .search-box { display: flex; align-items: center; background: #F4F7FE; border-radius: 30px; padding: 8px 15px; width: 250px; border: 1px solid transparent; }
        .search-box input { border: none; background: transparent; outline: none; font-family: 'Quicksand'; width: 100%; font-size: 0.9rem; margin-left: 5px; }
        
        .filter-capsule { background: #F4F7FE; padding: 5px; border-radius: 12px; display: flex; gap: 5px; }
        .btn-pill { border: none; background: transparent; padding: 8px 16px; border-radius: 8px; cursor: pointer; font-weight: 600; color: var(--text-grey); font-size: 0.9rem; display: flex; align-items: center; gap: 6px; transition: 0.2s; font-family: 'Outfit'; }
        .btn-pill.active { background: var(--white); color: var(--primary); box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .btn-pill.active-rev { background: var(--white); color: var(--alert-orange); box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        
        .btn-reset { background: var(--white); border: 1px solid #E9EDF7; color: var(--text-dark); padding: 8px 15px; border-radius: 12px; cursor: pointer; font-weight: 600; display: flex; align-items: center; gap: 5px; transition:0.2s; }
        .btn-reset:hover { background: #F4F7FE; }
        
        .stage-group { margin-bottom: 40px; animation: slideIn 0.3s ease; }
        @keyframes slideIn { from{opacity:0; transform:translateY(10px);} to{opacity:1; transform:translateY(0);} }
        
        .stage-header { font-family: 'Outfit'; font-weight: 700; font-size: 1.4rem; color: var(--text-dark); margin-bottom: 20px; display: flex; align-items: center; gap: 15px; }
        .count-bubble { background: #E9EDF7; color: var(--text-grey); padding: 2px 10px; border-radius: 10px; font-size: 0.9rem; }
        
        .kanban-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 25px; }
        
        .lote-card { background: var(--white); border-radius: 16px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.02); border: 1px solid rgba(0,0,0,0.02); transition: all 0.3s; display: flex; flex-direction: column; position: relative; }
        .lote-card:hover { transform: translateY(-5px); box-shadow: 0 15px 35px rgba(0,0,0,0.08); }
        
        .status-indicator { width: 6px; position: absolute; left: 0; top: 0; bottom: 0; }
        .lc-content { padding: 20px; flex: 1; margin-left: 6px; }
        .lc-top { display: flex; justify-content: space-between; margin-bottom: 10px; }
        .lc-id { font-size: 0.8rem; font-weight: 700; color: var(--text-grey); }
        .lc-status { font-size: 0.75rem; font-weight: 800; padding: 4px 10px; border-radius: 8px; text-transform: uppercase; }
        
        .lc-title { font-family: 'Outfit'; font-weight: 700; font-size: 1.2rem; color: var(--text-dark); margin: 0 0 5px 0; }
        .lc-meta { display: flex; justify-content: space-between; align-items: flex-end; margin-top: 15px; }
        .lc-detail { font-size: 0.9rem; color: var(--text-grey); font-weight: 500; }
        .lc-qty { font-size: 1.5rem; font-weight: 800; color: var(--text-dark); }
        
        .lc-action-bar { padding: 15px 20px; background: #F8F9FC; border-top: 1px solid #F4F7FE; margin-left: 6px; display: flex; justify-content: space-between; align-items: center; }
        .btn-card-action { border: none; padding: 8px 16px; border-radius: 10px; font-weight: 600; font-size: 0.85rem; cursor: pointer; display: flex; align-items: center; gap: 6px; transition: 0.2s; }

        /* === MODO LISTA PERFECCIONADO === */
        .kanban-grid.list-view { display: flex; flex-direction: column; gap: 10px; }
        .kanban-grid.list-view .lote-card { 
            display: grid; 
            /* Grid estricto: Indicador | ID | Titulo+Detalles | Cantidad | Botones */
            grid-template-columns: 6px 90px 1fr 80px 200px; 
            align-items: center; height: auto; min-height: 70px; padding: 0;
        }
        
        .kanban-grid.list-view .status-indicator { position: static; height: 100%; width: 100%; }
        
        /* Contenedores internos para la lista */
        .kanban-grid.list-view .lc-content { display: contents; } 
        
        .kanban-grid.list-view .lc-top { 
            flex-direction: column; justify-content: center; gap: 5px; margin: 0; padding: 0 15px; 
        }
        
        .kanban-grid.list-view .info-block {
            display: flex; flex-direction: column; justify-content: center; padding: 0 15px;
        }
        
        .kanban-grid.list-view .lc-title { font-size: 1rem; margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .kanban-grid.list-view .lc-detail { font-size: 0.85rem; display: block; } /* Mostrar detalle en lista */
        
        .kanban-grid.list-view .lc-meta { 
            margin: 0; padding: 0; justify-content: center; align-items: center;
        }
        .kanban-grid.list-view .lc-qty { font-size: 1.2rem; }
        
        .kanban-grid.list-view .lc-action-bar { 
            background: transparent; border: none; padding: 0 20px; margin: 0; 
            justify-content: flex-end; gap: 10px;
        }

        /* Ocultar elementos sobrantes en lista */
        .kanban-grid.list-view .lc-action-bar .material-icons-round { display: none; } /* Ocultar icono usuario en lista para ahorrar espacio */

        .modal-overlay { position: fixed; top:0; left:0; width:100%; height:100%; background:rgba(11,20,55,0.6); z-index:9999; display:none; justify-content:center; align-items:center; backdrop-filter: blur(5px); }
        .modal-box { background: white; padding: 30px; border-radius: 20px; width: 90%; max-width: 450px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); animation: zoomIn 0.3s; }
        @keyframes zoomIn { from{transform:scale(0.9); opacity:0;} to{transform:scale(1); opacity:1;} }
        .form-input { width: 100%; padding: 14px; border: 1px solid #E9EDF7; border-radius: 12px; margin-top: 8px; box-sizing: border-box; font-family: 'Quicksand'; font-size:1rem; outline:none; transition:0.2s; }
        .form-input:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(20,76,60,0.1); }
    </style>
</head>
<body>

    <?php include("php/encabezado_madera.php"); ?>
    <?php include("php/barra_navegacion.php"); ?>

    <div class="dashboard-wrapper">
        
        <aside class="sidebar-monitor">
            <a href="adm_pedidos.php" class="btn-premium-orders">
                <span class="material-icons-round">receipt_long</span> GESTIONAR PEDIDOS
            </a>

            <div id="monitor-container" style="display:flex; flex-direction:column; gap:20px;">
                <?php foreach($areasDef as $id => $info) {
                    $d = $monitorData[$id];
                    $total = $d['total'];
                    $pProc = $total > 0 ? ($d['proceso']/$total)*100 : 0;
                    $pRev = $total > 0 ? ($d['revision']/$total)*100 : 0;
                    
                    $alertHtml = "";
                    if($d['proceso'] == 0 && !empty($d['workers'])) $alertHtml = "<div class='alert-pill ap-gray'><span class='material-icons-round' style='font-size:16px'>bedtime</span> ÁREA EN DESCANSO</div>";
                    elseif ($d['cola'] == 0) $alertHtml = "<div class='alert-pill ap-red'><span class='material-icons-round' style='font-size:16px'>warning</span> SIN TRABAJO (COLA VACÍA)</div>";
                    elseif ($d['cola'] < 5) $alertHtml = "<div class='alert-pill ap-orange'><span class='material-icons-round' style='font-size:16px'>hourglass_empty</span> POCO TRABAJO (${d['cola']})</div>";

                    $wHtml = ""; foreach($d['workers'] as $w=>$q) $wHtml .= "<div class='wp-row'><span>$w</span><b>$q</b></div>";
                ?>
                <div class="widget-card" onclick="filtrarArea(<?php echo $id; ?>, this)">
                    <div class="wc-header">
                        <div style="display:flex; align-items:center;">
                            <div class="wc-icon" style="background:<?php echo $info['c']; ?>"><span class="material-icons-round"><?php echo $info['i']; ?></span></div>
                            <div class="wc-title"><?php echo $info['n']; ?></div>
                        </div>
                        <div class="wc-total"><?php echo $total; ?></div>
                    </div>
                    
                    <div class="progress-container">
                        <div class="bar-segment" style="width:<?php echo $pProc; ?>%; background:<?php echo $info['c']; ?>"></div>
                        <div class="bar-segment" style="width:<?php echo $pRev; ?>%; background:var(--alert-orange);"></div>
                    </div>

                    <div class="wc-stats" style="margin-top:10px;">
                        <div class="stat-item"><div class="stat-lbl">Cola</div><div class="stat-val"><?php echo $d['cola']; ?></div></div>
                        <div class="stat-item"><div class="stat-lbl">Proc</div><div class="stat-val" style="color:<?php echo $info['c']; ?>"><?php echo $d['proceso']; ?></div></div>
                        <div class="stat-item"><div class="stat-lbl">Rev</div><div class="stat-val" style="color:var(--alert-orange)"><?php echo $d['revision']; ?></div></div>
                    </div>
                    <?php echo $alertHtml; ?>
                    <div class="worker-panel"><?php echo $wHtml ? $wHtml : '<small style="color:#a3aed0">Sin personal activo</small>'; ?></div>
                </div>
                <?php } ?>

                <div class="widget-card" onclick="filtrarArea(7, this)" style="border-left: 5px solid var(--text-dark);">
                    <div class="wc-header">
                        <div style="display:flex; align-items:center;"><div class="wc-icon" style="background:var(--text-dark)"><span class="material-icons-round">inventory_2</span></div><div class="wc-title">ALMACÉN</div></div>
                        <div class="wc-total"><?php echo $stockTotal; ?></div>
                    </div>
                    <div style="text-align:center; color:var(--text-grey); font-size:0.8rem; font-weight:600; margin-top:5px;">PRODUCTOS TERMINADOS</div>
                </div>
            </div>
        </aside>

        <main class="main-content">
            
            <div class="top-toolbar">
                <div style="display:flex; gap:10px; align-items:center;">
                    <div style="font-family:'Outfit'; font-weight:700; font-size:1.5rem; color:var(--text-dark);">Tablero</div>
                    <button class="btn-reset" onclick="resetAll()"><span class="material-icons-round" style="font-size:16px">restart_alt</span> Ver Todo</button>
                </div>
                
                <div class="search-box">
                    <span class="material-icons-round" style="color:var(--text-grey); font-size:20px;">search</span>
                    <input type="text" id="searchInput" class="search-inp" placeholder="Buscar modelo..." onkeyup="filtrarPorBusqueda()">
                </div>

                <div class="filter-capsule">
                    <button class="btn-pill active" id="f-todo" onclick="filtrarEstado('todo')">Todos</button>
                    <button class="btn-pill" id="f-asignar" onclick="filtrarEstado('asignar')">Por Asignar</button>
                    <button class="btn-pill" id="f-revision" onclick="filtrarEstado('revision')">Revisión</button>
                    <button class="btn-pill" id="f-terminar" onclick="filtrarEstado('terminar')">Terminar</button>
                </div>

                <div style="display:flex; gap:5px;">
                    <button onclick="setVista('grid')" class="btn-pill" title="Grid"><span class="material-icons-round">grid_view</span></button>
                    <button onclick="setVista('list')" class="btn-pill" title="Lista"><span class="material-icons-round">view_list</span></button>
                </div>
            </div>

            <div id="board-grid">
                <?php foreach($areasDef as $id => $info) { $lotes = $listaMuebles[$id]; ?>
                <div class="stage-group" id="sec-<?php echo $id; ?>" style="<?php echo empty($lotes)?'display:none':''; ?>">
                    <div class="stage-header">
                        <?php echo $info['n']; ?> <span class="count-bubble"><?php echo count($lotes); ?></span>
                    </div>

                    <div class="kanban-grid" id="grid-<?php echo $id; ?>">
                        <?php foreach($lotes as $row) { 
                            $sub = $row['sub_estatus']; $asig = $row['asignado_a']; $nota = $row['mue_comentario'];
                            $barColor="#E9EDF7"; $stTxt="EN COLA"; $badgeBg="#F4F7FE"; $badgeCol="#A3AED0";
                            if($sub=='proceso') { $barColor=$info['c']; $stTxt="EN PROCESO"; $badgeBg=$info['c']; $badgeCol="white"; }
                            if($sub=='revision') { $barColor="var(--alert-orange)"; $stTxt="REVISIÓN"; $badgeBg="var(--alert-orange)"; $badgeCol="white"; }
                        ?>
                        <div class="lote-card" data-status="<?php echo $sub; ?>" data-asig="<?php echo $asig?'si':'no'; ?>" data-name="<?php echo strtolower($row['modelos_nombre']); ?>">
                            
                            <div class="status-indicator" style="background:<?php echo $barColor; ?>"></div>
                            
                            <div class="lc-content">
                                <div class="lc-top">
                                    <span class="lc-id">#<?php echo $row['id_muebles']; ?></span>
                                    <span class="lc-status" style="background:<?php echo $badgeBg; ?>; color:<?php echo $badgeCol; ?>"><?php echo $stTxt; ?></span>
                                </div>
                                
                                <div class="info-block">
                                    <h3 class="lc-title"><?php echo $row['modelos_nombre']; ?></h3>
                                    <?php if($row['id_pedido']){ ?>
                                        <div style="font-size:0.75rem; background:#EBF5FF; color:#0052CC; padding:3px 8px; border-radius:6px; display:inline-block; font-weight:700;">PEDIDO #<?php echo $row['id_pedido']; ?></div>
                                    <?php } else { ?>
                                        <div style="font-size:0.75rem; background:#F4F4F4; color:#666; padding:3px 8px; border-radius:6px; display:inline-block; font-weight:700;">STOCK LIBRE</div>
                                    <?php } ?>
                                    <?php if($nota){ ?><span class="material-icons-round" onclick="verNota('<?php echo htmlspecialchars($nota); ?>')" style="font-size:18px; color:var(--alert-orange); cursor:pointer; vertical-align:middle; margin-left:5px;">sticky_note_2</span><?php } ?>
                                    <div class="lc-detail"><?php echo $row['mue_color']; ?></div>
                                </div>

                                <div class="lc-meta">
                                    <div class="lc-qty"><?php echo $row['mue_cantidad']; ?></div>
                                </div>
                            </div>

                            <div class="lc-action-bar">
                                <div style="display:flex; align-items:center; gap:8px; font-size:0.85rem; font-weight:600; color:var(--text-grey);">
                                    <span class="material-icons-round" style="font-size:18px">account_circle</span>
                                    <?php echo $asig ? $asig : '<span style="color:var(--alert-red)">Sin Asignar</span>'; ?>
                                </div>

                                <div>
                                    <?php if(!$asig) { ?>
                                        <button class="btn-card-action" style="background:#F4F7FE; color:var(--text-dark);" onclick="saveScroll(); modalAsignar(<?php echo $row['id_muebles']; ?>, '<?php echo $row['modelos_nombre']; ?>', <?php echo $row['mue_cantidad']; ?>, '<?php echo $kw[$id]; ?>')">Asignar</button>
                                    <?php } else { 
                                        if($sub=='cola'){ ?>
                                            <button class="btn-card-action" style="background:var(--primary); color:white;" onclick="saveScroll(); go(<?php echo $row['id_muebles']; ?>,'proceso')"><span class="material-icons-round" style="font-size:14px">play_arrow</span> Iniciar</button>
                                        <?php } elseif($sub=='proceso'){ ?>
                                            <button class="btn-card-action" style="background:var(--text-dark); color:white;" onclick="saveScroll(); modalTerminar(<?php echo $row['id_muebles']; ?>, <?php echo $row['mue_cantidad']; ?>)"><span class="material-icons-round" style="font-size:14px">check</span> Terminar</button>
                                        <?php } elseif($sub=='revision'){ ?>
                                            <a href="adm_validar_movimiento.php?id=<?php echo $row['id_muebles']; ?>" class="btn-card-action" style="background:var(--alert-orange); color:white; text-decoration:none;" onclick="saveScroll()">Validar</a>
                                        <?php }
                                    } ?>
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                </div>
                <?php } ?>

                <div class="stage-group" id="sec-7" style="<?php echo ($resTerm && mysqli_num_rows($resTerm)>0)?'':'display:none'; ?>">
                    <div class="stage-header" style="color:var(--primary)">ALMACÉN TERMINADO</div>
                    <div class="kanban-grid">
                        <?php if($resTerm){ mysqli_data_seek($resTerm,0); while($row=mysqli_fetch_assoc($resTerm)){ ?>
                        <div class="lote-card">
                            <div class="status-indicator" style="background:var(--primary)"></div>
                            <div class="lc-content" style="text-align:center; display:block;">
                                <div class="lc-qty" style="font-size:2.5rem; color:var(--primary);"><?php echo $row['total_stock']; ?></div>
                                <h3 class="lc-title" style="margin-top:10px;"><?php echo $row['modelos_nombre']; ?></h3>
                                <div class="lc-detail"><?php echo $row['mue_color']; ?></div>
                            </div>
                            <div class="lc-action-bar" style="justify-content:center;">
                                <button class="btn-card-action" style="background:var(--text-dark); color:white;" onclick="saveScroll(); modalSalida('<?php echo $row['id_modelos']; ?>', '<?php echo $row['total_stock']; ?>')">REGISTRAR SALIDA</button>
                            </div>
                        </div>
                        <?php }} ?>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <div id="mAsignar" class="modal-overlay"><div class="modal-box"><h3 style="margin-top:0;">Asignar</h3><p id="lblM"></p><div id="areaAlert" style="display:none;background:#FFF4E5;color:#B45309;padding:10px;border-radius:10px;margin-bottom:15px;">⚠️ Otra área</div><form action="php/asignar_rapido.php" method="POST"><input type="hidden" name="id_mueble" id="inpId"><input type="number" name="cantidad_asignar" id="inpCant" class="form-input" required><select name="persona_asignada" id="selEmp" class="form-input" onchange="checkArea(this)" required></select><div style="display:flex;gap:10px;margin-top:20px;"><button type="button" onclick="closeM('mAsignar')" class="btn-card-action" style="background:#eee;flex:1;justify-content:center;">Cancelar</button><button type="submit" class="btn-card-action" style="background:var(--primary);color:white;flex:1;justify-content:center;">Guardar</button></div></form></div></div>
    <div id="mTerminar" class="modal-overlay"><div class="modal-box"><h3 style="margin-top:0;">Terminar</h3><input type="number" id="inpT" class="form-input" style="font-size:2rem;text-align:center;"><div style="display:flex;gap:10px;margin-top:20px;"><button type="button" onclick="closeM('mTerminar')" class="btn-card-action" style="background:#eee;flex:1;justify-content:center;">Cancelar</button><button onclick="sendT()" class="btn-card-action" style="background:var(--primary);color:white;flex:1;justify-content:center;">Enviar</button></div></div></div>
    <div id="mNota" class="modal-overlay"><div class="modal-box" style="background:#FFF8E6;"><h3 style="color:#B45309;margin-top:0;">Nota</h3><p id="txtNota"></p><button onclick="closeM('mNota')" class="btn-card-action" style="background:#FFB547;color:white;width:100%;justify-content:center;">Cerrar</button></div></div>
    <div id="mSalida" class="modal-overlay"><div class="modal-box"><h3 style="margin-top:0;">Salida</h3><form action="php/salida_almacen.php" method="POST"><input type="hidden" name="id_modelos" id="inpSalidaId"><input type="number" name="cantidad_salida" id="inpSalidaCant" class="form-input" style="font-size:2rem;text-align:center;" required><div style="display:flex;gap:10px;margin-top:20px;"><button type="button" onclick="closeM('mSalida')" class="btn-card-action" style="background:#eee;flex:1;justify-content:center;">Cancelar</button><button type="submit" class="btn-card-action" style="background:var(--primary);color:white;flex:1;justify-content:center;">Confirmar</button></div></form></div></div>

    <script>
        const empsByArea = <?php echo $jsonEmpleados; ?>;
        let currentArea = 'all'; let currentState = 'todo'; let searchText = '';

        // SCROLL KEEPER
        function saveScroll() { localStorage.setItem('scrollPos', window.scrollY); }
        document.addEventListener("DOMContentLoaded", function() {
            let scrollPos = localStorage.getItem('scrollPos');
            if (scrollPos) window.scrollTo(0, scrollPos);
            localStorage.removeItem('scrollPos');
        });

        // UI Logic
        function filtrarPorBusqueda() { searchText = document.getElementById('searchInput').value.toLowerCase(); aplicarFiltros(); }
        function filtrarArea(id, card) { document.querySelectorAll('.widget-card').forEach(c => c.classList.remove('active')); if(card) card.classList.add('active'); currentArea = id; aplicarFiltros(); }
        function filtrarEstado(estado) { document.querySelectorAll('.btn-pill').forEach(b => b.classList.remove('active', 'active-rev')); let btn = document.getElementById('f-'+estado); if(estado === 'revision') btn.classList.add('active-rev'); else btn.classList.add('active'); currentState = estado; aplicarFiltros(); }
        function resetAll() { currentArea = 'all'; currentState = 'todo'; searchText = ''; document.getElementById('searchInput').value = ''; document.querySelectorAll('.widget-card').forEach(c => c.classList.remove('active')); document.querySelectorAll('.btn-pill').forEach(b => b.classList.remove('active', 'active-rev')); document.getElementById('f-todo').classList.add('active'); aplicarFiltros(); }

        function aplicarFiltros() {
            document.querySelectorAll('.stage-group').forEach(sec => {
                let secId = parseInt(sec.id.split('-')[1]);
                let showSec = (currentArea === 'all' || currentArea === secId);
                if(showSec) {
                    sec.style.display = 'block';
                    let visible = 0;
                    sec.querySelectorAll('.lote-card').forEach(card => {
                        let show = true;
                        if(currentState === 'asignar' && card.dataset.asig === 'si') show = false;
                        if(currentState === 'revision' && card.dataset.status !== 'revision') show = false;
                        if(currentState === 'terminar' && card.dataset.status !== 'proceso') show = false;
                        if(searchText !== '' && !card.dataset.name.includes(searchText)) show = false;
                        card.style.display = show ? (document.querySelector('.kanban-grid.list-view') ? 'grid' : 'flex') : 'none';
                        if(show) visible++;
                    });
                    if(visible === 0) sec.style.display = 'none';
                } else { sec.style.display = 'none'; }
            });
        }

        function setVista(v) { 
            const grids = document.querySelectorAll('.kanban-grid'); 
            if(v === 'list') grids.forEach(g => g.classList.add('list-view')); 
            else grids.forEach(g => g.classList.remove('list-view')); 
            aplicarFiltros(); 
        }

        let curId = 0;
        function modalAsignar(id, nom, max, areaKey) {
            curId = id; document.getElementById('inpId').value = id; document.getElementById('inpCant').value = max; document.getElementById('lblM').innerText = nom; document.getElementById('areaAlert').style.display = 'none';
            let s = document.getElementById('selEmp'); s.innerHTML = '<option value="" data-match="none">-- Seleccionar --</option>';
            let grpRec = document.createElement('optgroup'); grpRec.label = "⭐ RECOMENDADOS (" + areaKey.toUpperCase() + ")";
            if(empsByArea[areaKey]) { empsByArea[areaKey].forEach(name => { let op = new Option(name, name); op.setAttribute('data-match', 'true'); grpRec.appendChild(op); }); }
            s.appendChild(grpRec);
            let grpOth = document.createElement('optgroup'); grpOth.label = "OTRAS ÁREAS (Cuidado)";
            for (const [key, list] of Object.entries(empsByArea)) { if(key !== areaKey) { list.forEach(item => { let val = (typeof item === 'string') ? item : item.n + " (" + item.p + ")"; let op = new Option(val, (typeof item === 'string') ? item : item.n); op.setAttribute('data-match', 'false'); grpOth.appendChild(op); }); } }
            s.appendChild(grpOth);
            document.getElementById('mAsignar').style.display='flex';
        }

        function checkArea(sel) { let op = sel.options[sel.selectedIndex]; let isMatch = op.getAttribute('data-match'); let alertBox = document.getElementById('areaAlert'); if(isMatch === 'false') { alertBox.style.display = 'flex'; } else { alertBox.style.display = 'none'; } }
        function modalTerminar(id, max) { curId=id; document.getElementById('inpT').value=max; document.getElementById('mTerminar').style.display='flex'; }
        function modalSalida(idModelo, maxStock) { document.getElementById('inpSalidaId').value = idModelo; document.getElementById('inpSalidaCant').max = maxStock; document.getElementById('inpSalidaCant').value = ""; document.getElementById('mSalida').style.display = 'flex'; }
        function verNota(txt) { document.getElementById('txtNota').innerText = txt; document.getElementById('mNota').style.display='flex'; }
        function closeM(id) { document.getElementById(id).style.display='none'; }
        function sendT() { window.location.href=`php/cambiar_sub_estatus.php?id=${curId}&estado=revision&cantidad=`+document.getElementById('inpT').value; }
        function go(id,st) { window.location.href=`php/cambiar_sub_estatus.php?id=${id}&estado=${st}`; }
    </script>
</body>
</html>
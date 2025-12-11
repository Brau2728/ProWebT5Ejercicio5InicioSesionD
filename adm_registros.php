<?php
session_start();
if (!isset($_SESSION['usuario']) || !isset($_SESSION['tipo'])) { header('Location: login.php'); exit(); }
include("php/conexion.php");

// ==========================================
// 1. LÓGICA DE DATOS (Exactamente igual)
// ==========================================
$sqlOp = "SELECT id_estatus_mueble, asignado_a, sub_estatus, SUM(mue_cantidad) as c 
          FROM muebles 
          WHERE asignado_a IS NOT NULL AND asignado_a != '' 
          AND id_estatus_mueble >= 2 AND id_estatus_mueble < 7 
          GROUP BY id_estatus_mueble, asignado_a, sub_estatus";
$resOp = db_query($sqlOp);

$dataReal = [];
for($i=2; $i<=6; $i++) $dataReal[$i] = [];
if($resOp) { while($r = mysqli_fetch_assoc($resOp)) { $dataReal[$r['id_estatus_mueble']][$r['asignado_a']][$r['sub_estatus']] = (int)$r['c']; }}

$sqlSin = "SELECT id_estatus_mueble, sub_estatus, SUM(mue_cantidad) as c 
           FROM muebles 
           WHERE (asignado_a IS NULL OR asignado_a = '') AND id_estatus_mueble >= 2 AND id_estatus_mueble < 7
           GROUP BY id_estatus_mueble, sub_estatus";
$resSin = db_query($sqlSin);

$sinAsignar = [];
for($i=2; $i<=6; $i++) $sinAsignar[$i] = ['cola'=>0, 'proceso'=>0, 'revision'=>0];
if($resSin) { while($r = mysqli_fetch_assoc($resSin)) { $sinAsignar[$r['id_estatus_mueble']][$r['sub_estatus']] = (int)$r['c']; }}

$rowStock = mysqli_fetch_assoc(db_query("SELECT SUM(mue_cantidad) as total FROM muebles WHERE id_estatus_mueble = 7"));
$totalAlmacen = $rowStock['total'] ? $rowStock['total'] : 0;
$jsonData = json_encode(['operarios' => $dataReal, 'generales' => $sinAsignar, 'stockTotal' => $totalAlmacen]);

$resEmp = db_query("SELECT usu_nom, usu_ap_pat, usu_puesto FROM usuarios WHERE usu_puesto IS NOT NULL");
$listaEmpleadosJS = [];
while($e = mysqli_fetch_assoc($resEmp)) {
    $puesto = strtolower($e['usu_puesto']); $nombre = $e['usu_nom'].' '.$e['usu_ap_pat'];
    if(strpos($puesto,'barniz')!==false) $listaEmpleadosJS[4][] = $nombre;
    if(strpos($puesto,'pint')!==false) $listaEmpleadosJS[5][] = $nombre;
    if(strpos($puesto,'adorn')!==false) $listaEmpleadosJS[6][] = $nombre;
    if(strpos($puesto,'armad')!==false) $listaEmpleadosJS[3][] = $nombre;
}
$jsonEmpleados = json_encode($listaEmpleadosJS);

$resActivos = db_query("SELECT m.*, mo.modelos_nombre, mo.modelos_imagen FROM muebles m INNER JOIN modelos mo ON m.id_modelos = mo.id_modelos WHERE m.id_estatus_mueble >= 2 AND m.id_estatus_mueble < 7 ORDER BY m.id_estatus_mueble ASC, m.sub_estatus ASC");
$mueblesPorEtapa = [];
for($i=2; $i<=6; $i++) $mueblesPorEtapa[$i] = [];
if($resActivos) { while($row = mysqli_fetch_assoc($resActivos)) { $mueblesPorEtapa[$row['id_estatus_mueble']][] = $row; }}

$resTerminados = db_query("SELECT m.id_modelos, mo.modelos_nombre, mo.modelos_imagen, m.mue_color, m.mue_herraje, SUM(m.mue_cantidad) as total_stock FROM muebles m INNER JOIN modelos mo ON m.id_modelos = mo.id_modelos WHERE m.id_estatus_mueble = 7 GROUP BY m.id_modelos, m.mue_color, m.mue_herraje");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Monitor de Planta</title>
    <link rel="stylesheet" href="estilos/Wave2.css">
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    
    <style>
        /* === ARREGLO DE LAYOUT (TÉCNICA BREAKOUT) === */
        /* Esto fuerza al contenedor a ocupar el ancho de la ventana, ignorando al padre */
        .breakout-wrapper {
            width: 99vw; /* Casi todo el ancho del viewport */
            position: relative;
            left: 50%;
            right: 50%;
            margin-left: -49.5vw;
            margin-right: -49.5vw;
            
            /* Altura calculada: Alto pantalla menos cabecera aprox */
            height: 85vh; 
            background: #F0F2F5;
            display: flex; /* Esto activa la doble columna */
            overflow: hidden; /* Evita doble scroll */
            border-top: 1px solid #ddd;
            font-family: 'Quicksand', sans-serif;
            box-sizing: border-box;
        }

        /* === PANEL IZQUIERDO (MONITOR) === */
        .panel-monitor {
            width: 320px; 
            min-width: 320px;
            background: #fff;
            border-right: 1px solid #dcdcdc;
            display: flex; 
            flex-direction: column;
            overflow-y: auto; 
            z-index: 10;
        }
        
        .monitor-header { padding: 15px; background: #fff; border-bottom: 1px solid #eee; position: sticky; top: 0; z-index: 5; text-align: center; }
        .monitor-cards-container { padding: 15px; display: flex; flex-direction: column; gap: 15px; }

        /* Estilos Monitor */
        .monitor-card { background: white; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); border: 1px solid #ddd; overflow: hidden; cursor: pointer; transition: 0.2s; }
        .monitor-card:hover { transform: translateY(-2px); box-shadow: 0 6px 15px rgba(0,0,0,0.12); }
        .monitor-card.active { border: 3px solid #144c3c; background: #f4f8f6; }
        .card-header-col { padding: 8px; color: white; font-weight: 800; text-align: center; text-transform: uppercase; font-size: 0.85rem; }
        .metrics-row { display: flex; justify-content: space-around; padding: 8px 5px; background: #fff; text-align: center; }
        .m-val { font-size: 1.2rem; font-weight: 800; display: block; line-height: 1; }
        .m-lbl { font-size: 0.6rem; font-weight: bold; color: #888; text-transform: uppercase; }
        .worker-item { display: flex; justify-content: space-between; font-size: 0.8rem; color: #333; padding: 4px 8px; border-bottom: 1px solid #eee; cursor:pointer; }
        .worker-item:hover { background-color:#f0f0f0; }

        /* === PANEL DERECHO (DETALLE) === */
        .panel-detail { flex: 1; padding: 20px 30px; overflow-y: auto; background: #F0F2F5; position: relative; }

        /* CONTENEDOR GRID vs LISTA */
        .cards-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
        }

        /* === MODO LISTA (ESTILOS NUEVOS) === */
        .cards-container.mode-list {
            display: flex; flex-direction: column; gap: 10px;
        }
        
        /* Transformación de tarjeta en modo lista */
        .cards-container.mode-list .mueble-card {
            flex-direction: row; 
            align-items: center; 
            min-height: auto;
        }
        
        .cards-container.mode-list .card-top {
            display: none; /* Ocultamos la barra superior en modo lista */
        }
        
        .cards-container.mode-list .card-content {
            flex-direction: row;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            padding: 10px 15px;
        }

        /* Ajustes de elementos internos para que se vean bien en fila */
        .cards-container.mode-list .info-block { display: flex; align-items: center; gap: 20px; flex: 2; }
        .cards-container.mode-list .status-block { flex: 1; text-align: center; }
        .cards-container.mode-list .action-block { flex: 1; text-align: right; }
        .cards-container.mode-list .title-mueble { font-size: 1rem; margin-bottom: 0; }
        .cards-container.mode-list .sub-info { margin: 0; font-size: 0.8rem; }

        /* === TARJETA MUEBLE BASE === */
        .mueble-card {
            background: white; border-radius: 10px; overflow: hidden;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05); border: 1px solid #e0e0e0;
            transition: all 0.3s ease; display: flex; flex-direction: column;
            border-left: 5px solid transparent; /* Preparado para borde de color */
        }
        .card-top { padding: 8px 12px; color: white; font-weight: bold; font-size: 0.8rem; display: flex; justify-content: space-between; align-items: center; }
        .card-content { padding: 15px; flex: 1; display: flex; flex-direction: column; }
        .title-mueble { margin: 0; color: #333; font-size: 1.1rem; font-weight: 700; }
        .sub-info { color: #666; font-size: 0.85rem; margin: 5px 0 10px 0; }
        
        /* BOTONES DE VISTA */
        .view-toggles { position: absolute; top: 15px; right: 30px; display: flex; gap: 5px; z-index: 50; }
        .btn-view { background: white; border: 1px solid #ccc; padding: 6px 12px; border-radius: 20px; cursor: pointer; display: flex; align-items: center; gap: 5px; font-weight: bold; font-size: 0.8rem; color: #555; }
        .btn-view.active { background: #144c3c; color: white; border-color: #144c3c; }

        /* OTROS BOTONES */
        .btn-action { width: 100%; padding: 10px; border: none; border-radius: 5px; font-weight: bold; color: white; cursor: pointer; margin-top: auto; text-transform: uppercase; font-size: 0.8rem; }
        .btn-asignar { background: #748579; } 
        .btn-iniciar { background: #144c3c; } 
        .btn-terminar { background: #5d6b62; } 
        .btn-validar { background: #94745c; } 
        
        .assign-badge { background: #f5f5f5; padding: 4px 10px; border-radius: 5px; font-size: 0.8rem; font-weight: bold; color: #555; display: inline-block; margin-bottom: 5px;}

        /* SECCIONES */
        .stage-block { margin-bottom: 30px; animation: fadeIn 0.3s ease; }
        .section-header-flex { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; border-bottom: 2px solid #ddd; padding-bottom: 5px; }

        @keyframes fadeIn { from { opacity:0; transform:translateY(10px); } to { opacity:1; transform:translateY(0); } }

        /* MODALES */
        .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 2000; display: none; justify-content: center; align-items: center; backdrop-filter: blur(2px); }
        .modal-box { background: white; width: 90%; max-width: 400px; padding: 25px; border-radius: 10px; box-shadow: 0 15px 40px rgba(0,0,0,0.2); text-align: center; }
        .inp-qty { font-size: 2rem; width: 100px; text-align: center; border: 2px solid #ddd; border-radius: 8px; margin: 15px auto; display: block; padding: 5px; color: #333; }
        .sel-w { width: 100%; padding: 10px; margin-top: 5px; border: 1px solid #ddd; border-radius: 5px; }

    </style>
</head>
<body>

    <?php include("php/encabezado_madera.php"); ?>
    <?php include("php/barra_navegacion.php"); ?>

    <div class="breakout-wrapper">
        
        <aside class="panel-monitor">
            <div class="monitor-header">
                <h3 style="margin:0; color:#144c3c;">Monitor Planta</h3>
                <div style="margin-top:10px; display:flex; gap:5px; justify-content:center;">
                    <button onclick="resetFiltros()" class="btn-view">Ver Todo</button>
                    <a href="adm_registros_registrar.php" class="btn-view" style="background:#144c3c; color:white; text-decoration:none;">+ Nuevo</a>
                </div>
            </div>
            <div class="monitor-cards-container" id="monitor-container"></div>
        </aside>

        <main class="panel-detail">
            
            <div class="view-toggles">
                <button class="btn-view active" id="btn-grid" onclick="setVista('grid')">
                    <span class="material-icons" style="font-size:16px">grid_view</span> Cartas
                </button>
                <button class="btn-view" id="btn-list" onclick="setVista('list')">
                    <span class="material-icons" style="font-size:16px">view_list</span> Lista
                </button>
            </div>

            <?php 
            $etapas = [
                2 => ['nom'=>'MAQUILA', 'col'=>'#5d6b62'],
                3 => ['nom'=>'ARMADO', 'col'=>'#94745c'],
                4 => ['nom'=>'BARNIZADO', 'col'=>'#748579'],
                5 => ['nom'=>'PINTADO', 'col'=>'#144c3c'],
                6 => ['nom'=>'ADORNADO', 'col'=>'#5d6b62']
            ];

            foreach($etapas as $idEtapa => $info) {
                $lista = isset($mueblesPorEtapa[$idEtapa]) ? $mueblesPorEtapa[$idEtapa] : [];
            ?>
            <div class="stage-block" id="block-<?php echo $idEtapa; ?>" data-etapa="<?php echo $idEtapa; ?>" style="<?php echo empty($lista)?'display:none;':''; ?>">
                
                <div class="section-header-flex" style="border-color: <?php echo $info['col']; ?>;">
                    <span style="font-size:1.4rem; font-weight:800; color:<?php echo $info['col']; ?>;"><?php echo $info['nom']; ?></span>
                    <span style="color:#999; font-size:0.8rem;">(<?php echo count($lista); ?> Lotes)</span>
                </div>

                <div class="cards-container" id="container-<?php echo $idEtapa; ?>">
                    <?php foreach($lista as $row) { 
                        $id = $row['id_muebles']; $sub = $row['sub_estatus']; $asig = $row['asignado_a'];
                        
                        $bgHead = '#748579'; // Cola
                        if($sub == 'proceso') $bgHead = '#144c3c';
                        if($sub == 'revision') $bgHead = '#94745c';
                    ?>
                    <div class="mueble-card" 
                         data-etapa="<?php echo $idEtapa; ?>" 
                         data-persona="<?php echo $asig ?: 'sin_asignar'; ?>" 
                         data-sub="<?php echo $sub; ?>"
                         style="border-left-color: <?php echo $bgHead; ?>;"> <div class="card-top" style="background-color: <?php echo $bgHead; ?>;">
                            <span><?php echo strtoupper($sub); ?></span>
                            <span onclick="eliminarLote(<?php echo $id; ?>)" style="cursor:pointer;">✕</span>
                        </div>
                        
                        <div class="card-content">
                            <div class="info-block">
                                <span style="font-size:1.5rem; font-weight:800; color:<?php echo $info['col']; ?>; margin-right:10px;"><?php echo $row['mue_cantidad']; ?></span>
                                <div>
                                    <h4 class="title-mueble"><?php echo $row['modelos_nombre']; ?></h4>
                                    <p class="sub-info"><?php echo $row['mue_color']; ?> • <?php echo $row['mue_herraje']; ?></p>
                                </div>
                            </div>

                            <div class="status-block">
                                <div class="assign-badge">
                                    <?php echo $asig ? '👤 '.$asig : '⚠️ Sin Asignar'; ?>
                                </div>
                            </div>

                            <div class="action-block">
                                <?php if(!$asig) { ?>
                                    <button class="btn-action btn-asignar" onclick="modalAsignar(<?php echo $id; ?>, <?php echo $idEtapa; ?>, '<?php echo $row['modelos_nombre']; ?>', <?php echo $row['mue_cantidad']; ?>)">ASIGNAR</button>
                                <?php } else { 
                                    if($sub=='cola') { ?> <button class="btn-action btn-iniciar" onclick="go(<?php echo $id; ?>,'proceso')">INICIAR</button> <?php }
                                    elseif($sub=='proceso') { ?> <button class="btn-action btn-terminar" onclick="modalTerminar(<?php echo $id; ?>, <?php echo $row['mue_cantidad']; ?>)">TERMINAR</button> <?php }
                                    elseif($sub=='revision') { ?> <a href="adm_validar_movimiento.php?id=<?php echo $id; ?>" class="btn-action btn-validar" style="display:block; text-align:center; text-decoration:none;">VALIDAR</a> <?php }
                                } ?>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                </div>
                <br>
            </div>
            <?php } ?>

            <div class="stage-block" id="block-7" data-etapa="7" style="<?php echo ($resTerminados && mysqli_num_rows($resTerminados)>0)?'':'display:none;'; ?>">
                <div class="section-header-flex" style="border-color:#144c3c;">
                    <span style="font-size:1.4rem; font-weight:800; color:#144c3c;">ALMACÉN TERMINADO</span>
                </div>
                <div class="cards-container">
                    <?php if($resTerminados) { mysqli_data_seek($resTerminados, 0); while($row = mysqli_fetch_assoc($resTerminados)) { ?>
                    <div class="mueble-card" style="border-left-color: #144c3c;">
                        <div class="card-top" style="background:#144c3c; justify-content:center;">DISPONIBLE</div>
                        <div class="card-content" style="text-align:center; display:block;"> 
                            <span style="font-size:2.5rem; font-weight:800; color:#144c3c;"><?php echo $row['total_stock']; ?></span>
                            <h4 class="title-mueble"><?php echo $row['modelos_nombre']; ?></h4>
                            <p class="sub-info"><?php echo $row['mue_color']; ?> • <?php echo $row['mue_herraje']; ?></p>
                            <button class="btn-action" style="background:#333;" onclick="modalSalida('<?php echo $row['id_modelos']; ?>', <?php echo $row['total_stock']; ?>)">SALIDA</button>
                        </div>
                    </div>
                    <?php }} ?>
                </div>
            </div>

            <div id="msg-empty" style="display:none; text-align:center; padding:50px; color:#999;">
                <h3>No hay registros con este filtro</h3>
            </div>
        </main>
    </div>

    <div id="mAsignar" class="modal-overlay">
        <div class="modal-box">
            <h3>Asignar Tarea</h3>
            <p id="lblMueble"></p>
            <form action="php/asignar_rapido.php" method="POST">
                <input type="hidden" name="id_mueble" id="inpId">
                <input type="number" name="cantidad_asignar" id="inpCant" class="inp-qty" required>
                <select name="persona_asignada" id="selEmp" class="sel-w"></select>
                <br><br>
                <div style="display:flex; gap:10px; justify-content:center;">
                    <button type="button" onclick="closeM('mAsignar')" class="btn-view">Cancelar</button>
                    <button type="submit" class="btn-view" style="background:#144c3c; color:white;">Guardar</button>
                </div>
            </form>
        </div>
    </div>
    
    <div id="mTerminar" class="modal-overlay">
        <div class="modal-box">
            <h3>Reportar Avance</h3>
            <input type="number" id="inpTerm" class="inp-qty">
            <br>
            <div style="display:flex; gap:10px; justify-content:center;">
                <button type="button" onclick="closeM('mTerminar')" class="btn-view">Cancelar</button>
                <button onclick="sendTerm()" class="btn-view" style="background:#144c3c; color:white;">Enviar</button>
            </div>
        </div>
    </div>

    <script>
        const data = <?php echo $jsonData; ?>;
        const emps = <?php echo $jsonEmpleados; ?>;
        let activeFilt = { t: null, v: null, s: null };
        let curId = null;

        // --- FUNCION CAMBIO DE VISTA ---
        function setVista(vista) {
            const containers = document.querySelectorAll('.cards-container');
            const btnG = document.getElementById('btn-grid');
            const btnL = document.getElementById('btn-list');

            if(vista === 'list') {
                containers.forEach(c => c.classList.add('mode-list'));
                btnL.classList.add('active'); btnG.classList.remove('active');
            } else {
                containers.forEach(c => c.classList.remove('mode-list'));
                btnG.classList.add('active'); btnL.classList.remove('active');
            }
        }

        // --- RENDER MONITOR ---
        const areas = [ {id:2, n:"MAQUILA", c:"#5d6b62"}, {id:3, n:"ARMADO", c:"#94745c"}, {id:4, n:"BARNIZADO", c:"#748579"}, {id:5, n:"PINTADO", c:"#144c3c"}, {id:6, n:"ADORNADO", c:"#5d6b62"}, {id:7, n:"ALMACÉN", c:"#144c3c"} ];
        const mc = document.getElementById('monitor-container');
        
        areas.forEach(a => {
            if(a.id === 7) {
                 mc.innerHTML += `<div class="monitor-card" id="card-${a.id}" onclick="filt('etapa',7)" style="border-top:5px solid ${a.c}"><div class="card-header-col" style="background:${a.c}">${a.n}</div><div style="text-align:center; padding:15px;"><span class="m-val" style="font-size:2rem; color:${a.c}">${data.stockTotal}</span><small style="color:#999;">DISPONIBLES</small></div></div>`;
            } else {
                let op = data.operarios[a.id] || {};
                let gen = data.generales[a.id] || {};
                let c=gen.cola||0, p=gen.proceso||0, r=gen.revision||0;
                let wList = '';
                for(let [nm, v] of Object.entries(op)) {
                    c+=v.cola||0; p+=v.proceso||0; r+=v.revision||0;
                    if((v.proceso||0)>0) wList += `<div class="worker-item" onclick="filt('per','${nm}',null,event)"><span>${nm}</span> <b>${v.proceso}</b></div>`;
                }
                if(gen.cola>0) wList += `<div class="worker-item" style="color:#94745c" onclick="filt('per','sin_asignar',null,event)"><span>⚠️ Sin Asignar</span> <b>${gen.cola}</b></div>`;
                let btnVal = r>0 ? `<button class="btn-view" style="width:100%; justify-content:center; margin-top:5px; background:#94745c; color:white; border:none;" onclick="filt('etapa',${a.id},'revision',event)">👁️ VALIDAR (${r})</button>` : '';

                mc.innerHTML += `
                    <div class="monitor-card" id="card-${a.id}" onclick="filt('etapa',${a.id})" style="border-top:5px solid ${a.c}">
                        <div class="card-header-col" style="background:${a.c}">${a.n}</div>
                        <div class="metrics-row"><div><span class="m-val" style="color:#748579">${c}</span><span class="m-lbl">COLA</span></div><div><span class="m-val" style="color:#144c3c">${p}</span><span class="m-lbl">PROC</span></div><div><span class="m-val" style="color:#94745c">${r}</span><span class="m-lbl">REV</span></div></div>
                        <div style="padding:0 10px 10px 10px">${wList}${btnVal}</div>
                    </div>`;
            }
        });

        function filt(t,v,s=null,e){ if(e)e.stopPropagation(); if(activeFilt.t===t && activeFilt.v===v && activeFilt.s===s){resetFiltros();return;} activeFilt={t,v,s}; document.querySelectorAll('.monitor-card').forEach(c=>c.classList.remove('active')); if(t==='etapa')document.getElementById(`card-${v}`).classList.add('active'); let hay=false; document.querySelectorAll('.stage-block').forEach(b=>{ let show=true; if(t==='etapa'&&b.dataset.etapa!=v)show=false; let cards=b.querySelectorAll('.mueble-card'); let vis=0; cards.forEach(c=>{ let showC=true; if(t==='per'&&c.dataset.persona!=v)showC=false; if(s&&c.dataset.sub!=s)showC=false; c.style.display=showC?'flex':'none'; if(showC)vis++; }); if(t==='per'&&vis===0)show=false; b.style.display=show?'block':'none'; if(show&&vis>0)hay=true; }); document.getElementById('msg-empty').style.display=hay?'none':'block'; }
        function resetFiltros(){ activeFilt={t:null,v:null,s:null}; document.querySelectorAll('.monitor-card').forEach(c=>c.classList.remove('active')); document.querySelectorAll('.stage-block').forEach(b=>{ if(b.querySelectorAll('.mueble-card').length>0)b.style.display='block'; b.querySelectorAll('.mueble-card').forEach(c=>c.style.display='flex'); }); document.getElementById('msg-empty').style.display='none'; }
        
        function modalAsignar(id,et,nom,max){ document.getElementById('inpId').value=id; document.getElementById('lblMueble').innerText=nom+" ("+max+" pzas)"; document.getElementById('inpCant').value=max; document.getElementById('inpCant').max=max; let s=document.getElementById('selEmp'); s.innerHTML='<option value="">...</option>'; if(emps[et])emps[et].forEach(e=>s.innerHTML+=`<option>${e}</option>`); document.getElementById('mAsignar').style.display='flex'; }
        function modalTerminar(id,max){ curId=id; document.getElementById('inpTerm').value=max; document.getElementById('mTerminar').style.display='flex'; }
        function sendTerm(){ window.location.href=`php/cambiar_sub_estatus.php?id=${curId}&estado=revision&cantidad=`+document.getElementById('inpTerm').value; }
        function go(id,st){ window.location.href=`php/cambiar_sub_estatus.php?id=${id}&estado=${st}`; }
        function closeM(id){ document.getElementById(id).style.display='none'; }
        function eliminarLote(id){ if(confirm("¿Eliminar?")) window.location.href=`php/eliminar_lote.php?id=${id}`; }
    </script>
</body>
</html>
<?php
// adm_pedidos.php - Versión Final: Flujo Logístico (Despacho -> Entrega -> Historial)
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
if (!isset($_SESSION['usuario'])) { header('Location: login.php'); exit(); }

include("php/conexion.php");

// --- 1. LÓGICA DE FILTRO (ACTIVOS vs HISTORIAL) ---
$ver_historial = isset($_GET['ver']) && $_GET['ver'] == 'historial';
$filtro_sql = $ver_historial ? "p.estatus_pedido = 'entregado'" : "p.estatus_pedido != 'entregado' AND p.estatus_pedido != 'cancelado'";
$titulo_pag = $ver_historial ? "Historial de Entregas" : "Tablero de Pedidos";
$btn_toggle_txt = $ver_historial ? "VER ACTIVOS" : "VER HISTORIAL";
$btn_toggle_icon = $ver_historial ? "dashboard" : "history";
$btn_toggle_link = $ver_historial ? "adm_pedidos.php" : "adm_pedidos.php?ver=historial";

// --- 2. CONSULTA SQL MAESTRA ---
$sql = "SELECT 
            p.id_pedido, 
            p.cliente_nombre, 
            p.destino, 
            p.fecha_entrega, 
            p.estatus_pedido,
            p.comentarios,
            COALESCE(SUM(m.mue_cantidad), 0) as total_piezas,
            
            -- Terminados (Físicamente listos)
            COALESCE(SUM(CASE WHEN m.id_estatus_mueble >= 7 THEN m.mue_cantidad ELSE 0 END), 0) as piezas_terminadas,
            
            -- En Proceso Real
            COALESCE(SUM(CASE WHEN m.id_estatus_mueble BETWEEN 2 AND 6 THEN m.mue_cantidad ELSE 0 END), 0) as piezas_proceso,
            
            -- Avance Ponderado
            COALESCE(SUM(
                m.mue_cantidad * CASE 
                    WHEN m.id_estatus_mueble = 1 THEN 0     
                    WHEN m.id_estatus_mueble = 2 THEN 10    
                    WHEN m.id_estatus_mueble = 3 THEN 30    
                    WHEN m.id_estatus_mueble = 4 THEN 60    
                    WHEN m.id_estatus_mueble = 5 THEN 80    
                    WHEN m.id_estatus_mueble = 6 THEN 90    
                    WHEN m.id_estatus_mueble >= 7 THEN 100  
                    ELSE 0 
                END
            ) / NULLIF(SUM(m.mue_cantidad),0), 0) as porcentaje_real,

            DATEDIFF(p.fecha_entrega, CURDATE()) as dias_restantes
        FROM pedidos p 
        LEFT JOIN muebles m ON p.id_pedido = m.id_pedido 
        WHERE $filtro_sql
        GROUP BY p.id_pedido 
        ORDER BY p.fecha_entrega ASC";

$result = db_query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?php echo $titulo_pag; ?> | Idealisa</title>
    <link rel="stylesheet" href="estilos/Wave2.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;700&family=Quicksand:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">

    <style>
        /* ESTILOS (Mantenemos tu diseño intacto) */
        :root {
            --primary: #144c3c;
            --primary-light: #e0f2f1;
            --accent: #94745c;
            --bg-body: #F4F7FE;
            --white: #ffffff;
            --text-dark: #2b3674;
            --text-grey: #a3aed0;
            --alert-red: #e31a1a; 
            --alert-orange: #ffb547;
            --alert-green: #01b574;
            --alert-blue: #4318ff;
            --bar-process: #5c6bc0;
            --status-ruta: #607d8b; /* Color para "En Ruta" */
        }

        body { font-family: 'Quicksand', sans-serif; background-color: var(--bg-body); color: var(--text-dark); margin: 0; padding-bottom: 60px; }
        .main-container { max-width: 1600px; margin: 0 auto; padding: 30px; }
        
        .page-header-custom { display: flex; justify-content: space-between; align-items: center; background: var(--white); padding: 20px 30px; border-radius: 20px; box-shadow: 0 5px 20px rgba(0,0,0,0.02); margin-bottom: 30px; }
        .ph-title { display: flex; align-items: center; gap: 15px; }
        .ph-icon { width: 50px; height: 50px; background: var(--primary-light); color: var(--primary); border-radius: 12px; display: flex; align-items: center; justify-content: center; }
        
        /* Botones Header */
        .header-actions { display: flex; gap: 10px; }
        .btn-new { background: var(--primary); color: white; padding: 12px 24px; border-radius: 15px; text-decoration: none; font-weight: 700; font-family: 'Outfit'; display: flex; align-items: center; gap: 10px; border: none; cursor: pointer; box-shadow: 0 10px 20px rgba(20, 76, 60, 0.2); transition: 0.3s; }
        .btn-new:hover { transform: translateY(-3px); box-shadow: 0 15px 30px rgba(20, 76, 60, 0.3); }
        
        .btn-toggle { background: white; color: var(--text-dark); border: 1px solid #E9EDF7; padding: 12px 20px; border-radius: 15px; font-weight: 700; text-decoration: none; display: flex; align-items: center; gap: 8px; transition: 0.2s; }
        .btn-toggle:hover { background: #F4F7FE; border-color: var(--primary); color: var(--primary); }

        /* GRID */
        .orders-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(360px, 1fr)); gap: 30px; }
        .order-card { background: var(--white); border-radius: 20px; padding: 25px; box-shadow: 0 5px 15px rgba(0,0,0,0.02); position: relative; overflow: hidden; display: flex; flex-direction: column; transition: 0.3s; border: 1px solid transparent; }
        .order-card:hover { transform: translateY(-5px); box-shadow: 0 20px 40px rgba(0,0,0,0.08); }
        .order-card::before { content: ''; position: absolute; left: 0; top: 0; bottom: 0; width: 6px; }
        
        /* Colores de Borde Lateral */
        .st-pendiente::before { background: var(--alert-orange); }
        .st-produccion::before { background: var(--alert-blue); }
        .st-ruta::before { background: var(--status-ruta); } /* Nuevo estado */
        .st-entregado::before { background: var(--alert-green); }

        .oc-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 15px; }
        .oc-id { font-size: 0.75rem; font-weight: 800; color: var(--text-grey); letter-spacing: 1px; }
        .oc-client { font-family: 'Outfit'; font-weight: 700; font-size: 1.3rem; color: var(--text-dark); margin: 5px 0; }
        .oc-dest { font-size: 0.9rem; color: var(--text-grey); display: flex; align-items: center; gap: 5px; }

        .status-badge { padding: 6px 14px; border-radius: 10px; font-size: 0.7rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; }
        .bg-pendiente { background: #FFF8E1; color: var(--alert-orange); }
        .bg-produccion { background: #F4F7FE; color: var(--alert-blue); }
        .bg-ruta { background: #eceff1; color: var(--status-ruta); } /* Nuevo badge */
        .bg-entregado { background: #E8F5E9; color: var(--alert-green); }

        /* Barras */
        .prog-info { display: flex; justify-content: space-between; font-size: 0.9rem; font-weight: 700; color: var(--text-dark); margin-bottom: 8px; }
        .prog-track { height: 12px; background: #E9EDF7; border-radius: 6px; overflow: hidden; display: flex; }
        .prog-fill-real { height: 100%; background: linear-gradient(90deg, var(--bar-process), var(--alert-blue)); transition: width 1s ease; }
        .prog-fill-complete { background: var(--alert-green); }
        .prog-legend { font-size: 0.75rem; color: var(--text-grey); margin-top: 8px; font-weight: 600; display: flex; gap: 15px; }
        .dot { width: 8px; height: 8px; border-radius: 50%; display: inline-block; margin-right: 4px; }

        /* Footer y Botones Lógicos */
        .oc-footer { display: flex; justify-content: space-between; align-items: center; margin-top: auto; padding-top: 20px; border-top: 1px solid #F4F7FE; }
        .date-chip { display: flex; align-items: center; gap: 6px; font-size: 0.85rem; font-weight: 700; padding: 8px 12px; border-radius: 10px; }
        .d-ok { background: var(--primary-light); color: var(--primary); }
        .d-warn { background: #FFF8E1; color: #b45309; }
        .d-late { background: #FFEBEE; color: var(--alert-red); }

        .actions-group { display: flex; gap: 8px; }
        .action-btn { width: 36px; height: 36px; border-radius: 10px; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: 0.2s; background: #F4F7FE; color: var(--text-grey); }
        .action-btn:hover { background: var(--primary); color: white; }
        
        /* Botones Grandes de Flujo */
        .btn-flow { padding: 8px 16px; border-radius: 10px; font-weight: 700; border: none; cursor: pointer; display: flex; align-items: center; gap: 6px; font-size: 0.8rem; font-family: 'Outfit'; transition: 0.2s; }
        .btn-dispatch { background: var(--status-ruta); color: white; }
        .btn-dispatch:hover { background: #455a64; }
        .btn-deliver { background: var(--alert-green); color: white; }
        .btn-deliver:hover { background: #2e7d32; }

        /* Modales */
        .modal-overlay { position: fixed; top:0; left:0; width:100%; height:100%; background:rgba(11,20,55,0.5); z-index:9999; display:none; justify-content:center; align-items:center; backdrop-filter: blur(5px); }
        .modal-box { background: white; padding: 35px; border-radius: 25px; width: 90%; max-width: 600px; box-shadow: 0 20px 60px rgba(0,0,0,0.2); animation: zoomIn 0.3s; max-height: 85vh; overflow-y: auto; }
        @keyframes zoomIn { from{transform:scale(0.95); opacity:0;} to{transform:scale(1); opacity:1;} }
        .table-detail { width: 100%; border-collapse: separate; border-spacing: 0 10px; margin-top: 15px; }
        .table-detail th { text-align: left; color: var(--text-grey); font-size: 0.85rem; padding: 0 10px; }
        .table-detail td { padding: 15px 10px; background: #F8F9FC; font-size: 0.95rem; color: var(--text-dark); }
        .table-detail tr td:first-child { border-top-left-radius: 10px; border-bottom-left-radius: 10px; }
        .table-detail tr td:last-child { border-top-right-radius: 10px; border-bottom-right-radius: 10px; }
        .form-inp { width: 100%; padding: 14px; border: 1px solid #E9EDF7; border-radius: 15px; margin-bottom: 20px; box-sizing: border-box; font-family: 'Quicksand'; font-size: 1rem; color: var(--text-dark); outline:none; }
        .btn-save { background: var(--primary); color: white; width: 100%; padding: 15px; border: none; border-radius: 15px; font-weight: 700; cursor: pointer; font-size: 1rem; }
    </style>
</head>
<body>

    <?php include("php/encabezado_madera.php"); ?>
    <?php include("php/barra_navegacion.php"); ?>

    <div class="main-container">
        
        <div class="page-header-custom">
            <div class="ph-title">
                <div class="ph-icon"><span class="material-icons-round" style="font-size:28px">receipt_long</span></div>
                <div>
                    <h1 style="margin:0; font-family:'Outfit'; font-size:1.8rem; color:var(--text-dark);"><?php echo $titulo_pag; ?></h1>
                    <span style="color:var(--text-grey); font-size:0.9rem;">Gestión de pedidos y entregas</span>
                </div>
            </div>
            
            <div class="header-actions">
                <a href="<?php echo $btn_toggle_link; ?>" class="btn-toggle">
                    <span class="material-icons-round"><?php echo $btn_toggle_icon; ?></span> <?php echo $btn_toggle_txt; ?>
                </a>
                
                <?php if(!$ver_historial): ?>
                <button onclick="abrirModalNuevo()" class="btn-new">
                    <span class="material-icons-round">add_circle</span> NUEVO
                </button>
                <?php endif; ?>
            </div>
        </div>

        <div class="orders-grid">
            <?php 
            if(mysqli_num_rows($result) > 0) {
                while($row = mysqli_fetch_assoc($result)) {
                    $id = $row['id_pedido'];
                    $total = $row['total_piezas'];
                    $pctReal = round($row['porcentaje_real']);
                    
                    // Colores y Estados
                    $fillClass = ($pctReal == 100) ? 'prog-fill-complete' : '';
                    $stClass = "st-" . strtolower($row['estatus_pedido']);
                    $bgBadge = "bg-" . strtolower($row['estatus_pedido']);
                    
                    // Fechas
                    $dias = $row['dias_restantes'];
                    $dClass = "d-ok"; $dIcon="event"; $dTxt="Entrega: ".date('d/m', strtotime($row['fecha_entrega']));
                    if($dias <= 3 && $dias >= 0 && $row['estatus_pedido'] != 'entregado') { $dClass="d-warn"; $dIcon="history"; $dTxt="¡Próxima Entrega!"; }
                    if($dias < 0 && $row['estatus_pedido'] != 'entregado') { $dClass="d-late"; $dIcon="warning"; $dTxt="Retraso: ".abs($dias)." días"; }
            ?>

            <div class="order-card <?php echo $stClass; ?>">
                
                <div class="oc-header">
                    <div>
                        <div class="oc-id">ORDEN #<?php echo str_pad($id, 4, '0', STR_PAD_LEFT); ?></div>
                        <h3 class="oc-client"><?php echo htmlspecialchars($row['cliente_nombre']); ?></h3>
                        <div class="oc-dest"><span class="material-icons-round" style="font-size:14px">place</span> <?php echo htmlspecialchars($row['destino']); ?></div>
                    </div>
                    <div class="status-badge <?php echo $bgBadge; ?>"><?php echo strtoupper($row['estatus_pedido']); ?></div>
                </div>

                <div style="margin: 20px 0;">
                    <div class="prog-info">
                        <span>Avance Real</span> 
                        <span style="color:var(--text-dark);"><?php echo $pctReal; ?>%</span>
                    </div>
                    <div class="prog-track">
                        <div class="prog-fill-real <?php echo $fillClass; ?>" style="width: <?php echo $pctReal; ?>%;"></div>
                    </div>
                    <div class="prog-legend">
                        <span style="color:var(--text-dark); font-weight:700;">
                            <?php 
                                if($row['estatus_pedido'] == 'ruta') echo "🚚 En Camino";
                                elseif($pctReal == 100) echo "📦 En Almacén";
                                else echo "🛠️ En Producción";
                            ?>
                        </span>
                        <span style="color:var(--text-grey); margin-left:auto;">Total: <?php echo $total; ?></span>
                    </div>
                </div>

                <div class="oc-footer">
                    <?php if(!$ver_historial): ?>
                        
                        <?php if($pctReal == 100 && $row['estatus_pedido'] != 'ruta'): ?>
                            <button class="btn-flow btn-dispatch" onclick="cambiarStatus(<?php echo $id; ?>, 'ruta')">
                                <span class="material-icons-round">local_shipping</span> DESPACHAR
                            </button>
                        
                        <?php elseif($row['estatus_pedido'] == 'ruta'): ?>
                            <button class="btn-flow btn-deliver" onclick="cambiarStatus(<?php echo $id; ?>, 'entregado')">
                                <span class="material-icons-round">check_circle</span> FINALIZAR
                            </button>
                        
                        <?php else: ?>
                            <div class="date-chip <?php echo $dClass; ?>">
                                <span class="material-icons-round" style="font-size:16px"><?php echo $dIcon; ?></span> <?php echo $dTxt; ?>
                            </div>
                        <?php endif; ?>

                    <?php else: ?>
                        <div class="date-chip d-ok">
                            <span class="material-icons-round">task_alt</span> Entregado
                        </div>
                    <?php endif; ?>
                    
                    <div class="actions-group">
                        <button class="action-btn" title="Ver Detalles" onclick="verDetalles(<?php echo $id; ?>, '<?php echo htmlspecialchars($row['cliente_nombre']); ?>')">
                            <span class="material-icons-round">visibility</span>
                        </button>
                        
                        <?php if(!$ver_historial): ?>
                        <a href="adm_registros_registrar.php?id_pedido=<?php echo $id; ?>" class="action-btn" title="Agregar Muebles">
                            <span class="material-icons-round">playlist_add</span>
                        </a>
                        <button class="action-btn" onclick="abrirModalEditar(this)" data-id="<?php echo $id; ?>" data-cliente="<?php echo htmlspecialchars($row['cliente_nombre']); ?>" data-destino="<?php echo htmlspecialchars($row['destino']); ?>" data-fecha="<?php echo $row['fecha_entrega']; ?>" data-coments="<?php echo htmlspecialchars($row['comentarios']); ?>"><span class="material-icons-round">edit</span></button>
                        <button class="action-btn" style="color:var(--alert-red);" onclick="eliminarPedido(<?php echo $id; ?>)"><span class="material-icons-round">delete</span></button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php } } else { echo "<div style='grid-column:1/-1; text-align:center; padding:60px; color:var(--text-grey);'><span class='material-icons-round' style='font-size:40px; display:block; margin-bottom:10px; opacity:0.5;'>inbox</span><h3>No hay pedidos en esta vista</h3></div>"; } ?>
        </div>
    </div>

    <div id="mGestion" class="modal-overlay">
        <div class="modal-box">
            <h3 id="mTitle" style="margin-top:0; color:var(--text-dark); font-family:'Outfit';">Nuevo Pedido</h3>
            <form action="php/guardar_pedido.php" method="POST">
                <input type="hidden" name="id_pedido" id="inpId">
                <input type="hidden" name="accion" id="inpAccion" value="crear">
                <label style="font-weight:700; color:var(--text-dark); display:block; margin-bottom:5px;">Cliente</label>
                <input type="text" name="cliente" id="inpCliente" class="form-inp" required>
                <label style="font-weight:700; color:var(--text-dark); display:block; margin-bottom:5px;">Destino</label>
                <input type="text" name="destino" id="inpDestino" class="form-inp" required>
                <label style="font-weight:700; color:var(--text-dark); display:block; margin-bottom:5px;">Fecha Entrega</label>
                <input type="date" name="fecha_entrega" id="inpFecha" class="form-inp" required>
                <label style="font-weight:700; color:var(--text-dark); display:block; margin-bottom:5px;">Comentarios</label>
                <textarea name="comentarios" id="inpComents" class="form-inp" style="height:80px; resize:none;"></textarea>
                <div style="display:flex; gap:15px;">
                    <button type="button" onclick="cerrar('mGestion')" class="btn-save" style="background:#F4F7FE; color:var(--text-grey);">Cancelar</button>
                    <button type="submit" class="btn-save">GUARDAR</button>
                </div>
            </form>
        </div>
    </div>

    <div id="mDetalles" class="modal-overlay">
        <div class="modal-box" style="max-width:700px;">
            <div style="display:flex; justify-content:space-between; align-items:center;">
                <div><h3 style="margin:0; color:var(--primary); font-family:'Outfit';" id="lblDetalleCliente">Detalles</h3><small style="color:var(--text-grey);">Listado de producción</small></div>
                <button onclick="cerrar('mDetalles')" style="background:none; border:none; cursor:pointer;"><span class="material-icons-round" style="font-size:24px; color:var(--text-grey);">close</span></button>
            </div>
            <div id="loadingDetalles" style="text-align:center; padding:40px; display:none;"><span class="material-icons-round" style="animation:spin 1s infinite; color:var(--primary); font-size:30px;">refresh</span></div>
            <div id="contenidoDetalles">
                <table class="table-detail"><thead><tr><th>Cant.</th><th>Mueble</th><th>Detalles</th><th>Estado</th></tr></thead><tbody id="tblDetallesBody"></tbody></table>
            </div>
        </div>
    </div>

    <script>
        function cerrar(id) { document.getElementById(id).style.display='none'; }
        
        // --- CAMBIAR STATUS (Despachar / Entregar) ---
        function cambiarStatus(id, nuevoStatus) {
            let msg = (nuevoStatus === 'ruta') ? "🚚 ¿Despachar pedido? Pasará a estado 'EN RUTA'." : "✅ ¿Confirmar entrega? El pedido se archivará en el HISTORIAL.";
            if(confirm(msg)) {
                let f = document.createElement('form'); f.action='php/guardar_pedido.php'; f.method='POST';
                f.appendChild(Object.assign(document.createElement('input'),{type:'hidden',name:'accion',value:'cambiar_status'}));
                f.appendChild(Object.assign(document.createElement('input'),{type:'hidden',name:'id_pedido',value:id}));
                f.appendChild(Object.assign(document.createElement('input'),{type:'hidden',name:'nuevo_status',value:nuevoStatus}));
                document.body.appendChild(f); f.submit();
            }
        }

        async function verDetalles(id, cliente) {
            document.getElementById('mDetalles').style.display = 'flex';
            document.getElementById('lblDetalleCliente').innerText = "Pedido #" + id + " | " + cliente;
            document.getElementById('loadingDetalles').style.display = 'block';
            document.getElementById('tblDetallesBody').innerHTML = '';
            try {
                const res = await fetch('php/obtener_detalle_pedido.php?id=' + id);
                const json = await res.json();
                document.getElementById('loadingDetalles').style.display = 'none';
                if(json.status === 'success' && json.data.length > 0) {
                    let html = '';
                    json.data.forEach(m => {
                        html += `<tr><td style="font-weight:800; text-align:center; color:var(--primary);">${m.mue_cantidad}</td><td><b>${m.modelos_nombre}</b></td><td style="font-size:0.85rem; color:var(--text-grey);">${m.mue_color} • ${m.mue_herraje}</td><td><span style="background:${m.color_badge}; color:white; padding:4px 10px; border-radius:8px; font-weight:700; font-size:0.75rem;">${m.estatus_final}</span></td></tr>`;
                    });
                    document.getElementById('tblDetallesBody').innerHTML = html;
                } else { document.getElementById('tblDetallesBody').innerHTML = '<tr><td colspan="4" style="text-align:center; padding:30px; color:var(--text-grey);">No hay muebles registrados.</td></tr>'; }
            } catch(e) { document.getElementById('loadingDetalles').style.display = 'none'; }
        }

        function eliminarPedido(id) {
            if(confirm("⚠️ ¿Eliminar pedido?")) {
                let f = document.createElement('form'); f.action='php/guardar_pedido.php'; f.method='POST';
                f.appendChild(Object.assign(document.createElement('input'),{type:'hidden',name:'accion',value:'eliminar'}));
                f.appendChild(Object.assign(document.createElement('input'),{type:'hidden',name:'id_pedido',value:id}));
                document.body.appendChild(f); f.submit();
            }
        }
        function abrirModalNuevo() { document.getElementById('mTitle').innerText="Nuevo Pedido"; document.getElementById('inpAccion').value="crear"; document.getElementById('inpId').value=""; limpiar(); document.getElementById('mGestion').style.display='flex'; }
        function abrirModalEditar(btn) { document.getElementById('mTitle').innerText="Editar Pedido #"+btn.dataset.id; document.getElementById('inpAccion').value="editar"; document.getElementById('inpId').value=btn.dataset.id; document.getElementById('inpCliente').value=btn.dataset.cliente; document.getElementById('inpDestino').value=btn.dataset.destino; document.getElementById('inpFecha').value=btn.dataset.fecha; document.getElementById('inpComents').value=btn.dataset.coments; document.getElementById('mGestion').style.display='flex'; }
        function limpiar() { document.getElementById('inpCliente').value=""; document.getElementById('inpDestino').value=""; document.getElementById('inpFecha').value=""; document.getElementById('inpComents').value=""; }
    </script>
    <style>@keyframes spin { 100% { transform:rotate(360deg); } }</style>
</body>
</html>
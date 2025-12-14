<?php
// 1. CONFIGURACIÓN
$page = 'pedidos'; 
session_start();

// 2. SEGURIDAD
if (!isset($_SESSION['usuario']) || !isset($_SESSION['tipo'])) {
    header('Location: login.php');
    exit();
}

include("php/conexion.php");

// 3. CONSULTA DE PEDIDOS
$sql = "SELECT p.*, 
        COUNT(m.id_muebles) as total_items, 
        SUM(CASE WHEN m.id_estatus_mueble = 7 THEN 1 ELSE 0 END) as terminados,
        DATEDIFF(p.fecha_entrega, CURDATE()) as dias_restantes
        FROM pedidos p 
        LEFT JOIN muebles m ON p.id_pedido = m.id_pedido 
        GROUP BY p.id_pedido 
        ORDER BY p.estatus_pedido ASC, p.fecha_entrega ASC";

$result = db_query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Tablero de Pedidos | Idealisa</title>
    <link rel="stylesheet" href="estilos/Wave2.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&family=Quicksand:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">

    <style>
        /* ESTILOS PREMIUM */
        :root {
            --primary: #144c3c;
            --accent: #94745c;
            --bg-page: #F4F7FE;
            --white: #ffffff;
            --text-dark: #2b3674;
            --text-grey: #a3aed0;
            
            --st-pend: #ffb547; --st-prod: #4318ff; --st-comp: #01b574;
            --time-ok: #05cd99; --time-warn: #ffb547; --time-late: #e31a1a;
        }

        body { font-family: 'Quicksand', sans-serif; background-color: var(--bg-page); margin: 0; padding-bottom: 80px; }
        .main-container { max-width: 1600px; margin: 0 auto; padding: 30px; }

        /* HEADER */
        .page-header {
            display: flex; justify-content: space-between; align-items: center; 
            background: var(--white); padding: 20px 30px; border-radius: 20px; 
            box-shadow: 0 5px 20px rgba(0,0,0,0.02); margin-bottom: 30px;
        }
        .ph-title h1 { margin: 0; color: var(--text-dark); font-family: 'Outfit'; font-weight: 700; font-size: 1.8rem; display: flex; align-items: center; gap: 12px; }
        
        .btn-new-order {
            background: var(--primary); color: white; padding: 12px 24px; border-radius: 15px; 
            text-decoration: none; font-weight: 700; font-family: 'Outfit'; display: flex; align-items: center; gap: 8px; 
            box-shadow: 0 10px 20px rgba(20, 76, 60, 0.2); border: none; cursor: pointer; transition: 0.3s;
        }
        .btn-new-order:hover { transform: translateY(-3px); }

        /* GRID */
        .orders-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 25px; }

        /* TARJETA */
        .order-card {
            background: var(--white); border-radius: 20px; padding: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.03); border: 1px solid rgba(0,0,0,0.02);
            transition: all 0.3s; display: flex; flex-direction: column; gap: 15px; border-left: 6px solid #ccc;
        }
        .order-card:hover { transform: translateY(-5px); box-shadow: 0 20px 40px rgba(0,0,0,0.08); }

        .oc-header { display: flex; justify-content: space-between; align-items: flex-start; }
        .oc-id { font-size: 0.8rem; font-weight: 700; color: var(--text-grey); text-transform: uppercase; letter-spacing: 1px; }
        .oc-client { font-family: 'Outfit'; font-weight: 700; font-size: 1.3rem; color: var(--text-dark); margin: 5px 0; }
        .oc-dest { font-size: 0.9rem; color: var(--accent); display: flex; align-items: center; gap: 5px; }
        
        .status-badge { padding: 5px 12px; border-radius: 10px; font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; }

        .progress-wrapper { display: flex; align-items: center; gap: 15px; margin: 10px 0; background: #F4F7FE; padding: 15px; border-radius: 15px; }
        .progress-text { flex: 1; }
        .pt-val { font-size: 1.1rem; font-weight: 700; color: var(--text-dark); }

        .date-alert { display: flex; align-items: center; gap: 8px; font-size: 0.85rem; font-weight: 600; padding: 8px 12px; border-radius: 10px; width: fit-content; }
        .da-late { background: #ffebeb; color: var(--time-late); }
        .da-warn { background: #fff8e6; color: var(--time-warn); }
        .da-ok { background: #e0f2f1; color: var(--time-ok); }

        .oc-footer { display: flex; justify-content: space-between; align-items: center; border-top: 1px solid #f0f0f0; padding-top: 15px; margin-top: auto; }
        
        .btn-icon-text { background: transparent; border: none; color: var(--text-grey); font-weight: 600; font-size: 0.9rem; cursor: pointer; display: flex; align-items: center; gap: 5px; }
        .btn-icon-text:hover { color: var(--primary); }

        .action-group { display: flex; gap: 10px; }
        .btn-mini { width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; border: none; cursor: pointer; transition: 0.2s; }
        .btn-edit { background: #E3F2FD; color: #1565C0; }
        .btn-del { background: #FFEBEE; color: #C62828; }

        .order-details { display: none; background: #fafafa; margin: 0 -25px -25px -25px; padding: 20px; border-top: 1px solid #eee; }
        .order-card.expanded .order-details { display: block; }
        .detail-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px dashed #eee; font-size: 0.9rem; color: #555; }

        /* MODAL */
        .modal-overlay { position: fixed; top:0; left:0; width:100%; height:100%; background:rgba(11,20,55,0.6); z-index:9999; display:none; justify-content:center; align-items:center; backdrop-filter: blur(5px); }
        .modal-box { background: white; padding: 30px; border-radius: 20px; width: 90%; max-width: 500px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); animation: zoomIn 0.3s; }
        @keyframes zoomIn { from{transform:scale(0.9); opacity:0;} to{transform:scale(1); opacity:1;} }
        
        .form-label { font-weight: 700; font-size: 0.9rem; color: var(--text-dark); margin-bottom: 5px; display: block; }
        .form-inp { width: 100%; padding: 12px; border: 1px solid #E9EDF7; border-radius: 10px; margin-bottom: 15px; font-family: 'Quicksand'; box-sizing: border-box; }
        .btn-save { background: var(--primary); color: white; width: 100%; padding: 12px; border: none; border-radius: 12px; font-weight: 700; cursor: pointer; font-size: 1rem; }
    </style>
</head>
<body>

    <?php include("php/encabezado_madera.php"); ?>
    <?php include("php/barra_navegacion.php"); ?>

    <div class="main-container">
        
        <div class="page-header">
            <div class="ph-title">
                <h1><span class="material-icons-round" style="color:var(--primary)">receipt_long</span> Pizarrón de Pedidos</h1>
            </div>
            <button onclick="abrirModalNuevo()" class="btn-new-order">
                <span class="material-icons-round">add_circle</span> CREAR PEDIDO
            </button>
        </div>

        <div class="orders-grid">
            <?php 
            if(mysqli_num_rows($result) > 0) {
                while($row = mysqli_fetch_assoc($result)) {
                    // Datos
                    $id = $row['id_pedido'];
                    $cliente = $row['cliente_nombre'];
                    $destino = $row['destino'];
                    $total = $row['total_items'];
                    $terminados = $row['terminados'];
                    $dias = $row['dias_restantes'];
                    $fecha = $row['fecha_entrega'];
                    $comentarios = $row['comentarios'];
                    
                    // Progreso
                    $pct = ($total > 0) ? round(($terminados / $total) * 100) : 0;
                    
                    // Estilos
                    $cardColor = "#ccc"; $stTxt = "PENDIENTE"; $badgeBg = "#eee"; $badgeCol = "#666";
                    if($pct > 0 && $pct < 100) { $cardColor = "var(--st-prod)"; $stTxt = "PRODUCCIÓN"; $badgeBg = "#E9EDF7"; $badgeCol = "var(--st-prod)"; }
                    if($pct == 100 && $total > 0) { $cardColor = "var(--st-comp)"; $stTxt = "COMPLETADO"; $badgeBg = "#E0F2F1"; $badgeCol = "var(--st-comp)"; }

                    // Fecha
                    $dateClass = "da-ok"; $dateIcon = "event_available"; $dateTxt = "$dias días restantes";
                    if($dias <= 3 && $dias >= 0) { $dateClass = "da-warn"; $dateIcon = "history"; $dateTxt = "¡Entrega cercana!"; }
                    if($dias < 0) { $dateClass = "da-late"; $dateIcon = "warning"; $dateTxt = "RETRASADO " . abs($dias) . " días"; }
            ?>

            <div class="order-card" style="border-left-color: <?php echo $cardColor; ?>" id="card-<?php echo $id; ?>">
                
                <div class="oc-header">
                    <div>
                        <div class="oc-id">PEDIDO #<?php echo $id; ?></div>
                        <div class="oc-client"><?php echo $cliente; ?></div>
                        <div class="oc-dest"><span class="material-icons-round" style="font-size:14px">place</span> <?php echo $destino; ?></div>
                    </div>
                    <div class="status-badge" style="background:<?php echo $badgeBg; ?>; color:<?php echo $badgeCol; ?>"><?php echo $stTxt; ?></div>
                </div>

                <div class="progress-wrapper">
                    <div style="position:relative; width:50px; height:50px; display:flex; align-items:center; justify-content:center;">
                        <div style="width:100%; height:100%; border-radius:50%; background: conic-gradient(<?php echo $cardColor; ?> <?php echo $pct; ?>%, #e0e0e0 0);"></div>
                        <div style="position:absolute; width:40px; height:40px; background:white; border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:bold; font-size:0.8rem; color:var(--text-dark);"><?php echo $pct; ?>%</div>
                    </div>
                    <div class="progress-text">
                        <div class="pt-label">Muebles Listos</div>
                        <div class="pt-val"><?php echo $terminados; ?> / <?php echo $total; ?></div>
                    </div>
                </div>

                <div class="date-alert <?php echo $dateClass; ?>">
                    <span class="material-icons-round" style="font-size:16px"><?php echo $dateIcon; ?></span> <?php echo $dateTxt; ?>
                </div>

                <div class="oc-footer">
                    <button class="btn-icon-text" onclick="toggleDetalles(<?php echo $id; ?>)">
                        Ver Muebles <span class="material-icons-round">expand_more</span>
                    </button>
                    
                    <div class="action-group">
                        <!-- BOTÓN EDITAR ROBUSTO (Usa data attributes) -->
                        <button class="btn-mini btn-edit" title="Editar" 
                                type="button"
                                data-id="<?php echo $id; ?>"
                                data-cliente="<?php echo htmlspecialchars($cliente, ENT_QUOTES); ?>"
                                data-destino="<?php echo htmlspecialchars($destino, ENT_QUOTES); ?>"
                                data-fecha="<?php echo $fecha; ?>"
                                data-coments="<?php echo htmlspecialchars($comentarios, ENT_QUOTES); ?>"
                                onclick="abrirModalEditar(this)">
                            <span class="material-icons-round" style="font-size:18px">edit</span>
                        </button>
                        
                        <!-- BOTÓN ELIMINAR -->
                        <button class="btn-mini btn-del" title="Eliminar" type="button" onclick="eliminarPedido(<?php echo $id; ?>)">
                            <span class="material-icons-round" style="font-size:18px">delete</span>
                        </button>
                    </div>
                </div>

                <div class="order-details">
                    <h4 style="margin:0 0 10px 0; color:var(--text-dark);">Contenido</h4>
                    <?php
                        $qM = db_query("SELECT mo.modelos_nombre, m.mue_cantidad, em.estatus_nombre FROM muebles m JOIN modelos mo ON m.id_modelos = mo.id_modelos JOIN estatus_muebles em ON m.id_estatus_mueble = em.id_estatus_mueble WHERE m.id_pedido = $id");
                        if(mysqli_num_rows($qM) > 0) {
                            while($m = mysqli_fetch_assoc($qM)) {
                                echo '<div class="detail-row"><span>'.$m['mue_cantidad'].'x '.$m['modelos_nombre'].'</span><span style="font-weight:bold; font-size:0.8rem;">'.strtoupper($m['estatus_nombre']).'</span></div>';
                            }
                        } else {
                            echo '<a href="adm_registros_registrar.php?id_pedido='.$id.'" style="display:block; margin-top:10px; color:var(--primary); font-weight:bold; text-decoration:none;">+ Agregar Lotes</a>';
                        }
                    ?>
                </div>
            </div>
            <?php } } else { echo "<div style='grid-column:1/-1; text-align:center; padding:50px; color:#a3aed0;'><h3>No hay pedidos activos</h3></div>"; } ?>
        </div>
    </div>

    <!-- MODAL -->
    <div id="mGestionPedido" class="modal-overlay">
        <div class="modal-box">
            <div style="display:flex; justify-content:space-between; margin-bottom:20px;">
                <h3 style="margin:0; color:var(--text-dark);" id="modalTitle">Nuevo Pedido</h3>
                <span class="material-icons-round" onclick="cerrarModal()" style="cursor:pointer; color:#ccc;">close</span>
            </div>
            
            <form action="php/guardar_pedido.php" method="POST">
                <input type="hidden" name="id_pedido" id="inpId">
                <input type="hidden" name="accion" id="inpAccion" value="crear">

                <label class="form-label">Cliente</label>
                <input type="text" name="cliente" id="inpCliente" class="form-inp" required>
                
                <label class="form-label">Destino</label>
                <input type="text" name="destino" id="inpDestino" class="form-inp" required>
                
                <label class="form-label">Fecha Entrega</label>
                <input type="date" name="fecha_entrega" id="inpFecha" class="form-inp" required>
                
                <label class="form-label">Comentarios</label>
                <textarea name="comentarios" id="inpComents" class="form-inp" style="height:80px; resize:none;"></textarea>

                <button type="submit" class="btn-save" id="btnSubmit">CREAR ORDEN</button>
            </form>
        </div>
    </div>

    <script>
        function toggleDetalles(id) {
            document.getElementById('card-' + id).classList.toggle('expanded');
        }

        function cerrarModal() { document.getElementById('mGestionPedido').style.display = 'none'; }

        function abrirModalNuevo() {
            document.getElementById('modalTitle').innerText = "Nuevo Pedido";
            document.getElementById('inpAccion').value = "crear";
            document.getElementById('inpId').value = "";
            document.getElementById('inpCliente').value = "";
            document.getElementById('inpDestino').value = "";
            document.getElementById('inpFecha').value = "";
            document.getElementById('inpComents').value = "";
            document.getElementById('btnSubmit').innerText = "CREAR ORDEN";
            document.getElementById('mGestionPedido').style.display = 'flex';
        }

        // EDICIÓN ROBUSTA (Lee data attributes)
        function abrirModalEditar(btn) {
            document.getElementById('modalTitle').innerText = "Editar Pedido #" + btn.dataset.id;
            document.getElementById('inpAccion').value = "editar";
            document.getElementById('inpId').value = btn.dataset.id;
            document.getElementById('inpCliente').value = btn.dataset.cliente;
            document.getElementById('inpDestino').value = btn.dataset.destino;
            document.getElementById('inpFecha').value = btn.dataset.fecha;
            document.getElementById('inpComents').value = btn.dataset.coments;
            document.getElementById('btnSubmit').innerText = "GUARDAR CAMBIOS";
            document.getElementById('mGestionPedido').style.display = 'flex';
        }

        function eliminarPedido(id) {
            if(confirm("⚠️ ¿Estás seguro de eliminar este pedido?\nSe borrará todo el historial relacionado.")) {
                // Formulario dinámico para enviar POST
                let f = document.createElement('form');
                f.action = 'php/guardar_pedido.php';
                f.method = 'POST';
                
                let i1 = document.createElement('input'); i1.type='hidden'; i1.name='accion'; i1.value='eliminar';
                let i2 = document.createElement('input'); i2.type='hidden'; i2.name='id_pedido'; i2.value=id;
                
                f.appendChild(i1); f.appendChild(i2);
                document.body.appendChild(f);
                f.submit();
            }
        }
    </script>

</body>
</html>
<?php
// adm_nomina.php - Control de Pagos por Destajo
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['tipo'] != 'admin') { header('Location: login.php'); exit(); }

include("php/conexion.php");
// Blindaje de conexión por si acaso
if (!isset($link)) { $link = mysqli_connect('localhost', 'root', '', 'equipo', 3306); }

// --- CONFIGURACIÓN DE FECHAS (Semana Actual) ---
// Por defecto mostramos lo "Pendiente de Pago" (semana_pagada = 0)
$filtro_estado = isset($_GET['view']) && $_GET['view'] == 'history' ? 1 : 0;
$titulo_estado = $filtro_estado ? "Historial Pagado" : "Nómina Activa (Pendiente)";

// --- ACCIONES ---
if (isset($_POST['accion']) && $_POST['accion'] == 'pagar_todo') {
    // Marcar todo lo pendiente como pagado
    $fecha_corte = date('Y-m-d H:i:s');
    $sql = "UPDATE historial_destajos SET semana_pagada = 1 WHERE semana_pagada = 0";
    if(mysqli_query($link, $sql)) {
        $msg = "✅ Nómina cerrada exitosamente. Los contadores inician en cero.";
    } else {
        $error = "Error al cerrar nómina.";
    }
}

// --- CONSULTAS ---
// 1. Totales Generales
$sqlTotal = "SELECT SUM(total_pagar) as monto_total, SUM(cantidad_piezas) as piezas_total 
             FROM historial_destajos WHERE semana_pagada = $filtro_estado";
$rowTotal = mysqli_fetch_assoc(mysqli_query($link, $sqlTotal));
$gran_total = $rowTotal['monto_total'] ? $rowTotal['monto_total'] : 0;
$piezas_total = $rowTotal['piezas_total'] ? $rowTotal['piezas_total'] : 0;

// 2. Desglose por Empleado
$sqlEmp = "SELECT h.id_usuario, u.usu_nom, u.usu_ap_pat, u.usu_puesto, u.usu_img, 
           SUM(h.total_pagar) as total_emp, SUM(h.cantidad_piezas) as piezas_emp
           FROM historial_destajos h
           JOIN usuarios u ON h.id_usuario = u.id_usuario
           WHERE h.semana_pagada = $filtro_estado
           GROUP BY h.id_usuario
           ORDER BY total_emp DESC";
$resEmp = mysqli_query($link, $sqlEmp);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nómina | Idealisa</title>
    <link rel="stylesheet" href="estilos/Wave2.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;700&family=Quicksand:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">

    <style>
        :root { --primary: #144c3c; --accent: #94745c; --bg-body: #F4F7FE; --white: #ffffff; --text-dark: #2b3674; --green-money: #27ae60; }
        body { font-family: 'Quicksand', sans-serif; background-color: var(--bg-body); margin: 0; padding-bottom: 60px; color: var(--text-dark); }
        .main-container { max-width: 1400px; margin: 0 auto; padding: 30px; }

        /* Header */
        .page-header { display: flex; justify-content: space-between; align-items: center; background: var(--white); padding: 20px 30px; border-radius: 20px; box-shadow: 0 5px 20px rgba(0,0,0,0.02); margin-bottom: 30px; }
        .ph-title h1 { margin: 0; font-family: 'Outfit'; font-size: 1.8rem; color: var(--text-dark); }
        .ph-title p { margin: 5px 0 0 0; color: #a3aed0; }

        /* KPIs */
        .kpi-row { display: flex; gap: 20px; margin-bottom: 30px; }
        .kpi-card { flex: 1; background: var(--white); padding: 20px; border-radius: 20px; box-shadow: 0 5px 15px rgba(0,0,0,0.02); display: flex; align-items: center; justify-content: space-between; }
        .kpi-val { font-size: 2rem; font-weight: 700; font-family: 'Outfit'; }
        .kpi-lbl { color: #a3aed0; font-size: 0.9rem; font-weight: 700; text-transform: uppercase; }
        .kpi-icon { width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 24px; color: white; }

        /* Lista Empleados */
        .emp-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px; }
        .emp-card { background: var(--white); border-radius: 16px; padding: 20px; box-shadow: 0 5px 15px rgba(0,0,0,0.02); transition: 0.3s; cursor: pointer; border: 1px solid transparent; }
        .emp-card:hover { transform: translateY(-5px); border-color: var(--primary); box-shadow: 0 15px 30px rgba(0,0,0,0.08); }
        
        .ec-header { display: flex; align-items: center; gap: 15px; border-bottom: 1px solid #f4f7fe; padding-bottom: 15px; margin-bottom: 15px; }
        .ec-img { width: 50px; height: 50px; border-radius: 50%; object-fit: cover; background: #eee; }
        .ec-role { font-size: 0.8rem; color: var(--accent); font-weight: 700; text-transform: uppercase; }
        .ec-name { font-weight: 700; font-size: 1.1rem; margin: 2px 0 0 0; }
        
        .ec-body { display: flex; justify-content: space-between; align-items: flex-end; }
        .ec-total { font-size: 1.5rem; font-weight: 800; color: var(--green-money); font-family: 'Outfit'; }
        .ec-sub { font-size: 0.8rem; color: #a3aed0; }

        /* Botones */
        .btn-pay { background: var(--primary); color: white; border: none; padding: 12px 25px; border-radius: 12px; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 8px; font-family: 'Outfit'; transition: 0.2s; }
        .btn-pay:hover { background: #0e362b; transform: scale(1.02); }
        .btn-hist { background: var(--white); border: 1px solid #e0e0e0; color: #555; padding: 12px 20px; border-radius: 12px; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 8px; text-decoration: none; }
        .btn-hist:hover { background: #f9f9f9; }

        /* Modal Detalles */
        .modal-overlay { position: fixed; top:0; left:0; width:100%; height:100%; background:rgba(11,20,55,0.6); z-index:9999; display:none; justify-content:center; align-items:center; backdrop-filter: blur(5px); }
        .modal-box { background: white; padding: 30px; border-radius: 20px; width: 90%; max-width: 700px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); animation: zoomIn 0.3s; max-height: 85vh; overflow-y: auto; }
        @keyframes zoomIn { from{transform:scale(0.9); opacity:0;} to{transform:scale(1); opacity:1;} }

        .detail-table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        .detail-table th { text-align: left; color: #a3aed0; font-size: 0.8rem; padding: 10px; border-bottom: 1px solid #eee; }
        .detail-table td { padding: 12px 10px; border-bottom: 1px solid #f9f9f9; font-weight: 600; color: var(--text-dark); font-size: 0.9rem; }
    </style>
</head>
<body>

    <?php include("php/encabezado_madera.php"); ?>
    <?php include("php/barra_navegacion.php"); ?>

    <div class="main-container">
        
        <div class="page-header">
            <div class="ph-title">
                <h1><?php echo $titulo_estado; ?></h1>
                <p>Resumen de destajos y producción</p>
            </div>
            <div style="display:flex; gap:10px;">
                <a href="adm_nomina.php?view=<?php echo $filtro_estado ? 'active' : 'history'; ?>" class="btn-hist">
                    <span class="material-icons-round"><?php echo $filtro_estado ? 'visibility' : 'history'; ?></span>
                    <?php echo $filtro_estado ? 'Ver Activa' : 'Ver Historial'; ?>
                </a>
                
                <?php if(!$filtro_estado && $gran_total > 0): ?>
                <form method="POST" onsubmit="return confirm('¿Cerrar nómina de la semana? Esto marcará todo como PAGADO.')">
                    <input type="hidden" name="accion" value="pagar_todo">
                    <button type="submit" class="btn-pay">
                        <span class="material-icons-round">check_circle</span> CERRAR SEMANA
                    </button>
                </form>
                <?php endif; ?>
            </div>
        </div>

        <?php if(isset($msg)) echo "<div style='background:#d4edda; color:#155724; padding:15px; border-radius:15px; margin-bottom:20px;'>$msg</div>"; ?>

        <div class="kpi-row">
            <div class="kpi-card">
                <div>
                    <div class="kpi-lbl">Total a Pagar</div>
                    <div class="kpi-val" style="color:var(--green-money)">$<?php echo number_format($gran_total, 2); ?></div>
                </div>
                <div class="kpi-icon" style="background:#e8f5e9; color:var(--green-money)"><span class="material-icons-round">payments</span></div>
            </div>
            <div class="kpi-card">
                <div>
                    <div class="kpi-lbl">Piezas Producidas</div>
                    <div class="kpi-val"><?php echo $piezas_total; ?></div>
                </div>
                <div class="kpi-icon" style="background:#fff8e1; color:#ffb300"><span class="material-icons-round">category</span></div>
            </div>
        </div>

        <?php if(mysqli_num_rows($resEmp) > 0): ?>
        <div class="emp-grid">
            <?php while($e = mysqli_fetch_assoc($resEmp)): 
                $img = !empty($e['usu_img']) ? $e['usu_img'] : 'img/user_placeholder.png'; // Asegúrate de tener una imagen default
            ?>
            <div class="emp-card" onclick="verDetalles(<?php echo $e['id_usuario']; ?>, '<?php echo $e['usu_nom'].' '.$e['usu_ap_pat']; ?>')">
                <div class="ec-header">
                    <img src="<?php echo $img; ?>" class="ec-img">
                    <div>
                        <div class="ec-role"><?php echo $e['usu_puesto']; ?></div>
                        <div class="ec-name"><?php echo $e['usu_nom'] . ' ' . $e['usu_ap_pat']; ?></div>
                    </div>
                </div>
                <div class="ec-body">
                    <div>
                        <div class="ec-sub"><?php echo $e['piezas_emp']; ?> trabajos</div>
                        <div style="font-size:0.8rem; color:var(--primary); font-weight:700;">Ver detalles &rarr;</div>
                    </div>
                    <div class="ec-total">$<?php echo number_format($e['total_emp'], 2); ?></div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
        <?php else: ?>
            <div style="text-align:center; padding:50px; color:#a3aed0;">
                <span class="material-icons-round" style="font-size:48px;">savings</span>
                <h3>No hay pagos pendientes</h3>
                <p>Todo está al día o no se ha reportado producción nueva.</p>
            </div>
        <?php endif; ?>

    </div>

    <div id="mDetalle" class="modal-overlay">
        <div class="modal-box">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px;">
                <h3 style="margin:0; color:var(--text-dark);" id="modalTitle">Detalle de Pago</h3>
                <button onclick="document.getElementById('mDetalle').style.display='none'" style="border:none; background:none; cursor:pointer;"><span class="material-icons-round">close</span></button>
            </div>
            <div id="modalContent">Cargando...</div>
        </div>
    </div>

    <script>
        async function verDetalles(idUser, nombre) {
            document.getElementById('mDetalle').style.display = 'flex';
            document.getElementById('modalTitle').innerText = "Nómina: " + nombre;
            document.getElementById('modalContent').innerHTML = '<p style="text-align:center">Cargando datos...</p>';

            // Hacemos fetch a un archivo PHP ligero para obtener los datos
            try {
                const response = await fetch(`php/obtener_detalle_nomina.php?id=${idUser}&estado=<?php echo $filtro_estado; ?>`);
                const html = await response.text();
                document.getElementById('modalContent').innerHTML = html;
            } catch (error) {
                document.getElementById('modalContent').innerHTML = "Error al cargar detalles.";
            }
        }
    </script>

</body>
</html>
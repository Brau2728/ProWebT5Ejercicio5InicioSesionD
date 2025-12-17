<?php
// === 1. MODO DEPURACIÓN (Evita pantalla blanca) ===
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
if (!isset($_SESSION['usuario'])) { header('Location: login.php'); exit(); }

// === 2. VERIFICACIÓN DE INCLUDES ===
// Aseguramos que los archivos existan antes de llamarlos para evitar errores fatales
if(!file_exists("php/conexion.php")) die("Error Crítico: No se encuentra 'php/conexion.php'");
include("php/conexion.php");

// Includes de diseño (si fallan, no detienen el script crítico, pero avisamos)

// === 3. LÓGICA SEGURA ===

// Capturar ID
$id_pedido = isset($_GET['id_pedido']) ? intval($_GET['id_pedido']) : 0;
$infoPedido = null;

// Protección PHP 8: Verificamos que la consulta sea válida antes de leerla
if($id_pedido > 0) {
    $sqlP = "SELECT cliente_nombre, destino FROM pedidos WHERE id_pedido = $id_pedido";
    $qP = db_query($sqlP);
    
    // Solo intentamos leer si $qP es un objeto mysqli_result válido
    if($qP && !is_bool($qP) && mysqli_num_rows($qP) > 0) {
        $infoPedido = mysqli_fetch_assoc($qP);
    }
}

// Cargar modelos
$sqlM = "SELECT id_modelos, modelos_nombre FROM modelos ORDER BY modelos_nombre ASC";
$resModelos = db_query($sqlM);

// Si falla la carga de modelos, mostramos error visual
if(!$resModelos || is_bool($resModelos)) {
    echo "<div style='color:red; padding:20px;'>Error al cargar modelos: " . mysqli_error($connection ?? null) . "</div>";
    // Creamos un objeto falso para que no rompa el HTML de abajo
    $resModelos = false; 
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Producción</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body { font-family: 'Quicksand', sans-serif; background: #F0F2F5; padding-bottom: 50px; }
        .registro-wrapper { max-width: 1200px; margin: 30px auto; display: grid; gap: 30px; grid-template-columns: 1fr 1.5fr; padding: 0 20px; }
        @media (max-width: 900px) { .registro-wrapper { grid-template-columns: 1fr; } }

        .panel-card { background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); height: fit-content; }
        .panel-left { border-top: 5px solid #144c3c; }
        .panel-right { border-top: 5px solid #94745c; }

        .form-group { margin-bottom: 15px; }
        .form-label { display: block; font-weight: bold; color: #144c3c; margin-bottom: 5px; font-size: 0.9rem; }
        .form-control { width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 8px; font-family: 'Quicksand'; font-size: 1rem; box-sizing: border-box; }
        
        .btn-add { background: #144c3c; color: white; width: 100%; padding: 12px; border: none; border-radius: 30px; font-weight: bold; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 10px; transition: 0.3s; }
        .btn-add:hover { background: #0f382c; transform: translateY(-2px); }

        .btn-save-all { background: #94745c; color: white; border: none; padding: 15px 40px; border-radius: 40px; font-size: 1.1rem; font-weight: bold; cursor: pointer; display: block; margin: 20px auto 0 auto; width: 100%; box-shadow: 0 5px 15px rgba(148, 116, 92, 0.4); }
        .btn-save-all:disabled { background: #ccc; cursor: not-allowed; box-shadow: none; }

        .table-custom { width: 100%; border-collapse: collapse; margin-top: 15px; }
        .table-custom th { text-align: left; color: #748579; font-size: 0.8rem; border-bottom: 2px solid #eee; padding: 10px; }
        .table-custom td { padding: 12px 10px; border-bottom: 1px solid #f0f0f0; color: #333; font-size: 0.9rem; }
        .btn-del { background: #fee; color: #d33; border: none; padding: 5px 10px; border-radius: 5px; cursor: pointer; }

        .order-banner {
            grid-column: 1 / -1;
            background: #E3F2FD; color: #1565C0;
            padding: 15px 20px; border-radius: 12px;
            display: flex; justify-content: space-between; align-items: center;
            border: 1px solid #BBDEFB;
        }
    </style>
</head>
<body>

    <div class="registro-wrapper">
        
        <?php if($infoPedido): ?>
        <div class="order-banner">
            <div>
                <strong style="display:block; font-size:1.1rem;">ORDEN DE PEDIDO #<?php echo $id_pedido; ?></strong>
                <span>Cliente: <b><?php echo htmlspecialchars($infoPedido['cliente_nombre']); ?></b> (<?php echo htmlspecialchars($infoPedido['destino']); ?>)</span>
            </div>
            <a href="adm_pedidos.php" style="text-decoration:none; color:#1565C0; font-weight:bold; font-size:0.9rem;">
                <span class="material-icons" style="vertical-align:middle; font-size:16px;">arrow_back</span> Volver
            </a>
        </div>
        <?php else: ?>
            <div style="grid-column: 1/-1;">
                 <a href="adm_registros.php" style="text-decoration:none; color:#777; font-weight:bold;"><span class="material-icons" style="vertical-align:middle;">arrow_back</span> Volver al Monitor</a>
            </div>
        <?php endif; ?>

        <div class="panel-card panel-left">
            <h3 style="color:#144c3c; margin-top:0;">1. Configurar Producto</h3>
            
            <div class="form-group">
                <label class="form-label">Modelo</label>
                <select id="modelSelect" class="form-control">
                    <option value="">-- Selecciona Modelo --</option>
                    <?php 
                    if($resModelos) {
                        while($m = mysqli_fetch_assoc($resModelos)){ 
                            echo '<option value="'.$m['id_modelos'].'">'.$m['modelos_nombre'].'</option>';
                        } 
                    }
                    ?>
                </select>
            </div>

            <div style="display:flex; gap:10px;">
                <div class="form-group" style="flex:1;">
                    <label class="form-label">Color</label>
                    <input type="text" id="colorInput" class="form-control" placeholder="Ej. Chocolate" list="listColores">
                    <datalist id="listColores"><option value="Natural"><option value="Chocolate"><option value="Nogal"><option value="Blanco"></datalist>
                </div>
                <div class="form-group" style="flex:1;">
                    <label class="form-label">Herraje</label>
                    <input type="text" id="herrajeInput" class="form-control" placeholder="Ej. Metal" list="listHerrajes">
                    <datalist id="listHerrajes"><option value="Metal"><option value="Sin herraje"><option value="Plástico"></datalist>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Cantidad</label>
                <input type="number" id="qtyInput" class="form-control" value="1" min="1" style="font-size:1.2rem; font-weight:bold; color:#144c3c; text-align:center;">
            </div>

            <div class="form-group">
                <label class="form-label">Observaciones</label>
                <textarea id="noteInput" class="form-control" placeholder="Detalles especiales..."></textarea>
            </div>

            <button onclick="agregarALista()" class="btn-add">
                <span class="material-icons">add_shopping_cart</span> AGREGAR A LA LISTA
            </button>
        </div>

        <div class="panel-card panel-right">
            <h3 style="color:#94745c; margin-top:0;">2. Resumen <?php echo ($id_pedido > 0) ? "del Pedido #$id_pedido" : "de Producción"; ?></h3>
            
            <div style="min-height: 250px; max-height: 500px; overflow-y: auto;">
                <table class="table-custom">
                    <thead>
                        <tr><th style="text-align:center;">Cant.</th><th>Descripción</th><th>Detalles</th><th></th></tr>
                    </thead>
                    <tbody id="tablaCuerpo"></tbody>
                </table>
                <div id="emptyMsg" style="text-align:center; color:#ccc; padding:40px;">
                    <span class="material-icons" style="font-size:48px;">playlist_add</span>
                    <p>La lista está vacía.</p>
                </div>
            </div>

            <div style="margin-top:20px; padding:15px; background:#f4f6f5; border-radius:8px; display:flex; justify-content:space-between; font-weight:bold; color:#144c3c;">
                <span>Total Lotes: <span id="txtTotalLotes">0</span></span>
                <span>Piezas: <span id="txtTotalPiezas" style="font-size:1.3rem;">0</span></span>
            </div>

            <button id="btnGuardar" onclick="guardarTodo()" class="btn-save-all" disabled>
                <span class="material-icons" style="vertical-align:middle;">save</span> 
                <?php echo ($id_pedido > 0) ? "GUARDAR EN PEDIDO" : "REGISTRAR LOTE"; ?>
            </button>
        </div>
    </div>

    <script>
        const ID_PEDIDO_ACTUAL = <?php echo $id_pedido; ?>;
        let listaPedidos = [];

        function agregarALista() {
            const select = document.getElementById('modelSelect');
            const idModel = select.value;
            const modelTxt = select.options[select.selectedIndex]?.text;
            const color = document.getElementById('colorInput').value.trim();
            const herraje = document.getElementById('herrajeInput').value.trim();
            const qty = parseInt(document.getElementById('qtyInput').value);
            const nota = document.getElementById('noteInput').value.trim();

            if(!idModel || !color || !herraje || isNaN(qty) || qty < 1) {
                Swal.fire('Faltan datos', 'Verifica modelo, color, herraje y cantidad.', 'warning');
                return;
            }

            listaPedidos.push({
                idTemp: Date.now(),
                idModel: idModel,
                modelTxt: modelTxt,
                color: color,
                herraje: herraje,
                qty: qty,
                nota: nota,
                idPedido: ID_PEDIDO_ACTUAL
            });
            
            document.getElementById('qtyInput').value = '1';
            document.getElementById('noteInput').value = '';
            renderizarTabla();
            
            const Toast = Swal.mixin({toast: true, position: 'top-end', showConfirmButton: false, timer: 1500});
            Toast.fire({icon: 'success', title: 'Agregado'});
        }

        function borrarItem(id) {
            listaPedidos = listaPedidos.filter(i => i.idTemp !== id);
            renderizarTabla();
        }

        function renderizarTabla() {
            const tbody = document.getElementById('tablaCuerpo');
            tbody.innerHTML = '';
            let totalPzas = 0;

            if(listaPedidos.length === 0) {
                document.getElementById('emptyMsg').style.display = 'block';
                document.getElementById('btnGuardar').disabled = true;
                document.getElementById('txtTotalLotes').innerText = 0;
                document.getElementById('txtTotalPiezas').innerText = 0;
                return;
            } 
            
            document.getElementById('emptyMsg').style.display = 'none';
            document.getElementById('btnGuardar').disabled = false;

            listaPedidos.forEach(item => {
                totalPzas += item.qty;
                let htmlNota = item.nota ? `<div style="font-size:0.8rem; color:#94745c;">${item.nota}</div>` : '';
                tbody.innerHTML += `
                    <tr>
                        <td style="font-weight:bold; color:#144c3c; text-align:center;">${item.qty}</td>
                        <td><strong>${item.modelTxt}</strong>${htmlNota}</td>
                        <td>${item.color} • ${item.herraje}</td>
                        <td style="text-align:right;"><button class="btn-del" onclick="borrarItem(${item.idTemp})"><span class="material-icons">delete</span></button></td>
                    </tr>`;
            });

            document.getElementById('txtTotalLotes').innerText = listaPedidos.length;
            document.getElementById('txtTotalPiezas').innerText = totalPzas;
        }

        function guardarTodo() {
            Swal.fire({
                title: '¿Confirmar Registro?',
                text: ID_PEDIDO_ACTUAL > 0 ? "Se guardarán en el Pedido #" + ID_PEDIDO_ACTUAL : "Se registrarán en inventario general.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#144c3c',
                confirmButtonText: 'Sí, guardar'
            }).then((result) => {
                if (result.isConfirmed) enviarAlBackend();
            });
        }

        async function enviarAlBackend() {
            Swal.fire({title: 'Guardando...', didOpen: () => Swal.showLoading()});

            try {
                const response = await fetch('php/guardar_masivo.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ items: listaPedidos, id_global_pedido: ID_PEDIDO_ACTUAL })
                });

                const text = await response.text();
                // Bloque Try-Catch para atrapar errores de "Unexpected token"
                try {
                    const data = JSON.parse(text);
                    if(data.status === 'success') {
                        Swal.fire('¡Éxito!', data.msg, 'success').then(() => {
                            if(ID_PEDIDO_ACTUAL > 0) window.location.href = 'adm_pedidos.php';
                            else { listaPedidos = []; renderizarTabla(); }
                        });
                    } else {
                        Swal.fire('Error', data.msg || 'Error desconocido', 'error');
                    }
                } catch(e) {
                    console.error("Respuesta cruda servidor:", text);
                    Swal.fire({
                        icon: 'error', 
                        title: 'Error de Respuesta',
                        html: `El servidor no devolvió JSON válido.<br><br><b>Detalle técnico:</b><div style="background:#eee;padding:10px;font-size:0.8em;text-align:left;max-height:100px;overflow:auto;">${text}</div>`
                    });
                }
            } catch (err) {
                Swal.fire('Error de Conexión', 'No se pudo contactar al servidor.', 'error');
            }
        }
    </script>
</body>
</html>
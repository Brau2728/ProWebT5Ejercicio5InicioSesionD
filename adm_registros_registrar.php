<?php
session_start();
if (!isset($_SESSION['usuario'])) { header('Location: login.php'); exit(); }

include("php/conexion.php");
include("php/encabezado_madera.php");
include("php/barra_navegacion.php");

// Cargar catálogo de modelos para el select
$resModelos = db_query("SELECT id_modelos, modelos_nombre FROM modelos ORDER BY modelos_nombre ASC");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nuevo Pedido de Producción</title>
    
    <link rel="stylesheet" href="estilos/Wave2.css">
    
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        /* === ESTILOS DEL REGISTRO === */
        body { font-family: 'Quicksand', sans-serif; background: #F0F2F5; padding-bottom: 50px; }
        
        /* Contenedor principal que centra y divide la pantalla */
        .registro-wrapper {
            max-width: 1200px; 
            margin: 30px auto; 
            display: grid; 
            gap: 30px;
            grid-template-columns: 1fr 1.5fr; /* Izquierda (Form) vs Derecha (Lista) */
            padding: 0 20px;
        }
        
        /* Responsivo: En celular se pone uno debajo del otro */
        @media (max-width: 900px) { .registro-wrapper { grid-template-columns: 1fr; } }

        /* TARJETAS (Paneles) */
        .panel-card {
            background: white; padding: 25px; border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            height: fit-content;
        }

        /* Colores de borde superior */
        .panel-left { border-top: 5px solid #144c3c; }
        .panel-right { border-top: 5px solid #94745c; }

        /* FORMULARIOS */
        .form-group { margin-bottom: 15px; }
        .form-label { display: block; font-weight: bold; color: #144c3c; margin-bottom: 5px; font-size: 0.9rem; }
        .form-control {
            width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 8px;
            font-family: 'Quicksand'; font-size: 1rem; box-sizing: border-box; transition: 0.3s;
        }
        .form-control:focus { border-color: #144c3c; outline: none; background: #fdfdfd; }
        textarea.form-control { resize: vertical; min-height: 80px; }

        /* BOTONES */
        .btn-add {
            background: #144c3c; color: white; width: 100%; padding: 12px; border: none;
            border-radius: 30px; font-weight: bold; cursor: pointer; display: flex; 
            align-items: center; justify-content: center; gap: 10px; transition: 0.3s; font-size: 1rem;
        }
        .btn-add:hover { background: #0f382c; transform: translateY(-2px); }

        .btn-save-all {
            background: #94745c; color: white; border: none; padding: 15px 40px; 
            border-radius: 40px; font-size: 1.1rem; font-weight: bold; cursor: pointer;
            box-shadow: 0 5px 15px rgba(148, 116, 92, 0.4); transition: 0.3s;
            display: block; margin: 20px auto 0 auto; width: 100%;
        }
        .btn-save-all:hover { background: #7a604d; transform: translateY(-2px); }
        .btn-save-all:disabled { background: #ccc; cursor: not-allowed; box-shadow: none; }

        .btn-del { background: #fee; color: #d33; border: none; padding: 5px 10px; border-radius: 5px; cursor: pointer; }
        .btn-del:hover { background: #fcc; }

        /* TABLA */
        .table-custom { width: 100%; border-collapse: collapse; margin-top: 15px; }
        .table-custom th { text-align: left; color: #748579; font-size: 0.8rem; border-bottom: 2px solid #eee; padding: 10px; }
        .table-custom td { padding: 12px 10px; border-bottom: 1px solid #f0f0f0; color: #333; font-size: 0.9rem; vertical-align: middle; }
        
        .row-comment { font-size: 0.8rem; color: #94745c; font-style: italic; display: block; margin-top: 3px; }

        /* RESUMEN TOTAL */
        .total-banner {
            margin-top: 20px; padding: 15px; background: #f4f6f5; border-radius: 8px;
            display: flex; justify-content: space-between; align-items: center;
            font-weight: bold; color: #144c3c;
        }
    </style>
</head>
<body>

    <div class="registro-wrapper">
        
        <div class="panel-card panel-left">
            <h3 style="color:#144c3c; margin-top:0;">1. Configurar Producto</h3>
            
            <div class="form-group">
                <label class="form-label">Modelo</label>
                <select id="modelSelect" class="form-control">
                    <option value="">-- Selecciona Modelo --</option>
                    <?php while($m = mysqli_fetch_assoc($resModelos)){ ?>
                        <option value="<?php echo $m['id_modelos']; ?>"><?php echo $m['modelos_nombre']; ?></option>
                    <?php } ?>
                </select>
            </div>

            <div style="display:flex; gap:10px;">
                <div class="form-group" style="flex:1;">
                    <label class="form-label">Color</label>
                    <input type="text" id="colorInput" class="form-control" placeholder="Ej. Chocolate" list="listColores">
                    <datalist id="listColores">
                        <option value="Natural"><option value="Chocolate"><option value="Nogal"><option value="Blanco">
                    </datalist>
                </div>
                <div class="form-group" style="flex:1;">
                    <label class="form-label">Herraje</label>
                    <input type="text" id="herrajeInput" class="form-control" placeholder="Ej. Metal" list="listHerrajes">
                    <datalist id="listHerrajes">
                        <option value="Metal"><option value="Botón"><option value="Plástico">
                    </datalist>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Cantidad</label>
                <input type="number" id="qtyInput" class="form-control" value="1" min="1" style="font-size:1.2rem; font-weight:bold; color:#144c3c; text-align:center;">
            </div>

            <div class="form-group">
                <label class="form-label">Observaciones (Opcional) <span class="material-icons" style="font-size:14px; vertical-align:middle; color:#94745c;">info</span></label>
                <textarea id="noteInput" class="form-control" placeholder="Ej: Cliente pide botones en lugar de jaladera..."></textarea>
            </div>

            <button onclick="agregarALista()" class="btn-add">
                <span class="material-icons">add_shopping_cart</span> AGREGAR A LA LISTA
            </button>
        </div>

        <div class="panel-card panel-right">
            <h3 style="color:#94745c; margin-top:0;">2. Resumen del Pedido</h3>
            
            <div style="min-height: 250px; max-height: 500px; overflow-y: auto;">
                <table class="table-custom">
                    <thead>
                        <tr>
                            <th style="width:50px; text-align:center;">Cant.</th>
                            <th>Descripción</th>
                            <th>Detalles</th>
                            <th style="width:40px;"></th>
                        </tr>
                    </thead>
                    <tbody id="tablaCuerpo">
                        </tbody>
                </table>
                
                <div id="emptyMsg" style="text-align:center; color:#ccc; padding:40px;">
                    <span class="material-icons" style="font-size:48px; color:#e0e0e0;">playlist_add</span>
                    <p style="margin-top:10px;">La lista está vacía.<br>Agrega productos desde el panel izquierdo.</p>
                </div>
            </div>

            <div class="total-banner">
                <span>Lotes: <span id="txtTotalLotes">0</span></span>
                <span>Total Piezas: <span id="txtTotalPiezas" style="font-size:1.3rem;">0</span></span>
            </div>

            <button id="btnGuardar" onclick="guardarTodo()" class="btn-save-all" disabled>
                <span class="material-icons" style="vertical-align:middle;">save</span> REGISTRAR PEDIDO
            </button>
        </div>
    </div>

    <script>
        // Array global para almacenar los pedidos temporalmente
        let listaPedidos = [];

        function agregarALista() {
            // 1. Obtener valores
            const select = document.getElementById('modelSelect');
            const idModel = select.value;
            const modelTxt = select.options[select.selectedIndex]?.text;
            const color = document.getElementById('colorInput').value.trim();
            const herraje = document.getElementById('herrajeInput').value.trim();
            const qty = parseInt(document.getElementById('qtyInput').value);
            const nota = document.getElementById('noteInput').value.trim();

            // 2. Validaciones básicas
            if(!idModel || !color || !herraje || isNaN(qty) || qty < 1) {
                Swal.fire('Faltan datos', 'Revisa: Modelo, Color, Herraje y Cantidad (mínimo 1).', 'warning');
                return;
            }

            // 3. Crear objeto pedido
            const item = {
                idTemp: Date.now(), 
                idModel: idModel,
                modelTxt: modelTxt,
                color: color,
                herraje: herraje,
                qty: qty,
                nota: nota
            };

            // 4. Agregar al array
            listaPedidos.push(item);
            
            // 5. Limpiar campos
            document.getElementById('qtyInput').value = '1';
            document.getElementById('noteInput').value = '';
            // document.getElementById('modelSelect').value = ''; // Opcional: limpiar modelo

            renderizarTabla();
            
            // Toast de éxito pequeño en la esquina
            const Toast = Swal.mixin({toast: true, position: 'top-end', showConfirmButton: false, timer: 1500});
            Toast.fire({icon: 'success', title: 'Agregado a la lista'});
        }

        function borrarItem(idTemp) {
            listaPedidos = listaPedidos.filter(i => i.idTemp !== idTemp);
            renderizarTabla();
        }

        function renderizarTabla() {
            const tbody = document.getElementById('tablaCuerpo');
            const empty = document.getElementById('emptyMsg');
            const btn = document.getElementById('btnGuardar');
            
            tbody.innerHTML = '';
            let totalPzas = 0;

            if(listaPedidos.length === 0) {
                empty.style.display = 'block';
                btn.disabled = true;
                document.getElementById('txtTotalLotes').innerText = 0;
                document.getElementById('txtTotalPiezas').innerText = 0;
                return;
            } 
            
            empty.style.display = 'none';
            btn.disabled = false;

            listaPedidos.forEach(item => {
                totalPzas += item.qty;
                // Si hay nota, mostrarla bonita
                let htmlNota = item.nota ? `<div class="row-comment"><span class="material-icons" style="font-size:12px; vertical-align:middle;">chat</span> ${item.nota}</div>` : '';

                let fila = `
                    <tr>
                        <td style="font-weight:bold; color:#144c3c; font-size:1.1rem; text-align:center;">${item.qty}</td>
                        <td>
                            <strong>${item.modelTxt}</strong>
                            ${htmlNota}
                        </td>
                        <td>${item.color} • ${item.herraje}</td>
                        <td style="text-align:right;">
                            <button class="btn-del" onclick="borrarItem(${item.idTemp})" title="Quitar"><span class="material-icons">delete</span></button>
                        </td>
                    </tr>
                `;
                tbody.innerHTML += fila;
            });

            document.getElementById('txtTotalLotes').innerText = listaPedidos.length;
            document.getElementById('txtTotalPiezas').innerText = totalPzas;
        }

        function guardarTodo() {
            if(listaPedidos.length === 0) return;
            
            Swal.fire({
                title: '¿Registrar Pedido?',
                text: `Se enviarán ${listaPedidos.length} lotes a producción.`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#144c3c',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, registrar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    enviarAlBackend();
                }
            })
        }

        // --- FUNCIÓN DE ENVÍO QUE DETECTA ERRORES ---
        async function enviarAlBackend() {
            // Mostrar "Cargando..."
            Swal.fire({title: 'Guardando...', text: 'Por favor espera', didOpen: () => { Swal.showLoading() }});

            try {
                const response = await fetch('php/guardar_masivo.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(listaPedidos)
                });

                // Leemos como texto primero para diagnosticar si no es JSON
                const text = await response.text(); 
                
                try {
                    const data = JSON.parse(text); // Intentamos convertir a JSON
                    
                    if(data.status === 'success') {
                        Swal.fire({
                            title: '¡Éxito!',
                            text: data.msg,
                            icon: 'success'
                        }).then(() => {
                            listaPedidos = []; // Limpiamos la lista
                            renderizarTabla();
                            // Opcional: Redirigir al monitor
                            // window.location.href = 'monitor.php'; 
                        });
                    } else {
                        // Error reportado controladamente por el PHP
                        Swal.fire('Atención', data.msg, 'error');
                    }

                } catch (e) {
                    // Si falla el JSON.parse, es que el servidor devolvió HTML (error fatal o warning)
                    console.error("Respuesta inválida:", text);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error de Servidor',
                        html: `El servidor no devolvió una respuesta válida. <br><b>Posible causa:</b> Error de sintaxis en PHP o ruta incorrecta.<br><br>
                               <div style="background:#f0f0f0; padding:10px; border-radius:5px; text-align:left; font-size:0.8rem; max-height:150px; overflow:auto;">
                               ${text}
                               </div>`
                    });
                }

            } catch (error) {
                console.error('Error Fetch:', error);
                Swal.fire('Error de Red', 'No se pudo contactar con "php/guardar_masivo.php". Verifica que el archivo exista en la carpeta correcta.', 'error');
            }
        }
    </script>
</body>
</html>
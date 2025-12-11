<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Mero Mero - Control de Producción</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">

    <style>
        /* --- ESTILOS MATERIAL DESIGN (CSS) --- */
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f0f2f5;
            margin: 0;
            padding: 20px;
        }

        h1 { color: #1a237e; text-align: center; margin-bottom: 5px; }
        .subtitle { text-align: center; color: #5c6bc0; margin-bottom: 40px; }

        /* Contenedor de las tarjetas */
        #dashboard-container {
            display: flex;
            flex-wrap: wrap;
            justify_content: center;
            gap: 24px;
            max-width: 1200px;
            margin: 0 auto;
        }

        /* Diseño de la Tarjeta */
        .area-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            width: 320px;
            padding: 20px;
            position: relative;
            transition: transform 0.2s;
            overflow: hidden; /* Para el borde superior */
        }

        .area-card:hover { transform: translateY(-5px); }

        /* El "Semáforo" (Barra superior) */
        .status-bar {
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 6px;
        }

        /* Encabezado */
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .card-header h3 { margin: 0; color: #333; font-size: 1.2rem; }

        /* Métricas (Grid de 2 columnas) */
        .metrics-grid {
            display: flex;
            background-color: #f5f5f5;
            border-radius: 12px;
            padding: 10px;
            margin-bottom: 20px;
        }
        .metric-col { flex: 1; text-align: center; }
        .metric-col:first-child { border-right: 1px solid #e0e0e0; }
        
        .metric-label { font-size: 0.7rem; font-weight: bold; text-transform: uppercase; display: block; margin-bottom: 5px; }
        .metric-value { font-size: 1.8rem; font-weight: 800; line-height: 1; }

        /* Operarios y Avatares */
        .workers-section { margin-top: 15px; }
        .workers-label { font-size: 0.8rem; color: #757575; font-weight: 600; margin-bottom: 8px; display: block; }
        
        .worker-chips { display: flex; flex-wrap: wrap; gap: 8px; }
        
        .worker-chip {
            display: flex;
            align-items: center;
            background: #e3f2fd;
            border-radius: 20px;
            padding-right: 10px;
            border: 1px solid transparent;
        }
        .worker-chip.overloaded { background: #ffebee; border-color: #ef5350; }

        .avatar {
            width: 32px; height: 32px;
            border-radius: 50%;
            background: #2196f3;
            color: white;
            display: flex; align-items: center; justify-content: center;
            font-size: 0.9rem;
            margin-right: 8px;
            font-weight: bold;
        }
        .worker-chip.overloaded .avatar { background: #d32f2f; }

        .load-badge { font-weight: bold; color: #1565c0; font-size: 0.9rem; }
        .worker-chip.overloaded .load-badge { color: #c62828; }

        /* Alerta de Texto */
        .alert-box {
            margin-top: 15px;
            padding: 8px 12px;
            border-radius: 8px;
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
        }

    </style>
</head>
<body>

    <h1>Panel Maestro de Producción</h1>
    <p class="subtitle">Estado en tiempo real de las áreas</p>

    <div id="dashboard-container">
        </div>

    <script>
        // 1. DATOS (Simulación - Esto luego vendría de tu base de datos PHP)
        const datosTaller = [
            {
                nombre: "Barnizado",
                cola: 2,
                operarios: [
                    { nombre: "Juana", carga: 3 },
                    { nombre: "Mary", carga: 5 } // Mary está cargada
                ]
            },
            {
                nombre: "Pintura",
                cola: 12, // ¡PELIGRO! Mucha cola
                operarios: [] // ¡PELIGRO! Nadie trabajando
            },
            {
                nombre: "Tapicería",
                cola: 1,
                operarios: [
                    { nombre: "Pedro", carga: 2 } // Relax
                ]
            }
        ];

        // 2. FUNCIÓN PARA GENERAR TARJETAS
        function renderizarPanel() {
            const container = document.getElementById('dashboard-container');
            container.innerHTML = ''; // Limpiar

            datosTaller.forEach(area => {
                // --- LÓGICA DE NEGOCIO (EL CEREBRO) ---
                const totalActivos = area.operarios.reduce((sum, op) => sum + op.carga, 0);
                
                let colorEstado = '#4caf50'; // Verde
                let mensajeAlerta = '';
                let bgAlerta = '';

                // Regla 1: Cuello de botella (Cola llena y nadie trabajando)
                if (area.cola > 5 && totalActivos === 0) {
                    colorEstado = '#d32f2f'; // Rojo
                    mensajeAlerta = '¡Línea detenida con cola!';
                    bgAlerta = '#ffebee';
                }
                // Regla 2: Poco trabajo
                else if (totalActivos < 3 && area.cola < 2) {
                    colorEstado = '#ff9800'; // Naranja
                    mensajeAlerta = 'Bajo flujo de trabajo';
                    bgAlerta = '#fff3e0';
                }

                // --- GENERAR HTML ---
                // Crear lista de trabajadores HTML
                let trabajadoresHTML = '';
                if (area.operarios.length > 0) {
                    area.operarios.forEach(op => {
                        const isOverloaded = op.carga > 4; // Si tiene más de 4, es mucho
                        trabajadoresHTML += `
                            <div class="worker-chip ${isOverloaded ? 'overloaded' : ''}" title="${op.nombre}: ${op.carga} muebles">
                                <div class="avatar">${op.nombre.charAt(0)}</div>
                                <span class="load-badge">${op.carga}</span>
                            </div>
                        `;
                    });
                } else {
                    trabajadoresHTML = '<span style="font-size:0.8rem; color:#999; font-style:italic;">Sin personal asignado</span>';
                }

                // Crear alerta HTML si existe
                let alertaHTML = '';
                if (mensajeAlerta) {
                    alertaHTML = `
                        <div class="alert-box" style="background-color: ${bgAlerta}; color: ${colorEstado}">
                            <span class="material-icons-round" style="font-size: 18px">warning</span>
                            ${mensajeAlerta}
                        </div>
                    `;
                }

                // Plantilla de la tarjeta
                const cardHTML = `
                    <div class="area-card">
                        <div class="status-bar" style="background-color: ${colorEstado}"></div>
                        
                        <div class="card-header">
                            <h3>${area.nombre}</h3>
                            <span class="material-icons-round" style="color: ${mensajeAlerta ? colorEstado : '#e0e0e0'}; font-size: 28px;">
                                ${mensajeAlerta ? 'error' : 'check_circle'}
                            </span>
                        </div>

                        <div class="metrics-grid">
                            <div class="metric-col">
                                <span class="metric-label" style="color: #757575">En Cola</span>
                                <div class="metric-value" style="color: #616161">${area.cola}</div>
                            </div>
                            <div class="metric-col">
                                <span class="metric-label" style="color: ${colorEstado}">En Proceso</span>
                                <div class="metric-value" style="color: ${colorEstado}">${totalActivos}</div>
                            </div>
                        </div>

                        <div class="workers-section">
                            <span class="workers-label">PERSONAL ASIGNADO</span>
                            <div class="worker-chips">
                                ${trabajadoresHTML}
                            </div>
                        </div>

                        ${alertaHTML}
                    </div>
                `;

                container.innerHTML += cardHTML;
            });
        }

        // Ejecutar al cargar
        renderizarPanel();
    </script>
</body>
</html>
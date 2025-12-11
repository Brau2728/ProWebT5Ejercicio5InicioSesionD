<style>
    .dashboard-wrapper {
        font-family: 'Roboto', sans-serif;
        padding: 10px 0 30px 0;
        width: 100%;
        border-bottom: 2px solid #e0e0e0; /* Separador con tu contenido antiguo */
        margin-bottom: 20px;
    }

    .dashboard-title { color: #1a237e; margin: 0 0 5px 20px; font-size: 1.5rem; }
    
    #dashboard-cards-container {
        display: flex;
        flex-wrap: wrap;
        justify-content: flex-start; /* Alineado a la izquierda */
        gap: 20px;
        padding: 0 20px;
    }

    /* Tarjeta */
    .area-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        width: 280px; 
        padding: 15px;
        position: relative;
        border-top: 5px solid #ccc; /* Color por defecto */
        display: flex;
        flex-direction: column;
    }

    /* Encabezado */
    .card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
    }
    .card-header h3 { margin: 0; font-size: 1.1rem; color: #333; }

    /* Métricas */
    .metrics-row {
        display: flex;
        gap: 10px;
        margin-bottom: 10px;
    }
    .metric-box {
        flex: 1;
        text-align: center;
        padding: 8px;
        border-radius: 8px;
    }
    .metric-box.queue { background-color: #f5f5f5; border: 1px dashed #bdbdbd; }
    .metric-box.active { background-color: #e3f2fd; }

    .metric-num { font-size: 1.4rem; font-weight: 800; display: block; }
    .metric-lbl { font-size: 0.7rem; text-transform: uppercase; font-weight: bold; }

    /* Operarios */
    .workers-list { display: flex; flex-wrap: wrap; gap: 5px; margin-top: auto; }
    .worker-badge {
        display: flex;
        align-items: center;
        background: #fff;
        border: 1px solid #e0e0e0;
        border-radius: 15px;
        padding: 2px 8px 2px 2px;
        font-size: 0.75rem;
    }
    .worker-avatar {
        width: 20px; height: 20px;
        border-radius: 50%;
        background: #3f51b5;
        color: white;
        text-align: center;
        line-height: 20px;
        margin-right: 5px;
        font-weight: bold;
    }

    /* Alertas */
    .alert-msg {
        margin-top: 10px;
        font-size: 0.8rem;
        font-weight: bold;
        display: flex;
        align-items: center;
        gap: 5px;
    }
</style>

<div class="dashboard-wrapper">
    <h2 class="dashboard-title">Monitor de Flujo</h2>
    <div id="dashboard-cards-container"></div>
</div>

<script>
    (function() {
        // --- DATOS SIMULADOS (TU FLUJO REAL) ---
        // Aquí configuramos la lógica de "Siguientes pasos"
        const areas = [
            { 
                id: 'barniz',
                nombre: "Barnizado", 
                cola: 5, // <--- AQUÍ ESTÁ EL PROBLEMA: Solo hay 5 (Bajo Stock)
                min_stock: 10, // Mínimo ideal antes de alerta
                operarios: [
                    { nombre: "Mary", carga: 5 },  // Mary tiene trabajo
                    { nombre: "Juana", carga: 5 }  // Juana tiene trabajo
                ]
            },
            { 
                id: 'pintura',
                nombre: "Pintura", 
                cola: 15, // Aquí llegaron los que Mary terminó
                min_stock: 5,
                operarios: [
                    { nombre: "Oliver", carga: 8 } // Oliver está pintando
                ]
            },
            { 
                id: 'tapiceria',
                nombre: "Tapicería", 
                cola: 0, 
                min_stock: 5,
                operarios: [] 
            }
        ];

        const container = document.getElementById('dashboard-cards-container');
        container.innerHTML = '';

        areas.forEach(area => {
            // --- LÓGICA DE ALERTAS CORREGIDA ---
            const totalEnProceso = area.operarios.reduce((acc, op) => acc + op.carga, 0);
            
            let colorBorde = '#4caf50'; // Verde por defecto
            let alertaTexto = '';
            let colorAlerta = '';

            // CASO 1: ALERTA DE STOCK BAJO (Lo que pediste para Barnizado)
            // Si hay menos en cola de lo ideal, avisa que pronto se quedarán sin trabajo
            if (area.cola <= area.min_stock && area.cola > 0) {
                colorBorde = '#ff9800'; // Naranja
                alertaTexto = `⚠️ Stock bajo en cola (${area.cola}/${area.min_stock})`;
                colorAlerta = '#e65100';
            }
            // CASO 2: HAMBRUNA TOTAL (Sin cola y sin trabajo)
            else if (area.cola === 0 && totalEnProceso === 0) {
                colorBorde = '#9e9e9e'; // Gris (Inactivo)
                alertaTexto = '💤 Área detenida';
                colorAlerta = '#616161';
            }
            // CASO 3: CUELLO DE BOTELLA (Mucha cola, nadie trabaja)
            else if (area.cola > 10 && totalEnProceso === 0) {
                colorBorde = '#d32f2f'; // Rojo
                alertaTexto = '🚨 ¡Acumulación crítica!';
                colorAlerta = '#d32f2f';
            }

            // HTML de Operarios
            let htmlOperarios = area.operarios.map(op => `
                <div class="worker-badge">
                    <div class="worker-avatar">${op.nombre[0]}</div>
                    ${op.nombre}: <strong>${op.carga}</strong>
                </div>
            `).join('');

            if (area.operarios.length === 0) htmlOperarios = '<span style="color:#999; font-size:0.7rem;">Sin asignación</span>';

            // Renderizar Tarjeta
            container.innerHTML += `
                <div class="area-card" style="border-top-color: ${colorBorde}">
                    <div class="card-header">
                        <h3>${area.nombre}</h3>
                    </div>

                    <div class="metrics-row">
                        <div class="metric-box queue">
                            <span class="metric-lbl" style="color:${colorAlerta || '#757575'}">En Cola</span>
                            <span class="metric-num" style="color:${colorAlerta || '#616161'}">${area.cola}</span>
                        </div>
                        <div class="metric-box active">
                            <span class="metric-lbl" style="color:#1565c0">Procesando</span>
                            <span class="metric-num" style="color:#1565c0">${totalEnProceso}</span>
                        </div>
                    </div>

                    <div class="workers-list">
                        ${htmlOperarios}
                    </div>

                    ${alertaTexto ? `<div class="alert-msg" style="color:${colorAlerta}">${alertaTexto}</div>` : ''}
                </div>
            `;
        });
    })();
</script>
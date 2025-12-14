<?php
// 1. DEFINIR PÁGINA ACTUAL (Para iluminar menú)
$page = 'modelos'; 

session_start();

// 2. SEGURIDAD DE SESIÓN
if(!isset($_SESSION['usuario']) || !isset($_SESSION['tipo'])){
    header('Location: login.php'); 
    exit();
}

include("php/conexion.php");

// ==========================================
// 3. CONSULTA SQL INTELIGENTE
// ==========================================
// Trae modelos + conteo de producción en tiempo real para el semáforo
$sql = "SELECT mo.*, 
        (SELECT SUM(mue_cantidad) FROM muebles mu WHERE mu.id_modelos = mo.id_modelos AND mu.id_estatus_mueble BETWEEN 2 AND 6) as en_produccion,
        (SELECT COUNT(*) FROM muebles mu WHERE mu.id_modelos = mo.id_modelos AND mu.sub_estatus = 'revision') as en_revision
        FROM modelos mo 
        ORDER BY mo.modelos_nombre ASC";

$result = db_query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Catálogo de Modelos | Idealisa</title>
    
    <!-- ESTILOS GLOBALES -->
    <link rel="stylesheet" href="estilos/Wave2.css">
    
    <!-- FUENTES Y ICONOS -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&family=Quicksand:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">

    <style>
        /* ESTILO PREMIUM LIMPIO (Coincide con adm_registros) */
        :root {
            --bg-page: #F4F7FE;
            --primary: #144c3c;
            --accent: #94745c;
            --text-dark: #2b3674;
            --white: #ffffff;
            
            /* Semáforos */
            --sem-red: #e31a1a;   
            --sem-orange: #ffb547; 
            --sem-green: #01b574;  
            --sem-blue: #4318ff;   
        }

        body { background-color: var(--bg-page); font-family: 'Quicksand', sans-serif; margin: 0; padding-bottom: 60px; }

        .main-container {
            padding: 30px;
            max-width: 1600px;
            margin: 0 auto;
        }

        /* HEADER DE LA PÁGINA */
        .page-header {
            display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; 
            background: var(--white); padding: 20px 30px; border-radius: 20px; 
            box-shadow: 0 5px 20px rgba(0,0,0,0.02);
        }

        .ph-title h1 { 
            margin: 0; color: var(--text-dark); font-family: 'Outfit'; font-weight: 700; font-size: 1.8rem; 
            display: flex; align-items: center; gap: 12px; 
        }
        .ph-subtitle { color: #a3aed0; margin: 5px 0 0 0; font-size: 0.95rem; font-weight: 500; }

        /* BOTONES Y FILTROS */
        .ph-actions { display: flex; gap: 15px; align-items: center; }

        .btn-new {
            background: var(--primary); color: white; padding: 12px 24px;
            border-radius: 12px; text-decoration: none; font-weight: 700; font-family: 'Outfit';
            display: flex; align-items: center; gap: 8px; transition: 0.3s;
            box-shadow: 0 10px 20px rgba(20, 76, 60, 0.2);
        }
        .btn-new:hover { transform: translateY(-3px); box-shadow: 0 15px 30px rgba(20, 76, 60, 0.3); }

        .filter-capsule {
            display: flex; gap: 5px; background: #F4F7FE; padding: 5px; border-radius: 12px;
        }
        .btn-filter {
            border: none; background: transparent; padding: 8px 16px; border-radius: 8px;
            cursor: pointer; font-weight: 600; color: #a3aed0; transition: 0.2s; font-family: 'Outfit';
        }
        .btn-filter:hover { color: var(--primary); }
        .btn-filter.active { background: var(--white); color: var(--primary); box-shadow: 0 2px 10px rgba(0,0,0,0.05); }

        /* GRID DE MODELOS */
        .models-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 30px;
        }

        /* TARJETA MODELO (Estilo Card Premium) */
        .model-card {
            background: var(--white); border-radius: 20px; overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.02); border: 1px solid rgba(0,0,0,0.02);
            transition: all 0.3s ease; display: flex; flex-direction: column; position: relative;
        }
        .model-card:hover { transform: translateY(-5px); box-shadow: 0 20px 40px rgba(0,0,0,0.08); }

        /* Imagen */
        .mc-image-box {
            height: 200px; width: 100%; background: #f4f7fe;
            position: relative; overflow: hidden; display: flex; align-items: center; justify-content: center;
        }
        .mc-image-box img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s; }
        .model-card:hover .mc-image-box img { transform: scale(1.05); }

        /* Semáforo Visual (Badge Flotante) */
        .status-badge {
            position: absolute; top: 15px; right: 15px;
            padding: 6px 12px; border-radius: 10px; font-size: 0.75rem; font-weight: 800;
            text-transform: uppercase; letter-spacing: 0.5px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1); backdrop-filter: blur(5px);
        }
        .sb-red { background: rgba(255, 235, 235, 0.95); color: var(--sem-red); }
        .sb-orange { background: rgba(255, 248, 230, 0.95); color: var(--sem-orange); }
        .sb-green { background: rgba(220, 252, 231, 0.95); color: var(--sem-green); }
        .sb-blue { background: rgba(227, 242, 253, 0.95); color: var(--sem-blue); }

        /* Contenido */
        .mc-content { padding: 20px; flex: 1; display: flex; flex-direction: column; }
        
        .mc-title { margin: 0; color: var(--text-dark); font-size: 1.3rem; font-weight: 700; font-family: 'Outfit'; }
        .mc-desc { 
            font-size: 0.9rem; color: #a3aed0; margin: 8px 0 15px 0; line-height: 1.5; 
            display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
        }

        /* Stats Internos */
        .mc-stats-row { 
            display: flex; gap: 15px; margin-top: auto; padding-top: 15px; border-top: 1px solid #f4f7fe;
        }
        .stat-pill { 
            display: flex; align-items: center; gap: 6px; font-size: 0.85rem; font-weight: 600; color: var(--text-dark);
            background: #f4f7fe; padding: 6px 12px; border-radius: 8px;
        }

        /* Footer Acciones */
        .mc-actions {
            padding: 15px 20px; background: #fafbfc; border-top: 1px solid #f1f5f9;
            display: flex; justify-content: space-between; align-items: center;
        }

        .link-price { 
            color: var(--accent); text-decoration: none; font-weight: 700; font-size: 0.9rem; 
            display: flex; align-items: center; gap: 5px; transition:0.2s;
        }
        .link-price:hover { color: var(--primary); }

        .action-icons { display: flex; gap: 8px; }
        .btn-icon {
            width: 35px; height: 35px; border-radius: 10px; display: flex; align-items: center; justify-content: center;
            color: #a3aed0; transition: 0.2s; text-decoration: none; border: 1px solid transparent;
        }
        .btn-icon:hover { background: #f4f7fe; color: var(--primary); }
        .btn-icon.del:hover { background: #ffebeb; color: var(--sem-red); }

        /* MODO LISTA */
        .models-grid.list-view { display: flex; flex-direction: column; gap: 15px; }
        .models-grid.list-view .model-card { flex-direction: row; align-items: center; height: 100px; padding-right: 20px; }
        .models-grid.list-view .mc-image-box { width: 120px; height: 100%; border-radius: 0; margin-right: 0; }
        .models-grid.list-view .mc-content { flex-direction: row; align-items: center; justify-content: space-between; padding: 0 20px; gap: 20px; border:none; }
        .models-grid.list-view .mc-desc { display: none; }
        .models-grid.list-view .mc-stats-row { margin: 0; padding: 0; border: none; }
        .models-grid.list-view .mc-actions { background: transparent; border: none; width: auto; padding: 0; }
        .models-grid.list-view .status-badge { position: static; margin-right: 20px; }

    </style>
</head>
<body>

    <!-- 4. INCLUIR BARRA DE NAVEGACIÓN -->
    <?php include("php/encabezado_madera.php"); ?>
    <?php include("php/barra_navegacion.php"); ?>

    <div class="main-container">

        <!-- CABECERA -->
        <div class="page-header">
            <div class="ph-title">
                <div>
                    <h1><span class="material-icons-round" style="color:var(--primary)">chair</span> Catálogo de Modelos</h1>
                    <p class="ph-subtitle">Administra diseños, precios y visualiza el estado de producción.</p>
                </div>
            </div>
            
            <div class="ph-actions">
                <div class="filter-capsule">
                    <button class="btn-filter active" onclick="filtrar('todos', this)">Todos</button>
                    <button class="btn-filter" onclick="filtrar('parados', this)">⚠️ Parados</button>
                    <button class="btn-filter" onclick="filtrar('revision', this)">👁️ Revisión</button>
                </div>

                <div style="display:flex; gap:5px;">
                    <button onclick="cambiarVista('grid')" class="btn-filter" title="Grid"><span class="material-icons-round">grid_view</span></button>
                    <button onclick="cambiarVista('list')" class="btn-filter" title="Lista"><span class="material-icons-round">view_list</span></button>
                </div>

                <a href="adm_modelos_registrar.php" class="btn-new">
                    <span class="material-icons-round">add</span> NUEVO MODELO
                </a>
            </div>
        </div>

        <!-- GRID RESULTADOS -->
        <div class="models-grid" id="contenedor-modelos">
            
            <?php 
            if($result && mysqli_num_rows($result) > 0) {
                while($row = mysqli_fetch_assoc($result)) {
                    
                    // DATOS
                    $id = $row['id_modelos'];
                    $nombre = $row['modelos_nombre'];
                    $desc = $row['modelos_descripcion'];
                    $img = !empty($row['modelos_imagen']) ? $row['modelos_imagen'] : '';
                    
                    // LÓGICA SEMÁFORO
                    $prod = (int)$row['en_produccion']; 
                    $rev = (int)$row['en_revision'];     

                    if($prod == 0) {
                        $stClass = "sb-red"; $stText = "PARADO"; $stState = "parado";
                    } elseif($prod < 5) {
                        $stClass = "sb-orange"; $stText = "POCO TRABAJO"; $stState = "poco";
                    } elseif($prod <= 7) {
                        $stClass = "sb-green"; $stText = "FLUJO IDEAL"; $stState = "medio";
                    } else {
                        $stClass = "sb-blue"; $stText = "ALTA PROD."; $stState = "alto";
                    }
                    
                    $hasRev = ($rev > 0) ? 'true' : 'false';
            ?>

            <div class="model-card" data-estado="<?php echo $stState; ?>" data-revision="<?php echo $hasRev; ?>">
                
                <!-- Badge Estado -->
                <div class="status-badge <?php echo $stClass; ?>">
                    <?php echo $stText; ?>
                </div>

                <div class="mc-image-box">
                    <?php if($img) { ?>
                        <img src="<?php echo $img; ?>" alt="<?php echo $nombre; ?>">
                    <?php } else { ?>
                        <span class="material-icons-round" style="font-size:48px; color:#d1d5db;">image_not_supported</span>
                    <?php } ?>
                </div>

                <div class="mc-content">
                    <h3 class="mc-title"><?php echo $nombre; ?></h3>
                    <p class="mc-desc"><?php echo $desc ? substr($desc, 0, 80).'...' : 'Sin descripción disponible.'; ?></p>
                    
                    <div class="mc-stats-row">
                        <div class="stat-pill">
                            <span class="material-icons-round" style="font-size:16px; color:var(--primary)">precision_manufacturing</span>
                            <?php echo $prod; ?> en planta
                        </div>
                        <?php if($rev > 0) { ?>
                        <div class="stat-pill" style="color:var(--sem-orange); background:#fff8e6;">
                            <span class="material-icons-round" style="font-size:16px">visibility</span>
                            <?php echo $rev; ?> validar
                        </div>
                        <?php } ?>
                    </div>
                </div>

                <div class="mc-actions">
                    <a href="gestionar_precios.php?id=<?php echo $id; ?>" class="link-price">
                        <span class="material-icons-round">payments</span> Precios Destajo
                    </a>

                    <div class="action-icons">
                        <a href="adm_modelos_modificar.php?id=<?php echo $id; ?>" class="btn-icon" title="Editar">
                            <span class="material-icons-round">edit</span>
                        </a>
                        <a href="#" onclick="borrar(<?php echo $id; ?>)" class="btn-icon del" title="Eliminar">
                            <span class="material-icons-round">delete</span>
                        </a>
                    </div>
                </div>
            </div>

            <?php 
                } // While
            } else {
                echo "<div style='grid-column:1/-1; text-align:center; padding:50px; color:#a3aed0;'>
                        <span class='material-icons-round' style='font-size:60px; opacity:0.5;'>folder_off</span>
                        <h3>No hay modelos registrados</h3>
                      </div>";
            }
            ?>

        </div>
    </div>

    <script>
        function cambiarVista(vista) {
            const cont = document.getElementById('contenedor-modelos');
            if(vista === 'list') cont.classList.add('list-view');
            else cont.classList.remove('list-view');
        }

        function filtrar(criterio, btn) {
            document.querySelectorAll('.btn-filter').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');

            document.querySelectorAll('.model-card').forEach(card => {
                let show = true;
                if (criterio === 'parados' && card.dataset.estado !== 'parado') show = false;
                if (criterio === 'revision' && card.dataset.revision !== 'true') show = false;
                card.style.display = show ? 'flex' : 'none';
            });
        }

        function borrar(id) {
            if(confirm("¿Estás seguro? Al eliminar el modelo se borrará todo su historial de producción.")) {
                window.location.href = "adm_modelos_eliminar.php?id=" + id;
            }
        }
    </script>

</body>
</html>
<?php
$page = 'modelos'; 
session_start();

// 1. SEGURIDAD
if (!isset($_SESSION['usuario']) || !isset($_SESSION['tipo'])) {
    header('Location: login.php');
    exit();
}

// 2. CONEXIÓN BLINDADA
$conexion = null;
$ruta = __DIR__ . '/php/conexion.php';
if(file_exists($ruta)) { include($ruta); if(isset($link)) $conexion = $link; if(isset($conn)) $conexion = $conn; }
if(!$conexion) { try { $conexion = mysqli_connect('localhost', 'root', '', 'equipo', 3306); } catch(Exception $e){} }

if(!$conexion) { die("Error Crítico: Sin conexión a la Base de Datos."); }
mysqli_set_charset($conexion, "utf8mb4");

// 3. OBTENER DATOS (GET)
if (!isset($_GET['id'])) { header('Location: adm_modelos.php'); exit(); }
$id = (int)$_GET['id'];

// Modelo
$sqlM = "SELECT * FROM modelos WHERE id_modelos = $id";
$resM = mysqli_query($conexion, $sqlM);
$modelo = mysqli_fetch_assoc($resM);
if(!$modelo) { echo "<script>alert('Modelo no encontrado.'); window.location='adm_modelos.php';</script>"; exit(); }

// Receta (Materiales)
$sqlD = "SELECT * FROM modelos_detalles WHERE id_modelo = $id";
$resD = mysqli_query($conexion, $sqlD);
$detalles = [];
while($r = mysqli_fetch_assoc($resD)) { $detalles[] = $r; }

// 4. PROCESAR ACTUALIZACIÓN (POST)
if (isset($_POST['btn_actualizar'])) {
    $nombre = mysqli_real_escape_string($conexion, $_POST['modelos_nombre']);
    $desc   = mysqli_real_escape_string($conexion, $_POST['modelos_descripcion']);
    
    // -- IMAGEN --
    $img_ruta = $modelo['modelos_imagen']; // Por defecto, mantenemos la anterior
    
    // Opción A: Subieron archivo
    if (isset($_FILES['foto_archivo']) && $_FILES['foto_archivo']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['foto_archivo']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg','jpeg','png','webp'])) {
            $dir = "img/uploads/";
            if(!file_exists($dir)) mkdir($dir, 0777, true);
            
            $nuevo = uniqid('mod_') . '.' . $ext;
            if(move_uploaded_file($_FILES['foto_archivo']['tmp_name'], $dir.$nuevo)) {
                $img_ruta = $dir.$nuevo;
            }
        }
    } 
    // Opción B: Pusieron URL nueva
    elseif (!empty($_POST['modelos_imagen_url']) && $_POST['modelos_imagen_url'] != $modelo['modelos_imagen']) {
        $img_ruta = mysqli_real_escape_string($conexion, $_POST['modelos_imagen_url']);
    }

    // --- CORRECCIÓN SOLICITADA: DEFAULT IMG/LOGO.JPG ---
    // Si después de todo esto no hay ruta (está vacía), asignamos el logo por defecto.
    if (empty($img_ruta)) {
        $img_ruta = 'img/logo.jpg';
    }

    // Actualizar Modelo
    $sqlUp = "UPDATE modelos SET modelos_nombre='$nombre', modelos_descripcion='$desc', modelos_imagen='$img_ruta' WHERE id_modelos=$id";
    
    if(mysqli_query($conexion, $sqlUp)) {
        
        // -- ACTUALIZAR RECETA (Estrategia: Borrar y Reescribir) --
        mysqli_query($conexion, "DELETE FROM modelos_detalles WHERE id_modelo=$id");

        if (isset($_POST['material_nombre']) && is_array($_POST['material_nombre'])) {
            $cats = $_POST['material_cat'];
            $noms = $_POST['material_nombre'];
            $cants = $_POST['material_cant'];
            $unds = $_POST['material_unidad'];

            for ($i=0; $i < count($noms); $i++) {
                if(!empty($noms[$i])) {
                    $c = mysqli_real_escape_string($conexion, $cats[$i]);
                    $n = mysqli_real_escape_string($conexion, $noms[$i]);
                    $q = (float)$cants[$i];
                    $u = mysqli_real_escape_string($conexion, $unds[$i]);
                    
                    $sqlIns = "INSERT INTO modelos_detalles (id_modelo,categoria,nombre_material,cantidad,unidad) 
                               VALUES ($id,'$c','$n','$q','$u')";
                    mysqli_query($conexion, $sqlIns);
                }
            }
        }

        // -------------------------------------------------------------
        // [FUTURO FIREBASE] -> Aquí iría el código de sincronización
        // $firebase->update("modelos/$id", ["nombre" => $nombre, ...]);
        // -------------------------------------------------------------
        
        echo "<script>alert('Modelo actualizado correctamente.'); window.location='adm_modelos.php';</script>";
    } else {
        echo "<script>alert('Error al actualizar: ".mysqli_error($conexion)."');</script>";
    }
}

// Sugerencias para autocompletado
$sugerencias = [];
$resS = mysqli_query($conexion, "SELECT DISTINCT nombre_material FROM modelos_detalles ORDER BY nombre_material ASC");
if($resS) while($row = mysqli_fetch_assoc($resS)) $sugerencias[] = $row['nombre_material'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Modelo - Idealisa</title>
    
    <link rel="stylesheet" href="estilos/Wave2.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;500;700&family=Quicksand:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
    
    <style>
        /* ESTILO PREMIUM IDEALISA */
        :root {
            --primary: #144c3c;
            --accent: #94745c;
            --bg-body: #F7F9FC;
        }

        body { font-family: 'Outfit', sans-serif; background-color: var(--bg-body); padding-bottom: 150px; margin: 0; }
        
        .form-wrapper { 
            background: white; max-width: 1000px; margin: 40px auto; 
            border-radius: 24px; 
            box-shadow: 0 20px 40px rgba(0,0,0,0.06); 
            overflow: hidden;
            border: 1px solid rgba(0,0,0,0.02);
        }

        .form-header { 
            background: linear-gradient(135deg, #fff 0%, #fcfcfc 100%); 
            padding: 30px; text-align: center; border-bottom: 1px solid #f0f0f0; 
        }
        .form-header h2 { margin: 0; color: var(--accent); font-weight: 700; letter-spacing: -0.5px; font-size: 2rem; }
        .form-header p { margin: 5px 0 0; color: #888; font-size: 0.95rem; }
        
        .form-body { padding: 40px; }

        .input-group { margin-bottom: 25px; }
        .input-group label { display: block; font-weight: 600; color: #5d6b62; margin-bottom: 10px; font-size: 0.9rem; }
        
        .form-control { 
            width: 100%; padding: 14px; border: 2px solid #eee; border-radius: 12px; 
            font-family: inherit; font-size: 1rem; box-sizing: border-box; background: #fafafa;
            transition: all 0.3s ease;
        }
        .form-control:focus { 
            border-color: var(--accent); outline: none; background: white; 
            box-shadow: 0 4px 12px rgba(148, 116, 92, 0.1); 
        }
        
        /* SECCIÓN FOTO */
        .photo-section {
            background: #fffbf7; border: 2px dashed #e6dace; border-radius: 16px;
            padding: 30px; text-align: center; position: relative;
            transition: 0.3s;
        }
        .photo-section:hover { border-color: var(--accent); background: #fff; }

        .img-preview { 
            width: 180px; height: 180px; border-radius: 16px; object-fit: cover;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1); margin-bottom: 20px;
            border: 4px solid white;
        }

        .file-upload-btn {
            display: inline-flex; align-items: center; gap: 8px;
            background: white; border: 2px solid var(--accent); color: var(--accent);
            padding: 10px 20px; border-radius: 30px; font-weight: 600; cursor: pointer;
            transition: 0.2s;
        }
        .file-upload-btn:hover { background: var(--accent); color: white; }

        /* SECCIÓN RECETA */
        .specs-container { 
            background: #fff; border: 1px solid #eee; border-radius: 16px; padding: 30px; 
            margin-top: 40px; position: relative; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.03);
        }
        .specs-label { 
            position: absolute; top: -15px; left: 30px; 
            background: var(--primary); color: white; 
            padding: 6px 16px; border-radius: 20px; 
            font-size: 0.8rem; font-weight: 700; letter-spacing: 1px;
            box-shadow: 0 4px 10px rgba(20, 76, 60, 0.3);
        }
        
        /* Filas Dinámicas */
        .dynamic-row { 
            display: flex; flex-wrap: wrap; gap: 15px; margin-bottom: 15px; 
            align-items: center; padding: 15px; 
            background: #f8fcf9; border-radius: 12px; 
            border-left: 4px solid var(--primary); 
            animation: slideIn 0.3s ease-out;
        }
        
        /* Columnas Responsive */
        .col-cat { flex: 1.2; min-width: 130px; } 
        .col-mat { flex: 3; min-width: 200px; }
        .col-cant { flex: 0.8; min-width: 90px; } 
        .col-und { flex: 1; min-width: 110px; }
        .col-del { width: 40px; text-align: center; }

        .btn-remove { 
            color: #ff6b6b; background: white; border: 1px solid #ffcccc; 
            width: 32px; height: 32px; border-radius: 50%; cursor: pointer; 
            display: flex; align-items: center; justify-content: center;
            transition: 0.2s;
        }
        .btn-remove:hover { background: #ffebeb; transform: scale(1.1); }

        .btn-add { 
            background: #f1f8f5; color: var(--primary); border: 2px dashed var(--primary); 
            width: 100%; padding: 15px; border-radius: 12px; 
            cursor: pointer; font-weight: 700; margin-top: 20px; 
            display: flex; align-items: center; justify-content: center; gap: 8px;
            transition: 0.2s;
        }
        .btn-add:hover { background: #e0f2eb; }

        .btn-submit { 
            background: var(--accent); color: white; border: none; width: 100%; padding: 18px; 
            border-radius: 16px; font-size: 1.1rem; font-weight: 700; cursor: pointer; 
            margin-top: 40px; display: flex; justify-content: center; gap: 10px; 
            transition: 0.3s; box-shadow: 0 10px 20px rgba(148, 116, 92, 0.3);
        }
        .btn-submit:hover { background: #7a604c; transform: translateY(-3px); }
        
        .btn-back { 
            display: inline-flex; align-items: center; gap: 8px; text-decoration: none; 
            color: #777; font-weight: 600; margin: 30px 0; padding: 10px 20px;
            background: white; border-radius: 30px; box-shadow: 0 4px 10px rgba(0,0,0,0.05);
            transition: 0.3s;
        }
        .btn-back:hover { transform: translateX(-5px); color: var(--primary); }
        
        datalist { display: none; }
        @keyframes slideIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body>
    <?php include("php/encabezado_madera.php"); include("php/barra_navegacion.php"); ?>
    
    <datalist id="lista_materiales"><?php foreach($sugerencias as $s) echo "<option value='".htmlspecialchars($s)."'>"; ?></datalist>

    <div style="max-width: 1000px; margin: 0 auto;">
        <a href="adm_modelos.php" class="btn-back">
            <span class="material-icons-round">arrow_back</span> Cancelar
        </a>
    </div>

    <div class="form-wrapper">
        <div class="form-header">
            <h2>Editar Modelo</h2>
            <p>Modifica los datos generales o ajusta la receta de materiales</p>
        </div>

        <form method="POST" class="form-body" enctype="multipart/form-data">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 40px;">
                
                <!-- Columna Izquierda: Datos -->
                <div>
                    <div class="input-group">
                        <label>Nombre del Modelo</label>
                        <input type="text" name="modelos_nombre" class="form-control" value="<?php echo htmlspecialchars($modelo['modelos_nombre']); ?>" required>
                    </div>
                    <div class="input-group">
                        <label>Descripción Comercial</label>
                        <textarea name="modelos_descripcion" class="form-control" style="height: 120px; resize: vertical;"><?php echo htmlspecialchars($modelo['modelos_descripcion']); ?></textarea>
                    </div>
                    <div class="input-group">
                         <label>URL Imagen (Opcional si usas link externo)</label>
                         <input type="text" name="modelos_imagen_url" class="form-control" placeholder="https://..." value="<?php echo (strpos($modelo['modelos_imagen'], 'http') === 0) ? htmlspecialchars($modelo['modelos_imagen']) : ''; ?>">
                    </div>
                </div>

                <!-- Columna Derecha: Foto -->
                <div class="photo-section">
                    <label style="font-weight:700; color:var(--accent); display:block; margin-bottom:15px;">Fotografía del Mueble</label>
                    
                    <!-- Previsualización con fallback a logo.jpg -->
                    <img src="<?php echo !empty($modelo['modelos_imagen']) ? $modelo['modelos_imagen'] : 'img/logo.jpg'; ?>" class="img-preview" id="preview" onerror="this.src='img/logo.jpg'">
                    
                    <br>
                    <label class="file-upload-btn">
                        <span class="material-icons-round">cloud_upload</span> Subir Nueva
                        <input type="file" name="foto_archivo" style="display:none;" accept="image/*" onchange="verPreview(this)">
                    </label>
                    <p style="font-size:0.8rem; color:#aaa; margin-top:10px;">Formatos: JPG, PNG, WEBP</p>
                </div>
            </div>

            <!-- Sección Receta -->
            <div class="specs-container">
                <span class="specs-label">RECETA DE MATERIALES</span>
                
                <div id="contenedor-materiales">
                    <?php if(empty($detalles)): ?>
                        <p style="text-align:center; color:#999; font-size:0.9rem; padding:20px; border: 2px dashed #eee; border-radius:10px;">
                            No hay materiales registrados. ¡Agrega uno abajo!
                        </p>
                    <?php endif; ?>

                    <?php foreach($detalles as $d): ?>
                    <div class="dynamic-row">
                        <div class="col-cat">
                            <select name="material_cat[]" class="form-control" style="padding:10px;">
                                <?php 
                                $opts = ['Armado','Barniz','Pintura','Adornado','Empaque','General'];
                                foreach($opts as $o) echo "<option value='$o' ".($d['categoria']==$o?'selected':'').">$o</option>";
                                ?>
                            </select>
                        </div>
                        <div class="col-mat">
                            <input type="text" name="material_nombre[]" list="lista_materiales" class="form-control" value="<?php echo htmlspecialchars($d['nombre_material']); ?>" required>
                        </div>
                        <div class="col-cant">
                            <input type="number" step="0.01" name="material_cant[]" class="form-control" value="<?php echo $d['cantidad']; ?>">
                        </div>
                        <div class="col-und">
                            <select name="material_unidad[]" class="form-control" style="padding:10px;">
                                <?php 
                                $unds = ['pza'=>'Pzas','jgo'=>'Juego','ml'=>'ml','lts'=>'Litros','gr'=>'Gramos','kg'=>'Kilos','mts'=>'Metros','cm'=>'CM','hoja'=>'Hoja'];
                                foreach($unds as $k=>$v) echo "<option value='$k' ".($d['unidad']==$k?'selected':'').">$v</option>";
                                ?>
                            </select>
                        </div>
                        <div class="col-del">
                            <button type="button" class="btn-remove" onclick="eliminarFila(this)" title="Eliminar">
                                <span class="material-icons-round" style="font-size:18px">close</span>
                            </button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <button type="button" class="btn-add" onclick="agregarFila()">
                    <span class="material-icons-round">add_circle_outline</span> Agregar Nuevo Material
                </button>
            </div>

            <button type="submit" name="btn_actualizar" class="btn-submit">
                <span class="material-icons-round">save</span> GUARDAR CAMBIOS
            </button>
        </form>
    </div>

    <?php include("php/olas.php"); ?>

    <script>
        function verPreview(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) { document.getElementById('preview').src = e.target.result; }
                reader.readAsDataURL(input.files[0]);
            }
        }

        function eliminarFila(btn) { 
            const row = btn.closest('.dynamic-row');
            row.style.opacity = '0';
            setTimeout(() => row.remove(), 300);
        }
        
        function agregarFila() {
            const div = document.createElement('div');
            div.className = 'dynamic-row';
            div.innerHTML = `
                <div class="col-cat">
                    <select name="material_cat[]" class="form-control" style="padding:10px;">
                        <option>Armado</option><option>Barniz</option><option>Pintura</option><option>Adornado</option><option>General</option>
                    </select>
                </div>
                <div class="col-mat">
                    <input type="text" name="material_nombre[]" list="lista_materiales" class="form-control" placeholder="Buscar..." required>
                </div>
                <div class="col-cant">
                    <input type="number" step="0.01" name="material_cant[]" class="form-control" placeholder="0">
                </div>
                <div class="col-und">
                    <select name="material_unidad[]" class="form-control" style="padding:10px;">
                        <option value="pza">Pzas</option><option value="ml">ml</option><option value="lts">Litros</option><option value="gr">Gramos</option><option value="kg">Kilos</option><option value="mts">Metros</option>
                    </select>
                </div>
                <div class="col-del">
                    <button type="button" class="btn-remove" onclick="eliminarFila(this)">
                        <span class="material-icons-round" style="font-size:18px">close</span>
                    </button>
                </div>
            `;
            div.style.opacity = 0;
            document.getElementById('contenedor-materiales').appendChild(div);
            setTimeout(() => div.style.opacity = 1, 50);
        }
    </script>
</body>
</html>
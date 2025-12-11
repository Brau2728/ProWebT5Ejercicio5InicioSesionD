<?php
$page = 'modelos'; // Variable para iluminar el menú
session_start();

// Validación de sesión
if(!isset($_SESSION['usuario']) || !isset($_SESSION['tipo'])){
    header('Location: login.php'); 
    exit();
}

include("php/conexion.php");

// Consultar modelos (Usando tu función select o mysqli directo)
$result = select("modelos");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modelos - Idealiza</title>
    
    <link rel="stylesheet" href="estilos/Wave2.css">
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <style>
        /* PALETA DE COLORES PERSONALIZADA
           Marrón: #94745c
           Verde Claro: #cedfcd
           Verde Oscuro: #144c3c
           Gris Verdoso: #5d6b62
           Verde Salvia: #748579
        */

        body { font-family: 'Quicksand', sans-serif; background-color: #F0F2F5; padding-bottom: 100px; }
        .container-models { max-width: 1200px; margin: 0 auto; padding: 40px 20px; }

        /* ENCABEZADO */
        .header-action { 
            display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px; 
            background: white; padding: 20px 30px; border-radius: 12px; 
            box-shadow: 0 4px 15px rgba(0,0,0,0.05); border-left: 6px solid #144c3c; /* Verde Oscuro */
        }
        
        .title-page { margin: 0; color: #144c3c; font-weight: 700; font-size: 1.8rem; display: flex; align-items: center; gap: 10px; }
        
        .btn-add { 
            background: #144c3c; color: white; text-decoration: none; padding: 12px 25px; 
            border-radius: 30px; font-weight: bold; display: flex; align-items: center; gap: 8px;
            transition: 0.3s; box-shadow: 0 4px 10px rgba(20, 76, 60, 0.3);
        }
        .btn-add:hover { background: #0f382c; transform: translateY(-2px); }

        /* GRID DE TARJETAS (Sin Bootstrap viejo) */
        .grid-modelos {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); /* Responsivo automático */
            gap: 30px;
        }

        /* TARJETA DE MUEBLE */
        .model-card {
            background: white; border-radius: 16px; overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08); transition: all 0.3s ease;
            display: flex; flex-direction: column; height: 100%;
            border-top: 5px solid #94745c; /* Marrón elegante arriba */
        }
        .model-card:hover { transform: translateY(-5px); box-shadow: 0 12px 25px rgba(0,0,0,0.15); }

        /* IMAGEN */
        .card-img-box {
            height: 200px; width: 100%; background: #cedfcd; /* Verde claro de fondo por si no carga */
            position: relative; overflow: hidden;
        }
        .card-img-box img {
            width: 100%; height: 100%; object-fit: cover; /* Mantiene proporción sin estirar */
            transition: transform 0.5s;
        }
        .model-card:hover .card-img-box img { transform: scale(1.05); }

        /* CONTENIDO */
        .card-body { padding: 20px; flex: 1; display: flex; flex-direction: column; }
        
        .model-title { 
            margin: 0 0 10px 0; color: #144c3c; font-size: 1.3rem; font-weight: 700; 
            border-bottom: 2px solid #cedfcd; padding-bottom: 8px;
        }
        
        .model-desc { 
            color: #5d6b62; /* Gris verdoso */
            font-size: 0.9rem; line-height: 1.5; margin-bottom: 20px; flex: 1; 
        }

        /* BOTONES DE ACCIÓN */
        .card-actions { 
            display: flex; gap: 10px; margin-top: auto; 
        }
        
        .btn-card {
            flex: 1; padding: 10px; border-radius: 8px; text-decoration: none; text-align: center;
            font-weight: bold; font-size: 0.9rem; transition: 0.2s; display: flex; justify-content: center; align-items: center; gap: 5px;
        }
        
        .btn-edit { 
            background: #cedfcd; color: #144c3c; /* Verde pálido fondo, texto oscuro */
        }
        .btn-edit:hover { background: #b8d6b6; }

        .btn-del { 
            background: #fff; color: #94745c; border: 1px solid #94745c;
        }
        .btn-del:hover { background: #94745c; color: white; }

    </style>
</head>
<body>

    <?php include("php/encabezado_madera.php"); ?>
    <?php include("php/barra_navegacion.php"); ?>

    <div class="container-models">
        
        <div class="header-action">
            <h1 class="title-page">
                <span class="material-icons" style="font-size: 32px;">chair</span> 
                Catálogo de Modelos
            </h1>
            <a href="adm_modelos_registrar.php" class="btn-add">
                <span class="material-icons">add_circle</span> Crear Modelo
            </a>
        </div>

        <div class="grid-modelos">
            <?php
            if ($result && mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_object($result)) {
                    // Imagen por defecto si no existe
                    $imagen_url = !empty($row->modelos_imagen) ? $row->modelos_imagen : 'https://dummyimage.com/600x400/cedfcd/144c3c&text=Sin+Imagen';
            ?>
            
            <div class="model-card">
                <div class="card-img-box">
                    <img src="<?php echo $imagen_url; ?>" alt="Foto del mueble">
                </div>
                
                <div class="card-body">
                    <h3 class="model-title"><?php echo $row->modelos_nombre; ?></h3>
                    <p class="model-desc">
                        <?php 
                        // Recortar texto si es muy largo para que no rompa el diseño
                        $desc = $row->modelos_descripcion;
                        echo (strlen($desc) > 80) ? substr($desc, 0, 80) . '...' : $desc; 
                        ?>
                    </p>
                    
                    <div class="card-actions">
                        <a href="adm_modelos_modificar.php?id=<?php echo $row->id_modelos; ?>" class="btn-card btn-edit">
                            <span class="material-icons" style="font-size:16px">edit</span> Editar
                        </a>
                        <a href="#" onclick="confirmarBorrado(<?php echo $row->id_modelos; ?>)" class="btn-card btn-del">
                            <span class="material-icons" style="font-size:16px">delete</span>
                        </a>
                    </div>
                </div>
            </div>

            <?php 
                } // Fin While
            } else {
                echo "<p style='width:100%; text-align:center; color:#748579; font-size:1.2rem; grid-column: 1 / -1;'>
                        <span class='material-icons' style='font-size:40px; display:block; margin-bottom:10px;'>inventory_2</span>
                        No hay modelos registrados aún.
                      </p>";
            } 
            ?>
        </div>

    </div>

    <?php include("php/olas.php"); ?>

    <script>
        function confirmarBorrado(id) {
            if(confirm("¿Estás seguro de eliminar este modelo? Se borrarán también los registros de producción asociados.")) {
                window.location.href = "adm_modelos_eliminar.php?id=" + id;
            }
        }
    </script>

</body>
</html>
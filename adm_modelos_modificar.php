<?php
$page = 'modelos'; // Mantiene iluminado el menú "Modelos"
session_start();

if (!isset($_SESSION['usuario']) || !isset($_SESSION['tipo'])) {
    header('Location: login.php');
    exit();
}

include("php/conexion.php");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Modelo - Idealiza</title>
    
    <link rel="stylesheet" href="estilos/Wave2.css">
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <style>
        /* PALETA DE COLORES PERSONALIZADA
           Marrón: #94745c
           Verde Claro: #cedfcd
           Verde Oscuro: #144c3c
           Gris Verdoso: #5d6b62
        */

        body { font-family: 'Quicksand', sans-serif; background-color: #F0F2F5; padding-bottom: 100px; }
        
        .form-wrapper {
            background: white; max-width: 700px; margin: 40px auto; padding: 40px;
            border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            /* Borde superior Marrón Bronce */
            border-top: 6px solid #94745c; 
        }

        h2 { color: #144c3c; text-align: center; margin-bottom: 30px; font-weight: 700; font-size: 1.8rem; }
        
        .input-group { margin-bottom: 20px; }
        .input-group label { display: block; font-weight: bold; color: #5d6b62; margin-bottom: 8px; font-size: 0.95rem; }
        
        .form-control {
            width: 100%; padding: 12px; border: 1px solid #cedfcd; border-radius: 8px;
            font-family: inherit; font-size: 1rem; box-sizing: border-box; transition: 0.3s;
            color: #333; background-color: #FAFAFA;
        }
        
        /* Foco Marrón al escribir */
        .form-control:focus { 
            border-color: #94745c; outline: none; 
            box-shadow: 0 0 0 3px rgba(148, 116, 92, 0.1); 
            background-color: #fff;
        }

        /* Área de texto más alta */
        textarea.form-control { min-height: 100px; resize: vertical; }

        /* Botones */
        .btn-submit {
            background: #144c3c; /* Verde Oscuro */
            color: white; border: none; width: 100%; padding: 15px;
            border-radius: 30px; font-size: 1.1rem; font-weight: bold; cursor: pointer;
            transition: 0.3s; margin-top: 20px; display: flex; justify-content: center; align-items: center; gap: 10px;
        }
        .btn-submit:hover { background: #0f382c; transform: translateY(-2px); box-shadow: 0 5px 15px rgba(20, 76, 60, 0.3); }

        .btn-back {
            display: inline-flex; align-items: center; gap: 5px; text-decoration: none;
            color: #748579; font-weight: bold; margin-bottom: 25px; transition: 0.3s;
        }
        .btn-back:hover { color: #144c3c; transform: translateX(-5px); }

        @media (max-width: 600px) {
            .form-wrapper { padding: 25px; margin: 20px; }
        }
    </style>
</head>
<body>

    <?php include("php/encabezado_madera.php"); ?>
    <?php include("php/barra_navegacion.php"); ?>

    <div class="form-wrapper">
        <a href="adm_modelos.php" class="btn-back">
            <span class="material-icons">arrow_back</span> Volver al Catálogo
        </a>

        <h2>Registrar Nuevo Modelo</h2>

        <form method="POST" action="adm_modelos_registrar_modelos.php">
            
            <div class="input-group">
                <label>Nombre del Modelo:</label>
                <input type="text" name="modelos_nombre" class="form-control" placeholder="Ej. Silla Imperial" required>
            </div>

            <div class="input-group">
                <label>Descripción:</label>
                <textarea name="modelos_descripcion" class="form-control" placeholder="Detalles del mueble, materiales, medidas..."></textarea>
            </div>

            <div class="input-group">
                <label>URL de la Imagen:</label>
                <input type="text" name="modelos_imagen" class="form-control" placeholder="https://...">
            </div>

            <button type="submit" name="btn_guardar" class="btn-submit">
                <span class="material-icons">save</span> Guardar Modelo
            </button>

        </form>
    </div>

    <?php include("php/olas.php"); ?>

</body>
</html>
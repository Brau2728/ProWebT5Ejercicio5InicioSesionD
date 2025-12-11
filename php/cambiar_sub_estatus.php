<?php
session_start();

// Habilitar reporte de errores para ver qué pasa
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 1. CONEXIÓN MANUAL SEGURA (Para evitar conflictos)
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'equipo';
$db_port = '3306';

$link = mysqli_connect($db_host, $db_user, $db_pass, $db_name, $db_port);

if (!$link) {
    die("Error crítico de conexión: " . mysqli_connect_error());
}

// 2. RECIBIR DATOS
if(isset($_GET['id']) && isset($_GET['estado'])) {
    
    $id = mysqli_real_escape_string($link, $_GET['id']);
    $estado = mysqli_real_escape_string($link, $_GET['estado']);
    $cantidadReportada = isset($_GET['cantidad']) ? (int)$_GET['cantidad'] : 0;

    // --- LÓGICA DE REPORTE DE PRODUCCIÓN ---
    if ($estado == 'revision' && $cantidadReportada > 0) {
        
        // Obtenemos TODOS los datos del mueble original
        $sqlInfo = "SELECT * FROM muebles WHERE id_muebles = '$id'";
        $resInfo = mysqli_query($link, $sqlInfo);
        
        if($resInfo && mysqli_num_rows($resInfo) > 0) {
            $mueble = mysqli_fetch_assoc($resInfo);
            $cantidadTotal = (int)$mueble['mue_cantidad'];

            // CASO A: Terminó TODO el lote
            if ($cantidadReportada >= $cantidadTotal) {
                $sql = "UPDATE muebles SET sub_estatus = 'revision' WHERE id_muebles = '$id'";
                if(!mysqli_query($link, $sql)) {
                    die("Error al actualizar estado completo: " . mysqli_error($link));
                }
            } 
            // CASO B: Terminó solo una PARTE (Dividir Lote)
            else {
                // 1. Calcular restante
                $nuevaCantidadRestante = $cantidadTotal - $cantidadReportada;
                
                // 2. Actualizar el original (se queda con lo que falta por hacer)
                $sqlReduce = "UPDATE muebles SET mue_cantidad = '$nuevaCantidadRestante' WHERE id_muebles = '$id'";
                if(!mysqli_query($link, $sqlReduce)) {
                    die("Error al reducir cantidad original: " . mysqli_error($link));
                }

                // 3. Crear el nuevo lote (CLON EXACTO) con lo terminado
                // IMPORTANTE: Incluimos el PRECIO y otros campos para que no falle
                $precio = $mueble['mue_precio']; // Dato que faltaba
                
                $sqlClon = "INSERT INTO muebles 
                            (id_modelos, id_estatus_mueble, mue_cantidad, mue_color, mue_herraje, mue_precio, asignado_a, sub_estatus)
                            VALUES 
                            ('{$mueble['id_modelos']}', '{$mueble['id_estatus_mueble']}', '$cantidadReportada', '{$mueble['mue_color']}', '{$mueble['mue_herraje']}', '$precio', '{$mueble['asignado_a']}', 'revision')";
                
                if(!mysqli_query($link, $sqlClon)) {
                    die("Error al crear el lote dividido: " . mysqli_error($link));
                }
            }
        } else {
            die("Error: No se encontró el mueble original ID: $id");
        }

    } else {
        // CAMBIO DE ESTADO NORMAL (Iniciar, etc.)
        if(in_array($estado, ['cola', 'proceso', 'revision'])) {
            $sql = "UPDATE muebles SET sub_estatus = '$estado' WHERE id_muebles = '$id'";
            if(!mysqli_query($link, $sql)) {
                die("Error al cambiar estado simple: " . mysqli_error($link));
            }
        }
    }
}

// Si todo salió bien, volvemos
header("Location: ../adm_registros.php");
exit();
?>
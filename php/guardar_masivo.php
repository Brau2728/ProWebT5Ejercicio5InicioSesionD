<?php
error_reporting(0);
header('Content-Type: application/json; charset=utf-8');
session_start();

// === ZONA DE CONEXIÓN ===
// Asegúrate de que este nombre sea el correcto (el que copiaste antes)
$servidor = "localhost";
$usuario  = "root";        
$password = "";            
$baseDatos = "equipo"; // <--- ¡VERIFICA QUE ESTE SEA EL NOMBRE QUE VISTE EN PHPMYADMIN!

$con = mysqli_connect($servidor, $usuario, $password, $baseDatos);

if (!$con) {
    echo json_encode(['status'=>'error', 'msg'=>'Falló conexión: ' . mysqli_connect_error()]);
    exit();
}
// ========================

$input = file_get_contents('php://input');
$lista = json_decode($input, true);

if (!$lista) {
    echo json_encode(['status'=>'error', 'msg'=>'No llegaron datos.']);
    exit();
}

$guardados = 0;

foreach ($lista as $item) {
    $idMod = (int)$item['idModel'];
    $col   = mysqli_real_escape_string($con, $item['color']);
    $herr  = mysqli_real_escape_string($con, $item['herraje']);
    $cant  = (int)$item['qty'];
    $nota  = mysqli_real_escape_string($con, $item['nota']);

    // === CORRECCIÓN AQUÍ: Quitamos 'fecha_registro' ===
    $sql = "INSERT INTO muebles (
                id_modelos, mue_color, mue_herraje, mue_cantidad, mue_comentario, 
                id_estatus_mueble, sub_estatus
            ) VALUES (
                '$idMod', '$col', '$herr', '$cant', '$nota', 
                2, 'cola'
            )";
    
    if (mysqli_query($con, $sql)) {
        $guardados++;
    }
}

if ($guardados > 0) {
    echo json_encode(['status'=>'success', 'msg'=>"Registrados: $guardados"]);
} else {
    // Si falla, mostramos el error exacto para saber qué pasa
    echo json_encode(['status'=>'error', 'msg'=>'Error SQL: ' . mysqli_error($con)]);
}
?>
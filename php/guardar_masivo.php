<?php
// 1. LIMPIEZA AGRESIVA DE ERRORES (Anti-Basura)
// Desactivamos que los errores se impriman en pantalla para no romper el JSON
error_reporting(0);
ini_set('display_errors', 0);

// Iniciamos un "colchón" (buffer) para atrapar cualquier texto indeseado
ob_start();

// 2. CONFIGURACIÓN
header('Content-Type: application/json; charset=utf-8');
include("conexion.php");

// 3. RECIBIR DATOS
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Limpiamos el buffer (borra cualquier warning de include o espacios en blanco previos)
ob_clean(); 

// 4. VALIDACIÓN DE ESTRUCTURA
$listaItems = [];
$idPedidoGlobal = null;

if (isset($data['items'])) {
    $listaItems = $data['items'];
    $idPedidoGlobal = isset($data['id_global_pedido']) ? intval($data['id_global_pedido']) : null;
} else {
    $listaItems = $data;
}

if (empty($listaItems) || !is_array($listaItems)) {
    echo json_encode(['status' => 'error', 'msg' => 'No se recibieron datos válidos.']);
    exit;
}

// 5. PROCESAMIENTO
$guardados = 0;
$errores = 0;
$detallesError = "";

foreach ($listaItems as $item) {
    if (!is_array($item)) continue;

    $idModel = isset($item['idModel']) ? intval($item['idModel']) : 0;
    $qty     = intval($item['qty']);
    $color   = isset($item['color']) ? $item['color'] : '';
    $herraje = isset($item['herraje']) ? $item['herraje'] : '';
    $nota    = isset($item['nota']) ? $item['nota'] : '';
    
    // Calcular ID Pedido
    $idPed = $idPedidoGlobal ? $idPedidoGlobal : (isset($item['idPedido']) ? intval($item['idPedido']) : 0);
    $valIdPed = ($idPed > 0) ? $idPed : 'NULL';

    // QUERY (Asegúrate que coincida con tus columnas)
    $sql = "INSERT INTO muebles 
            (id_modelos, id_estatus_mueble, mue_cantidad, mue_color, mue_herraje, mue_comentario, id_pedido, sub_estatus) 
            VALUES 
            ($idModel, 2, $qty, '$color', '$herraje', '$nota', $valIdPed, 'cola')";

    // Ejecutar usando la conexión global si existe, o creando una nueva
    // (Asumimos que db_query está en conexion.php)
    $resultado = db_query($sql);

    if ($resultado) {
        $guardados++;
    } else {
        $errores++;
        // Capturamos el error de MySQL solo si existe la variable de conexión
        // (Esto es para debugging interno tuyo, no se muestra al usuario si error_reporting es 0)
    }
}

// 6. RESPUESTA FINAL
if ($guardados > 0 && $errores === 0) {
    echo json_encode(['status' => 'success', 'msg' => "Se registraron $guardados lotes correctamente."]);
} elseif ($guardados > 0 && $errores > 0) {
    echo json_encode(['status' => 'warning', 'msg' => "Se guardaron $guardados, pero fallaron $errores."]);
} else {
    echo json_encode(['status' => 'error', 'msg' => "No se pudo guardar ningún registro. Verifica la base de datos."]);
}
?>
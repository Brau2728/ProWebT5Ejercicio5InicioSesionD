<?php
// php/obtener_detalle_pedido.php - Con corrección de nombres de estatus
error_reporting(0);
ini_set('display_errors', 0);
ob_start();

include("conexion.php");

header('Content-Type: application/json; charset=utf-8');

$id_pedido = isset($_GET['id']) ? intval($_GET['id']) : 0;
ob_clean(); // Limpiamos basura anterior

if ($id_pedido <= 0) {
    echo json_encode(['status' => 'error', 'msg' => 'ID inválido']);
    exit;
}

// Consultamos los muebles
$sql = "SELECT 
            m.id_muebles,
            mo.modelos_nombre,
            m.mue_cantidad,
            m.mue_color,
            m.mue_herraje,
            m.sub_estatus,
            m.asignado_a,
            em.estatus_mueble,
            em.id_estatus_mueble
        FROM muebles m
        JOIN modelos mo ON m.id_modelos = mo.id_modelos
        LEFT JOIN estatus_muebles em ON m.id_estatus_mueble = em.id_estatus_mueble
        WHERE m.id_pedido = $id_pedido";

$result = db_query($sql);
$muebles = [];

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        
        // --- AQUÍ ESTÁ LA CORRECCIÓN ---
        // Forzamos el nombre correcto según el ID para arreglar el error visual
        $idSt = $row['id_estatus_mueble'];
        
        switch($idSt) {
            case 1: $estatusTexto = "Por Iniciar"; break;
            case 2: $estatusTexto = "Maquila"; break;
            case 3: $estatusTexto = "Armado"; break;
            case 4: $estatusTexto = "Barniz"; break;   // Corregido: ID 4 es Barniz
            case 5: $estatusTexto = "Pintado"; break;  // Corregido: ID 5 es Pintado
            case 6: $estatusTexto = "Adornado"; break;
            default: 
                // Si es 7 o mayor, o desconocido, usamos el de la BD o "Terminado"
                $estatusTexto = ($idSt >= 7) ? "TERMINADO" : $row['estatus_mueble'];
                break;
        }

        // Definimos el color del badge (Etiqueta)
        $colorBadge = "#777"; // Gris por defecto
        
        // Lógica visual de colores
        if ($idSt >= 7) { 
            // Terminado final
            $estatusTexto = "TERMINADO"; 
            $colorBadge = "#2E7D32"; // Verde Fuerte
        } elseif ($idSt == 1) {
            // Sin iniciar
            $colorBadge = "#ED6C02"; // Naranja
        } else {
            // En proceso (IDs 2 al 6)
            $colorBadge = "#1976D2"; // Azul
            
            // Agregamos detalle del sub-estatus si existe
            if($row['sub_estatus'] == 'cola') $estatusTexto .= " (Cola)";
            if($row['sub_estatus'] == 'proceso') $estatusTexto .= " (Andando)";
            if($row['sub_estatus'] == 'revision') $estatusTexto .= " (Revisión)";
        }

        $row['estatus_final'] = $estatusTexto;
        $row['color_badge'] = $colorBadge;
        $muebles[] = $row;
    }
}

echo json_encode(['status' => 'success', 'data' => $muebles]);
?>
<?php
// php/cambiar_sub_estatus.php - CORREGIDO: Lógica de Split y Pagos por Modelo
session_start();
include("conexion.php");

// Blindaje conexión
if (!isset($link) || $link === null) {
    $link = mysqli_connect('localhost', 'root', '', 'equipo', 3306);
}

// Recibir datos
$id_mueble = isset($_GET['id']) ? intval($_GET['id']) : 0;
$nuevo_estado = isset($_GET['estado']) ? $_GET['estado'] : '';
$cantidad_terminada = isset($_GET['cantidad']) ? intval($_GET['cantidad']) : 0;

if ($id_mueble > 0 && $nuevo_estado != '') {

    // --- LOGICA 1: INICIAR ---
    if ($nuevo_estado == 'proceso') {
        mysqli_query($link, "UPDATE muebles SET sub_estatus = 'proceso' WHERE id_muebles = $id_mueble");
    } 
    
    // --- LOGICA 2: TERMINAR TRABAJO ---
    elseif ($nuevo_estado == 'revision') {
        
        // 1. Obtener datos del lote actual
        $qMueble = mysqli_query($link, "SELECT * FROM muebles WHERE id_muebles = $id_mueble");
        $lote = mysqli_fetch_assoc($qMueble);
        
        if ($lote) {
            $cant_total_lote = intval($lote['mue_cantidad']);
            $id_modelo = $lote['id_modelos'];
            $nombre_empleado = $lote['asignado_a'];
            $id_etapa_num = $lote['id_estatus_mueble'];
            $id_pedido = $lote['id_pedido']; // ¡IMPORTANTE!

            // ---------------------------------------------------------
            // A. DIVISIÓN DE LOTE (SPLIT) SI ES PARCIAL
            // ---------------------------------------------------------
            // Si termina MENOS de lo que tiene asignado
            if ($cantidad_terminada < $cant_total_lote) {
                // 1. Calcular sobrante
                $sobrante = $cant_total_lote - $cantidad_terminada;

                // 2. Crear NUEVO LOTE para el sobrante (Se queda en proceso/asignado a la misma persona)
                $id_ped_sql = $id_pedido ? "'$id_pedido'" : "NULL";
                $coment_sql = mysqli_real_escape_string($link, $lote['mue_comentario']);
                $asig_sql   = mysqli_real_escape_string($link, $lote['asignado_a']);
                
                $sqlSplit = "INSERT INTO muebles 
                            (id_modelos, id_estatus_mueble, mue_cantidad, mue_color, mue_herraje, mue_comentario, asignado_a, sub_estatus, id_pedido) 
                            VALUES 
                            ('$id_modelo', '$id_etapa_num', '$sobrante', '{$lote['mue_color']}', '{$lote['mue_herraje']}', '$coment_sql', '$asig_sql', 'proceso', $id_ped_sql)";
                mysqli_query($link, $sqlSplit);

                // 3. Actualizar el lote ACTUAL con lo terminado (y pasarlo a revisión)
                mysqli_query($link, "UPDATE muebles SET mue_cantidad = $cantidad_terminada, sub_estatus = 'revision' WHERE id_muebles = $id_mueble");
            
            } else {
                // Terminó todo: Solo actualizar estado
                mysqli_query($link, "UPDATE muebles SET sub_estatus = 'revision' WHERE id_muebles = $id_mueble");
            }

            // ---------------------------------------------------------
            // B. REGISTRO DE PAGO (Buscar precio por MODELO)
            // ---------------------------------------------------------
            $mapa_precios = [2=>'mue_precio_maquila', 3=>'mue_precio_armado', 4=>'mue_precio_barnizado', 5=>'mue_precio_pintado', 6=>'mue_precio_adornado'];
            $col_precio = $mapa_precios[$id_etapa_num];
            $etapa_txt = str_replace('mue_precio_', '', $col_precio);

            // Buscar Usuario
            $id_usuario = 0;
            if(!empty($nombre_empleado)) {
                $nom = explode(' ', trim($nombre_empleado))[0];
                $qUser = mysqli_query($link, "SELECT id_usuario FROM usuarios WHERE usu_nom LIKE '%$nom%' LIMIT 1");
                if ($rUser = mysqli_fetch_assoc($qUser)) $id_usuario = $rUser['id_usuario'];
            }

            // Buscar Precio en la NUEVA tabla vinculada a MODELOS
            $precio_unitario = 0;
            $qPrecio = mysqli_query($link, "SELECT $col_precio as precio FROM precios_empleados WHERE id_modelos = $id_modelo");
            if ($rPrecio = mysqli_fetch_assoc($qPrecio)) {
                $precio_unitario = floatval($rPrecio['precio']);
            }

            // Guardar Deuda
            $total_pagar = $cantidad_terminada * $precio_unitario;
            if ($id_usuario > 0 && $total_pagar > 0) {
                $sqlPago = "INSERT INTO historial_destajos (id_usuario, id_mueble, etapa, cantidad_piezas, precio_unitario, total_pagar) VALUES ($id_usuario, $id_mueble, '$etapa_txt', $cantidad_terminada, $precio_unitario, $total_pagar)";
                mysqli_query($link, $sqlPago);
            }
        }
    }
}

header("Location: ../adm_registros.php");
exit();
?>
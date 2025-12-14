<?php
session_start();

// 1. SEGURIDAD BÁSICA
if (!isset($_SESSION['usuario']) || !isset($_SESSION['tipo'])) {
    header('Location: ../login.php');
    exit();
}

// 2. CONEXIÓN A BASE DE DATOS
include("conexion.php");

// Asegurar conexión si el include falla
if (!isset($link)) {
    $link = mysqli_connect('localhost', 'root', '', 'equipo', 3306);
}

// 3. PROCESAR SOLICITUDES
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Identificar acción (crear, editar, eliminar)
    $accion = isset($_POST['accion']) ? $_POST['accion'] : '';
    
    // === CASO 1: CREAR NUEVO PEDIDO ===
    if ($accion === 'crear') {
        $cliente = mysqli_real_escape_string($link, trim($_POST['cliente']));
        $destino = mysqli_real_escape_string($link, trim($_POST['destino']));
        $fecha_entrega = mysqli_real_escape_string($link, $_POST['fecha_entrega']);
        $comentarios = mysqli_real_escape_string($link, trim($_POST['comentarios']));
        
        $fecha_pedido = date('Y-m-d');
        $estatus = 'pendiente';

        $sql = "INSERT INTO pedidos (cliente_nombre, destino, fecha_pedido, fecha_entrega, estatus_pedido, comentarios) 
                VALUES ('$cliente', '$destino', '$fecha_pedido', '$fecha_entrega', '$estatus', '$comentarios')";

        if (mysqli_query($link, $sql)) {
            $nuevo_id = mysqli_insert_id($link);
            // Redirigir a agregar muebles directamente
            header("Location: ../adm_registros_registrar.php?id_pedido=" . $nuevo_id);
        } else {
            echo "Error al crear: " . mysqli_error($link);
        }
    }

    // === CASO 2: EDITAR PEDIDO EXISTENTE ===
    elseif ($accion === 'editar') {
        $id_pedido = mysqli_real_escape_string($link, $_POST['id_pedido']);
        $cliente = mysqli_real_escape_string($link, trim($_POST['cliente']));
        $destino = mysqli_real_escape_string($link, trim($_POST['destino']));
        $fecha_entrega = mysqli_real_escape_string($link, $_POST['fecha_entrega']);
        $comentarios = mysqli_real_escape_string($link, trim($_POST['comentarios']));

        $sql = "UPDATE pedidos SET 
                cliente_nombre = '$cliente', 
                destino = '$destino', 
                fecha_entrega = '$fecha_entrega', 
                comentarios = '$comentarios' 
                WHERE id_pedido = '$id_pedido'";

        if (mysqli_query($link, $sql)) {
            header("Location: ../adm_pedidos.php?msg=updated");
        } else {
            echo "Error al actualizar: " . mysqli_error($link);
        }
    }

    // === CASO 3: ELIMINAR PEDIDO ===
    elseif ($accion === 'eliminar') {
        $id_pedido = mysqli_real_escape_string($link, $_POST['id_pedido']);

        // Primero eliminamos los muebles asociados (si no hay ON DELETE CASCADE en la BD)
        // Nota: Si configuraste tu BD con llaves foráneas en cascada, este paso es automático, 
        // pero es buena práctica hacerlo explícito por seguridad.
        $sql_muebles = "DELETE FROM muebles WHERE id_pedido = '$id_pedido'";
        mysqli_query($link, $sql_muebles);

        // Luego eliminamos el pedido
        $sql = "DELETE FROM pedidos WHERE id_pedido = '$id_pedido'";

        if (mysqli_query($link, $sql)) {
            header("Location: ../adm_pedidos.php?msg=deleted");
        } else {
            echo "Error al eliminar: " . mysqli_error($link);
        }
    }

} else {
    header("Location: ../adm_pedidos.php");
    exit();
}
?>
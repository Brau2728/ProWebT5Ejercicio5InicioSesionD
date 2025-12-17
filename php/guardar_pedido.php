<?php
// php/guardar_pedido.php - Lógica completa (Crear, Editar, Eliminar y CAMBIAR ESTATUS)
session_start();

// 1. SEGURIDAD BÁSICA
if (!isset($_SESSION['usuario'])) {
    header('Location: ../login.php');
    exit();
}

// 2. CONEXIÓN A BASE DE DATOS
include("conexion.php");

// Asegurar conexión si el include falla (Backup)
if (!isset($link)) {
    // Ajusta esto si tu variable de conexión en conexion.php tiene otro nombre (ej: $con, $conn)
    // En tus archivos anteriores usabas db_query, pero aquí usamos mysqli nativo directo
    $link = mysqli_connect('localhost', 'root', '', 'equipo', 3306);
}

// 3. PROCESAR SOLICITUDES
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Identificar acción
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

        // Borramos muebles primero por limpieza
        $sql_muebles = "DELETE FROM muebles WHERE id_pedido = '$id_pedido'";
        mysqli_query($link, $sql_muebles);

        // Borramos el pedido
        $sql = "DELETE FROM pedidos WHERE id_pedido = '$id_pedido'";

        if (mysqli_query($link, $sql)) {
            header("Location: ../adm_pedidos.php?msg=deleted");
        } else {
            echo "Error al eliminar: " . mysqli_error($link);
        }
    }

    // === CASO 4: CAMBIAR ESTATUS (Despachar / Entregar) ===
    // *** ESTO ES LO QUE FALTABA ***
    elseif ($accion === 'cambiar_status') {
        $id_pedido = mysqli_real_escape_string($link, $_POST['id_pedido']);
        $nuevo_status = mysqli_real_escape_string($link, $_POST['nuevo_status']); // 'ruta' o 'entregado'

        $sql = "UPDATE pedidos SET estatus_pedido = '$nuevo_status' WHERE id_pedido = '$id_pedido'";

        if (mysqli_query($link, $sql)) {
            // Si el estatus es 'entregado', opcionalmente podríamos mover todos los muebles a estatus final
            // Pero por ahora solo cambiamos la orden para que el flujo visual funcione.
            header("Location: ../adm_pedidos.php?msg=status_changed");
        } else {
            echo "Error al cambiar estatus: " . mysqli_error($link);
        }
    }

} else {
    // Si intentan entrar directo sin POST
    header("Location: ../adm_pedidos.php");
    exit();
}
?>
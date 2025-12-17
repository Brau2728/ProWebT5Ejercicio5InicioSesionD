<?php
include("conexion.php");
if (!isset($link)) { $link = mysqli_connect('localhost', 'root', '', 'equipo', 3306); }

$id_user = intval($_GET['id']);
$estado = intval($_GET['estado']);

$sql = "SELECT h.*, m.mue_color, mo.modelos_nombre 
        FROM historial_destajos h
        JOIN muebles m ON h.id_mueble = m.id_muebles
        JOIN modelos mo ON m.id_modelos = mo.id_modelos
        WHERE h.id_usuario = $id_user AND h.semana_pagada = $estado
        ORDER BY h.fecha_termino DESC";

$res = mysqli_query($link, $sql);

if(mysqli_num_rows($res) > 0) {
    echo '<table class="detail-table">
            <thead><tr><th>Fecha</th><th>Mueble</th><th>Tarea</th><th>Cant.</th><th>Total</th></tr></thead>
            <tbody>';
    
    while($r = mysqli_fetch_assoc($res)) {
        $fecha = date('d/m H:i', strtotime($r['fecha_termino']));
        echo "<tr>
                <td>$fecha</td>
                <td>{$r['modelos_nombre']} <br><small style='color:#999'>{$r['mue_color']}</small></td>
                <td style='text-transform:capitalize'>{$r['etapa']}</td>
                <td style='text-align:center'>{$r['cantidad_piezas']}</td>
                <td style='color:#27ae60'>$".number_format($r['total_pagar'], 2)."</td>
              </tr>";
    }
    echo '</tbody></table>';
} else {
    echo "<p>No hay registros.</p>";
}
?>
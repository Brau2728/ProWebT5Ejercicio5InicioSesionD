<?php
// Intentar incluir la conexión
if (file_exists("conexion.php")) {
    include("conexion.php");
    
    if ($con) {
        echo "✅ ¡Conexión EXITOSA! La base de datos responde.";
    } else {
        echo "❌ Error: Se encontró el archivo, pero la conexión falló. <br>";
        echo "Detalle: " . mysqli_connect_error();
    }
} else {
    echo "❌ Error CRÍTICO: No encuentro el archivo 'conexion.php' en esta carpeta.";
}
?>
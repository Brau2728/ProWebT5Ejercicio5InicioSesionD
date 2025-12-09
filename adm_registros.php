<?php
session_start();

// Verificar si se ha iniciado sesión correctamente
if (!isset($_SESSION['usuario']) || !isset($_SESSION['tipo'])) {
    echo "Usuario no Logueado";
    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <meta name="description" content="Sistemas computacionales">
    <meta name="keywords" content="MySql, conexión, Wamp">
    <meta name="author" content="Ramirez Erik, Sistemas">

    <title>Administración de Muebles - Idealiza</title>
    <link rel="stylesheet" href="css/estilos_admin.css">
    <link rel="stylesheet" href="css/menu1.css">
    <link rel="icon" href="Img/Icons/logo-idealisa.ico" type="image/png">

    <!-- --------------------------------------------------------- -->
    <link rel="stylesheet" href="estilos/Wave2.css">
    <!-- Fuente Personalizada -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300&display=swap" rel="stylesheet">
   
</head>

<body>
<article>
    <?php include('php/header_admin.php');?>
    <?php include('php/menu_admin.php');?>
    
    <div id="content">
        <div id="section">
            <h2>Añadir registro</h2>
            <br>
            <p><a href="adm_registros_registrar.php" class="button2">Crear registro</a></p>
            <br>
            <h2>Muebles Registrados</h2>
            
            <?php
            include("php/conexion.php");
            
            // Consulta SQL para obtener los detalles de los muebles y los nombres de los modelos
            $sql = "SELECT m.id_muebles, mo.modelos_nombre, m.id_estatus_mueble, m.mue_precio, m.mue_cantidad, m.mue_color, m.mue_herraje
                    FROM muebles m
                    INNER JOIN modelos mo ON m.id_modelos = mo.id_modelos";
            
            $result = db_query($sql);
            ?>
            <table border="1" align="center">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre del Modelo</th>
                        <th>Estatus</th>
                        <th>Precio</th>
                        <th>Cantidad</th>
                        <th>Color</th>
                        <th>Herraje</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result && mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                    ?>
                            <tr>
                                <td><?php echo $row['id_muebles']; ?></td>
                                <td><?php echo $row['modelos_nombre']; ?></td>
                                <td>
                                    <?php
                                    switch ($row['id_estatus_mueble']) {
                                        case 1:
                                            echo 'Sin estado';
                                            break;
                                        case 2:
                                            echo 'Maquilado';
                                            break;
                                        case 3:
                                            echo 'Armado';
                                            break;
                                        case 4:
                                            echo 'Barnizado';
                                            break;
                                        case 5:
                                            echo 'Pintado';
                                            break;
                                        case 6:
                                            echo 'Adornado';
                                            break;
                                        case 7:
                                            echo 'Terminado';
                                            break;
                                        default:
                                            echo 'Desconocido';
                                            break;
                                    }
                                    ?>
                                </td>
                                <td><?php echo $row['mue_precio']; ?></td>
                                <td><?php echo $row['mue_cantidad']; ?></td>
                                <td><?php echo $row['mue_color']; ?></td>
                                <td><?php echo $row['mue_herraje']; ?></td>
                                <td>
                                    <a href="adm_registros_modificar.php?id=<?php echo $row['id_muebles']; ?>" class="button">Modificar</a>
                                </td>
                            </tr>
                    <?php
                        }
                    } else {
                        echo "<tr><td colspan='8'>No hay ningún registro</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>            
    </div>
    <div class="wave wave1"> </div>
    <div class="wave wave2"> </div>
    <div class="wave wave3"> </div>
    <div class="wave wave4"> </div>
</article>
    
    <?php include("php/footer.php"); ?>
    <script>
        function confirmarEliminacion() {
            if (confirm("¿Realmente desea eliminar el registro?")) {
                return true;
            }
            return false;
        }
    </script>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>


    
</body>
</html>

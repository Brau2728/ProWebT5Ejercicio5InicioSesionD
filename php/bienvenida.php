<?php
session_start();

// 1. SEGURIDAD
if (!isset($_SESSION['usuario']) || !isset($_SESSION['tipo'])) {
    header('Location: login.php');
    exit();
}

// 2. CONEXIÓN BLINDADA
$conexion = null;
$ruta = __DIR__ . '/php/conexion.php';
if(file_exists($ruta)) { include($ruta); if(isset($link)) $conexion = $link; if(isset($conn)) $conexion = $conn; }
if(!$conexion) { try { $conexion = mysqli_connect('localhost', 'root', '', 'equipo', 3306); } catch(Exception $e){} }

$total_pedidos = 0;
$total_modelos = 0;
$total_staff = 0;
$saludo_genero = "Bienvenido";

if($conexion) {
    // KPIs
    $q1 = mysqli_query($conexion, "SELECT COUNT(*) as c FROM muebles WHERE id_estatus_mueble < 7");
    if($r1 = mysqli_fetch_assoc($q1)) $total_pedidos = $r1['c'];

    $q2 = mysqli_query($conexion, "SELECT COUNT(*) as c FROM modelos");
    if($r2 = mysqli_fetch_assoc($q2)) $total_modelos = $r2['c'];

    $q3 = mysqli_query($conexion, "SELECT COUNT(*) as c FROM usuarios");
    if($r3 = mysqli_fetch_assoc($q3)) $total_staff = $r3['c'];

    // DETECTAR GÉNERO
    $id_u = isset($_SESSION['id_usuario']) ? $_SESSION['id_usuario'] : 0;
    $nom_u = $_SESSION['usuario'];
    
    // Intentar buscar por ID o Nombre
    $sqlSex = "SELECT usu_sexo FROM usuarios WHERE id_usuario = '$id_u' OR usu_nom = '$nom_u' LIMIT 1";
    $qSex = mysqli_query($conexion, $sqlSex);

    if($qSex && $rSex = mysqli_fetch_assoc($qSex)) {
        $sexo = strtolower($rSex['usu_sexo']); 
        if (strpos($sexo, 'fem') !== false || strpos($sexo, 'mujer') !== false) {
            $saludo_genero = "Bienvenida";
        }
    }
}

// 3. FECHA EN ESPAÑOL
$dias = array("Domingo","Lunes","Martes","Miércoles","Jueves","Viernes","Sábado");
$meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
$fecha_actual = $dias[date('w')] . " " . date('d') . " de " . $meses[date('n')-1] . " del " . date('Y');
?>
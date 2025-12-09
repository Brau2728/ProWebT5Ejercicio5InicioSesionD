-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 24-11-2023 a las 22:51:24
-- Versión del servidor: 10.4.28-MariaDB
-- Versión de PHP: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `equipo`
--
CREATE DATABASE IF NOT EXISTS `equipo` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `equipo`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estatus_muebles`
--

CREATE TABLE `estatus_muebles` (
  `id_estatus_mueble` int(11) NOT NULL,
  `id_muebles` int(11) DEFAULT NULL,
  `estatus_mueble` varchar(64) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inventario`
--

CREATE TABLE `inventario` (
  `id_inventario` int(11) NOT NULL,
  `id_termindado` int(11) DEFAULT NULL,
  `cantidad_inventario` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `modelos`
--

CREATE TABLE `modelos` (
  `id_modelos` int(11) NOT NULL,
  `nombre_modelos` varchar(64) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `muebles`
--

CREATE TABLE `muebles` (
  `id_muebles` int(11) NOT NULL,
  `id_modelos` int(11) DEFAULT NULL,
  `id_estatus_mueble` int(11) DEFAULT NULL,
  `mue_precio` int(11) DEFAULT NULL,
  `mue_cantidad` int(11) DEFAULT NULL,
  `mue_color` varchar(64) DEFAULT NULL,
  `mue_herraje` varchar(64) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `nomina`
--

CREATE TABLE `nomina` (
  `id_nomina` int(11) NOT NULL,
  `id_muebles` int(11) DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `fecha_nomina` date DEFAULT NULL,
  `nomina_extra` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `precios_empleados`
--

CREATE TABLE `precios_empleados` (
  `id_precios` int(11) NOT NULL,
  `id_muebles` int(11) DEFAULT NULL,
  `mue_precio_maquila` int(11) DEFAULT NULL,
  `mue_precio_armado` int(11) DEFAULT NULL,
  `mue_precio_barnizado` int(11) DEFAULT NULL,
  `mue_precio_pintado` int(11) DEFAULT NULL,
  `mue_precio_adornado` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `terminado`
--

CREATE TABLE `terminado` (
  `id_terminado` int(11) NOT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `id_muebles` int(11) DEFAULT NULL,
  `term_estacion_proceso` int(1) DEFAULT NULL,
  `id_inventario` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `Id_usuario` int(11) NOT NULL,
  `usu_nombre` varchar(64) DEFAULT NULL,
  `usu_apellido_p` varchar(64) DEFAULT NULL,
  `usu_apellido_m` varchar(64) DEFAULT NULL,
  `usu_fecha_nacimiento` date DEFAULT NULL,
  `usu_sexo` varchar(1) DEFAULT NULL,
  `usu_tipo_deusuario` varchar(64) DEFAULT NULL,
  `usu_puesto` varchar(64) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `estatus_muebles`
--
ALTER TABLE `estatus_muebles`
  ADD PRIMARY KEY (`id_estatus_mueble`),
  ADD KEY `fk_muebles2` (`id_muebles`);

--
-- Indices de la tabla `inventario`
--
ALTER TABLE `inventario`
  ADD PRIMARY KEY (`id_inventario`);

--
-- Indices de la tabla `modelos`
--
ALTER TABLE `modelos`
  ADD PRIMARY KEY (`id_modelos`);

--
-- Indices de la tabla `muebles`
--
ALTER TABLE `muebles`
  ADD PRIMARY KEY (`id_muebles`),
  ADD KEY `fk_modelos` (`id_modelos`);

--
-- Indices de la tabla `nomina`
--
ALTER TABLE `nomina`
  ADD PRIMARY KEY (`id_nomina`),
  ADD KEY `fk_usuarios` (`id_usuario`),
  ADD KEY `fk_muebles1` (`id_muebles`);

--
-- Indices de la tabla `precios_empleados`
--
ALTER TABLE `precios_empleados`
  ADD PRIMARY KEY (`id_precios`),
  ADD KEY `fk_muebles3` (`id_muebles`);

--
-- Indices de la tabla `terminado`
--
ALTER TABLE `terminado`
  ADD PRIMARY KEY (`id_terminado`),
  ADD KEY `fk_usuarios1` (`id_usuario`),
  ADD KEY `fk_muebles` (`id_muebles`),
  ADD KEY `fk_inventario` (`id_inventario`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`Id_usuario`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `estatus_muebles`
--
ALTER TABLE `estatus_muebles`
  MODIFY `id_estatus_mueble` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `inventario`
--
ALTER TABLE `inventario`
  MODIFY `id_inventario` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `modelos`
--
ALTER TABLE `modelos`
  MODIFY `id_modelos` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `muebles`
--
ALTER TABLE `muebles`
  MODIFY `id_muebles` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `nomina`
--
ALTER TABLE `nomina`
  MODIFY `id_nomina` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `precios_empleados`
--
ALTER TABLE `precios_empleados`
  MODIFY `id_precios` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `terminado`
--
ALTER TABLE `terminado`
  MODIFY `id_terminado` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `Id_usuario` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `estatus_muebles`
--
ALTER TABLE `estatus_muebles`
  ADD CONSTRAINT `fk_muebles2` FOREIGN KEY (`id_muebles`) REFERENCES `muebles` (`id_muebles`);

--
-- Filtros para la tabla `muebles`
--
ALTER TABLE `muebles`
  ADD CONSTRAINT `fk_modelos` FOREIGN KEY (`id_modelos`) REFERENCES `modelos` (`id_modelos`);

--
-- Filtros para la tabla `nomina`
--
ALTER TABLE `nomina`
  ADD CONSTRAINT `fk_muebles1` FOREIGN KEY (`id_muebles`) REFERENCES `muebles` (`id_muebles`),
  ADD CONSTRAINT `fk_usuarios` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`Id_usuario`);

--
-- Filtros para la tabla `precios_empleados`
--
ALTER TABLE `precios_empleados`
  ADD CONSTRAINT `fk_muebles3` FOREIGN KEY (`id_muebles`) REFERENCES `muebles` (`id_muebles`);

--
-- Filtros para la tabla `terminado`
--
ALTER TABLE `terminado`
  ADD CONSTRAINT `fk_inventario` FOREIGN KEY (`id_inventario`) REFERENCES `inventario` (`id_inventario`),
  ADD CONSTRAINT `fk_muebles` FOREIGN KEY (`id_muebles`) REFERENCES `muebles` (`id_muebles`),
  ADD CONSTRAINT `fk_usuarios1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`Id_usuario`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

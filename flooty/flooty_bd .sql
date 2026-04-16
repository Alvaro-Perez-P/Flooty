-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 06-04-2026 a las 20:24:41
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `flooty_bd`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `descripcion` text NOT NULL,
  `categoria` varchar(100) DEFAULT NULL,
  `precio_dia` decimal(10,2) NOT NULL,
  `fianza` decimal(10,2) DEFAULT NULL,
  `ciudad` varchar(100) DEFAULT NULL,
  `imagenes` text DEFAULT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `usuario` varchar(50) NOT NULL,
  `clave` varchar(255) NOT NULL,
  `rol` varchar(20) NOT NULL DEFAULT 'usuario',
  `nombre` varchar(100) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `imagen` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `email`, `usuario`, `clave`, `rol`, `nombre`, `telefono`, `direccion`, `imagen`) VALUES
(2, 'juani@gmail.com', 'juani', '$2y$10$S.E0r9Gb1O6XHlxI4PDfo.o/3tRO0YsIDgbIMMpqS1lPWm3Ce.Z16', 'usuario', '', NULL, NULL, NULL),
(3, 'pedro@gmail.com', 'pedro', '$2y$10$zdvY727QkH5AkEZ.cuwK3u249Feqvy8EWkb/BFQRAZ1sykJNpPP/a', 'usuario', '', NULL, NULL, NULL),
(4, 'mario@gmail.com', 'Mario', '$2y$10$tqMitxHhN34VJeJjI/MP6ee6WBLVhhBcN8MpxZ4OnrvKT5JkDEJlm', 'usuario', '', NULL, NULL, NULL),
(5, 'alvaro@perez.com', 'alvaro', '$2y$10$ocGwIBKs/h7Aco7ceu9SCOSgTS2Y6qDVh0AmU43MEyfy2EVDnjS6q', 'usuario', '', NULL, NULL, NULL),
(7, 'admin@admin.com', 'admin', '$2y$10$K2uD2kuj5C56TKpY9e8Iyeu7mc9LiOFAaNrspXZULmsQcogkN4grq', 'admin', '', NULL, NULL, NULL);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

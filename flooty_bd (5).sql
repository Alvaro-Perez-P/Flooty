-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 13-05-2026 a las 22:10:31
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
-- Estructura de tabla para la tabla `anuncios`
--

CREATE TABLE `anuncios` (
  `id` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `categoria` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `precio` decimal(10,2) NOT NULL,
  `imagen` varchar(255) DEFAULT NULL,
  `estado` enum('activo','inactivo') DEFAULT 'activo',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `anuncios`
--

INSERT INTO `anuncios` (`id`, `titulo`, `categoria`, `descripcion`, `precio`, `imagen`, `estado`, `created_at`) VALUES
(1, 'Botines Nike Mercurial', 'Botines', 'Botines profesionales para césped artificial', 89.99, 'botines.jpg', 'activo', '2026-05-08 08:00:02'),
(2, 'Camiseta Argentina', 'Camisetas', 'Camiseta titular temporada 2026', 59.99, 'argentina.jpg', 'activo', '2026-05-08 08:00:02'),
(3, 'Pelota Adidas', 'Pelotas', 'Pelota oficial tamaño 5', 35.50, 'pelota.jpg', 'inactivo', '2026-05-08 08:00:02');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`id`, `nombre`) VALUES
(1, 'Herramientas'),
(2, 'Tecnología'),
(3, 'Deporte'),
(5, 'Hogar'),
(6, 'Eventos'),
(7, 'Instrumentos Musicales');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `favoritos`
--

CREATE TABLE `favoritos` (
  `id` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `favoritos`
--

INSERT INTO `favoritos` (`id`, `id_usuario`, `id_producto`) VALUES
(7, 2, 3),
(5, 2, 4),
(9, 3, 5);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `imagenes_producto`
--

CREATE TABLE `imagenes_producto` (
  `id` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `ruta` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `likes`
--

CREATE TABLE `likes` (
  `id` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notificaciones`
--

CREATE TABLE `notificaciones` (
  `id` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_producto` int(11) DEFAULT NULL,
  `id_reserva` int(11) DEFAULT NULL,
  `tipo` varchar(50) NOT NULL,
  `mensaje` text NOT NULL,
  `leida` tinyint(1) DEFAULT 0,
  `fecha_creacion` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `notificaciones`
--

INSERT INTO `notificaciones` (`id`, `id_usuario`, `id_producto`, `id_reserva`, `tipo`, `mensaje`, `leida`, `fecha_creacion`) VALUES
(1, 3, 6, 6, 'nueva_reserva', 'juani quiere reservar tu producto: Bicicleta de carrera del 21/05/2026 al 30/05/2026.', 1, '2026-05-13 20:38:08'),
(2, 3, 6, 7, 'nueva_reserva', 'juani quiere reservar tu producto: Bicicleta de carrera del 13/06/2026 al 28/06/2026.', 1, '2026-05-13 20:43:45'),
(3, 3, 6, 8, 'nueva_reserva', 'juani quiere reservar tu producto: Bicicleta de carrera del 30/06/2026 al 01/08/2026.', 1, '2026-05-13 20:47:00'),
(4, 2, 6, 8, 'reserva_aceptada', 'Tu reserva fue aceptada. Producto: Bicicleta de carrera', 1, '2026-05-13 20:58:59'),
(5, 2, 6, 7, 'reserva_rechazada', 'Tu reserva fue rechazada. Producto: Bicicleta de carrera', 1, '2026-05-13 20:59:03'),
(6, 3, 6, 9, 'nueva_reserva', 'juani quiere reservar tu producto: Bicicleta de carrera del 16/05/2026 al 17/05/2026.', 1, '2026-05-13 21:23:05'),
(7, 3, 6, 10, 'nueva_reserva', 'juani quiere reservar tu producto: Bicicleta de carrera del 21/08/2026 al 30/08/2026.', 1, '2026-05-13 21:34:39'),
(8, 2, 5, 11, 'nueva_reserva', 'pedro quiere reservar tu producto: Tabla Surf del 27/05/2026 al 27/05/2026.', 1, '2026-05-13 21:40:06'),
(9, 3, 5, 11, 'reserva_aceptada', 'Tu reserva fue aceptada. Producto: Tabla Surf', 1, '2026-05-13 21:41:02'),
(10, 2, 5, 11, 'reserva_pagada', 'pedro ha pagado la reserva del producto: Tabla Surf.', 0, '2026-05-13 21:41:22'),
(11, 3, 7, 4, 'reserva_aceptada', 'Tu reserva fue aceptada. Producto: Piano', 1, '2026-05-13 21:56:58'),
(12, 2, 7, 4, 'reserva_pagada', 'pedro ha pagado 1.800,00 € por la reserva del producto: Piano. El cliente se pondrá en contacto para coordinar la entrega.', 0, '2026-05-13 21:57:18'),
(13, 3, 7, 4, 'pago_confirmado', 'Tu pago de 1.800,00 € por el producto Piano fue registrado correctamente. Ahora puedes contactar al dueño desde Mis reservas.', 1, '2026-05-13 21:57:18'),
(14, 2, 7, 2, 'reserva_pagada', 'pedro ha pagado 1.650,00 € por la reserva del producto: Piano. El cliente se pondrá en contacto para coordinar la entrega.', 0, '2026-05-13 22:01:42'),
(15, 3, 7, 2, 'pago_confirmado', 'Tu pago de 1.650,00 € por el producto Piano fue registrado correctamente. Ahora puedes contactar al dueño desde Mis reservas.', 0, '2026-05-13 22:01:42'),
(16, 2, 9, 12, 'nueva_reserva', 'pedro quiere reservar tu producto: Bicicleta para niños del 17/05/2026 al 18/05/2026.', 0, '2026-05-13 22:07:32'),
(17, 3, 9, 12, 'reserva_aceptada', 'Tu reserva fue aceptada. Producto: Bicicleta para niños', 0, '2026-05-13 22:07:53'),
(18, 2, 9, 12, 'reserva_pagada', 'pedro ha pagado 43,00 € por la reserva del producto: Bicicleta para niños. El cliente se pondrá en contacto para coordinar la entrega.', 0, '2026-05-13 22:08:05'),
(19, 3, 9, 12, 'pago_confirmado', 'Tu pago de 43,00 € por el producto Bicicleta para niños fue registrado correctamente. Ahora puedes contactar al dueño desde Mis reservas.', 0, '2026-05-13 22:08:05');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id` int(11) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `descripcion` text NOT NULL,
  `precio_dia` decimal(10,2) NOT NULL,
  `fianza` decimal(10,2) DEFAULT NULL,
  `ciudad` varchar(100) DEFAULT NULL,
  `imagenes` text DEFAULT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `id_categoria` int(11) DEFAULT NULL,
  `id_usuario` int(11) NOT NULL,
  `estado` varchar(20) NOT NULL DEFAULT 'activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id`, `titulo`, `descripcion`, `precio_dia`, `fianza`, `ciudad`, `imagenes`, `fecha_creacion`, `id_categoria`, `id_usuario`, `estado`) VALUES
(2, 'Camara fotografica', 'Camara profecional', 15.00, 50.00, 'Malaga', '[\"imagenes\\/productos\\/1776960987_0_69ea45db8e001.jpeg\"]', '2026-04-23 16:16:27', 2, 2, 'activo'),
(3, 'Bicicleta de montaña', 'Muy buena para pasear en el campo', 25.00, 100.00, 'Malaga', '[\"imagenes\\/productos\\/1776961480_0_69ea47c847947.jpeg\"]', '2026-04-23 16:24:40', 3, 2, 'activo'),
(4, 'Patinete', 'Excelente para pasear pos la ciudad', 25.00, 150.00, 'Malaga', '[\"imagenes\\/productos\\/1776961657_0_69ea487908329.jpeg\"]', '2026-04-23 16:27:37', 2, 3, 'activo'),
(5, 'Tabla Surf', 'Ideal para divertirse', 15.00, 20.00, 'Malaga', '[\"imagenes\\/productos\\/1776962073_0_69ea4a1978619.webp\",\"imagenes\\/productos\\/1776962073_1_69ea4a1978cb5.webp\",\"imagenes\\/productos\\/1776962073_2_69ea4a19793f7.webp\"]', '2026-04-23 16:34:33', 3, 2, 'activo'),
(6, 'Bicicleta de carrera', 'Ideal para paseos', 15.00, 100.00, 'Malaga', '[\"imagenes\\/productos\\/1777018083_0_69eb24e36c31b.jpeg\"]', '2026-04-24 08:08:03', 3, 3, 'activo'),
(7, 'Piano', 'Piano de concirto. Especial para profecionales', 150.00, 1500.00, 'Malaga', '[\"imagenes\\/productos\\/1778601682_0_6a034ed29d400.webp\",\"imagenes\\/productos\\/1778601682_1_6a034ed29dc51.webp\",\"imagenes\\/productos\\/1778601682_2_6a034ed29e2ab.webp\"]', '2026-05-12 16:01:22', 6, 2, 'activo'),
(9, 'Bicicleta para niños', 'Excelente tla para niños entre 5 y 10 años', 10.00, 23.00, 'Malaga', '[\"imagenes\\/productos\\/1778691782_0_6a04aec62843d.jpg\"]', '2026-05-13 17:03:02', 3, 2, 'activo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reservas`
--

CREATE TABLE `reservas` (
  `id` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `id_solicitante` int(11) NOT NULL,
  `id_propietario` int(11) NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `dias` int(11) NOT NULL,
  `precio_dia` decimal(10,2) NOT NULL,
  `fianza` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total` decimal(10,2) NOT NULL,
  `estado` enum('pendiente','aceptada','rechazada','cancelada','finalizada','pagada') NOT NULL DEFAULT 'pendiente',
  `mensaje` text DEFAULT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `reservas`
--

INSERT INTO `reservas` (`id`, `id_producto`, `id_solicitante`, `id_propietario`, `fecha_inicio`, `fecha_fin`, `dias`, `precio_dia`, `fianza`, `total`, `estado`, `mensaje`, `fecha_creacion`) VALUES
(1, 6, 2, 3, '2026-05-10', '2026-05-10', 1, 15.00, 100.00, 115.00, 'aceptada', '', '2026-05-10 10:33:05'),
(2, 7, 3, 2, '2026-05-12', '2026-05-12', 1, 150.00, 1500.00, 1650.00, '', '', '2026-05-12 17:06:28'),
(3, 5, 3, 2, '2026-05-13', '2026-05-14', 2, 15.00, 20.00, 50.00, 'pendiente', 'Hola esta disponible', '2026-05-13 17:20:10'),
(4, 7, 3, 2, '2026-05-13', '2026-05-14', 2, 150.00, 1500.00, 1800.00, '', '', '2026-05-13 17:58:29'),
(5, 6, 2, 3, '2026-05-13', '2026-05-13', 1, 15.00, 100.00, 115.00, 'pendiente', '', '2026-05-13 18:04:54'),
(6, 6, 2, 3, '2026-05-21', '2026-05-30', 10, 15.00, 100.00, 250.00, 'pendiente', '', '2026-05-13 18:38:08'),
(7, 6, 2, 3, '2026-06-13', '2026-06-28', 16, 15.00, 100.00, 340.00, 'rechazada', '', '2026-05-13 18:43:45'),
(8, 6, 2, 3, '2026-06-30', '2026-08-01', 33, 15.00, 100.00, 595.00, 'aceptada', '', '2026-05-13 18:47:00'),
(9, 6, 2, 3, '2026-05-16', '2026-05-17', 2, 15.00, 100.00, 130.00, 'pendiente', '', '2026-05-13 19:23:05'),
(10, 6, 2, 3, '2026-08-21', '2026-08-30', 10, 15.00, 100.00, 250.00, 'pendiente', '', '2026-05-13 19:34:39'),
(11, 5, 3, 2, '2026-05-27', '2026-05-27', 1, 15.00, 20.00, 35.00, '', '', '2026-05-13 19:40:06'),
(12, 9, 3, 2, '2026-05-17', '2026-05-18', 2, 10.00, 23.00, 43.00, 'pagada', '', '2026-05-13 20:07:32');

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
  `imagen` varchar(255) DEFAULT NULL,
  `activo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `email`, `usuario`, `clave`, `rol`, `nombre`, `telefono`, `direccion`, `imagen`, `activo`) VALUES
(2, 'juani@gmail.com', 'juani', '$2y$10$S.E0r9Gb1O6XHlxI4PDfo.o/3tRO0YsIDgbIMMpqS1lPWm3Ce.Z16', 'usuario', 'Juan Ignacio', '02494014320', 'Pasteur 23', 'img/avatares/avatar_2_1778606254.jpg', 1),
(3, 'pedro@gmail.com', 'pedro', '$2y$10$zdvY727QkH5AkEZ.cuwK3u249Feqvy8EWkb/BFQRAZ1sykJNpPP/a', 'usuario', '', NULL, NULL, NULL, 1),
(4, 'mario@gmail.com', 'Mario', '$2y$10$tqMitxHhN34VJeJjI/MP6ee6WBLVhhBcN8MpxZ4OnrvKT5JkDEJlm', 'usuario', '', NULL, NULL, NULL, 1),
(5, 'alvaro@perez.com', 'alvaro', '$2y$10$ocGwIBKs/h7Aco7ceu9SCOSgTS2Y6qDVh0AmU43MEyfy2EVDnjS6q', 'usuario', '', NULL, NULL, NULL, 1),
(7, 'admin@admin.com', 'admin', '$2y$10$K2uD2kuj5C56TKpY9e8Iyeu7mc9LiOFAaNrspXZULmsQcogkN4grq', 'admin', 'Administrador', '647831936', 'Calle Carlos Frontaura, 3', 'img/avatares/avatar_7_1778613876.jpg', 1);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `anuncios`
--
ALTER TABLE `anuncios`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `favoritos`
--
ALTER TABLE `favoritos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unico_favorito` (`id_usuario`,`id_producto`);

--
-- Indices de la tabla `imagenes_producto`
--
ALTER TABLE `imagenes_producto`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `likes`
--
ALTER TABLE `likes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id_usuario` (`id_usuario`,`id_producto`);

--
-- Indices de la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_categoria` (`id_categoria`);

--
-- Indices de la tabla `reservas`
--
ALTER TABLE `reservas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_producto` (`id_producto`),
  ADD KEY `idx_solicitante` (`id_solicitante`),
  ADD KEY `idx_propietario` (`id_propietario`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `anuncios`
--
ALTER TABLE `anuncios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `favoritos`
--
ALTER TABLE `favoritos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `imagenes_producto`
--
ALTER TABLE `imagenes_producto`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `likes`
--
ALTER TABLE `likes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `reservas`
--
ALTER TABLE `reservas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `productos`
--
ALTER TABLE `productos`
  ADD CONSTRAINT `fk_categoria` FOREIGN KEY (`id_categoria`) REFERENCES `categorias` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `reservas`
--
ALTER TABLE `reservas`
  ADD CONSTRAINT `fk_reserva_producto` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_reserva_propietario` FOREIGN KEY (`id_propietario`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_reserva_solicitante` FOREIGN KEY (`id_solicitante`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

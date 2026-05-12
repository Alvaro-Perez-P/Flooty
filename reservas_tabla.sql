-- --------------------------------------------------------
-- Tabla: reservas
-- --------------------------------------------------------
CREATE TABLE `reservas` (
  `id`              int(11)        NOT NULL AUTO_INCREMENT,
  `id_producto`     int(11)        NOT NULL,
  `id_solicitante`  int(11)        NOT NULL,   -- usuario que alquila
  `id_propietario`  int(11)        NOT NULL,   -- dueño del producto
  `fecha_inicio`    date           NOT NULL,
  `fecha_fin`       date           NOT NULL,
  `dias`            int(11)        NOT NULL,
  `precio_dia`      decimal(10,2)  NOT NULL,
  `fianza`          decimal(10,2)  NOT NULL DEFAULT 0.00,
  `total`           decimal(10,2)  NOT NULL,
  `estado`          enum('pendiente','aceptada','rechazada','cancelada','completada')
                                   NOT NULL DEFAULT 'pendiente',
  `mensaje`         text           DEFAULT NULL,   -- mensaje opcional del solicitante
  `fecha_creacion`  timestamp      NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_producto`    (`id_producto`),
  KEY `idx_solicitante` (`id_solicitante`),
  KEY `idx_propietario` (`id_propietario`),
  -- Evita que el mismo usuario reserve el mismo producto en fechas solapadas
  -- (la validación de solapamiento se hace en PHP, esto solo es referencial)
  CONSTRAINT `fk_reserva_producto`    FOREIGN KEY (`id_producto`)    REFERENCES `productos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_reserva_solicitante` FOREIGN KEY (`id_solicitante`) REFERENCES `usuarios`  (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_reserva_propietario` FOREIGN KEY (`id_propietario`) REFERENCES `usuarios`  (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

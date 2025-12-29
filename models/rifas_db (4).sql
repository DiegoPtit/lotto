-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 25-12-2025 a las 16:39:16
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
-- Base de datos: `rifas_db`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `actor_type` enum('usuario','jugador','sistema') NOT NULL,
  `actor_id` bigint(20) UNSIGNED DEFAULT NULL,
  `accion` varchar(255) NOT NULL,
  `entidad` varchar(100) DEFAULT NULL,
  `entidad_id` bigint(20) UNSIGNED DEFAULT NULL,
  `datos` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`datos`)),
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `boletos`
--

CREATE TABLE `boletos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `codigo` varchar(100) NOT NULL,
  `id_rifa` bigint(20) UNSIGNED NOT NULL,
  `id_jugador` bigint(20) UNSIGNED NOT NULL,
  `cantidad_numeros` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `total_precio` decimal(18,2) NOT NULL DEFAULT 0.00,
  `estado` enum('reservado','pagado','anulado','reembolsado','ganador') NOT NULL DEFAULT 'reservado',
  `acepta_condiciones` tinyint(1) NOT NULL DEFAULT 0,
  `reserved_until` datetime DEFAULT NULL,
  `reserva_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Disparadores `boletos`
--
DELIMITER $$
CREATE TRIGGER `trg_audit_boletos_update` AFTER UPDATE ON `boletos` FOR EACH ROW BEGIN
  INSERT INTO audit_logs(
    actor_type, accion, entidad, entidad_id, datos, created_at
  ) VALUES (
    'sistema',
    'update',
    'boletos',
    NEW.id,
    JSON_OBJECT(
      'estado_old', OLD.estado,
      'estado_new', NEW.estado
    ),
    NOW()
  );
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_boletos_after_delete_soft` AFTER UPDATE ON `boletos` FOR EACH ROW BEGIN
  IF NEW.is_deleted = 1 AND OLD.is_deleted = 0 THEN
    UPDATE boleto_numeros
    SET is_deleted = 1,
        deleted_at = NOW()
    WHERE id_boleto = NEW.id;
  END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_boletos_before_insert` BEFORE INSERT ON `boletos` FOR EACH ROW BEGIN
  IF NEW.estado = 'reservado' THEN
    IF NEW.reserved_until IS NULL THEN
      SIGNAL SQLSTATE '45000'
      SET MESSAGE_TEXT = 'Un boleto reservado debe tener reserved_until';
    END IF;

    IF NEW.reserved_until <= NOW() THEN
      SIGNAL SQLSTATE '45000'
      SET MESSAGE_TEXT = 'reserved_until debe ser una fecha futura';
    END IF;
  END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_boletos_before_insert_precio` BEFORE INSERT ON `boletos` FOR EACH ROW BEGIN
  DECLARE v_precio DECIMAL(18,2);

  SELECT precio_boleto
  INTO v_precio
  FROM rifas
  WHERE id = NEW.id_rifa;

  SET NEW.total_precio = NEW.cantidad_numeros * v_precio;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `boleto_numeros`
--

CREATE TABLE `boleto_numeros` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `id_boleto` bigint(20) UNSIGNED NOT NULL,
  `numero` varchar(100) NOT NULL,
  `is_golden` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comentarios`
--

CREATE TABLE `comentarios` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `id_testimonio` bigint(20) UNSIGNED NOT NULL,
  `id_comentario_padre` bigint(20) UNSIGNED DEFAULT NULL,
  `nombre` varchar(200) NOT NULL,
  `mensaje` text NOT NULL,
  `contador_likes` int(10) UNSIGNED DEFAULT 0,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `evidencia_entrega`
--

CREATE TABLE `evidencia_entrega` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `id_premio` bigint(20) UNSIGNED DEFAULT NULL,
  `id_boleto` bigint(20) UNSIGNED DEFAULT NULL,
  `url_media` varchar(255) DEFAULT NULL,
  `tipo_media` enum('foto','video','documento','otro') DEFAULT 'foto',
  `descripcion` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `jugadores`
--

CREATE TABLE `jugadores` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `cedula` varchar(50) DEFAULT NULL,
  `nombre` varchar(200) NOT NULL,
  `pais` varchar(50) DEFAULT 'VE',
  `telefono` varchar(50) DEFAULT NULL,
  `correo` varchar(200) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `metodos_pago`
--

CREATE TABLE `metodos_pago` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tipo_id` tinyint(3) UNSIGNED NOT NULL,
  `banco` varchar(150) DEFAULT NULL,
  `titular` varchar(200) DEFAULT NULL,
  `cedula` varchar(50) DEFAULT NULL,
  `telefono` varchar(50) DEFAULT NULL,
  `nro_cuenta` varchar(100) DEFAULT NULL,
  `visibilidad` enum('publica','privada') NOT NULL DEFAULT 'publica',
  `id_operador_registro` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `metodos_pago`
--

INSERT INTO `metodos_pago` (`id`, `tipo_id`, `banco`, `titular`, `cedula`, `telefono`, `nro_cuenta`, `visibilidad`, `id_operador_registro`, `created_at`) VALUES
(1, 1, 'Banesco', 'DIEGO JOSE PETIT ACERO', '31407532', '04245630569', NULL, 'publica', 1, '2025-12-23 18:49:06');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `metodos_pago_tipo`
--

CREATE TABLE `metodos_pago_tipo` (
  `id` tinyint(3) UNSIGNED NOT NULL,
  `descripcion` varchar(100) NOT NULL,
  `has_banco` tinyint(1) NOT NULL DEFAULT 0,
  `has_titular` tinyint(1) NOT NULL DEFAULT 0,
  `has_cedula` tinyint(1) NOT NULL DEFAULT 0,
  `has_telefono` tinyint(1) NOT NULL DEFAULT 0,
  `has_nro_cuenta` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `metodos_pago_tipo`
--

INSERT INTO `metodos_pago_tipo` (`id`, `descripcion`, `has_banco`, `has_titular`, `has_cedula`, `has_telefono`, `has_nro_cuenta`) VALUES
(1, 'PAGO MOVIL', 1, 1, 1, 1, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pagos`
--

CREATE TABLE `pagos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `id_boleto` bigint(20) UNSIGNED NOT NULL,
  `id_jugador` bigint(20) UNSIGNED DEFAULT NULL,
  `monto` decimal(18,2) NOT NULL,
  `moneda` varchar(10) DEFAULT 'VES',
  `transaction_id` varchar(255) DEFAULT NULL,
  `id_metodo_pago` bigint(20) UNSIGNED DEFAULT NULL,
  `estado` enum('pending','confirmed','failed','refunded') NOT NULL DEFAULT 'pending',
  `comprobante_url` varchar(255) DEFAULT NULL,
  `notas` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Disparadores `pagos`
--
DELIMITER $$
CREATE TRIGGER `trg_pagos_after_update` AFTER UPDATE ON `pagos` FOR EACH ROW BEGIN
  IF NEW.estado = 'confirmed' AND OLD.estado <> 'confirmed' THEN
    UPDATE boletos
    SET estado = 'pagado',
        updated_at = NOW()
    WHERE id = NEW.id_boleto;
  END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_pagos_before_insert` BEFORE INSERT ON `pagos` FOR EACH ROW BEGIN
  DECLARE v_estado VARCHAR(20);

  SELECT estado INTO v_estado
  FROM boletos
  WHERE id = NEW.id_boleto;

  IF v_estado IN ('anulado','reembolsado') THEN
    SIGNAL SQLSTATE '45000'
    SET MESSAGE_TEXT = 'No se puede pagar un boleto anulado o reembolsado';
  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `politicas`
--

CREATE TABLE `politicas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tipo` enum('PRIVACIDAD','RESPONSABILIDAD','DESARROLLADOR','LEGAL','OTRO') NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `descripcion` text NOT NULL,
  `id_operador_registro` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `premios`
--

CREATE TABLE `premios` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `id_rifa` bigint(20) UNSIGNED NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `valor_estimado` decimal(18,2) DEFAULT NULL,
  `orden` int(11) NOT NULL DEFAULT 1,
  `entregado` tinyint(1) NOT NULL DEFAULT 0,
  `entregado_en` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rifas`
--

CREATE TABLE `rifas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `top_message` varchar(255) DEFAULT NULL,
  `titulo` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `precio_boleto` decimal(18,2) NOT NULL DEFAULT 0.00,
  `moneda` varchar(10) DEFAULT 'VES',
  `img` varchar(255) DEFAULT NULL,
  `max_numeros` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `estado` enum('borrador','activa','cerrada','sorteada','cancelada') NOT NULL DEFAULT 'borrador',
  `id_operador_registro` bigint(20) UNSIGNED NOT NULL,
  `fecha_inicio` datetime DEFAULT NULL,
  `fecha_fin` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id` tinyint(3) UNSIGNED NOT NULL,
  `nombre` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sorteos`
--

CREATE TABLE `sorteos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `id_rifa` bigint(20) UNSIGNED NOT NULL,
  `fecha_sorteo` datetime NOT NULL,
  `metodo_sorteo` varchar(100) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sorteos_ganadores`
--

CREATE TABLE `sorteos_ganadores` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `id_sorteo` bigint(20) UNSIGNED NOT NULL,
  `id_boleto` bigint(20) UNSIGNED NOT NULL,
  `id_premio` bigint(20) UNSIGNED DEFAULT NULL,
  `numero_ganador` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Disparadores `sorteos_ganadores`
--
DELIMITER $$
CREATE TRIGGER `trg_sorteos_ganadores_after_insert_boleto` AFTER INSERT ON `sorteos_ganadores` FOR EACH ROW BEGIN
  UPDATE boletos
  SET estado = 'ganador',
      updated_at = NOW()
  WHERE id = NEW.id_boleto;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_sorteos_ganadores_after_insert_rifa` AFTER INSERT ON `sorteos_ganadores` FOR EACH ROW BEGIN
  DECLARE v_id_rifa BIGINT UNSIGNED;

  SELECT b.id_rifa
  INTO v_id_rifa
  FROM boletos b
  WHERE b.id = NEW.id_boleto
  LIMIT 1;

  UPDATE rifas
  SET estado = 'sorteada',
      updated_at = NOW()
  WHERE id = v_id_rifa
    AND estado <> 'sorteada';
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_sorteos_ganadores_before_insert` BEFORE INSERT ON `sorteos_ganadores` FOR EACH ROW BEGIN
  IF EXISTS (
    SELECT 1 FROM sorteos_ganadores
    WHERE id_boleto = NEW.id_boleto
  ) THEN
    SIGNAL SQLSTATE '45000'
    SET MESSAGE_TEXT = 'Este boleto ya fue marcado como ganador';
  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `terms_acceptances`
--

CREATE TABLE `terms_acceptances` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `id_terms_version` bigint(20) UNSIGNED NOT NULL,
  `id_boleto` bigint(20) UNSIGNED NOT NULL,
  `id_jugador` bigint(20) UNSIGNED NOT NULL,
  `aceptado_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `terms_versions`
--

CREATE TABLE `terms_versions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `contenido` text NOT NULL,
  `version` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `testimonios`
--

CREATE TABLE `testimonios` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `id_sorteo` bigint(20) UNSIGNED NOT NULL,
  `id_jugador_ganador` bigint(20) UNSIGNED NOT NULL,
  `url_media` varchar(255) DEFAULT NULL,
  `titulo` varchar(80) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `contador_likes` int(10) UNSIGNED DEFAULT 0,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `correo` varchar(200) NOT NULL,
  `password_hash` varchar(255) DEFAULT NULL,
  `rol_id` tinyint(3) UNSIGNED NOT NULL,
  `google_id` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `correo`, `password_hash`, `rol_id`, `google_id`, `created_at`, `updated_at`, `is_deleted`, `deleted_at`) VALUES
(1, 'Admin Temp', 'diegojptit@gmail.com', '$2y$13$cNPlocxRQpOcIxErNdc4oeQ9tHmaXa7xiR7WPBk6Bc55P/hmjVzc2', 1, NULL, '2025-12-23 20:26:57', NULL, 0, NULL);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vw_boletos_completo`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vw_boletos_completo` (
`id` bigint(20) unsigned
,`codigo` varchar(100)
,`id_rifa` bigint(20) unsigned
,`rifa_titulo` varchar(255)
,`id_jugador` bigint(20) unsigned
,`jugador_nombre` varchar(200)
,`cantidad_numeros` int(10) unsigned
,`total_precio` decimal(18,2)
,`estado` enum('reservado','pagado','anulado','reembolsado','ganador')
,`created_at` timestamp
);

-- --------------------------------------------------------

--
-- Estructura para la vista `vw_boletos_completo`
--
DROP TABLE IF EXISTS `vw_boletos_completo`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_boletos_completo`  AS SELECT `b`.`id` AS `id`, `b`.`codigo` AS `codigo`, `b`.`id_rifa` AS `id_rifa`, `r`.`titulo` AS `rifa_titulo`, `b`.`id_jugador` AS `id_jugador`, `j`.`nombre` AS `jugador_nombre`, `b`.`cantidad_numeros` AS `cantidad_numeros`, `b`.`total_precio` AS `total_precio`, `b`.`estado` AS `estado`, `b`.`created_at` AS `created_at` FROM ((`boletos` `b` join `rifas` `r` on(`r`.`id` = `b`.`id_rifa`)) join `jugadores` `j` on(`j`.`id` = `b`.`id_jugador`)) ;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_audit_actor` (`actor_type`,`actor_id`),
  ADD KEY `idx_audit_entidad` (`entidad`,`entidad_id`);

--
-- Indices de la tabla `boletos`
--
ALTER TABLE `boletos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_boletos_rifa` (`id_rifa`),
  ADD KEY `idx_boletos_jugador` (`id_jugador`),
  ADD KEY `idx_boletos_estado` (`estado`),
  ADD KEY `idx_boletos_reserved_until` (`reserved_until`);

--
-- Indices de la tabla `boleto_numeros`
--
ALTER TABLE `boleto_numeros`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_boleto_numeros_boleto` (`id_boleto`),
  ADD KEY `idx_boleto_numeros_numero` (`numero`);

--
-- Indices de la tabla `comentarios`
--
ALTER TABLE `comentarios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_comentarios_testimonio` (`id_testimonio`),
  ADD KEY `fk_comentarios_padre` (`id_comentario_padre`);

--
-- Indices de la tabla `evidencia_entrega`
--
ALTER TABLE `evidencia_entrega`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_evidencia_premio` (`id_premio`),
  ADD KEY `idx_evidencia_boleto` (`id_boleto`);

--
-- Indices de la tabla `jugadores`
--
ALTER TABLE `jugadores`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `correo` (`correo`),
  ADD KEY `idx_jugadores_cedula` (`cedula`);

--
-- Indices de la tabla `metodos_pago`
--
ALTER TABLE `metodos_pago`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_metodos_pago_operador` (`id_operador_registro`),
  ADD KEY `idx_metodos_pago_tipo` (`tipo_id`);

--
-- Indices de la tabla `metodos_pago_tipo`
--
ALTER TABLE `metodos_pago_tipo`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_pagos_boleto` (`id_boleto`),
  ADD KEY `fk_pagos_jugador` (`id_jugador`),
  ADD KEY `fk_pagos_metodo` (`id_metodo_pago`),
  ADD KEY `idx_pagos_estado` (`estado`),
  ADD KEY `idx_pagos_transaction` (`transaction_id`);

--
-- Indices de la tabla `politicas`
--
ALTER TABLE `politicas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_politicas_operador` (`id_operador_registro`);

--
-- Indices de la tabla `premios`
--
ALTER TABLE `premios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_premios_rifa` (`id_rifa`);

--
-- Indices de la tabla `rifas`
--
ALTER TABLE `rifas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_rifas_estado` (`estado`),
  ADD KEY `idx_rifas_operador` (`id_operador_registro`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `sorteos`
--
ALTER TABLE `sorteos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_sorteos_rifa` (`id_rifa`);

--
-- Indices de la tabla `sorteos_ganadores`
--
ALTER TABLE `sorteos_ganadores`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_premio` (`id_premio`),
  ADD KEY `idx_sg_sorteo` (`id_sorteo`),
  ADD KEY `idx_sg_boleto` (`id_boleto`);

--
-- Indices de la tabla `terms_acceptances`
--
ALTER TABLE `terms_acceptances`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_terms_version` (`id_terms_version`),
  ADD KEY `idx_ta_boleto` (`id_boleto`),
  ADD KEY `idx_ta_jugador` (`id_jugador`);

--
-- Indices de la tabla `terms_versions`
--
ALTER TABLE `terms_versions`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `testimonios`
--
ALTER TABLE `testimonios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_testimonios_sorteo` (`id_sorteo`),
  ADD KEY `fk_testimonios_jugador` (`id_jugador_ganador`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `correo` (`correo`),
  ADD KEY `fk_usuarios_roles` (`rol_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `boletos`
--
ALTER TABLE `boletos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `boleto_numeros`
--
ALTER TABLE `boleto_numeros`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `comentarios`
--
ALTER TABLE `comentarios`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `evidencia_entrega`
--
ALTER TABLE `evidencia_entrega`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `jugadores`
--
ALTER TABLE `jugadores`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `metodos_pago`
--
ALTER TABLE `metodos_pago`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `metodos_pago_tipo`
--
ALTER TABLE `metodos_pago_tipo`
  MODIFY `id` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `pagos`
--
ALTER TABLE `pagos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `politicas`
--
ALTER TABLE `politicas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `premios`
--
ALTER TABLE `premios`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `rifas`
--
ALTER TABLE `rifas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sorteos`
--
ALTER TABLE `sorteos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `sorteos_ganadores`
--
ALTER TABLE `sorteos_ganadores`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `terms_acceptances`
--
ALTER TABLE `terms_acceptances`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `terms_versions`
--
ALTER TABLE `terms_versions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `testimonios`
--
ALTER TABLE `testimonios`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `boletos`
--
ALTER TABLE `boletos`
  ADD CONSTRAINT `fk_boletos_jugadores` FOREIGN KEY (`id_jugador`) REFERENCES `jugadores` (`id`),
  ADD CONSTRAINT `fk_boletos_rifas` FOREIGN KEY (`id_rifa`) REFERENCES `rifas` (`id`);

--
-- Filtros para la tabla `boleto_numeros`
--
ALTER TABLE `boleto_numeros`
  ADD CONSTRAINT `fk_boleto_numeros_boleto` FOREIGN KEY (`id_boleto`) REFERENCES `boletos` (`id`);

--
-- Filtros para la tabla `comentarios`
--
ALTER TABLE `comentarios`
  ADD CONSTRAINT `fk_comentarios_padre` FOREIGN KEY (`id_comentario_padre`) REFERENCES `comentarios` (`id`),
  ADD CONSTRAINT `fk_comentarios_testimonio` FOREIGN KEY (`id_testimonio`) REFERENCES `testimonios` (`id`);

--
-- Filtros para la tabla `evidencia_entrega`
--
ALTER TABLE `evidencia_entrega`
  ADD CONSTRAINT `fk_evidencia_boleto` FOREIGN KEY (`id_boleto`) REFERENCES `boletos` (`id`),
  ADD CONSTRAINT `fk_evidencia_premio` FOREIGN KEY (`id_premio`) REFERENCES `premios` (`id`);

--
-- Filtros para la tabla `metodos_pago`
--
ALTER TABLE `metodos_pago`
  ADD CONSTRAINT `fk_metodos_pago_operador` FOREIGN KEY (`id_operador_registro`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `fk_metodos_pago_tipo` FOREIGN KEY (`tipo_id`) REFERENCES `metodos_pago_tipo` (`id`);

--
-- Filtros para la tabla `pagos`
--
ALTER TABLE `pagos`
  ADD CONSTRAINT `fk_pagos_boleto` FOREIGN KEY (`id_boleto`) REFERENCES `boletos` (`id`),
  ADD CONSTRAINT `fk_pagos_jugador` FOREIGN KEY (`id_jugador`) REFERENCES `jugadores` (`id`),
  ADD CONSTRAINT `fk_pagos_metodo` FOREIGN KEY (`id_metodo_pago`) REFERENCES `metodos_pago` (`id`);

--
-- Filtros para la tabla `politicas`
--
ALTER TABLE `politicas`
  ADD CONSTRAINT `fk_politicas_operador` FOREIGN KEY (`id_operador_registro`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `premios`
--
ALTER TABLE `premios`
  ADD CONSTRAINT `fk_premios_rifas` FOREIGN KEY (`id_rifa`) REFERENCES `rifas` (`id`);

--
-- Filtros para la tabla `rifas`
--
ALTER TABLE `rifas`
  ADD CONSTRAINT `fk_rifas_operador` FOREIGN KEY (`id_operador_registro`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `sorteos`
--
ALTER TABLE `sorteos`
  ADD CONSTRAINT `fk_sorteos_rifa` FOREIGN KEY (`id_rifa`) REFERENCES `rifas` (`id`);

--
-- Filtros para la tabla `sorteos_ganadores`
--
ALTER TABLE `sorteos_ganadores`
  ADD CONSTRAINT `sorteos_ganadores_ibfk_1` FOREIGN KEY (`id_sorteo`) REFERENCES `sorteos` (`id`),
  ADD CONSTRAINT `sorteos_ganadores_ibfk_2` FOREIGN KEY (`id_boleto`) REFERENCES `boletos` (`id`),
  ADD CONSTRAINT `sorteos_ganadores_ibfk_3` FOREIGN KEY (`id_premio`) REFERENCES `premios` (`id`);

--
-- Filtros para la tabla `terms_acceptances`
--
ALTER TABLE `terms_acceptances`
  ADD CONSTRAINT `terms_acceptances_ibfk_1` FOREIGN KEY (`id_terms_version`) REFERENCES `terms_versions` (`id`),
  ADD CONSTRAINT `terms_acceptances_ibfk_2` FOREIGN KEY (`id_boleto`) REFERENCES `boletos` (`id`),
  ADD CONSTRAINT `terms_acceptances_ibfk_3` FOREIGN KEY (`id_jugador`) REFERENCES `jugadores` (`id`);

--
-- Filtros para la tabla `testimonios`
--
ALTER TABLE `testimonios`
  ADD CONSTRAINT `fk_testimonios_jugador` FOREIGN KEY (`id_jugador_ganador`) REFERENCES `jugadores` (`id`),
  ADD CONSTRAINT `fk_testimonios_sorteo` FOREIGN KEY (`id_sorteo`) REFERENCES `sorteos` (`id`);

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `fk_usuarios_roles` FOREIGN KEY (`rol_id`) REFERENCES `roles` (`id`);

DELIMITER $$
--
-- Eventos
--
CREATE DEFINER=`root`@`localhost` EVENT `expire_reservas` ON SCHEDULE EVERY 1 MINUTE STARTS '2025-12-07 17:15:02' ON COMPLETION NOT PRESERVE ENABLE DO BEGIN
  -- 1) Marcar boletos expirados como anulados y soft-delete
  UPDATE boletos b
  SET b.estado = 'anulado', b.updated_at = NOW(), b.is_deleted = 1, b.deleted_at = NOW()
  WHERE b.estado = 'reservado' AND b.reserved_until IS NOT NULL AND b.reserved_until < NOW();

  -- 2) Marcar los números asociados como liberados (soft-delete)
  UPDATE boleto_numeros bn
  JOIN boletos bb ON bn.id_boleto = bb.id
  SET bn.is_deleted = 1, bn.updated_at = NOW(), bn.deleted_at = NOW()
  WHERE bb.estado = 'anulado' AND bb.is_deleted = 1 AND bn.is_deleted = 0;

  -- 3) Insertar entradas en audit_logs para los boletos expirados (agrega trazabilidad)
  INSERT INTO audit_logs(actor_type, actor_id, accion, entidad, entidad_id, datos, created_at)
  SELECT 'sistema', NULL, 'expire_reserva', 'boletos', b.id,
         JSON_OBJECT('reserved_until', DATE_FORMAT(b.reserved_until, '%Y-%m-%d %H:%i:%s'), 'estado_prev', 'reservado'), NOW()
  FROM boletos b
  WHERE b.estado = 'anulado' AND b.deleted_at >= NOW() - INTERVAL 2 MINUTE;
END$$

DELIMITER ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

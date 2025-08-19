-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 19-08-2025 a las 17:48:43
-- Versión del servidor: 11.8.2-MariaDB
-- Versión de PHP: 8.4.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `megan`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `alertas_stock`
--

CREATE TABLE `alertas_stock` (
  `id` int(11) NOT NULL,
  `id_item` int(11) NOT NULL,
  `cantidad_restante` int(11) NOT NULL,
  `mensaje` text NOT NULL,
  `fecha_alerta` datetime NOT NULL,
  `leida` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

--
-- Volcado de datos para la tabla `alertas_stock`
--

INSERT INTO `alertas_stock` (`id`, `id_item`, `cantidad_restante`, `mensaje`, `fecha_alerta`, `leida`) VALUES
(1, 7, 0, 'ALERTA: El producto Correa 22¨ (Categoría: Repuestos) tiene bajo stock: 0 unidades restantes.', '2025-05-29 16:18:44', 0),
(2, 7, 0, 'ALERTA: El producto Correa 22¨ (Categoría: Repuestos) tiene bajo stock: 0 unidades restantes.', '2025-08-14 14:47:26', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `fecha_creacion` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`id`, `nombre`, `fecha_creacion`) VALUES
(1, 'Repuestos', '2025-05-26 16:08:27'),
(2, 'Lubricantes', '2025-05-26 16:08:27'),
(3, 'Herramientas', '2025-05-26 16:08:27'),
(4, 'Neumáticos', '2025-05-26 16:08:27'),
(5, 'Consumibles de Oficina', '2025-05-26 16:08:27'),
(6, 'Equipos de Protección Personal (EPP)', '2025-05-26 16:08:27'),
(7, 'Limpieza y Mantenimiento', '2025-05-26 16:08:27'),
(8, 'Accesorios para Vehículos', '2025-05-26 16:08:27'),
(9, 'Fluidos (AdBlue, Refrigerante, etc.)', '2025-05-26 16:08:27'),
(10, 'Electrónicos y Baterías', '2025-05-26 16:08:27');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inventario`
--

CREATE TABLE `inventario` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `categoria_id` int(11) DEFAULT NULL,
  `cantidad` int(11) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `fecha_ingreso` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

--
-- Volcado de datos para la tabla `inventario`
--

INSERT INTO `inventario` (`id`, `nombre`, `categoria_id`, `cantidad`, `descripcion`, `fecha_ingreso`) VALUES
(1, 'Filtro de Aceite', 1, 48, 'Filtro para motor diesel', '2025-05-24 14:13:46'),
(2, 'Aceite 15W-40', 2, 120, 'Aceite sintético para camiones', '2025-05-24 14:13:46'),
(3, 'Llave de Tubo', 3, 30, 'Llave ajustable 1/2 pulgada', '2025-05-24 14:13:46'),
(4, 'Neumático 22.5\"', 4, 25, 'Neumático para trailer', '2025-05-24 14:13:46'),
(5, 'Bujías', 1, 70, 'Bujías para motor a gasolina', '2025-05-24 14:13:46'),
(6, 'Grasa Multipropósito', 2, 65, 'Grasa para chasis', '2025-05-24 14:13:46'),
(7, 'Correa 22¨', 1, 0, 'Correa de 22 Pulgadas', '2025-05-26 16:16:58'),
(8, 'Correa 22¨', 1, 9, 'Correa de 22 Pulgadas', '2025-05-29 20:17:51'),
(9, 'Aceite 20 80', 2, 50, 'Aceite para camion', '2025-08-14 18:46:37');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedores`
--

CREATE TABLE `proveedores` (
  `id` int(11) NOT NULL,
  `rif` varchar(20) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `direccion` text DEFAULT NULL,
  `tlf` varchar(20) DEFAULT NULL,
  `correo` varchar(255) DEFAULT NULL,
  `web` varchar(255) DEFAULT NULL,
  `social` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `salidas_inventario`
--

CREATE TABLE `salidas_inventario` (
  `id` int(11) NOT NULL,
  `id_item` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `motivo` text NOT NULL,
  `fecha_salida` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

--
-- Volcado de datos para la tabla `salidas_inventario`
--

INSERT INTO `salidas_inventario` (`id`, `id_item`, `id_usuario`, `cantidad`, `motivo`, `fecha_salida`) VALUES
(1, 1, 2, 5, 'Mantenimiento preventivo camión #45', '2025-05-24 14:13:46'),
(2, 2, 3, 10, 'Cambio de aceite flota de buses', '2025-05-24 14:13:46'),
(3, 3, 2, 2, 'Reparación motor #12', '2025-05-24 14:13:46'),
(4, 4, 4, 4, 'Reemplazo por desgaste', '2025-05-24 14:13:46'),
(5, 1, 3, 3, 'Reparación urgente camión #78', '2025-05-24 14:13:46'),
(6, 7, 1, 10, 'Salida para Camion 350', '2025-05-26 18:15:59'),
(7, 7, 1, 1, 'Reemplazo en HMB55', '2025-05-29 20:04:07'),
(8, 1, 1, 2, 'Prueba', '2025-05-29 20:15:21'),
(9, 7, 1, 9, 'Prueba', '2025-05-29 20:18:44'),
(10, 5, 1, 10, 'venta', '2025-06-03 19:10:16'),
(11, 7, 1, 2, 'Camion 350', '2025-08-14 18:47:26');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rol` enum('admin','tecnico','mecanico') NOT NULL,
  `pregunta_seguridad` varchar(255) NOT NULL,
  `respuesta_seguridad` varchar(255) NOT NULL,
  `fecha_registro` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `email`, `password`, `rol`, `pregunta_seguridad`, `respuesta_seguridad`, `fecha_registro`) VALUES
(1, 'Admin Principal', 'admin@megan.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', '¿Cuál es tu color favorito?', 'azul', '2025-05-24 14:13:46'),
(2, 'Luis Carlos Pérez', 'tecnico1@megan.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'tecnico', '¿Nombre de tu primera mascota?', 'max', '2025-05-24 14:13:46'),
(3, 'Luisa Rodríguez', 'mecanico1@megan.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'mecanico', '¿Ciudad de nacimiento?', 'caracas', '2025-05-24 14:13:46'),
(4, 'Juan Gómez', 'tecnico2@megan.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'tecnico', '¿Modelo de tu primer carro?', 'corolla', '2025-05-24 14:13:46');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `alertas_stock`
--
ALTER TABLE `alertas_stock`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_item` (`id_item`);

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `inventario`
--
ALTER TABLE `inventario`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `proveedores`
--
ALTER TABLE `proveedores`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `rif` (`rif`),
  ADD UNIQUE KEY `correo` (`correo`);

--
-- Indices de la tabla `salidas_inventario`
--
ALTER TABLE `salidas_inventario`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_item` (`id_item`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `alertas_stock`
--
ALTER TABLE `alertas_stock`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `inventario`
--
ALTER TABLE `inventario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `proveedores`
--
ALTER TABLE `proveedores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `salidas_inventario`
--
ALTER TABLE `salidas_inventario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `alertas_stock`
--
ALTER TABLE `alertas_stock`
  ADD CONSTRAINT `alertas_stock_ibfk_1` FOREIGN KEY (`id_item`) REFERENCES `inventario` (`id`);

--
-- Filtros para la tabla `salidas_inventario`
--
ALTER TABLE `salidas_inventario`
  ADD CONSTRAINT `salidas_inventario_ibfk_1` FOREIGN KEY (`id_item`) REFERENCES `inventario` (`id`),
  ADD CONSTRAINT `salidas_inventario_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

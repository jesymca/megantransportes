/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19-11.8.2-MariaDB, for Linux (x86_64)
--
-- Host: localhost    Database: megan
-- ------------------------------------------------------
-- Server version	11.8.2-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*M!100616 SET @OLD_NOTE_VERBOSITY=@@NOTE_VERBOSITY, NOTE_VERBOSITY=0 */;

--
-- Table structure for table `alertas_stock`
--

DROP TABLE IF EXISTS `alertas_stock`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `alertas_stock` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_item` int(11) NOT NULL,
  `cantidad_restante` int(11) NOT NULL,
  `mensaje` text NOT NULL,
  `fecha_alerta` datetime NOT NULL,
  `leida` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `id_item` (`id_item`),
  CONSTRAINT `alertas_stock_ibfk_1` FOREIGN KEY (`id_item`) REFERENCES `inventario` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `alertas_stock`
--

LOCK TABLES `alertas_stock` WRITE;
/*!40000 ALTER TABLE `alertas_stock` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `alertas_stock` VALUES
(1,7,0,'ALERTA: El producto Correa 22¨ (Categoría: Repuestos) tiene bajo stock: 0 unidades restantes.','2025-05-29 16:18:44',0);
/*!40000 ALTER TABLE `alertas_stock` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `categorias`
--

DROP TABLE IF EXISTS `categorias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `categorias` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `fecha_creacion` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categorias`
--

LOCK TABLES `categorias` WRITE;
/*!40000 ALTER TABLE `categorias` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `categorias` VALUES
(1,'Repuestos','2025-05-26 16:08:27'),
(2,'Lubricantes','2025-05-26 16:08:27'),
(3,'Herramientas','2025-05-26 16:08:27'),
(4,'Neumáticos','2025-05-26 16:08:27'),
(5,'Consumibles de Oficina','2025-05-26 16:08:27'),
(6,'Equipos de Protección Personal (EPP)','2025-05-26 16:08:27'),
(7,'Limpieza y Mantenimiento','2025-05-26 16:08:27'),
(8,'Accesorios para Vehículos','2025-05-26 16:08:27'),
(9,'Fluidos (AdBlue, Refrigerante, etc.)','2025-05-26 16:08:27'),
(10,'Electrónicos y Baterías','2025-05-26 16:08:27');
/*!40000 ALTER TABLE `categorias` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `inventario`
--

DROP TABLE IF EXISTS `inventario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `inventario` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `categoria_id` int(11) DEFAULT NULL,
  `cantidad` int(11) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `fecha_ingreso` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventario`
--

LOCK TABLES `inventario` WRITE;
/*!40000 ALTER TABLE `inventario` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `inventario` VALUES
(1,'Filtro de Aceite',1,48,'Filtro para motor diesel','2025-05-24 14:13:46'),
(2,'Aceite 15W-40',2,120,'Aceite sintético para camiones','2025-05-24 14:13:46'),
(3,'Llave de Tubo',3,30,'Llave ajustable 1/2 pulgada','2025-05-24 14:13:46'),
(4,'Neumático 22.5\"',4,25,'Neumático para trailer','2025-05-24 14:13:46'),
(5,'Bujías',1,70,'Bujías para motor a gasolina','2025-05-24 14:13:46'),
(6,'Grasa Multipropósito',2,65,'Grasa para chasis','2025-05-24 14:13:46'),
(7,'Correa 22¨',1,2,'Correa de 22 Pulgadas','2025-05-26 16:16:58'),
(8,'Correa 22¨',1,9,'Correa de 22 Pulgadas','2025-05-29 20:17:51');
/*!40000 ALTER TABLE `inventario` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `salidas_inventario`
--

DROP TABLE IF EXISTS `salidas_inventario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `salidas_inventario` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_item` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `motivo` text NOT NULL,
  `fecha_salida` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `id_item` (`id_item`),
  KEY `id_usuario` (`id_usuario`),
  CONSTRAINT `salidas_inventario_ibfk_1` FOREIGN KEY (`id_item`) REFERENCES `inventario` (`id`),
  CONSTRAINT `salidas_inventario_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `salidas_inventario`
--

LOCK TABLES `salidas_inventario` WRITE;
/*!40000 ALTER TABLE `salidas_inventario` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `salidas_inventario` VALUES
(1,1,2,5,'Mantenimiento preventivo camión #45','2025-05-24 14:13:46'),
(2,2,3,10,'Cambio de aceite flota de buses','2025-05-24 14:13:46'),
(3,3,2,2,'Reparación motor #12','2025-05-24 14:13:46'),
(4,4,4,4,'Reemplazo por desgaste','2025-05-24 14:13:46'),
(5,1,3,3,'Reparación urgente camión #78','2025-05-24 14:13:46'),
(6,7,1,10,'Salida para Camion 350','2025-05-26 18:15:59'),
(7,7,1,1,'Reemplazo en HMB55','2025-05-29 20:04:07'),
(8,1,1,2,'Prueba','2025-05-29 20:15:21'),
(9,7,1,9,'Prueba','2025-05-29 20:18:44'),
(10,5,1,10,'venta','2025-06-03 19:10:16');
/*!40000 ALTER TABLE `salidas_inventario` ENABLE KEYS */;
UNLOCK TABLES;
commit;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rol` enum('admin','tecnico','mecanico') NOT NULL,
  `pregunta_seguridad` varchar(255) NOT NULL,
  `respuesta_seguridad` varchar(255) NOT NULL,
  `fecha_registro` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
set autocommit=0;
INSERT INTO `usuarios` VALUES
(1,'Admin Principal','admin@megan.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','admin','¿Cuál es tu color favorito?','azul','2025-05-24 14:13:46'),
(2,'Luis Carlos Pérez','tecnico1@megan.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','tecnico','¿Nombre de tu primera mascota?','max','2025-05-24 14:13:46'),
(3,'Luisa Rodríguez','mecanico1@megan.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','mecanico','¿Ciudad de nacimiento?','caracas','2025-05-24 14:13:46'),
(4,'Juan Gómez','tecnico2@megan.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','tecnico','¿Modelo de tu primer carro?','corolla','2025-05-24 14:13:46');
/*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;
UNLOCK TABLES;
commit;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*M!100616 SET NOTE_VERBOSITY=@OLD_NOTE_VERBOSITY */;

-- Dump completed on 2025-08-14 18:35:32

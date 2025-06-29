-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jun 29, 2025 at 03:56 PM
-- Server version: 9.1.0
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `gestion_academica`
--

-- --------------------------------------------------------

--
-- Table structure for table `alumno`
--

DROP TABLE IF EXISTS `alumno`;
CREATE TABLE IF NOT EXISTS `alumno` (
  `id_alumno` int NOT NULL AUTO_INCREMENT,
  `id_usuario` int NOT NULL,
  `id_carrera` int NOT NULL,
  `fecha_ingreso` date NOT NULL,
  PRIMARY KEY (`id_alumno`),
  UNIQUE KEY `id_usuario` (`id_usuario`),
  KEY `id_carrera` (`id_carrera`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `carrera`
--

DROP TABLE IF EXISTS `carrera`;
CREATE TABLE IF NOT EXISTS `carrera` (
  `id_carrera` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  PRIMARY KEY (`id_carrera`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `carrera`
--

INSERT INTO `carrera` (`id_carrera`, `nombre`) VALUES
(1, 'Licenciatura en Sistemas'),
(2, 'Abogacía');

-- --------------------------------------------------------

--
-- Table structure for table `estado_inscripcion_materia`
--

DROP TABLE IF EXISTS `estado_inscripcion_materia`;
CREATE TABLE IF NOT EXISTS `estado_inscripcion_materia` (
  `id_estado` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  PRIMARY KEY (`id_estado`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `estado_inscripcion_materia`
--

INSERT INTO `estado_inscripcion_materia` (`id_estado`, `nombre`) VALUES
(2, 'promocionada'),
(3, 'recursa'),
(1, 'regular');

-- --------------------------------------------------------

--
-- Table structure for table `evaluacion`
--

DROP TABLE IF EXISTS `evaluacion`;
CREATE TABLE IF NOT EXISTS `evaluacion` (
  `id_evaluacion` int NOT NULL AUTO_INCREMENT,
  `id_inscripcion` int NOT NULL,
  `id_tipo` int NOT NULL,
  `fecha` date NOT NULL,
  `nota` decimal(3,1) DEFAULT NULL,
  PRIMARY KEY (`id_evaluacion`),
  KEY `id_inscripcion` (`id_inscripcion`),
  KEY `id_tipo` (`id_tipo`)
) ;

--
-- Triggers `evaluacion`
--
DROP TRIGGER IF EXISTS `trg_actualizar_estado_inscripcion`;
DELIMITER $$
CREATE TRIGGER `trg_actualizar_estado_inscripcion` AFTER INSERT ON `evaluacion` FOR EACH ROW BEGIN
    DECLARE notas_bajas TINYINT DEFAULT 0;
    DECLARE todas_7 TINYINT DEFAULT 1;
    DECLARE id_regular INT;
    DECLARE id_promocionada INT;
    DECLARE id_recursa INT;

    -- Obtener los IDs de estado según el nombre
    SELECT id_estado INTO id_regular FROM estado_inscripcion_materia WHERE nombre = 'regular';
    SELECT id_estado INTO id_promocionada FROM estado_inscripcion_materia WHERE nombre = 'promocionada';
    SELECT id_estado INTO id_recursa FROM estado_inscripcion_materia WHERE nombre = 'recursa';

    -- Verificar notas del alumno en esta inscripción
    SELECT
        SUM(CASE WHEN nota < 6 THEN 1 ELSE 0 END),
        MIN(CASE WHEN nota < 7 THEN 0 ELSE 1 END)
    INTO
        notas_bajas, todas_7
    FROM evaluacion
    WHERE id_inscripcion = NEW.id_inscripcion;

    -- Actualizar el estado según las notas
    IF notas_bajas > 0 THEN
        UPDATE inscripcion_materia
        SET id_estado = id_recursa
        WHERE id_inscripcion = NEW.id_inscripcion;
    ELSEIF todas_7 = 1 THEN
        UPDATE inscripcion_materia
        SET id_estado = id_promocionada
        WHERE id_inscripcion = NEW.id_inscripcion;
    ELSE
        UPDATE inscripcion_materia
        SET id_estado = id_regular
        WHERE id_inscripcion = NEW.id_inscripcion;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `inscripcion_materia`
--

DROP TABLE IF EXISTS `inscripcion_materia`;
CREATE TABLE IF NOT EXISTS `inscripcion_materia` (
  `id_inscripcion` int NOT NULL AUTO_INCREMENT,
  `id_alumno` int NOT NULL,
  `id_materia` int NOT NULL,
  `anio_academico` year NOT NULL,
  `semestre` tinyint NOT NULL,
  `id_estado` int NOT NULL,
  `intentos_final` tinyint DEFAULT '0',
  PRIMARY KEY (`id_inscripcion`),
  KEY `id_alumno` (`id_alumno`),
  KEY `id_materia` (`id_materia`),
  KEY `id_estado` (`id_estado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `materia`
--

DROP TABLE IF EXISTS `materia`;
CREATE TABLE IF NOT EXISTS `materia` (
  `id_materia` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `anio` smallint NOT NULL,
  `semestre` tinyint NOT NULL,
  `id_carrera` int NOT NULL,
  PRIMARY KEY (`id_materia`),
  KEY `id_carrera` (`id_carrera`)
) ENGINE=InnoDB AUTO_INCREMENT=81 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `materia`
--

INSERT INTO `materia` (`id_materia`, `nombre`, `anio`, `semestre`, `id_carrera`) VALUES
(1, 'Introducción a la Programación', 1, 1, 1),
(2, 'Matemática Discreta', 1, 1, 1),
(3, 'Sistemas Operativos', 1, 1, 1),
(4, 'Arquitectura de Computadoras', 1, 1, 1),
(5, 'Lógica y Estructuras de Datos', 1, 1, 1),
(6, 'Algoritmos y Programación I', 1, 2, 1),
(7, 'Álgebra y Geometría Analítica', 1, 2, 1),
(8, 'Base de Datos I', 1, 2, 1),
(9, 'Redes de Computadoras I', 1, 2, 1),
(10, 'Inglés Técnico', 1, 2, 1),
(11, 'Algoritmos y Programación II', 2, 1, 1),
(12, 'Cálculo Diferencial e Integral', 2, 1, 1),
(13, 'Base de Datos II', 2, 1, 1),
(14, 'Programación Orientada a Objetos', 2, 1, 1),
(15, 'Estadística y Probabilidad', 2, 1, 1),
(16, 'Diseño de Sistemas', 2, 2, 1),
(17, 'Matemática Superior', 2, 2, 1),
(18, 'Programación Web I', 2, 2, 1),
(19, 'Análisis y Diseño de Algoritmos', 2, 2, 1),
(20, 'Contabilidad General', 2, 2, 1),
(21, 'Ingeniería de Software I', 3, 1, 1),
(22, 'Programación Web II', 3, 1, 1),
(23, 'Sistemas Distribuidos', 3, 1, 1),
(24, 'Investigación Operativa', 3, 1, 1),
(25, 'Seguridad Informática', 3, 1, 1),
(26, 'Ingeniería de Software II', 3, 2, 1),
(27, 'Inteligencia Artificial', 3, 2, 1),
(28, 'Programación Móvil', 3, 2, 1),
(29, 'Modelado y Simulación', 3, 2, 1),
(30, 'Legislación Informática', 3, 2, 1),
(31, 'Proyecto de Software', 4, 1, 1),
(32, 'Big Data y Data Mining', 4, 1, 1),
(33, 'Computación en la Nube', 4, 1, 1),
(34, 'Gestión de Proyectos de Software', 4, 1, 1),
(35, 'Seminario de Actualización Tecnológica I', 4, 1, 1),
(36, 'Testing de Software', 4, 2, 1),
(37, 'Robótica y Automatización', 4, 2, 1),
(38, 'Blockchain y Criptomonedas', 4, 2, 1),
(39, 'Auditoría de Sistemas', 4, 2, 1),
(40, 'Ética Profesional y Social', 4, 2, 1),
(41, 'Introducción al Derecho', 1, 1, 2),
(42, 'Historia Constitucional Argentina', 1, 1, 2),
(43, 'Derecho Romano', 1, 1, 2),
(44, 'Teoría del Estado', 1, 1, 2),
(45, 'Introducción a la Filosofía del Derecho', 1, 1, 2),
(46, 'Derecho Civil I (Parte General)', 1, 2, 2),
(47, 'Derecho Constitucional I', 1, 2, 2),
(48, 'Economía Política', 1, 2, 2),
(49, 'Sociología del Derecho', 1, 2, 2),
(50, 'Derechos Humanos', 1, 2, 2),
(51, 'Derecho Civil II (Obligaciones)', 2, 1, 2),
(52, 'Derecho Constitucional II', 2, 1, 2),
(53, 'Derecho Penal I (Parte General)', 2, 1, 2),
(54, 'Derecho Administrativo I', 2, 1, 2),
(55, 'Finanzas Públicas y Derecho Tributario', 2, 1, 2),
(56, 'Derecho Civil III (Contratos)', 2, 2, 2),
(57, 'Derecho Penal II (Parte Especial)', 2, 2, 2),
(58, 'Derecho Administrativo II', 2, 2, 2),
(59, 'Derecho Laboral y de la Seguridad Social I', 2, 2, 2),
(60, 'Derecho de Familia y Sucesiones', 2, 2, 2),
(61, 'Derecho Procesal Civil y Comercial I', 3, 1, 2),
(62, 'Derecho Comercial I (Parte General y Sociedades)', 3, 1, 2),
(63, 'Derecho Internacional Público', 3, 1, 2),
(64, 'Derecho Ambiental', 3, 1, 2),
(65, 'Teoría del Delito', 3, 1, 2),
(66, 'Derecho Procesal Civil y Comercial II', 3, 2, 2),
(67, 'Derecho Comercial II (Contratos Comerciales)', 3, 2, 2),
(68, 'Derecho Internacional Privado', 3, 2, 2),
(69, 'Derecho Procesal Penal', 3, 2, 2),
(70, 'Derecho Agrario y Minero', 3, 2, 2),
(71, 'Derecho de los Recursos Naturales', 4, 1, 2),
(72, 'Derecho Concursal', 4, 1, 2),
(73, 'Derecho de los Consumidores y Usuarios', 4, 1, 2),
(74, 'Práctica Profesional Supervisada I', 4, 1, 2),
(75, 'Derecho de la Navegación y Aeronáutico', 4, 1, 2),
(76, 'Derecho Informático', 4, 2, 2),
(77, 'Argumentación Jurídica', 4, 2, 2),
(78, 'Clínica Jurídica', 4, 2, 2),
(79, 'Derecho de la Integración Regional', 4, 2, 2),
(80, 'Seminario de Tesis', 4, 2, 2);

-- --------------------------------------------------------

--
-- Table structure for table `profesor`
--

DROP TABLE IF EXISTS `profesor`;
CREATE TABLE IF NOT EXISTS `profesor` (
  `id_profesor` int NOT NULL AUTO_INCREMENT,
  `id_usuario` int NOT NULL,
  `titulo_academico` varchar(100) NOT NULL,
  `especialidad` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id_profesor`),
  UNIQUE KEY `id_usuario` (`id_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `profesor_materia`
--

DROP TABLE IF EXISTS `profesor_materia`;
CREATE TABLE IF NOT EXISTS `profesor_materia` (
  `id_profesor` int NOT NULL,
  `id_materia` int NOT NULL,
  PRIMARY KEY (`id_profesor`,`id_materia`),
  KEY `id_materia` (`id_materia`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rol_usuario`
--

DROP TABLE IF EXISTS `rol_usuario`;
CREATE TABLE IF NOT EXISTS `rol_usuario` (
  `id_rol` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  PRIMARY KEY (`id_rol`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `rol_usuario`
--

INSERT INTO `rol_usuario` (`id_rol`, `nombre`) VALUES
(1, 'admin'),
(2, 'alumno'),
(3, 'profesor');

-- --------------------------------------------------------

--
-- Table structure for table `tipo_evaluacion`
--

DROP TABLE IF EXISTS `tipo_evaluacion`;
CREATE TABLE IF NOT EXISTS `tipo_evaluacion` (
  `id_tipo` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  PRIMARY KEY (`id_tipo`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tipo_evaluacion`
--

INSERT INTO `tipo_evaluacion` (`id_tipo`, `nombre`) VALUES
(5, 'final'),
(1, 'parcial1'),
(2, 'parcial2'),
(3, 'recuperatorio1'),
(4, 'recuperatorio2');

-- --------------------------------------------------------

--
-- Table structure for table `usuario`
--

DROP TABLE IF EXISTS `usuario`;
CREATE TABLE IF NOT EXISTS `usuario` (
  `id_usuario` int NOT NULL AUTO_INCREMENT,
  `contrasena` varchar(255) NOT NULL,
  `id_rol` int NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `activo` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id_usuario`),
  UNIQUE KEY `email` (`email`),
  KEY `id_rol` (`id_rol`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `alumno`
--
ALTER TABLE `alumno`
  ADD CONSTRAINT `alumno_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `alumno_ibfk_2` FOREIGN KEY (`id_carrera`) REFERENCES `carrera` (`id_carrera`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table `evaluacion`
--
ALTER TABLE `evaluacion`
  ADD CONSTRAINT `evaluacion_ibfk_1` FOREIGN KEY (`id_inscripcion`) REFERENCES `inscripcion_materia` (`id_inscripcion`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `evaluacion_ibfk_2` FOREIGN KEY (`id_tipo`) REFERENCES `tipo_evaluacion` (`id_tipo`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table `inscripcion_materia`
--
ALTER TABLE `inscripcion_materia`
  ADD CONSTRAINT `inscripcion_materia_ibfk_1` FOREIGN KEY (`id_alumno`) REFERENCES `alumno` (`id_alumno`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `inscripcion_materia_ibfk_2` FOREIGN KEY (`id_materia`) REFERENCES `materia` (`id_materia`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `inscripcion_materia_ibfk_3` FOREIGN KEY (`id_estado`) REFERENCES `estado_inscripcion_materia` (`id_estado`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table `materia`
--
ALTER TABLE `materia`
  ADD CONSTRAINT `materia_ibfk_1` FOREIGN KEY (`id_carrera`) REFERENCES `carrera` (`id_carrera`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table `profesor`
--
ALTER TABLE `profesor`
  ADD CONSTRAINT `profesor_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `profesor_materia`
--
ALTER TABLE `profesor_materia`
  ADD CONSTRAINT `profesor_materia_ibfk_1` FOREIGN KEY (`id_profesor`) REFERENCES `profesor` (`id_profesor`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `profesor_materia_ibfk_2` FOREIGN KEY (`id_materia`) REFERENCES `materia` (`id_materia`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table `usuario`
--
ALTER TABLE `usuario`
  ADD CONSTRAINT `usuario_ibfk_1` FOREIGN KEY (`id_rol`) REFERENCES `rol_usuario` (`id_rol`) ON DELETE RESTRICT ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jul 28, 2025 at 07:15 PM
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

DELIMITER $$
--
-- Procedures
--
DROP PROCEDURE IF EXISTS `actualizar_notas_alumnos`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `actualizar_notas_alumnos` (IN `p_datos` JSON, IN `p_id_materia` INT, IN `p_anio` INT)   BEGIN
    DECLARE i INT DEFAULT 0;
    DECLARE n INT;
    DECLARE v_id_insc INT;
    DECLARE v_alumno INT;
    DECLARE v, p1, p2, r1, r2, nf DECIMAL(4,1);
    DECLARE cnt_parciales INT;
    DECLARE id_cursar, id_regular, id_promocion, id_recursar INT;
    DECLARE hoy DATE DEFAULT CURDATE();

    -- Obtener los IDs de estado
    SELECT id_estado INTO id_cursar   FROM estado_inscripcion_materia WHERE nombre='cursar';
    SELECT id_estado INTO id_regular  FROM estado_inscripcion_materia WHERE nombre='regular';
    SELECT id_estado INTO id_promocion FROM estado_inscripcion_materia WHERE nombre='promocion';
    SELECT id_estado INTO id_recursar FROM estado_inscripcion_materia WHERE nombre='recursar';

    SET n = JSON_LENGTH(p_datos);

    WHILE i < n DO
        -- leer JSON
        SET v_alumno = JSON_UNQUOTE(JSON_EXTRACT(p_datos, CONCAT('$[',i,'].id_alumno')));
        SET p1       = NULLIF(JSON_UNQUOTE(JSON_EXTRACT(p_datos, CONCAT('$[',i,'].parcial_1'))), '');
        SET p2       = NULLIF(JSON_UNQUOTE(JSON_EXTRACT(p_datos, CONCAT('$[',i,'].parcial_2'))), '');
        SET r1       = NULLIF(JSON_UNQUOTE(JSON_EXTRACT(p_datos, CONCAT('$[',i,'].recuperatorio_1'))), '');
        SET r2       = NULLIF(JSON_UNQUOTE(JSON_EXTRACT(p_datos, CONCAT('$[',i,'].recuperatorio_2'))), '');
        SET nf       = NULLIF(JSON_UNQUOTE(JSON_EXTRACT(p_datos, CONCAT('$[',i,'].nota_final'))), '');

        -- buscar inscripción
        SELECT id_inscripcion INTO v_id_insc
          FROM inscripcion_materia
         WHERE id_alumno=v_alumno
           AND id_materia=p_id_materia
           AND anio_academico=p_anio
         LIMIT 1;

        -- UP- / INSERT de cada evaluación
        -- tipologías 1=parcial1, 2=parcial2, 3=rec1,4=rec2,5=final
        -- macro para no repetir código:
        IF p1 IS NOT NULL THEN
            INSERT INTO evaluacion(id_inscripcion,id_tipo,fecha,nota)
            VALUES(v_id_insc,1,hoy,CAST(p1 AS DECIMAL(4,1)))
            ON DUPLICATE KEY UPDATE nota=VALUES(nota), fecha=VALUES(fecha);
        END IF;
        IF p2 IS NOT NULL THEN
            INSERT INTO evaluacion(id_inscripcion,id_tipo,fecha,nota)
            VALUES(v_id_insc,2,hoy,CAST(p2 AS DECIMAL(4,1)))
            ON DUPLICATE KEY UPDATE nota=VALUES(nota), fecha=VALUES(fecha);
        END IF;
        IF r1 IS NOT NULL THEN
            INSERT INTO evaluacion(id_inscripcion,id_tipo,fecha,nota)
            VALUES(v_id_insc,3,hoy,CAST(r1 AS DECIMAL(4,1)))
            ON DUPLICATE KEY UPDATE nota=VALUES(nota), fecha=VALUES(fecha);
        END IF;
        IF r2 IS NOT NULL THEN
            INSERT INTO evaluacion(id_inscripcion,id_tipo,fecha,nota)
            VALUES(v_id_insc,4,hoy,CAST(r2 AS DECIMAL(4,1)))
            ON DUPLICATE KEY UPDATE nota=VALUES(nota), fecha=VALUES(fecha);
        END IF;
        -- nota final opcional
        IF nf IS NOT NULL THEN
            INSERT INTO evaluacion(id_inscripcion,id_tipo,fecha,nota)
            VALUES(v_id_insc,5,hoy,CAST(nf AS DECIMAL(4,1)))
            ON DUPLICATE KEY UPDATE nota=VALUES(nota), fecha=VALUES(fecha);
        END IF;

        -- Releer notas para decidir estado
        SELECT 
          MAX(IF(id_tipo=1,nota,NULL)),
          MAX(IF(id_tipo=2,nota,NULL)),
          MAX(IF(id_tipo=3,nota,NULL)),
          MAX(IF(id_tipo=4,nota,NULL))
        INTO p1,p2,r1,r2
        FROM evaluacion
        WHERE id_inscripcion=v_id_insc;

        -- contar parciales cargados (tipos 1 y 2)
        SELECT COUNT(*) INTO cnt_parciales
        FROM evaluacion
        WHERE id_inscripcion=v_id_insc
          AND id_tipo IN (1,2);

        -- decidir estado
        IF cnt_parciales < 2 THEN
            UPDATE inscripcion_materia
               SET id_estado = id_cursar
             WHERE id_inscripcion = v_id_insc;

        ELSEIF p1 > 8 AND p2 > 8 THEN
            UPDATE inscripcion_materia
               SET id_estado = id_promocion
             WHERE id_inscripcion = v_id_insc;

        ELSEIF (p1 >= 6 OR IFNULL(r1,0) >= 6)
             AND (p2 >= 6 OR IFNULL(r2,0) >= 6) THEN
            UPDATE inscripcion_materia
               SET id_estado = id_regular
             WHERE id_inscripcion = v_id_insc;

        ELSE
            UPDATE inscripcion_materia
               SET id_estado = id_recursar
             WHERE id_inscripcion = v_id_insc;
        END IF;

        SET i = i + 1;
    END WHILE;
END$$

DROP PROCEDURE IF EXISTS `calcular_notas_finales_promocionadas`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `calcular_notas_finales_promocionadas` ()   BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE v_id_inscripcion INT;
    DECLARE v_parcial1 DECIMAL(3,1);
    DECLARE v_parcial2 DECIMAL(3,1);
    DECLARE v_promedio DECIMAL(3,1);
    DECLARE v_id_tipo_final INT;

    DECLARE cur CURSOR FOR
        SELECT im.id_inscripcion
        FROM inscripcion_materia im
        JOIN estado_inscripcion_materia eim ON im.id_estado = eim.id_estado
        WHERE eim.nombre = 'promocion'
        AND NOT EXISTS (
            SELECT 1 FROM evaluacion ev
            JOIN tipo_evaluacion te ON ev.id_tipo = te.id_tipo
            WHERE ev.id_inscripcion = im.id_inscripcion AND te.nombre = 'final'
        );

    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

    SELECT id_tipo INTO v_id_tipo_final FROM tipo_evaluacion WHERE nombre = 'final';

    OPEN cur;

    inscripciones_loop:LOOP
        FETCH cur INTO v_id_inscripcion;
        IF done THEN
            LEAVE inscripciones_loop;
        END IF;

        SELECT MAX(ev.nota)
        INTO v_parcial1
        FROM evaluacion ev
        JOIN tipo_evaluacion te ON ev.id_tipo = te.id_tipo
        WHERE ev.id_inscripcion = v_id_inscripcion AND te.nombre = 'parcial1';

        SELECT MAX(ev.nota)
        INTO v_parcial2
        FROM evaluacion ev
        JOIN tipo_evaluacion te ON ev.id_tipo = te.id_tipo
        WHERE ev.id_inscripcion = v_id_inscripcion AND te.nombre = 'parcial2';

        SET v_promedio = ROUND((v_parcial1 + v_parcial2) / 2, 1);

        INSERT INTO evaluacion(id_inscripcion, id_tipo, fecha, nota)
        VALUES (v_id_inscripcion, v_id_tipo_final, CURDATE(), v_promedio);

    END LOOP;

    CLOSE cur;
END$$

DROP PROCEDURE IF EXISTS `cargar_datos_iniciales`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `cargar_datos_iniciales` ()   BEGIN
    -- Desactivar restricciones temporales para evitar errores de integridad referencial
    SET FOREIGN_KEY_CHECKS = 0;
    
    -- Desactivar lógica de trigger para evitar errores al cargar evaluaciones
	SET @desactivar_trigger_final = TRUE;

    -- Eliminar todos los registros de las tablas relacionadas, en orden adecuado
    TRUNCATE TABLE evaluacion;
    TRUNCATE TABLE inscripcion_materia;
    TRUNCATE TABLE profesor_materia;
    TRUNCATE TABLE profesor;
    TRUNCATE TABLE alumno;
    TRUNCATE TABLE usuario;

    -- Restaurar restricciones de claves foráneas
    SET FOREIGN_KEY_CHECKS = 1;

    -- Insertar usuarios (alumnos y profesores)
    INSERT INTO usuario(contrasena, id_rol, nombre, apellido, direccion, telefono, email, activo) VALUES 
        (MD5('1234'), 2, 'Juan','Alonso','Calle Random 123','2616743521','juan.alonso@alumno.com', true),
        (MD5('1234'), 2, 'Jorge','Perez','Calle Random 123','2616743521','jorge.perez@alumno.com', true),
        (MD5('1234'), 2, 'Carla','Suarez','Calle Random 123','2616743521','carla.suarez@alumno.com', true),
        (MD5('1234'), 2, 'Delfina','Quiroga','Calle Random 123','2616743521','delfina.quiroga@alumno.com', true),
        (MD5('1234'), 2, 'Pablo','Ramiz','Calle Random 123','2616743521','pablo.ramiz@alumno.com', true),
        (MD5('1234'), 2, 'Martina','Ruiz','Calle Random 123','2616743521','martina.ruiz@alumno.com', true),
        (MD5('1234'), 3, 'Pedro','Alonso','Calle Random 124','2616743521','pedro.alonso@profesor.com', true),
        (MD5('1234'), 3, 'Marisa','Rodriguez','Calle Random 124','2616743521','marisa.rodriguez@profesor.com', true);

    -- Insertar alumnos (IDs se asignan automáticamente)
    INSERT INTO alumno(id_usuario, id_carrera, fecha_ingreso) VALUES 
        (1, 1, '2025-03-01'),
        (2, 1, '2025-03-01'),
        (3, 1, '2025-03-01'),
        (4, 1, '2025-03-01'),
        (5, 1, '2025-03-01'),
        (6, 1, '2025-03-01');

    -- Inscripciones (IDs automáticos)
    INSERT INTO inscripcion_materia(id_alumno, id_materia, anio_academico, semestre) VALUES 
        (1, 1, 2025, 1),
        (2, 1, 2025, 1),
        (3, 1, 2025, 1),
        (4, 1, 2025, 1),
        (5, 1, 2025, 1),
        (6, 1, 2025, 1);

    -- Evaluaciones (corresponden al id_inscripcion generado)
    INSERT INTO evaluacion(id_inscripcion, id_tipo, fecha, nota) VALUES
        (1,1,'2025-04-18',6),
        (1,2,'2025-04-18',9),
        (2,1,'2025-04-18',9),
        (2,2,'2025-04-18',3),
        (2,4,'2025-04-30',6),
        (3,1,'2025-04-18',10),
        (3,2,'2025-04-18',8),
        (4,1,'2025-04-18',2),
        (4,2,'2025-04-18',3),
        (4,3,'2025-04-18',4),
        (4,4,'2025-04-30',4),
        (5,1,'2025-04-18',9),
        (5,2,'2025-04-18',9.5),
        (6,1,'2025-04-18',5),
        (6,2,'2025-04-18',5),
        (6,3,'2025-04-18',6),
        (6,4,'2025-04-18',6);

    -- Insertar profesores
    INSERT INTO profesor(id_usuario, titulo_academico, especialidad) VALUES
        (7, 'Profesor Universitario', 'Ciencias Exactas'),
        (8, 'Profesor Universitario', 'Ciencias Exactas');

    -- Relación profesor-materia
    INSERT INTO profesor_materia(id_profesor, id_materia) VALUES
        (1,1),
        (2,1);
        
	-- Reactivar trigger y calcular notas finales
	SET @desactivar_trigger_final = FALSE;
	CALL calcular_notas_finales_promocionadas();

END$$

--
-- Functions
--
DROP FUNCTION IF EXISTS `inscribir_alumno_materia`$$
CREATE DEFINER=`root`@`localhost` FUNCTION `inscribir_alumno_materia` (`p_id_alumno` INT, `p_id_materia` INT) RETURNS VARCHAR(255) CHARSET utf8mb4 DETERMINISTIC BEGIN
    DECLARE v_semestre_materia TINYINT;
    DECLARE v_mes_actual TINYINT;
    DECLARE v_anio_actual YEAR;
    DECLARE v_inscripciones_existentes INT DEFAULT 0;
    DECLARE v_msg VARCHAR(255);

    -- Obtener el semestre al que pertenece la materia
    SELECT semestre INTO v_semestre_materia
    FROM materia
    WHERE id_materia = p_id_materia;

    SET v_mes_actual = MONTH(CURDATE());
    SET v_anio_actual = YEAR(CURDATE());

    -- Verificar si ya existe inscripción para ese alumno, materia y año
    SELECT COUNT(*) INTO v_inscripciones_existentes
    FROM inscripcion_materia
    WHERE id_alumno = p_id_alumno
      AND id_materia = p_id_materia
      AND anio_academico = v_anio_actual;

    IF v_inscripciones_existentes > 0 THEN
        SET v_msg = 'Inscripción rechazada. Ya estás inscripto en esta materia para el año académico actual.';
    
    ELSEIF (v_semestre_materia = 1 AND v_mes_actual IN (12, 1, 2)) OR
           (v_semestre_materia = 2 AND v_mes_actual IN (7, 8)) THEN

        -- Insertar inscripción válida
        INSERT INTO inscripcion_materia(id_alumno, id_materia, anio_academico, semestre)
        VALUES (p_id_alumno, p_id_materia, v_anio_actual, v_semestre_materia);

        SET v_msg = 'Inscripción realizada con éxito.';
    
    ELSE
        -- Mensaje personalizado según el semestre
        IF v_semestre_materia = 1 THEN
            SET v_msg = 'Inscripción rechazada. Las materias del primer semestre solo pueden inscribirse en diciembre, enero o febrero. Por favor, intenta nuevamente en esas fechas.';
        ELSE
            SET v_msg = 'Inscripción rechazada. Las materias del segundo semestre solo pueden inscribirse en julio o agosto. Por favor, intenta nuevamente en esas fechas.';
        END IF;
    END IF;

    RETURN v_msg;
END$$

DELIMITER ;

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
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `alumno`
--

INSERT INTO `alumno` (`id_alumno`, `id_usuario`, `id_carrera`, `fecha_ingreso`) VALUES
(1, 1, 1, '2025-03-01'),
(2, 2, 1, '2025-03-01'),
(3, 3, 1, '2025-03-01'),
(4, 4, 1, '2025-03-01'),
(5, 5, 1, '2025-03-01'),
(6, 6, 1, '2025-03-01');

-- --------------------------------------------------------

--
-- Table structure for table `carrera`
--

DROP TABLE IF EXISTS `carrera`;
CREATE TABLE IF NOT EXISTS `carrera` (
  `id_carrera` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  PRIMARY KEY (`id_carrera`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `carrera`
--

INSERT INTO `carrera` (`id_carrera`, `nombre`) VALUES
(2, 'Abogacía'),
(3, 'Ingeniería en Alimentos'),
(1, 'Licenciatura en Sistemas');

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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `estado_inscripcion_materia`
--

INSERT INTO `estado_inscripcion_materia` (`id_estado`, `nombre`) VALUES
(4, 'cursar'),
(2, 'promocion'),
(3, 'recursar'),
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
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `evaluacion`
--

INSERT INTO `evaluacion` (`id_evaluacion`, `id_inscripcion`, `id_tipo`, `fecha`, `nota`) VALUES
(1, 1, 1, '2025-04-18', 6.0),
(2, 1, 2, '2025-04-18', 9.0),
(3, 2, 1, '2025-04-18', 9.0),
(4, 2, 2, '2025-04-18', 3.0),
(5, 2, 4, '2025-04-30', 6.0),
(6, 3, 1, '2025-04-18', 10.0),
(7, 3, 2, '2025-04-18', 8.0),
(8, 4, 1, '2025-04-18', 2.0),
(9, 4, 2, '2025-04-18', 3.0),
(10, 4, 3, '2025-04-18', 4.0),
(11, 4, 4, '2025-04-30', 4.0),
(12, 5, 1, '2025-04-18', 9.0),
(13, 5, 2, '2025-04-18', 9.5),
(14, 6, 1, '2025-04-18', 5.0),
(15, 6, 2, '2025-04-18', 5.0),
(16, 6, 3, '2025-04-18', 6.0),
(17, 6, 4, '2025-04-18', 6.0);

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
  `id_estado` int NOT NULL DEFAULT '4',
  `intentos_final` tinyint DEFAULT '0',
  PRIMARY KEY (`id_inscripcion`),
  KEY `id_alumno` (`id_alumno`),
  KEY `id_materia` (`id_materia`),
  KEY `id_estado` (`id_estado`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `inscripcion_materia`
--

INSERT INTO `inscripcion_materia` (`id_inscripcion`, `id_alumno`, `id_materia`, `anio_academico`, `semestre`, `id_estado`, `intentos_final`) VALUES
(1, 1, 1, '2025', 1, 4, 0),
(2, 2, 1, '2025', 1, 4, 0),
(3, 3, 1, '2025', 1, 4, 0),
(4, 4, 1, '2025', 1, 4, 0),
(5, 5, 1, '2025', 1, 4, 0),
(6, 6, 1, '2025', 1, 4, 0);

--
-- Triggers `inscripcion_materia`
--
DROP TRIGGER IF EXISTS `trg_insertar_nota_final_automaticamente`;
DELIMITER $$
CREATE TRIGGER `trg_insertar_nota_final_automaticamente` AFTER UPDATE ON `inscripcion_materia` FOR EACH ROW BEGIN
    DECLARE parcial1 DECIMAL(3,1);
    DECLARE parcial2 DECIMAL(3,1);
    DECLARE promedio DECIMAL(3,1);
    DECLARE id_tipo_final INT;

    IF @desactivar_trigger_final IS NULL OR @desactivar_trigger_final = FALSE THEN
        IF NEW.id_estado = (SELECT id_estado FROM estado_inscripcion_materia WHERE nombre = 'promocion')
           AND OLD.id_estado <> NEW.id_estado THEN

            SELECT MAX(e.nota)
            INTO parcial1
            FROM evaluacion e
            JOIN tipo_evaluacion t ON e.id_tipo = t.id_tipo
            WHERE e.id_inscripcion = NEW.id_inscripcion AND t.nombre = 'parcial1';

            SELECT MAX(e.nota)
            INTO parcial2
            FROM evaluacion e
            JOIN tipo_evaluacion t ON e.id_tipo = t.id_tipo
            WHERE e.id_inscripcion = NEW.id_inscripcion AND t.nombre = 'parcial2';

            SET promedio = ROUND((parcial1 + parcial2)/2, 1);

            SELECT id_tipo INTO id_tipo_final FROM tipo_evaluacion WHERE nombre = 'final';

            IF NOT EXISTS (
                SELECT 1 FROM evaluacion
                WHERE id_inscripcion = NEW.id_inscripcion AND id_tipo = id_tipo_final
            ) THEN
                INSERT INTO evaluacion(id_inscripcion, id_tipo, fecha, nota)
                VALUES (NEW.id_inscripcion, id_tipo_final, CURDATE(), promedio);
            END IF;

        END IF;
    END IF;
END
$$
DELIMITER ;

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
) ENGINE=InnoDB AUTO_INCREMENT=87 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
(80, 'Seminario de Tesis', 4, 2, 2),
(86, 'Tomate I', 1, 1, 3);

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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `profesor`
--

INSERT INTO `profesor` (`id_profesor`, `id_usuario`, `titulo_academico`, `especialidad`) VALUES
(1, 7, 'Profesor Universitario', 'Ciencias Exactas'),
(2, 8, 'Profesor Universitario', 'Ciencias Exactas');

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

--
-- Dumping data for table `profesor_materia`
--

INSERT INTO `profesor_materia` (`id_profesor`, `id_materia`) VALUES
(1, 1),
(2, 1);

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
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `usuario`
--

INSERT INTO `usuario` (`id_usuario`, `contrasena`, `id_rol`, `nombre`, `apellido`, `direccion`, `telefono`, `email`, `activo`) VALUES
(1, '81dc9bdb52d04dc20036dbd8313ed055', 2, 'Juan', 'Alonso', 'Calle Random 123', '2616743521', 'juan.alonso@alumno.com', 1),
(2, '81dc9bdb52d04dc20036dbd8313ed055', 2, 'Jorge', 'Perez', 'Calle Random 123', '2616743521', 'jorge.perez@alumno.com', 1),
(3, '81dc9bdb52d04dc20036dbd8313ed055', 2, 'Carla', 'Suarez', 'Calle Random 123', '2616743521', 'carla.suarez@alumno.com', 1),
(4, '81dc9bdb52d04dc20036dbd8313ed055', 2, 'Delfina', 'Quiroga', 'Calle Random 123', '2616743521', 'delfina.quiroga@alumno.com', 1),
(5, '81dc9bdb52d04dc20036dbd8313ed055', 2, 'Pablo', 'Ramiz', 'Calle Random 123', '2616743521', 'pablo.ramiz@alumno.com', 1),
(6, '81dc9bdb52d04dc20036dbd8313ed055', 2, 'Martina', 'Ruiz', 'Calle Random 123', '2616743521', 'martina.ruiz@alumno.com', 1),
(7, '81dc9bdb52d04dc20036dbd8313ed055', 3, 'Pedro', 'Alonso', 'Calle Random 124', '2616743521', 'pedro.alonso@profesor.com', 1),
(8, '81dc9bdb52d04dc20036dbd8313ed055', 3, 'Marisa', 'Rodriguez', 'Calle Random 124', '2616743521', 'marisa.rodriguez@profesor.com', 1),
(9, '81dc9bdb52d04dc20036dbd8313ed055', 1, 'admin', 'admin', 'Calle Admin 100', '2616946712', 'admin@admin.com', 1);

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_notas_por_inscripcion`
-- (See below for the actual view)
--
DROP VIEW IF EXISTS `v_notas_por_inscripcion`;
CREATE TABLE IF NOT EXISTS `v_notas_por_inscripcion` (
`id_inscripcion` int
,`id_materia` int
,`anio_academico` year
,`id_alumno` int
,`nombre_alumno` varchar(100)
,`apellido_alumno` varchar(100)
,`estado_inscripcion` varchar(50)
,`parcial_1` decimal(3,1)
,`parcial_2` decimal(3,1)
,`recuperatorio_1` decimal(3,1)
,`recuperatorio_2` decimal(3,1)
,`nota_final` decimal(3,1)
);

-- --------------------------------------------------------

--
-- Structure for view `v_notas_por_inscripcion`
--
DROP TABLE IF EXISTS `v_notas_por_inscripcion`;

DROP VIEW IF EXISTS `v_notas_por_inscripcion`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_notas_por_inscripcion`  AS SELECT `im`.`id_inscripcion` AS `id_inscripcion`, `im`.`id_materia` AS `id_materia`, `im`.`anio_academico` AS `anio_academico`, `a`.`id_alumno` AS `id_alumno`, `u`.`nombre` AS `nombre_alumno`, `u`.`apellido` AS `apellido_alumno`, `eim`.`nombre` AS `estado_inscripcion`, max((case when (`tev`.`nombre` = 'parcial1') then `ev`.`nota` end)) AS `parcial_1`, max((case when (`tev`.`nombre` = 'parcial2') then `ev`.`nota` end)) AS `parcial_2`, max((case when (`tev`.`nombre` = 'recuperatorio1') then `ev`.`nota` end)) AS `recuperatorio_1`, max((case when (`tev`.`nombre` = 'recuperatorio2') then `ev`.`nota` end)) AS `recuperatorio_2`, max((case when (`tev`.`nombre` = 'final') then `ev`.`nota` end)) AS `nota_final` FROM (((((`inscripcion_materia` `im` join `alumno` `a` on((`im`.`id_alumno` = `a`.`id_alumno`))) join `usuario` `u` on((`a`.`id_usuario` = `u`.`id_usuario`))) join `estado_inscripcion_materia` `eim` on((`im`.`id_estado` = `eim`.`id_estado`))) left join `evaluacion` `ev` on((`ev`.`id_inscripcion` = `im`.`id_inscripcion`))) left join `tipo_evaluacion` `tev` on((`ev`.`id_tipo` = `tev`.`id_tipo`))) GROUP BY `im`.`id_inscripcion`, `im`.`id_materia`, `im`.`anio_academico`, `a`.`id_alumno`, `u`.`nombre`, `u`.`apellido`, `eim`.`nombre` ;

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


-- Creates the initial data for the database (Alumnos)
INSERT INTO `usuario`(`id_usuario`, `contrasena`, `id_rol`, `nombre`, `apellido`, `direccion`, `telefono`, `email`, `activo`) VALUES (1, md5('1234'), 2, 'Juan','Alonso','Calle Random 123','2616743521','juan.alonso@alumno.com', true);
INSERT INTO `alumno`(`id_alumno`, `id_usuario`, `id_carrera`, `fecha_ingreso`) VALUES (1,1,1,'2025-03-01');
INSERT INTO `inscripcion_materia`(`id_inscripcion`, `id_alumno`, `id_materia`, `anio_academico`, `semestre`) VALUES (1,1,1,2025,1);
INSERT INTO `evaluacion`(`id_inscripcion`, `id_tipo`, `fecha`, `nota`) VALUES (1,1,'2025-04-18',6);
INSERT INTO `evaluacion`(`id_inscripcion`, `id_tipo`, `fecha`, `nota`) VALUES (1,2,'2025-04-18',9);

INSERT INTO `usuario`(`id_usuario`, `contrasena`, `id_rol`, `nombre`, `apellido`, `direccion`, `telefono`, `email`, `activo`) VALUES (2, md5('1234'), 2, 'Jorge','Perez','Calle Random 123','2616743521','jorge.perez@alumno.com', true);
INSERT INTO `alumno`(`id_alumno`, `id_usuario`, `id_carrera`, `fecha_ingreso`) VALUES (2,2,1,'2025-03-01');
INSERT INTO `inscripcion_materia`(`id_inscripcion`, `id_alumno`, `id_materia`, `anio_academico`, `semestre`) VALUES (2,2,1,2025,1);
INSERT INTO `evaluacion`(`id_inscripcion`, `id_tipo`, `fecha`, `nota`) VALUES (2,1,'2025-04-18',9);
INSERT INTO `evaluacion`(`id_inscripcion`, `id_tipo`, `fecha`, `nota`) VALUES (2,2,'2025-04-18',3);
INSERT INTO `evaluacion`(`id_inscripcion`, `id_tipo`, `fecha`, `nota`) VALUES (2,4,'2025-04-30',6);

INSERT INTO `usuario`(`id_usuario`, `contrasena`, `id_rol`, `nombre`, `apellido`, `direccion`, `telefono`, `email`, `activo`) VALUES (3, md5('1234'), 2, 'Carla','Suarez','Calle Random 123','2616743521','carla.suarez@alumno.com', true);
INSERT INTO `alumno`(`id_alumno`, `id_usuario`, `id_carrera`, `fecha_ingreso`) VALUES (3,3,1,'2025-03-01');
INSERT INTO `inscripcion_materia`(`id_inscripcion`, `id_alumno`, `id_materia`, `anio_academico`, `semestre`) VALUES (3,3,1,2025,1);
INSERT INTO `evaluacion`(`id_inscripcion`, `id_tipo`, `fecha`, `nota`) VALUES (3,1,'2025-04-18',10);
INSERT INTO `evaluacion`(`id_inscripcion`, `id_tipo`, `fecha`, `nota`) VALUES (3,2,'2025-04-18',8);

INSERT INTO `usuario`(`id_usuario`, `contrasena`, `id_rol`, `nombre`, `apellido`, `direccion`, `telefono`, `email`, `activo`) VALUES (4, md5('1234'), 2, 'Delfina','Quiroga','Calle Random 123','2616743521','delfina.quiroga@alumno.com', true);
INSERT INTO `alumno`(`id_alumno`, `id_usuario`, `id_carrera`, `fecha_ingreso`) VALUES (4,4,1,'2025-03-01');
INSERT INTO `inscripcion_materia`(`id_inscripcion`, `id_alumno`, `id_materia`, `anio_academico`, `semestre`) VALUES (4,4,1,2025,1);
INSERT INTO `evaluacion`(`id_inscripcion`, `id_tipo`, `fecha`, `nota`) VALUES (4,1,'2025-04-18',2);
INSERT INTO `evaluacion`(`id_inscripcion`, `id_tipo`, `fecha`, `nota`) VALUES (4,2,'2025-04-18',3);
INSERT INTO `evaluacion`(`id_inscripcion`, `id_tipo`, `fecha`, `nota`) VALUES (4,3,'2025-04-18',4);
INSERT INTO `evaluacion`(`id_inscripcion`, `id_tipo`, `fecha`, `nota`) VALUES (4,4,'2025-04-30',4);

INSERT INTO `usuario`(`id_usuario`, `contrasena`, `id_rol`, `nombre`, `apellido`, `direccion`, `telefono`, `email`, `activo`) VALUES (5, md5('1234'), 2, 'Pablo','Ramiz','Calle Random 123','2616743521','pablo.ramiz@alumno.com', true);
INSERT INTO `alumno`(`id_alumno`, `id_usuario`, `id_carrera`, `fecha_ingreso`) VALUES (5,5,1,'2025-03-01');
INSERT INTO `inscripcion_materia`(`id_inscripcion`, `id_alumno`, `id_materia`, `anio_academico`, `semestre`) VALUES (5,5,1,2025,1);
INSERT INTO `evaluacion`(`id_inscripcion`, `id_tipo`, `fecha`, `nota`) VALUES (5,1,'2025-04-18',9);
INSERT INTO `evaluacion`(`id_inscripcion`, `id_tipo`, `fecha`, `nota`) VALUES (5,2,'2025-04-18',9.5);

INSERT INTO `usuario`(`id_usuario`, `contrasena`, `id_rol`, `nombre`, `apellido`, `direccion`, `telefono`, `email`, `activo`) VALUES (6, md5('1234'), 2, 'Martina','Ruiz','Calle Random 123','2616743521','martina.ruiz@alumno.com', true);
INSERT INTO `alumno`(`id_alumno`, `id_usuario`, `id_carrera`, `fecha_ingreso`) VALUES (6,6,1,'2025-03-01');
INSERT INTO `inscripcion_materia`(`id_inscripcion`, `id_alumno`, `id_materia`, `anio_academico`, `semestre`) VALUES (6,6,1,2025,1);
INSERT INTO `evaluacion`(`id_inscripcion`, `id_tipo`, `fecha`, `nota`) VALUES (6,1,'2025-04-18',5);
INSERT INTO `evaluacion`(`id_inscripcion`, `id_tipo`, `fecha`, `nota`) VALUES (6,2,'2025-04-18',5);
INSERT INTO `evaluacion`(`id_inscripcion`, `id_tipo`, `fecha`, `nota`) VALUES (6,3,'2025-04-18',6);
INSERT INTO `evaluacion`(`id_inscripcion`, `id_tipo`, `fecha`, `nota`) VALUES (6,4,'2025-04-18',6);

-- Creas the initial data for the database (Profesores)

INSERT INTO `usuario`(`id_usuario`, `contrasena`, `id_rol`, `nombre`, `apellido`, `direccion`, `telefono`, `email`, `activo`) VALUES (7, md5('1234'), 3, 'Pedro','Alonso','Calle Random 124','2616743521','pedro.alonso@profesor.com', true)
INSERT INTO `profesor`(`id_profesor`, `id_usuario`, `titulo_academico`, `especialidad`) VALUES (1, 7,'Profesor Universitario','Ciencias Exactas')
INSERT INTO `profesor_materia`(`id_profesor`, `id_materia`) VALUES (1,1)

INSERT INTO `usuario`(`id_usuario`, `contrasena`, `id_rol`, `nombre`, `apellido`, `direccion`, `telefono`, `email`, `activo`) VALUES (8, md5('1234'), 3, 'Marisa','Rodriguez','Calle Random 124','2616743521','marisa.rodriguez@profesor.com', true)
INSERT INTO `profesor`(`id_profesor`, `id_usuario`, `titulo_academico`, `especialidad`) VALUES (2, 8,'Profesor Universitario','Ciencias Exactas')
INSERT INTO `profesor_materia`(`id_profesor`, `id_materia`) VALUES (2,1)


-- Procedimiento

DELIMITER $$

CREATE PROCEDURE cargar_datos_iniciales()
BEGIN
    -- Desactivar restricciones temporales para evitar errores de integridad referencial
    SET FOREIGN_KEY_CHECKS = 0;

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
END$$

DELIMITER ;


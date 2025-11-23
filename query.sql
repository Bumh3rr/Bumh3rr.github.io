-- Base de datos para Sistema de Gestión de Horarios Académicos
DROP DATABASE IF EXISTS railway;
CREATE DATABASE IF NOT EXISTS railway CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE railway;

-- Tabla de docentes
CREATE TABLE IF NOT EXISTS docente
(
    id             INT AUTO_INCREMENT PRIMARY KEY,
    nombre         VARCHAR(100)        NOT NULL,
    apellido       VARCHAR(100)        NOT NULL,
    email          VARCHAR(150) UNIQUE NOT NULL,
    telefono       VARCHAR(20),
    rfc            VARCHAR(13) UNIQUE,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    activo         BOOLEAN   DEFAULT TRUE,
    INDEX idx_email (email),
    INDEX idx_rfc (rfc)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

-- Tabla de carreras
CREATE TABLE IF NOT EXISTS carreras
(
    id                 INT AUTO_INCREMENT PRIMARY KEY,
    nombre             VARCHAR(150)       NOT NULL,
    codigo             VARCHAR(20) UNIQUE NOT NULL,
    descripcion        TEXT,
    duracion_semestres INT                NOT NULL,
    activo             BOOLEAN   DEFAULT TRUE,
    fecha_creacion     TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

-- Tabla de materias/asignaturas
CREATE TABLE IF NOT EXISTS materias
(
    id              INT AUTO_INCREMENT PRIMARY KEY,
    nombre          VARCHAR(150)       NOT NULL,
    codigo          VARCHAR(20) UNIQUE NOT NULL,
    carrera_id      INT                NOT NULL,
    semestre        INT                NOT NULL,
    creditos        INT                NOT NULL,
    horas_semanales INT                NOT NULL,
    descripcion     TEXT,
    activo          BOOLEAN   DEFAULT TRUE,
    alumnos_inscriptos      BOOLEAN   DEFAULT FALSE,
    fecha_creacion  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (carrera_id) REFERENCES carreras (id) ON DELETE CASCADE,
    INDEX idx_carrera (carrera_id),
    INDEX idx_semestre (semestre)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

-- Tabla de aulas
CREATE TABLE IF NOT EXISTS aulas
(
    id             INT AUTO_INCREMENT PRIMARY KEY,
    nombre         VARCHAR(100) NOT NULL,
    edificio       VARCHAR(100),
    capacidad      INT          NOT NULL,
    tipo           ENUM(
        'teorica',
        'laboratorio',
        'auditorio',
        'taller',
        'computacion',
        'software'
        ) DEFAULT 'teorica',
    activo         BOOLEAN                                                DEFAULT TRUE,
    fecha_creacion TIMESTAMP                                              DEFAULT CURRENT_TIMESTAMP
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

-- Tabla de grupos
CREATE TABLE IF NOT EXISTS grupos
(
    id                INT AUTO_INCREMENT PRIMARY KEY,
    materia_id        INT         NOT NULL,
    profesor_id       INT         NULL,
    nombre            VARCHAR(50) NOT NULL,
    cupo_maximo       INT         NOT NULL,
    alumnos_inscriptos INT         NOT NULL,
    semestre_actual   VARCHAR(20) NOT NULL,
    periodo_academico VARCHAR(50) NOT NULL,
    activo            BOOLEAN   DEFAULT TRUE,
    fecha_creacion    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (materia_id) REFERENCES materias (id) ON DELETE CASCADE,
    FOREIGN KEY (profesor_id) REFERENCES docente (id) ON DELETE SET NULL,
    INDEX idx_materia (materia_id),
    INDEX idx_profesor (profesor_id),
    INDEX idx_periodo (periodo_academico)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

-- Tabla de horarios
CREATE TABLE IF NOT EXISTS horarios
(
    id             INT AUTO_INCREMENT PRIMARY KEY,
    grupo_id       INT                                                                  NOT NULL,
    aula_id        INT                                                                  NOT NULL,
    dia_semana     ENUM ('Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes')           NOT NULL,
    hora_inicio    TIME                                                                 NOT NULL,
    hora_fin       TIME                                                                 NOT NULL,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (grupo_id) REFERENCES grupos (id) ON DELETE CASCADE,
    FOREIGN KEY (aula_id) REFERENCES aulas (id) ON DELETE CASCADE,
    INDEX idx_grupo (grupo_id),
    INDEX idx_aula (aula_id),
    INDEX idx_dia (dia_semana)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

-- Insertar datos de ejemplo
INSERT INTO carreras (nombre, codigo, descripcion, duracion_semestres)
VALUES
    ('Ingeniería en Sistemas Computacionales', 'ISC', 'Carrera enfocada en el desarrollo de software y sistemas', 9),
    ('Ingeniería Civil', 'IC', 'Carrera de construcción e infraestructura', 10),
    ('Licenciatura en Administración', 'LA', 'Carrera de gestión empresarial', 8),
    ('Licenciatura en Contaduría', 'LC', 'Carrera de Contador Público', 10),
    ('Ingeniería Informática', 'II', 'Carrera de Ingeniería en Informática', 10)
;

INSERT INTO docente (nombre, apellido, email, rfc, telefono)
VALUES ('Juan', 'Pérez García', 'juan.perez@profesor.com', 'PEGJ850101ABC', '7471234567'),
       ('María', 'López Hernández', 'maria.lopez@profesor.com', 'LOHM860202XYZ', '7471234568'),
       ('Carlos', 'González Ruiz', 'carlos.gonzalez@docente.com', 'GORC870303DEF', '7471234569'),
       ('Ana', 'Martínez Sánchez', 'ana.martinez@docente.com', 'MASA880404GHI', '7471234570')
;

INSERT INTO materias (nombre, codigo, carrera_id, semestre, creditos, horas_semanales, descripcion)
VALUES ('Programación Orientada a Objetos', 'POO-101', 1, 3, 8, 6, 'Fundamentos de POO con Java'),
       ('Bases de Datos', 'BD-201', 1, 4, 8, 6, 'Diseño y gestión de bases de datos'),
       ('Estructuras de Datos', 'ED-102', 1, 3, 8, 6, 'Algoritmos y estructuras de datos'),
       ('Desarrollo Web', 'DW-301', 1, 5, 6, 5, 'Desarrollo de aplicaciones web');

INSERT INTO aulas (nombre, edificio, capacidad, tipo)
VALUES ('R1-01', 'R1', 30, 'computacion'),
       ('R1-02', 'R1', 30, 'computacion'),
       ('R1-03', 'R1', 30, 'teorica'),
       ('R1-04', 'R1', 30, 'computacion'),
       ('R1-05', 'R1', 30, 'software');

INSERT INTO aulas (nombre, edificio, capacidad, tipo)
VALUES ('R2-01', 'R2', 30, 'teorica'),
       ('R2-02', 'R2', 30, 'computacion'),
       ('R2-03', 'R2', 30, 'laboratorio'),
       ('R2-04', 'R2', 30, 'computacion'),
       ('R2-05', 'R2', 30, 'taller');

INSERT INTO aulas (nombre, edificio, capacidad, tipo)
VALUES ('E-01', 'E', 30, 'laboratorio'),
       ('E-02', 'E', 30, 'taller'),
       ('E-03', 'E', 30, 'auditorio'),
       ('E-04', 'E', 30, 'teorica'),
       ('E-05', 'E', 30, 'taller');

INSERT INTO aulas (nombre, edificio, capacidad, tipo)
VALUES ('P-01', 'P', 30, 'taller'),
       ('P-02', 'P', 30, 'software'),
       ('P-03', 'P', 30, 'laboratorio'),
       ('P-04', 'P', 30, 'taller'),
       ('P-05', 'P', 30, 'taller');

INSERT INTO grupos (materia_id, profesor_id, nombre, cupo_maximo,alumnos_inscriptos, semestre_actual, periodo_academico)
VALUES (1, 1, 'POO-A', 30,30, '3', 'Agosto-Diciembre 2024'),
       (2, 2, 'BD-A', 30,30, '4', 'Agosto-Diciembre 2024'),
       (3, 3, 'ED-A', 30, 30,'3', 'Agosto-Diciembre 2024');

INSERT INTO horarios (grupo_id, aula_id, dia_semana, hora_inicio, hora_fin)
VALUES (1, 1, 'Lunes', '07:00:00', '09:00:00'),
       (1, 1, 'Viernes', '07:00:00', '09:00:00'),
       (2, 2, 'Martes', '09:00:00', '11:00:00'),
       (2, 2, 'Jueves', '09:00:00', '11:00:00'),
       (3, 3, 'Lunes', '11:00:00', '13:00:00');


select * from grupos;
select * from materias;
-- insert_mysql.sql
-- Datos de ejemplo para el sistema de registro de giras
-- Compatible con MySQL

-- Usuarios (login)
INSERT INTO Usuarios (Nombre, Correo, ContrasenaHash, Rol) VALUES
('Mario Barsallo', 'mario.barsallo@up.ac.pa', 'hash123', 'Administrador'),
('Cristopher Hull', 'cristopher.hull@up.ac.pa', 'hash456', 'Estudiante');

-- Facultades
INSERT INTO Facultades (Nombre) VALUES
('Ingeniería de Sistemas'),
('Ingeniería Electrónica');

-- Organizadores
INSERT INTO Organizadores (Nombre, Correo, FacultadID) VALUES
('Ana Torres', 'ana.torres@up.ac.pa', 1),
('Carlos Méndez', 'carlos.mendez@up.ac.pa', 2);

-- Giras disponibles
INSERT INTO Giras (Nombre, Fecha, Lugar, Descripcion, OrganizadorID) VALUES
('Visita a Cable & Wireless', '2025-11-15', 'Panamá', 'Recorrido por centro de datos', 1),
('Tour a UTP', '2025-12-01', 'Campus Tocumen', 'Exploración de laboratorios de redes', 2);

-- Inscripciones (registro de selección de giras)
INSERT INTO Inscripciones (UsuarioID, GiraID, Estado) VALUES
(2, 1, 'Registrado'),
(2, 2, 'Registrado');
-- update_mysql.sql
-- Actualizaciones para el sistema de registro de giras
-- Compatible con MySQL

-- 1. Actualizar la contraseña de un usuario
UPDATE Usuarios
SET ContrasenaHash = 'nuevoHash789'
WHERE Correo = 'cristopher.hull@up.ac.pa';

-- 2. Cambiar el estado de una inscripción
UPDATE Inscripciones
SET Estado = 'Confirmado'
WHERE UsuarioID = 2 AND GiraID = 1;

-- 3. Modificar la fecha de una gira
UPDATE Giras
SET Fecha = '2025-11-20'
WHERE Nombre = 'Visita a Cable & Wireless';

-- 4. Actualizar el correo de un organizador
UPDATE Organizadores
SET Correo = 'ana.torres.actualizado@up.ac.pa'
WHERE OrganizadorID = 1;

-- 5. Cambiar el nombre de una facultad
UPDATE Facultades
SET Nombre = 'Ingeniería en Sistemas Computacionales'
WHERE FacultadID = 1;

-- 6. Editar un comentario
UPDATE Comentarios
SET Texto = 'Excelente recorrido, aprendí mucho.'
WHERE ComentarioID = 1;

-- 7. Corregir navegador en bitácora de accesos
UPDATE BitacoraAccesos
SET Navegador = 'Edge'
WHERE AccesoID = 2;
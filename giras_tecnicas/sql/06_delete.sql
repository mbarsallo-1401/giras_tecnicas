-- delete_mysql.sql
-- Eliminaciones controladas para el sistema de registro de giras

-- 1. Eliminar un comentario específico
DELETE FROM Comentarios
WHERE ComentarioID = 1;

-- 2. Cancelar una inscripción (eliminar registro)
DELETE FROM Inscripciones
WHERE UsuarioID = 2 AND GiraID = 1;

-- 3. Eliminar un acceso de bitácora
DELETE FROM BitacoraAccesos
WHERE AccesoID = 2;

-- 4. Eliminar una gira (solo si no tiene inscripciones ni comentarios)
DELETE FROM Giras
WHERE GiraID = 2
AND GiraID NOT IN (SELECT GiraID FROM Inscripciones)
AND GiraID NOT IN (SELECT GiraID FROM Comentarios);

-- 5. Eliminar un organizador (solo si no tiene giras asociadas)
DELETE FROM Organizadores
WHERE OrganizadorID = 2
AND OrganizadorID NOT IN (SELECT OrganizadorID FROM Giras);

-- 6. Eliminar un usuario (solo si no tiene inscripciones ni comentarios ni accesos)
DELETE FROM Usuarios
WHERE UsuarioID = 2
AND UsuarioID NOT IN (SELECT UsuarioID FROM Inscripciones)
AND UsuarioID NOT IN (SELECT UsuarioID FROM Comentarios)
AND UsuarioID NOT IN (SELECT UsuarioID FROM BitacoraAccesos);
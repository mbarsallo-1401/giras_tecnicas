-- select_mysql.sql
-- Consultas SELECT para el sistema de registro de giras
-- Compatible con MySQL

-- 1. Listar todos los usuarios registrados
SELECT UsuarioID, Nombre, Correo, Rol, FechaRegistro
FROM Usuarios;

-- 2. Mostrar todas las giras disponibles
SELECT GiraID, Nombre, Fecha, Lugar, Descripcion
FROM Giras;

-- 3. Ver inscripciones por estudiante
SELECT U.Nombre AS Estudiante, G.Nombre AS Gira, I.Estado, I.FechaInscripcion
FROM Inscripciones I
JOIN Usuarios U ON I.UsuarioID = U.UsuarioID
JOIN Giras G ON I.GiraID = G.GiraID;

-- 4. Ver comentarios por gira
SELECT G.Nombre AS Gira, U.Nombre AS Usuario, C.Texto, C.FechaComentario
FROM Comentarios C
JOIN Usuarios U ON C.UsuarioID = U.UsuarioID
JOIN Giras G ON C.GiraID = G.GiraID;

-- 5. Ver accesos recientes (bitácora)
SELECT U.Nombre AS Usuario, B.FechaAcceso, B.IP, B.Navegador
FROM BitacoraAccesos B
JOIN Usuarios U ON B.UsuarioID = U.UsuarioID;

-- 6. Listar organizadores y su facultad
SELECT O.Nombre AS Organizador, O.Correo, F.Nombre AS Facultad
FROM Organizadores O
JOIN Facultades F ON O.FacultadID = F.FacultadID;

-- 7. Contar inscripciones por gira
SELECT G.Nombre AS Gira, COUNT(*) AS TotalInscritos
FROM Inscripciones I
JOIN Giras G ON I.GiraID = G.GiraID
GROUP BY G.Nombre;
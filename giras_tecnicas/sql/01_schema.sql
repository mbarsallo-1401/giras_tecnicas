-- schema_mysql.sql
-- Proyecto: Registro de Giras Técnicas - Universidad de Panamá
-- Versión MySQL

CREATE TABLE Usuarios (
    UsuarioID INT PRIMARY KEY AUTO_INCREMENT,
    Nombre VARCHAR(100) NOT NULL,
    Correo VARCHAR(100) UNIQUE NOT NULL,
    ContrasenaHash VARCHAR(256) NOT NULL,
    Rol VARCHAR(50) CHECK (Rol IN ('Estudiante', 'Organizador', 'Administrador')),
    FechaRegistro DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE Facultades (
    FacultadID INT PRIMARY KEY AUTO_INCREMENT,
    Nombre VARCHAR(100) NOT NULL
);

CREATE TABLE Organizadores (
    OrganizadorID INT PRIMARY KEY AUTO_INCREMENT,
    Nombre VARCHAR(100) NOT NULL,
    Correo VARCHAR(100),
    FacultadID INT,
    FOREIGN KEY (FacultadID) REFERENCES Facultades(FacultadID)
);

CREATE TABLE Giras (
    GiraID INT PRIMARY KEY AUTO_INCREMENT,
    Nombre VARCHAR(100) NOT NULL,
    Fecha DATE NOT NULL,
    Lugar VARCHAR(100),
    Descripcion TEXT,
    OrganizadorID INT,
    FOREIGN KEY (OrganizadorID) REFERENCES Organizadores(OrganizadorID)
);

CREATE TABLE Inscripciones (
    InscripcionID INT PRIMARY KEY AUTO_INCREMENT,
    UsuarioID INT,
    GiraID INT,
    FechaInscripcion DATETIME DEFAULT CURRENT_TIMESTAMP,
    Estado VARCHAR(50) CHECK (Estado IN ('Registrado', 'Cancelado', 'Confirmado')),
    FOREIGN KEY (UsuarioID) REFERENCES Usuarios(UsuarioID),
    FOREIGN KEY (GiraID) REFERENCES Giras(GiraID)
);

CREATE TABLE BitacoraAccesos (
    AccesoID INT PRIMARY KEY AUTO_INCREMENT,
    UsuarioID INT,
    FechaAcceso DATETIME DEFAULT CURRENT_TIMESTAMP,
    IP VARCHAR(50),
    Navegador VARCHAR(100),
    FOREIGN KEY (UsuarioID) REFERENCES Usuarios(UsuarioID)
);

CREATE TABLE Comentarios (
    ComentarioID INT PRIMARY KEY AUTO_INCREMENT,
    UsuarioID INT,
    GiraID INT,
    Texto TEXT,
    FechaComentario DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (UsuarioID) REFERENCES Usuarios(UsuarioID),
    FOREIGN KEY (GiraID) REFERENCES Giras(GiraID)
);
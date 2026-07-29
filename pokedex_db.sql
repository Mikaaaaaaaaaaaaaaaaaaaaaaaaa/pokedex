CREATE DATABASE IF NOT EXISTS pokedex_db;
USE pokedex_db;

CREATE TABLE IF NOT EXISTS tipos (
    identificador INTEGER AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    imagen_ruta VARCHAR(255) NOT NULL
    );

CREATE TABLE IF NOT EXISTS usuarios (
    identificador INTEGER AUTO_INCREMENT PRIMARY KEY,
    usuario VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
    );

CREATE TABLE IF NOT EXISTS pokemon (
    identificador INTEGER AUTO_INCREMENT PRIMARY KEY,
    numero INTEGER NOT NULL UNIQUE,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    imagen_ruta VARCHAR(255) NOT NULL,
    tipo_identificador INTEGER NOT NULL,

    FOREIGN KEY (tipo_identificador) REFERENCES tipos(identificador)
    );

INSERT INTO tipos (nombre, imagen_ruta) VALUES
    ('Planta', 'assets/img/tipos/planta.png'),
    ('Fuego', 'assets/img/tipos/fuego.png'),
    ('Agua', 'assets/img/tipos/agua.png');

INSERT INTO usuarios (usuario, password)
VALUES ('administrador', '$2y$10$QoY.D7P7IqH0b.N9W1.z3.H4e.Kz.F6J/w8N9X.Z2g.3tQ.jX.2');

INSERT INTO pokemon (numero, nombre, descripcion, imagen_ruta, tipo_identificador) VALUES
    (1, 'Bulbasaur', 'Este Pokémon nace con una semilla en el lomo.', 'assets/img/pokemon/001.png', 1),
    (4, 'Charmander', 'Prefiere los sitios calientes. Dicen que cuando llueve sale vapor de la punta de su cola.', 'assets/img/pokemon/004.png', 2),
    (7, 'Squirtle', 'Cuando retrae su largo cuello en el caparazón, dispara agua a una presión increíble.', 'assets/img/pokemon/007.png', 3);
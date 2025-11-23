-- Active: 1763402130729@@127.0.0.1@5432@shopper
CREATE TABLE usuarios (
    id SERIAL PRIMARY KEY,
    email VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    rol VARCHAR(255) NOT NULL,
    activo BOOLEAN DEFAULT TRUE,
    fecha_creacion TIMESTAMP DEFAULT NOW()
);

CREATE TABLE productos (
    id SERIAL PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    codigo VARCHAR(100) NOT NULL,
    precio_costo NUMERIC(10,2) NOT NULL,
    precio_venta NUMERIC(10,2) NOT NULL,
    fecha_creacion TIMESTAMP DEFAULT NOW()
);

CREATE TABLE facturas (
    id SERIAL PRIMARY KEY,
    empleado_id INT REFERENCES usuarios(id),
    cliente_id VARCHAR(100) NOT NULL,
    total_venta INT NOT NULL,
    activo BOOLEAN NOT NULL,
    fecha_creacion TIMESTAMP DEFAULT NOW()
);

CREATE TABLE ventas (
    id SERIAL PRIMARY KEY,
    producto_id INT REFERENCES productos(id),
    factura_id INT REFERENCES facturas(id),
    cantidad INT NOT NULL,
    total_producto INT NOT NULL,
    fecha TIMESTAMP DEFAULT NOW()
);

CREATE TABLE jornadas (
    id SERIAL PRIMARY KEY,
    empleado_id INT REFERENCES usuarios(id),
    hora_inicio TIMESTAMP DEFAULT NOW(),
    hora_fin TIMESTAMP DEFAULT NULL
);
CREATE TABLE IF NOT EXISTS productos (
    id SERIAL PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL,
    descripcion TEXT,
    imagen_url TEXT,
    precio_unitario NUMERIC(10,2) NOT NULL,
    costo_envio NUMERIC(10,2) NOT NULL DEFAULT 0,
    cantidad INT NOT NULL DEFAULT 0,
    calificacion NUMERIC(2,1) CHECK (calificacion >= 0 AND calificacion <= 5),

    creado_en TIMESTAMP DEFAULT NOW(),
    actualizado_en TIMESTAMP DEFAULT NOW()
);

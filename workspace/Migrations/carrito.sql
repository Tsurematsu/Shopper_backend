CREATE TABLE IF NOT EXISTS carrito (
    id SERIAL PRIMARY KEY,

    titulo VARCHAR(255) NOT NULL,
    cantidad_seleccionada INT NOT NULL DEFAULT 1,
    costo_envio NUMERIC(10,2) NOT NULL DEFAULT 0,
    precio_unitario NUMERIC(10,2) NOT NULL,
    imagen_url TEXT,

    -- Relación sin FOREIGN KEY
    cliente_id INT NOT NULL,

    creado_en TIMESTAMP DEFAULT NOW(),
    actualizado_en TIMESTAMP DEFAULT NOW()
);

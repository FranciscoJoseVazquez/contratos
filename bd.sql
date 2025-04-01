-- Crear la base de datos (si no existe)
CREATE DATABASE IF NOT EXISTS contratos;

-- Usar la base de datos
USE contratos;

-- Crear la tabla 'contratos_firmados'
CREATE TABLE IF NOT EXISTS `contratos_firmados` (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fecha_del_contrato DATE NOT NULL,
    denominacion_social VARCHAR(255) NOT NULL,
    domicilio_fiscal VARCHAR(255) NOT NULL,
    identificacion_fiscal VARCHAR(100) NOT NULL,
    apoderado VARCHAR(255) NOT NULL,
    notaria VARCHAR(255) NOT NULL,
    notario VARCHAR(255) NOT NULL,
    protocolo VARCHAR(255) NOT NULL
);

-- Crear la tabla 'contratos_sin_firmar'
CREATE TABLE IF NOT EXISTS `contratos_sin_firmar` (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fecha_del_contrato DATE NOT NULL,
    denominacion_social VARCHAR(255) NOT NULL,
    domicilio_fiscal VARCHAR(255) NOT NULL,
    identificacion_fiscal VARCHAR(100) NOT NULL,
    apoderado VARCHAR(255) NOT NULL,
    notaria VARCHAR(255) NOT NULL,
    notario VARCHAR(255) NOT NULL,
    protocolo VARCHAR(255) NOT NULL
);

DROP TABLE IF EXISTS tipo_usuario;
CREATE TABLE tipo_usuario (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    tipo VARCHAR(255) NOT NULL,
    descricao TEXT
);

INSERT INTO tipo_usuario (tipo, descricao) VALUES ('ADMIN', 'Administrador'), ('COLABORADOR', 'Colaborador'), ('CLIENTE', 'Cliente');
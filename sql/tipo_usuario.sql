DROP TABLE IF EXISTS tipo_usuario;
CREATE TABLE tipo_usuario (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    tipo VARCHAR(255) NOT NULL,
    descricao TEXT,
    pagina_padrao BIGINT,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_atualizacao DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (pagina_padrao) REFERENCES pagina(id)
);

INSERT INTO tipo_usuario (tipo, descricao) VALUES ('ADMIN', 'Administrador'), ('COLABORADOR', 'Colaborador'), ('CLIENTE', 'Cliente');
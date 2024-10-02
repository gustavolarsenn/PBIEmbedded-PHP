DROP TABLE IF EXISTS TipoUsuario;
CREATE TABLE TipoUsuario (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    tipo VARCHAR(255) NOT NULL,
    descricao TEXT,
    pagina_padrao BIGINT,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_atualizacao DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (pagina_padrao) REFERENCES Pagina(id)
);

INSERT INTO TipoUsuario (tipo, descricao, pagina_padrao) VALUES ('ADMIN', 'Administrador', 15), ('COLABORADOR', 'Colaborador', 15), ('CLIENTE', 'Cliente', 15);
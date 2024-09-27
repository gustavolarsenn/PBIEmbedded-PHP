DROP TABLE IF EXISTS PermissoesPagina;
DROP TABLE IF EXISTS RelatorioPBI;
DROP TABLE IF EXISTS SessaoPBI;
DROP TABLE IF EXISTS Usuario;
DROP TABLE IF EXISTS TipoUsuario;
DROP TABLE IF EXISTS Pagina;
DROP TABLE IF EXISTS CategoriasPagina;
DROP TABLE IF EXISTS ControlePrancha;
DROP TABLE IF EXISTS DescarregamentoNavioPlanejado;
DROP TABLE IF EXISTS DescarregamentoNavio;
DROP TABLE IF EXISTS Navio;

CREATE TABLE Navio (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    porto VARCHAR(50),
    navio VARCHAR(50),
    data DATETIME,
    produto VARCHAR(100),
    berco VARCHAR(50),
    volume_manifestado FLOAT,
    modalidade VARCHAR(50),
    prancha_minima FLOAT
);

CREATE TABLE DescarregamentoNavio (
    no BIGINT,
    id_viagem BIGINT,
    ticket VARCHAR(50),
    placa VARCHAR(20),
    peso float,
    data date,
    periodo VARCHAR(50),
    porao INT,
    cliente VARCHAR(100),
    armazem VARCHAR(100),
    transportadora VARCHAR(100),
    cliente_armazem_lote_di_produto VARCHAR(255),
    produto VARCHAR(50),
    observacao VARCHAR(255),
    di VARCHAR(20),
    FOREIGN KEY (id_viagem) REFERENCES Navio(id)
);

CREATE TABLE DescarregamentoNavioPlanejado (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    id_viagem BIGINT,
    cliente VARCHAR(100),
    di VARCHAR(10),
    armazem VARCHAR(100),
    produto VARCHAR(100),
    planejado FLOAT,
    FOREIGN KEY (id_viagem) REFERENCES Navio(id)
);

CREATE TABLE ControlePrancha (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    id_viagem BIGINT,
    relatorio_no VARCHAR(20),
    ternos FLOAT,
    periodo_inicial DATETIME,
    periodo_final DATETIME,
    data DATE,
    duracao TIME,
    chuva TIME,
    transporte TIME,
    forca_maior TIME,
    outros TIME,
    horas_operacionais TIME,
    volume FLOAT,
    meta FLOAT,
    observacao VARCHAR(255),
    FOREIGN KEY (id_viagem) REFERENCES Navio(id)
);

CREATE TABLE RelatorioPBI(
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    id_relatorio VARCHAR(255),
    id_dataset VARCHAR(255),
    relatorio VARCHAR(255),
    relatorio_clean VARCHAR(255),
    rls BOOLEAN DEFAULT FALSE,
    ativo BOOLEAN DEFAULT TRUE
);
INSERT INTO RelatorioPBI (id_relatorio, id_dataset, relatorio, relatorio_clean) VALUES

('44cece6c-3c3a-4343-8c95-5da3fc5aacf1', 'a0c518ac-3314-4cfa-bccb-790d7bd8297e', 'Port Statistics', 'port_statistics'),
('af1d8571-368a-4016-985c-1e53bb0c9aaa', '1160d68b-da8b-4b7e-9b69-efaba2a70d1a', 'Line Up - Forecast', 'line_up_forecast'),
('7d3ce275-3dd3-4c0e-b62b-66a7e9a41ce3', '863e0b95-1073-46ea-abb5-9b65ff4e5d8e', 'Line Up - Brazil', 'line_up_brazil');

CREATE TABLE CategoriasPagina (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    categoria VARCHAR(255),
    categoria_clean VARCHAR(255),
    icon VARCHAR(255),
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_atualizacao DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO CategoriasPagina (categoria, categoria_clean, icon)
VALUES
('Cadastro', 'cadastro', 'icon icon-single-04'),
('Inclusão', 'inclusao', 'icon icon-form'),
('Relatórios', 'relatorio', 'icon icon-layout-25'),
('Relatórios - BI', 'PBI/relatorio_pbi.php?reportName=', 'icon icon-chart-bar-33'),
('Página inicial', 'pagina_inicial', NULL);

CREATE TABLE Pagina (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    pagina VARCHAR(100),
    pagina_clean VARCHAR(100),
    id_categoria BIGINT,
    caminho_pagina VARCHAR(255),
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_atualizacao DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_categoria) REFERENCES CategoriasPagina(id)
);

DELIMITER //

DROP TRIGGER IF EXISTS before_insert_permissoes_pagina//
CREATE TRIGGER before_insert_permissoes_pagina
BEFORE INSERT ON Pagina
FOR EACH ROW
BEGIN
    DECLARE categoria_clean_value VARCHAR(255);
    
    SELECT categoria_clean INTO categoria_clean_value
    FROM CategoriasPagina
    WHERE id = NEW.id_categoria;
    
    IF categoria_clean_value IS NULL THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'categoria_clean_value is NULL';
    END IF;
    
    IF categoria_clean_value = 'PBI/relatorio_pbi.php?reportName=' THEN
        SET NEW.caminho_pagina = CONCAT('views/', categoria_clean_value, NEW.pagina_clean);
    ELSE IF categoria_clean_value = 'pagina_inicial' THEN
        SET NEW.caminho_pagina = CONCAT(categoria_clean_value, NEW.pagina_clean);
    ELSE
        SET NEW.caminho_pagina = CONCAT('views/', categoria_clean_value, '/', NEW.pagina_clean, '.php');
    END IF;
END//

DELIMITER ;

INSERT INTO Pagina (id_categoria, pagina, pagina_clean)
VALUES
(1, 'Navio', 'navio'),
(1, 'Cliente', 'cliente'),
(1, 'Carga', 'carga'),
(1, 'Usuário', 'usuario'),

(2, 'Escala', 'escala'),
(2, 'Período trabalhado', 'periodo_trabalhado'),
(2, 'Plano de Distribuição', 'plano_de_distribuicao'),

(3, 'Relatório de Escala', 'relatorio_de_escala'),
(3, 'Relatório por Período', 'relatorio_por_periodo'),
(3, 'Relatório por Cliente', 'relatorio_por_cliente'),
(3, 'Relatório Chuva', 'relatorio_chuva'),
(3, 'Relatório Balança', 'relatorio_balanca'),
(3, 'Controle de Prancha', 'controle_de_prancha'),

(4, 'Port Statistics', 'port_statistics'),
(4, 'Line Up - Forecast', 'line_up_forecast'),

(5, 'Página inicial', 'index');

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

CREATE TABLE Usuario (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(255),
    email VARCHAR(255),
    senha VARCHAR(255),
    tipo BIGINT,
    ativo BOOLEAN DEFAULT TRUE,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_atualizacao DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (tipo) REFERENCES TipoUsuario(id)
);

DELIMITER //

CREATE TRIGGER set_default_tipo BEFORE INSERT ON Usuario
FOR EACH ROW
BEGIN
    IF NEW.tipo IS NULL THEN
        SET NEW.tipo = (SELECT id FROM TipoUsuario WHERE tipo = 'CLIENTE');
    END IF;
END //

DELIMITER ;

CREATE TABLE SessaoPBI (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    id_usuario BIGINT,
    data_entrada TIMESTAMP DEFAULT CURRENT_TIMESTAMP ,
    data_validade DATETIME DEFAULT (CURRENT_TIMESTAMP + INTERVAL 1 HOUR) ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usuario) REFERENCES Usuario(id)
);

DROP TABLE IF EXISTS PermissoesPagina;
CREATE TABLE PermissoesPagina (
    id_categoria BIGINT,
    id_pagina BIGINT,
    id_tipo_usuario BIGINT,
    ativo BOOLEAN DEFAULT TRUE,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_atualizacao DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_categoria) REFERENCES CategoriasPagina(id),
    FOREIGN KEY (id_tipo_usuario) REFERENCES TipoUsuario(id),
    FOREIGN KEY (id_pagina) REFERENCES Pagina(id),
    PRIMARY KEY (id_pagina, id_tipo_usuario)
);

INSERT INTO PermissoesPagina (id_categoria, id_pagina, id_tipo_usuario)
VALUES
(1, 1, 1),
(1, 1, 2),
(1, 2, 1),
(1, 2, 2),
(1, 3, 1),
(1, 3, 2),
(1, 4, 1),

(2, 5, 1),
(2, 5, 2),
(2, 6, 1),
(2, 6, 2),
(2, 7, 1),
(2, 7, 2),

(3, 8, 1),
(3, 8, 2),
(3, 9, 1),
(3, 9, 2),
(3, 10, 1),
(3, 10, 2),
(3, 11, 1),
(3, 11, 2),
(3, 12, 1),
(3, 12, 2),
(3, 12, 3),
(3, 13, 1),
(3, 13, 2),
(3, 13, 3),

(4, 14, 1),
(4, 14, 2),
(4, 14, 3),
(4, 15, 1),
(4, 15, 2),
(4, 15, 3),

(5, 16, 1),
(5, 16, 2),
(5, 16, 3);
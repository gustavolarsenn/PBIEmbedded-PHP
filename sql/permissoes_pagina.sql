DROP TABLE IF EXISTS permissoes_pagina;
CREATE TABLE permissoes_pagina (
    pagina VARCHAR(100),
    pagina_clean VARCHAR(100),
    id_categoria BIGINT,
    caminho_pagina VARCHAR(255),
    id_tipo_usuario BIGINT,
    ativo BOOLEAN DEFAULT TRUE,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_atualizacao DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_categoria) REFERENCES categorias_pagina(id),
    FOREIGN KEY (id_tipo_usuario) REFERENCES tipo_usuario(id),
    PRIMARY KEY (pagina, id_tipo_usuario)
);

DELIMITER //

CREATE TRIGGER before_insert_permissoes_pagina
BEFORE INSERT ON permissoes_pagina
FOR EACH ROW
BEGIN
    DECLARE categoria_clean_value VARCHAR(255);
    
    SELECT categoria_clean INTO categoria_clean_value
    FROM categorias_pagina
    WHERE id = NEW.id_categoria;
    
    IF categoria_clean_value IS NULL THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'categoria_clean_value is NULL';
    END IF;
    
    SET NEW.caminho_pagina = CONCAT('views/', categoria_clean_value, '/', NEW.pagina_clean, '.php');
END//

DELIMITER ;

INSERT INTO permissoes_pagina (id_categoria, pagina, pagina_clean, id_tipo_usuario)
VALUES
(1, 'Navio', 'navio', 1),
(1, 'Navio', 'navio', 2),
(1, 'Cliente', 'cliente', 1),
(1, 'Cliente', 'cliente', 2),
(1, 'Carga', 'carga', 1),
(1, 'Carga', 'carga', 2),
(1, 'Usuário', 'usuario', 1),

(2, 'Escala', 'escala', 1),
(2, 'Escala', 'escala', 2),
(2, 'Período trabalhado', 'periodo_trabalhado', 1),
(2, 'Período trabalhado', 'periodo_trabalhado', 2),
(2, 'Plano de Distribuição', 'plano_de_distribuicao', 1),
(2, 'Plano de Distribuição', 'plano_de_distribuicao', 2),

(3, 'Relatório de Escala', 'relatorio_de_escala', 1),
(3, 'Relatório de Escala', 'relatorio_de_escala', 2),
(3, 'Relatório por Período', 'relatorio_por_periodo', 1),
(3, 'Relatório por Período', 'relatorio_por_periodo', 2),
(3, 'Relatório por Cliente', 'relatorio_por_cliente', 1),
(3, 'Relatório por Cliente', 'relatorio_por_cliente', 2),
(3, 'Relatório Chuva', 'relatorio_chuva', 1),
(3, 'Relatório Chuva', 'relatorio_chuva', 2),
(3, 'Relatório Balança', 'relatorio_balanca', 1),
(3, 'Relatório Balança', 'relatorio_balanca', 2),
(3, 'Relatório Balança', 'relatorio_balanca', 3),
(3, 'Controle de Prancha', 'controle_de_prancha', 1),
(3, 'Controle de Prancha', 'controle_de_prancha', 2),
(3, 'Controle de Prancha', 'controle_de_prancha', 3),

(4, 'Port Statistics', 'port_statistics', 1),
(4, 'Port Statistics', 'port_statistics', 2),
(4, 'Port Statistics', 'port_statistics', 3),
(4, 'Line Up - Forecast', 'line_up_forecast', 1),
(4, 'Line Up - Forecast', 'line_up_forecast', 2),
(4, 'Line Up - Forecast', 'line_up_forecast', 3);
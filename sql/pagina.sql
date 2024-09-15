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
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
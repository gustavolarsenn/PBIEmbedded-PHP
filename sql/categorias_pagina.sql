CREATE TABLE categorias_pagina (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    categoria VARCHAR(255),
    categoria_clean VARCHAR(255),
    icon VARCHAR(255)
);

INSERT INTO categorias_pagina (categoria, categoria_clean, icon)
VALUES
('Cadastro', 'cadastro', 'icon icon-single-04'),
('Inclusão', 'inclusao', 'icon icon-form'),
('Relatórios', 'relatorio', 'icon icon-layout-25'),
('Relatórios - BI', 'relatorio_bi', 'icon icon-chart-bar-33');
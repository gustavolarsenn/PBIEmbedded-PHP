DROP TABLE IF EXISTS pranchaReports;
CREATE TABLE pranchaReports (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    navio VARCHAR(255),
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
    observacao VARCHAR(255)
);
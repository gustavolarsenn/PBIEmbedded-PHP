DROP TABLE IF EXISTS ControlePrancha;
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
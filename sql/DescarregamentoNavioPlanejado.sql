DROP TABLE IF EXISTS DescarregamentoNavioPlanejado;
CREATE TABLE DescarregamentoNavioPlanejado (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    navio VARCHAR(100),
    cliente VARCHAR(100),
    di VARCHAR(10),
    armazem VARCHAR(100),
    produto VARCHAR(100),
    planejado FLOAT
);
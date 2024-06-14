DROP TABLE IF EXISTS shipplanned;
CREATE TABLE shipplanned (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    navio VARCHAR(100),
    cliente VARCHAR(100),
    DI VARCHAR(10),
    armazem VARCHAR(100),
    produto VARCHAR(100),
    planejado FLOAT
);
DROP TABLE IF EXISTS navio;
CREATE TABLE navio (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    navio VARCHAR(50),
    data DATETIME,
    produto VARCHAR(100),
    berco VARCHAR(50),
    volume_manifestado FLOAT,
    modalidade VARCHAR(50),
    prancha_minima FLOAT
)
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
)

DROP TABLE IF EXISTS Navioteste;
CREATE TABLE Navioteste (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    porto VARCHAR(50),
    navio VARCHAR(50),
    data DATETIME,
    produto VARCHAR(100),
    berco VARCHAR(50),
    volume_manifestado FLOAT,
    modalidade VARCHAR(50),
    prancha_minima FLOAT,
    CONSTRAINT unique_vessel UNIQUE (navio, produto, berco, volume_manifestado)
);
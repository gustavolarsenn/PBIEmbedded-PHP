DROP TABLE IF EXISTS DescarregamentoNavio;
CREATE TABLE DescarregamentoNavio (
    no BIGINT,
    id_viagem BIGINT,
    ticket VARCHAR(50),
    placa VARCHAR(20),
    peso float,
    data date,
    periodo VARCHAR(50),
    porao INT,
    cliente VARCHAR(100),
    armazem VARCHAR(100),
    transportadora VARCHAR(100),
    cliente_armazem_lote_di_produto VARCHAR(255),
    produto VARCHAR(50),
    observacao VARCHAR(255),
    di VARCHAR(20),
    FOREIGN KEY (id_viagem) REFERENCES Navio(id)
);
DROP TABLE IF EXISTS DescarregamentoNavio;
CREATE TABLE DescarregamentoNavio (
    no BIGINT,
    navio VARCHAR(50),
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
    di VARCHAR(20)
)
CREATE TABLE RelatorioPBI(
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    id_relatorio VARCHAR(255),
    id_dataset VARCHAR(255),
    relatorio VARCHAR(255),
    relatorio_clean VARCHAR(255),
    rls BOOLEAN DEFAULT FALSE,
    ativo BOOLEAN DEFAULT TRUE
);

INSERT INTO RelatorioPBI (id_relatorio, id_dataset, relatorio, relatorio_clean) VALUES

('44cece6c-3c3a-4343-8c95-5da3fc5aacf1', 'a0c518ac-3314-4cfa-bccb-790d7bd8297e', 'Port Statistics', 'port_statistics'),
('af1d8571-368a-4016-985c-1e53bb0c9aaa', '1160d68b-da8b-4b7e-9b69-efaba2a70d1a', 'Line Up - Forecast', 'line_up_forecast');
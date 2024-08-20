DROP TABLE IF EXISTS permissoes_pagina;
CREATE TABLE permissoes_pagina (
    id_categoria BIGINT,
    id_pagina BIGINT,
    id_tipo_usuario BIGINT,
    ativo BOOLEAN DEFAULT TRUE,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_atualizacao DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_categoria) REFERENCES categorias_pagina(id),
    FOREIGN KEY (id_tipo_usuario) REFERENCES tipo_usuario(id),
    FOREIGN KEY (id_pagina) REFERENCES pagina(id),
    PRIMARY KEY (id_pagina, id_tipo_usuario)
);

INSERT INTO permissoes_pagina (id_categoria, id_pagina, id_tipo_usuario)
VALUES
(1, 1, 1),
(1, 1, 2),
(1, 2, 1),
(1, 2, 2),
(1, 3, 1),
(1, 3, 2),
(1, 4, 1),

(2, 5, 1),
(2, 5, 2),
(2, 6, 1),
(2, 6, 2),
(2, 7, 1),
(2, 7, 2),

(3, 8, 1),
(3, 8, 2),
(3, 9, 1),
(3, 9, 2),
(3, 10, 1),
(3, 10, 2),
(3, 11, 1),
(3, 11, 2),
(3, 12, 1),
(3, 12, 2),
(3, 12, 3),
(3, 13, 1),
(3, 13, 2),
(3, 13, 3),

(4, 14, 1),
(4, 14, 2),
(4, 14, 3),
(4, 15, 1),
(4, 15, 2),
(4, 15, 3);
INSERT INTO plantas (nome_comum, nome_cientifico, tipo, clima, regiao_origem, luminosidade, frequencia_rega, umidade_min, umidade_max, descricao) VALUES
('Jiboia', 'Epipremnum aureum', 'Trepadeira', 'Tropical', 'Ilhas Salomão', 'Meia sombra', '2x por semana', 40.00, 70.00, 'Planta pendente de fácil cuidado, ideal para ambientes internos.'),
('Lírio-da-paz', 'Spathiphyllum wallisii', 'Erva', 'Tropical', 'América Central', 'Sombra', '3x por semana', 50.00, 80.00, 'Conhecida por sua flor branca e propriedades purificadoras do ar.'),
('Peperômia', 'Peperomia obtusifolia', 'Erva', 'Tropical', 'América do Sul', 'Meia sombra', 'Semanal', 30.00, 60.00, 'Compacta, com folhas suculentas, perfeita para escritórios.'),
('Maranta', 'Maranta leuconeura', 'Erva', 'Tropical', 'Brasil', 'Sombra', '3x por semana', 50.00, 75.00, 'Planta ornamental conhecida por suas folhas com padrões distintos.');
INSERT INTO planta_usuario (IdUsuario, IdPlanta, apelido, localizacao) VALUES
(1, 11, 'Jiboiuda', 'Sala'),
(2, 12, 'Branquinha', 'Varanda'),
(3, 13, 'Pepe', 'Cozinha'),
(4, 14, 'Marantinha', 'Escritório'),
(1, 13, 'Suavidade', 'Quarto');
INSERT INTO condicoes_planta (ID_planta, umidade_atual) VALUES
(1, 60.50),
(2, 70.00),
(3, 45.20),
(4, 52.00),
(5, 48.75);
INSERT INTO analise_planta (ID_planta_usuario, status_saude, status_umidade) VALUES
(7, 'Boa', 'Regular'),
(4, 'Ruim', 'Baixa'),
(1, 'Boa', 'Regular'),
(2, 'Regular', 'Alta'),
(8, 'Boa', 'Baixa'),
(5, 'Doente', 'Alta');
INSERT INTO recomendacoes (titulo, descricao) VALUES
('Regar com menos frequência', 'A umidade está alta, reduza a quantidade de água.'),
('Aumentar rega', 'A umidade está baixa, recomenda-se regar mais vezes na semana.'),
('Mudar de local', 'A planta pode estar recebendo luz inadequada, avalie a luminosidade.'),
('Adubar o solo', 'Adicione fertilizante para melhorar a saúde da planta.'),
('Verificar pragas', 'Folhas danificadas podem indicar presença de pragas.');
INSERT INTO analiseXrecomendacao (ID_analise, ID_recomendacao) VALUES
(20,2),
(11, 3),
(12, 1),
(13, 2),
(14, 4),
(15, 5),
(15, 1);

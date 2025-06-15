CREATE DATABASE IF NOT EXISTS smartgrow_db;
USE smartgrow_db;

CREATE TABLE cadastro_usuario (
    ID_usuario INT AUTO_INCREMENT PRIMARY KEY,
    Nome VARCHAR(255) NOT NULL,
    Email VARCHAR(255) NOT NULL UNIQUE,
    Senha_hash VARCHAR(255) NOT NULL,
    Data_criacao DATETIME DEFAULT CURRENT_TIMESTAMP,
    Data_atualizacao DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    Ativo BOOLEAN DEFAULT TRUE
);

CREATE TABLE plantas (
    ID_planta INT AUTO_INCREMENT PRIMARY KEY,
    nome_comum VARCHAR(100) NOT NULL,
    nome_cientifico VARCHAR(150),
    tipo VARCHAR(50), -- Ex: erva, arbusto, árvore
    clima VARCHAR(100), -- Ex: tropical, temperado
    regiao_origem VARCHAR(100),
    luminosidade VARCHAR(50), -- Ex: sol pleno, meia sombra, sombra
    frequencia_rega VARCHAR(50), -- Ex: diária, 2x por semana 
    umidade_min decimal(4,2),
    umidade_max decimal(4,2),
    descricao TEXT
);
CREATE TABLE usuarioXplanta (
	ID int not null primary key auto_increment,
	ID_usuario INT,
    ID_planta INT,
    foreign key (ID_usuario) references cadastro_usuario(ID_usuario),
    foreign key (ID_planta) references plantas(ID_planta)
);

CREATE TABLE planta_usuario (
	ID int auto_increment primary key not null,
    ID_usuarioplanta int,
    foreign key (ID_usuarioplanta) references usuarioXplanta(ID),
	apelido varchar(255),
    localizacao varchar(255)
);

CREATE TABLE condicoes_planta (
	ID int auto_increment primary key not null,
    ID_planta int,
    data_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
    foreign key (ID_planta) references planta_usuario(ID),
    umidade_atual decimal(5,2)
);

CREATE TABLE analise_planta (
	ID int auto_increment primary key not null,
    ID_planta_usuario int,
    foreign key (ID_planta_usuario) references planta_usuario(ID),
    data_analise datetime default current_timestamp,
    status_saude ENUM('Boa', 'Regular', 'Ruim', 'Doente'),
    status_umidade ENUM('Baixo', 'Alto', 'Regular')
);

CREATE TABLE recomendacoes (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(100) NOT NULL,
    descricao TEXT NOT NULL
);

CREATE TABLE analiseXrecomendacao (
    ID_analise INT NOT NULL,
    ID_recomendacao INT NOT NULL,
    PRIMARY KEY (ID_analise, ID_recomendacao),
    FOREIGN KEY (ID_analise) REFERENCES analise_planta(ID) ON DELETE CASCADE,
    FOREIGN KEY (ID_recomendacao) REFERENCES recomendacoes(ID) ON DELETE CASCADE
);

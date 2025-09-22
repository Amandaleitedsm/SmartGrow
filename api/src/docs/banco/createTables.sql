CREATE DATABASE IF NOT EXISTS smartgrow_db;
use smartgrow_db;

CREATE TABLE cadastro_usuario (
    ID_usuario INT AUTO_INCREMENT PRIMARY KEY,
    Nome VARCHAR(255) NOT NULL,
    Email VARCHAR(255) NOT NULL UNIQUE,
    Senha_hash VARCHAR(255) NOT NULL,
    roleUsuario ENUM('usuario', 'admin') DEFAULT 'usuario',
    Data_criacao DATETIME DEFAULT CURRENT_TIMESTAMP,
    Data_atualizacao DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    Ativo BOOLEAN DEFAULT TRUE
);

CREATE TABLE plantas (
    ID_planta INT AUTO_INCREMENT PRIMARY KEY,
    nome_comum VARCHAR(100),
    nome_cientifico VARCHAR(150) not null,
    tipo VARCHAR(50), -- Ex: erva, arbusto, árvore
    clima VARCHAR(100), -- Ex: tropical, temperado
    regiao_origem VARCHAR(100),
    luminosidade VARCHAR(50), -- Ex: sol pleno, meia sombra, sombra
    frequencia_rega VARCHAR(50), -- Ex: diária, 2x por semana 
    umidade_min decimal(4,2) NOT NULL,
    umidade_max decimal(4,2) NOT NULL,
    descricao TEXT
);

CREATE TABLE planta_usuario (
	ID int auto_increment primary key not null,
    IdUsuario int,
    foreign key (IdUsuario) references cadastro_usuario(ID_usuario) on delete cascade,
    IdPlanta INT,
    foreign key (IdPlanta) references plantas(ID_planta) on delete cascade,
	apelido varchar(255),
    localizacao varchar(255)
);

CREATE TABLE condicoes_planta (
	ID int auto_increment primary key not null,
    ID_planta int,
    data_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
    foreign key (ID_planta) references planta_usuario(ID) on delete cascade,
    umidade_atual decimal(5,2)
);

CREATE TABLE analise_planta (
	ID int auto_increment primary key not null,
    ID_planta_usuario int,
    foreign key (ID_planta_usuario) references planta_usuario(ID) on delete cascade,
    data_analise  datetime default current_timestamp not null,
    status_saude varchar(255),
    status_umidade ENUM('Baixa', 'Alta', 'Regular'),
    imagem VARCHAR(255)
);

CREATE TABLE recomendacoes (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(100) NOT NULL,
    descricao TEXT NOT NULL
);

CREATE TABLE analiseXrecomendacao (
	IDAssoc INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    ID_analise INT NOT NULL,
    ID_recomendacao INT NOT NULL,
    FOREIGN KEY (ID_analise) REFERENCES analise_planta(ID) ON DELETE CASCADE ,
    FOREIGN KEY (ID_recomendacao) REFERENCES recomendacoes(ID) ON DELETE CASCADE
);

CREATE TABLE tokens_ativos (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    ID_usuario INT NOT NULL,
    Token TEXT NOT NULL,
    Criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
    Expira_em DATETIME NOT NULL,
    Valido BOOLEAN DEFAULT TRUE,

    FOREIGN KEY (ID_usuario) REFERENCES cadastro_usuario(ID_usuario)
        ON DELETE CASCADE
);

CREATE TABLE codigos_verificacao (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    codigo VARCHAR(10) NOT NULL,
    data_criacao DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    expira_em DATETIME NOT NULL
);
select * from cadastro_usuario;
CREATE DATABASE IF NOT EXISTS mysql_database;
USE mysql_database;

-- Criar tabela de usuários
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,  -- Garantir que o email seja único
    data_criacao DATETIME DEFAULT CURRENT_TIMESTAMP
);

drop table produtos;
drop table usuarios;
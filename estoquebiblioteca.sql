-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Tempo de geração: 14/05/2025 às 14:04
-- Versão do servidor: 8.3.0
-- Versão do PHP: 8.3.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `senai_login`
--
CREATE DATABASE IF NOT EXISTS `estoquebiblioteca` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `estoquebiblioteca`;

-- --------------------------------------------------------
DROP TABLE IF EXISTS `produto`;
CREATE TABLE `produto` (
    id_produto INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL,
    autor VARCHAR(255) NOT NULL,
    isbn VARCHAR(20) UNIQUE,
    editora VARCHAR(100),
    ano_publicacao INT,
    categoria VARCHAR(50),
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Inserir dados em produto
TRUNCATE TABLE produto;

INSERT INTO produto (titulo, autor, isbn, editora, ano_publicacao, categoria) VALUES
    ('Dom Casmurro', 'Machado de Assis', '978-85-359-0277-6', 'Companhia das Letras', 2008, 'Literatura Brasileira'),
    ('O Cortiço', 'Aluísio Azevedo', '978-85-264-0123-9', 'Ática', 2010, 'Literatura Brasileira'),
    ('1984', 'George Orwell', '978-85-250-4786-1', 'Companhia das Letras', 2009, 'Ficção Científica'),
    ('O Pequeno Príncipe', 'Antoine de Saint-Exupéry', '978-85-359-0182-3', 'Agir', 2015, 'Infantil'),
    ('Sapiens', 'Yuval Noah Harari', '978-85-359-2702-0', 'Companhia das Letras', 2015, 'História'),
    ('Clean Code', 'Robert C. Martin', '978-01-323-5088-6', 'Prentice Hall', 2008, 'Tecnologia');

--
-- Estrutura para tabela `cliente`
--

DROP TABLE IF EXISTS `cliente`;
CREATE TABLE IF NOT EXISTS `cliente` (
  `id_cliente` int NOT NULL AUTO_INCREMENT,
  `nome_cliente` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `endereco` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `telefone` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `id_funcionario_responsavel` int DEFAULT NULL,
  PRIMARY KEY (`id_cliente`),
  UNIQUE KEY `email` (`email`),
  KEY `fk_cliente_funcionario` (`id_funcionario_responsavel`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `cliente`
--

INSERT INTO `cliente` (`id_cliente`, `nome_cliente`, `endereco`, `telefone`, `email`, `id_funcionario_responsavel`) VALUES
(2, 'Teresa Lisbon', 'Rua California', '(47)1234-4568', 'teresa@teresa', NULL),
(3, 'Chefe Bolden', 'Rua dos Bombeiros, 123', '(99)1234-4321', 'bolden@bolden', NULL),
(4, 'Capitão Hermann', 'Rua do Molys, 123', '(21)6547-7854', 'hermann@hermann', NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `fornecedor`
--

DROP TABLE IF EXISTS `fornecedor`;
CREATE TABLE IF NOT EXISTS `fornecedor` (
  `id_fornecedor` int NOT NULL AUTO_INCREMENT,
  `nome_fornecedor` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `endereco` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `telefone` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `contato` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `id_funcionario_registro` int DEFAULT NULL,
  PRIMARY KEY (`id_fornecedor`),
  UNIQUE KEY `email` (`email`),
  KEY `fk_fornecedor_funcionario` (`id_funcionario_registro`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `fornecedor`
--

INSERT INTO `fornecedor` (`id_fornecedor`, `nome_fornecedor`, `endereco`, `telefone`, `email`, `contato`, `id_funcionario_registro`) VALUES
(1, 'Tech Supplies', 'Av. Paulista, 1000', '11912345678', 'contato@techsupplies.com', 'José Silva', NULL),
(2, 'Gamer Store', 'Rua dos Gamers, 200', '21912345678', 'contato@gamerstore.com', 'Marcos Souza', NULL),
(3, 'Eletrônicos BR', 'Av. Brasil, 300', '31912345678', 'contato@eletronicosbr.com', 'Fernanda Lima', NULL),
(4, 'InfoTech', 'Rua da Tecnologia, 400', '41912345678', 'contato@infotech.com', 'Carlos Mendes', NULL),
(5, 'Bombeiros Fire Ltda', 'Rua Chicago 214', '(78)8521-1254', 'fire@fire', 'Dick Wolf', NULL);

-- --------------------------------------------------------

DROP TABLE IF EXISTS `fornecedor_produto`;
CREATE TABLE `fornecedor_produto` (
  `id_fornecedor` int NOT NULL,
  `id_produto` int NOT NULL,
  PRIMARY KEY (`id_fornecedor`, `id_produto`),
  KEY `id_produto` (`id_produto`),
  CONSTRAINT `fornecedor_produto_ibfk_1` FOREIGN KEY (`id_fornecedor`) REFERENCES `fornecedor` (`id_fornecedor`) ON DELETE CASCADE,
  CONSTRAINT `fornecedor_produto_ibfk_2` FOREIGN KEY (`id_produto`) REFERENCES `produto` (`id_produto`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dados
INSERT INTO `fornecedor_produto` (`id_fornecedor`, `id_produto`) VALUES
(1, 1),
(2, 2),
(3, 3),
(4, 4);

-- --------------------------------------------------------

--
-- Estrutura para tabela `funcionario`
--

DROP TABLE IF EXISTS `funcionario`;
CREATE TABLE IF NOT EXISTS `funcionario` (
  `id_funcionario` int NOT NULL AUTO_INCREMENT,
  `nome_funcionario` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `endereco` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `telefone` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id_funcionario`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `funcionario`
--

INSERT INTO `funcionario` (`id_funcionario`, `nome_funcionario`, `endereco`, `telefone`, `email`) VALUES
(1, 'João Silva', 'Rua X, 500', '11955555555', 'joao@email.com'),
(2, 'Mariana Oliveira', 'Rua Y, 600', '21966666666', 'mariana@email.com'),
(3, 'Roberto Santos', 'Rua Z, 700', '31977777777', 'roberto@email.com'),
(4, 'Camila Ferreira', 'Rua W, 800', '41988888888', 'camila@email.com'),
(5, 'Jesse Pinkman', 'Rua Novo Mexico, 171', '2132145874', 'jesse@jesse.com');

-- --------------------------------------------------------

--
-- Estrutura para tabela `perfil`
--

DROP TABLE IF EXISTS `perfil`;
CREATE TABLE IF NOT EXISTS `perfil` (
  `id_perfil` int NOT NULL AUTO_INCREMENT,
  `nome_perfil` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id_perfil`),
  UNIQUE KEY `nome_perfil` (`nome_perfil`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `perfil`
--

INSERT INTO `perfil` (`id_perfil`, `nome_perfil`) VALUES
(1, 'Adm'),
(3, 'Almoxarife'),
(4, 'Cliente'),
(2, 'Secretaria');

-- --------------------------------------------------------

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuario`
--

DROP TABLE IF EXISTS `usuario`;
CREATE TABLE IF NOT EXISTS `usuario` (
  `id_usuario` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `senha` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `id_perfil` int DEFAULT NULL,
  `senha_temporaria` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id_usuario`),
  UNIQUE KEY `email` (`email`),
  KEY `id_perfil` (`id_perfil`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `usuario`
--

INSERT INTO `usuario` (`id_usuario`, `nome`, `senha`, `email`, `id_perfil`, `senha_temporaria`) VALUES
(1, 'Administrador', '$2y$10$rIJhd7oXSRM1XbAdQCEsA.PF3n/rxNtIAUqCkcFybzE5J.mLBsq.q', 'admin@admin', 1, 0),
(2, 'Sergio Luiz da Silveira', '$2y$10$AKaq2b1ZyNzZs5u6ueiJq.t5xj02aj0aroz4IjHDPhdAsrhZL8MO.', 'sergio@sergio', 1, 1),
(6, 'Maria Souza', '$2y$10$RRDyLe.N/SHniQ03fG3mnuRN84K/D4wVS3BkftU7nUUFEqyOhwFDu', 'maria@empresa.com', 2, 0),
(7, 'Carlos Mendes', '$2y$10$RRDyLe.N/SHniQ03fG3mnuRN84K/D4wVS3BkftU7nUUFEqyOhwFDu', 'carlos@empresa.com', 3, 0),
(8, 'Ana Pereira', '$2y$10$xaWdXzOzYETic/DhbeHV2OZCAgBaOJzqo9j38DeAEKV2.grcV.L3u', 'ana@empresa.com', 4, 0),
(9, 'Joao Vitor', '$2y$10$2nzDym9SuKZba3OcGeUWKu7RB3CRhpVb1v.LXb9kYxBWVh1/dAG22', 'vitor@vitor', 1, 0),
(12, 'Grace Van Pelt', '$2y$10$g5h1LI20ufnY/p6062h5r.ezKU7eFlhhwRCSkuKTJiYUYulPIQjxq', 'grace@grace', 4, 0),
(13, 'Xavier', '$2y$10$ErMocH1x.avm4asmRnKzeOUF30fi4ZO33C/9H6D2opvlFZ6zEorR.', 'xavier@xavier', 1, 0);

--
-- Restrições para tabelas despejadas
--

ALTER TABLE `cliente`
  ADD CONSTRAINT `fk_cliente_funcionario` FOREIGN KEY (`id_funcionario_responsavel`) REFERENCES `funcionario` (`id_funcionario`) ON DELETE SET NULL;

ALTER TABLE `fornecedor`
  ADD CONSTRAINT `fk_fornecedor_funcionario` FOREIGN KEY (`id_funcionario_registro`) REFERENCES `funcionario` (`id_funcionario`) ON DELETE SET NULL;

ALTER TABLE `usuario`
  ADD CONSTRAINT `usuario_ibfk_1` FOREIGN KEY (`id_perfil`) REFERENCES `perfil` (`id_perfil`);

-- Alterar os nomes dos perfis na tabela perfil
UPDATE perfil SET nome_perfil = 'Cliente' WHERE nome_perfil = 'Almoxarife';
UPDATE perfil SET nome_perfil = 'Funcionario' WHERE nome_perfil = 'Secretaria';

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
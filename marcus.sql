-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 08/09/2025 às 20:20
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `marcus`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `artigos`
--

CREATE TABLE `artigos` (
  `id` int(11) NOT NULL,
  `titulo` varchar(200) NOT NULL,
  `slug` varchar(200) NOT NULL,
  `conteudo` text NOT NULL,
  `categoria_id` int(11) DEFAULT NULL,
  `autor_id` int(11) DEFAULT NULL,
  `data_evento` date DEFAULT NULL,
  `criado_em` datetime DEFAULT current_timestamp(),
  `atualizado_em` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Despejando dados para a tabela `artigos`
--

INSERT INTO `artigos` (`id`, `titulo`, `slug`, `conteudo`, `categoria_id`, `autor_id`, `data_evento`, `criado_em`, `atualizado_em`) VALUES
(1, 'Câmara Temática de Inovação: O Futuro de Assis Chateaubriand em Pauta', 'Futuro', 'Assis Chateaubriand, 24 de novembro de 2023 - Com o objetivo de impulsionar o desenvolvimento local por meio da inovação, ciência e tecnologia, a Câmara Temática de Ciência, Tecnologia e Inovação de Assis Chateaubriand deu passos importantes em sua estruturação. Em reunião realizada na quinta-feira (23), o grupo aprovou a proposta do regimento interno, um marco para o início dos trabalhos que visam transformar o cenário socioeconômico do município.', 1, NULL, '0000-00-00', '2025-09-05 14:57:11', '2025-09-05 14:57:11'),
(3, 'asdflçkadfçl', 'klf', 'sdfç´~skfgl~gsdfklçsdgklçsdfgkglçs kasdlkslç~dgksdglçdglç sdlçgkdlçsk', 1, NULL, '0000-00-00', '2025-09-05 15:12:06', '2025-09-05 15:12:06'),
(4, 'Reuniao camara de inovação', 'asd', 'adafdadsddg gadggsdgffd', 3, NULL, '0000-00-00', '2025-09-08 10:49:04', '2025-09-08 10:49:04'),
(5, 'Convite para Reunião da Câmara de Inovação', '', 'A Câmara de Inovação de Assis Chateaubriand convida você para participar de sua próxima reunião. Será um momento de diálogo, troca de ideias e construção coletiva de estratégias para fortalecer o ecossistema de inovação, empreendedorismo e tecnologia em nosso município.\r\n\r\nData: 25/10/2025\r\nHorário: 14h00\r\nLocal: Auditório IFPR\r\n\r\nSua presença é fundamental para pensarmos juntos soluções criativas e impulsionarmos o desenvolvimento da nossa região.', 2, NULL, '2025-10-14', '2025-09-08 11:03:06', '2025-09-08 14:38:33'),
(7, 'Teste novo evento', 'gfhfg', 'douisdh fsoadih foiasdh fiusd iuf gsadiufg sdiouf isdugf iusdgf puoisdh fpiusd fipushdipfuihsd fiuhsd fuhsd uhsd pufhsd ipfuh dsiufh sdiouofh sdouifh iopsdufh iosduhf iopsoduhf osduhf osudhf siduhf osduhf iosudhf iopsduhf isdufh isduhf isduhf sdiu', 2, NULL, '2025-09-17', '2025-09-08 15:01:15', '2025-09-08 15:01:15');

-- --------------------------------------------------------

--
-- Estrutura para tabela `categorias`
--

CREATE TABLE `categorias` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `descricao` text DEFAULT NULL,
  `criado_em` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Despejando dados para a tabela `categorias`
--

INSERT INTO `categorias` (`id`, `nome`, `descricao`, `criado_em`) VALUES
(1, 'Notícias', 'Notícias gerais', '2025-08-08 15:15:45'),
(2, 'Eventos', NULL, '2025-08-08 15:24:37'),
(3, 'Projetos', NULL, '2025-08-08 15:25:24'),
(4, 'Teste', NULL, '2025-09-08 14:27:25');

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `tipo` enum('admin','editor','visitante') DEFAULT 'editor',
  `foto_perfil` varchar(255) DEFAULT NULL,
  `criado_em` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `artigos`
--
ALTER TABLE `artigos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `categoria_id` (`categoria_id`),
  ADD KEY `autor_id` (`autor_id`);

--
-- Índices de tabela `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `artigos`
--
ALTER TABLE `artigos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de tabela `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `artigos`
--
ALTER TABLE `artigos`
  ADD CONSTRAINT `artigos_ibfk_1` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`),
  ADD CONSTRAINT `artigos_ibfk_2` FOREIGN KEY (`autor_id`) REFERENCES `usuarios` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

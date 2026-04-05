-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 30/01/2026 às 22:53
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
-- Banco de dados: `sistema_erp`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `addresses`
--

CREATE TABLE `addresses` (
  `id` int(11) NOT NULL,
  `street` varchar(255) DEFAULT NULL,
  `number` varchar(50) DEFAULT NULL,
  `complement` varchar(255) DEFAULT NULL,
  `neighborhood` varchar(100) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `state` varchar(50) DEFAULT NULL,
  `region` varchar(50) DEFAULT NULL,
  `postal_code` varchar(20) DEFAULT NULL,
  `country` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `clients`
--

CREATE TABLE `clients` (
  `id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `type` enum('fisica','juridica') NOT NULL DEFAULT 'fisica',
  `name` varchar(255) DEFAULT NULL,
  `cpf` varchar(20) DEFAULT NULL,
  `company_name` varchar(255) DEFAULT NULL,
  `fantasy_name` varchar(255) DEFAULT NULL,
  `cnpj` varchar(255) DEFAULT NULL,
  `ie` varchar(50) DEFAULT NULL,
  `segment` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `cep` varchar(15) DEFAULT NULL,
  `street` varchar(255) DEFAULT NULL,
  `number` varchar(20) DEFAULT NULL,
  `complement` varchar(100) DEFAULT NULL,
  `neighborhood` varchar(100) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `state` varchar(2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `clients`
--

INSERT INTO `clients` (`id`, `company_id`, `created_by`, `updated_by`, `type`, `name`, `cpf`, `company_name`, `fantasy_name`, `cnpj`, `ie`, `segment`, `email`, `phone`, `cep`, `street`, `number`, `complement`, `neighborhood`, `city`, `state`, `created_at`, `updated_at`) VALUES
(2, 5, NULL, NULL, 'fisica', 'Carlos Alberto Ferreira', '123.456.789-10', 'Distribuidora de Alimentos e Logística Sudeste S.A.', 'Distribuidora de Alimentos e Logística Sudeste', '11.222.333/0001-44', '', '', 'carlos.alberto@email.com', '(11) 98888-1111', '01001-000', 'Praça da Sé', '846', 'S/C', 'Sé', 'São Paulo', 'SP', '2026-01-08 01:25:03', '2026-01-08 01:25:03'),
(3, 5, NULL, NULL, 'juridica', 'Restaurante Sabor & Arte Ltda', '', 'Restaurante Sabor & Arte Ltda', 'Sabor & Arte', '11.222.333/0001-44', 'S/N', 'Gastronomia', 'restaurante@gmail.com', '(32) 35012-5826', '20040-002', 'Avenida Rio Branco', 'S/N', 'S/C', 'Centro', 'Rio de Janeiro', 'RJ', '2026-01-08 01:27:05', '2026-01-08 01:27:19'),
(4, 5, NULL, NULL, 'juridica', 'Tech Edu Inovação', '', 'Organizações Internacionais de Tecnologia, Educação & Inovação do Brasil S.A.', 'Tech Edu Inovação', '99.888.777/0001-00', 'S/N', 'Tech', 'contato@organizacoes-tech-edu.com.br', '(11) 98888-1111', '70160-900', 'Praça dos Três Poderes', 'S/N', '140 - APTO', 'Zona Cívico-Administrativa', 'Brasília', 'DF', '2026-01-08 01:28:37', '2026-01-08 01:28:54'),
(5, 5, NULL, NULL, 'fisica', 'Ana Beatriz Souza', '444.555.666-77', '', '', '', '', '', 'anabeatriz11@gmail.com', '(19) 97766-5544', '30140-071', 'Rua dos Aimorés', '441', 'APTO', 'Boa Viagem', 'Belo Horizonte', 'MG', '2026-01-08 01:29:41', '2026-01-08 01:29:41'),
(6, 5, NULL, NULL, 'fisica', 'Roberto Silva', '222.333.444-55', '', '', '', '', '', 'roberto.silva@email.com', '(33) 3277-5985', '01310-200', 'Avenida Paulista', '8585', '', 'Bela Vista', 'São Paulo', 'SP', '2026-01-10 00:34:41', '2026-01-10 00:34:41'),
(7, 5, NULL, NULL, 'juridica', 'Logística Express', '', 'Logística Express', 'Logística Express S.A', '44.555.666/0001-77', 'S/N', 'Transporte', 'transportelogica@gmail.com.br', '(85) 3247-4989', '05407002', 'Rua Cardeal Arcoverde', '8599', '', 'Pinheiros', 'São Paulo', 'SP', '2026-01-10 00:36:13', '2026-01-10 00:36:27');

-- --------------------------------------------------------

--
-- Estrutura para tabela `companies`
--

CREATE TABLE `companies` (
  `id` int(11) NOT NULL,
  `address_id` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `cnpj` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `companies`
--

INSERT INTO `companies` (`id`, `address_id`, `name`, `cnpj`, `created_at`, `updated_at`) VALUES
(1, NULL, 'Empresa Matriz', NULL, '2026-01-04 17:05:53', '2026-01-06 15:52:13'),
(2, NULL, 'Empresa Filial', NULL, '2026-01-04 17:05:53', '2026-01-06 15:52:13'),
(3, NULL, 'Transportes Russio', NULL, '2026-01-04 17:56:30', '2026-01-06 15:52:13'),
(4, NULL, 'Transportes Victor', NULL, '2026-01-04 18:35:26', '2026-01-06 15:52:13'),
(5, NULL, 'Tech Solutions LTDA', '12345678000190', '2026-01-06 02:52:30', '2026-01-06 15:52:13'),
(7, NULL, 'Padaria Sonho Meu Ltda', '12345678000190', '2026-01-07 01:36:25', '2026-01-07 01:36:25');

-- --------------------------------------------------------

--
-- Estrutura para tabela `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `company_id` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `code` varchar(50) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `stock` int(11) DEFAULT 0,
  `category` varchar(50) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `active` tinyint(1) DEFAULT 1,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `products`
--

INSERT INTO `products` (`id`, `company_id`, `category_id`, `name`, `code`, `price`, `created_at`, `stock`, `category`, `updated_at`, `active`, `created_by`, `updated_by`) VALUES
(1, 5, NULL, 'Notebook Gamer X1', NULL, 4500.00, '2026-01-06 03:16:31', 9, 'Tech', '2026-01-16 02:34:05', 1, NULL, NULL),
(2, 5, NULL, 'Teclado Gamer', NULL, 245.00, '2026-01-06 03:17:18', 8, 'Tech', '2026-01-08 01:36:26', 1, NULL, NULL),
(3, 5, NULL, 'Cadeira Gamer', NULL, 500.00, '2026-01-10 00:27:44', 9, 'Tech', '2026-01-16 02:27:08', 1, NULL, NULL),
(4, 5, NULL, 'SSD 1TB NVMe', NULL, 450.00, '2026-01-10 00:30:40', 8, 'Hardware', '2026-01-16 02:27:08', 1, NULL, NULL),
(5, 5, NULL, 'Mouse Gamer RGB', NULL, 180.00, '2026-01-10 00:31:06', 25, 'Periféricos', '2026-01-10 00:31:06', 1, NULL, NULL),
(6, 5, NULL, 'Monitor 27\" Curvo', NULL, 1250.00, '2026-01-10 00:31:39', 7, 'Periféricos', '2026-01-16 02:27:08', 1, NULL, NULL),
(7, 5, NULL, 'Licença Antivírus', NULL, 120.00, '2026-01-10 00:32:34', 98, 'Serviços', '2026-01-10 01:39:16', 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `sales`
--

CREATE TABLE `sales` (
  `id` int(11) NOT NULL,
  `company_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `client_id` int(11) DEFAULT NULL,
  `total` decimal(10,2) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `sales`
--

INSERT INTO `sales` (`id`, `company_id`, `user_id`, `updated_by`, `client_id`, `total`, `status`, `created_at`, `updated_at`) VALUES
(2, 5, NULL, NULL, 3, 490.00, 'Finalizada', '2026-01-08 01:36:26', '2026-01-10 00:39:37'),
(3, 5, NULL, NULL, 2, 18000.00, 'Finalizada', '2026-01-08 01:55:30', '2026-01-08 01:57:23'),
(4, 5, 14, 12, 4, 240.00, 'Finalizada', '2026-01-10 01:39:16', '2026-01-10 01:42:34'),
(5, 5, 14, 12, 2, 2250.00, 'Finalizada', '2026-01-10 23:38:33', '2026-01-11 02:01:53'),
(6, 5, 14, 12, 5, 2650.00, 'Pendente', '2026-01-16 02:27:08', '2026-01-30 21:38:50'),
(7, 5, 16, NULL, 4, 13500.00, 'Finalizada', '2026-01-16 02:34:05', '2026-01-16 02:34:05');

-- --------------------------------------------------------

--
-- Estrutura para tabela `sale_items`
--

CREATE TABLE `sale_items` (
  `id` int(11) NOT NULL,
  `sale_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `unit_price` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `sale_items`
--

INSERT INTO `sale_items` (`id`, `sale_id`, `product_id`, `quantity`, `unit_price`) VALUES
(1, 2, 2, 2, 245.00),
(2, 3, 1, 4, 4500.00),
(3, 4, 7, 2, 120.00),
(4, 5, 4, 5, 450.00),
(5, 6, 3, 1, 500.00),
(6, 6, 6, 1, 1250.00),
(7, 6, 4, 2, 450.00),
(8, 7, 1, 3, 4500.00);

-- --------------------------------------------------------

--
-- Estrutura para tabela `stock`
--

CREATE TABLE `stock` (
  `id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `type` varchar(10) NOT NULL,
  `date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `stock_movements`
--

CREATE TABLE `stock_movements` (
  `id` int(11) NOT NULL,
  `company_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `type` varchar(10) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `stock_movements`
--

INSERT INTO `stock_movements` (`id`, `company_id`, `product_id`, `user_id`, `type`, `quantity`, `created_at`, `updated_at`) VALUES
(1, 5, 1, NULL, 'entrada', 10, '2026-01-08 02:18:34', '2026-01-08 02:18:34');

-- --------------------------------------------------------

--
-- Estrutura para tabela `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `company_id` int(11) DEFAULT NULL,
  `address_id` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` varchar(50) DEFAULT NULL,
  `document` varchar(50) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `users`
--

INSERT INTO `users` (`id`, `company_id`, `address_id`, `name`, `email`, `password`, `role`, `document`, `phone`, `created_at`, `updated_at`) VALUES
(7, 1, NULL, 'Admin', 'admin@erp.com', '$2y$10$Y4KwG9IbhMOIyR.AnJcj2edbfYn1.IqVM.eLDof0xcRpRw9WVdv/6', 'admin', NULL, NULL, '2026-01-04 17:05:53', '2026-01-06 15:52:30'),
(8, 1, NULL, 'Vendedor', 'vendedor@erp.com', '$2y$10$M6p94Z/hwpruSI45zOlzy.rl8eK1HOeq8nDGO.671aj/r84LDZgRG', 'vendedor', NULL, NULL, '2026-01-04 17:05:53', '2026-01-06 15:52:30'),
(9, 2, NULL, 'Vendedor Empresa 2', 'vendedor2@erp.com', '$2y$10$H47atpBEBxYNKzk3PZ2d.OzltL8eCxuj6L.JEXAUtwommC7Fcxpp.', 'vendedor', NULL, NULL, '2026-01-04 17:05:53', '2026-01-06 15:52:30'),
(12, 5, NULL, 'Ana Beatriz Cavalcante', 'ana.beatriz.pro@icloud.co', '$2y$10$Nkpfo3iDKtxZ4l7GpVsi2OZZuvuDdzRz1/D7YsUkPrGJGjgqYOyxe', 'admin', NULL, NULL, '2026-01-06 02:52:30', '2026-01-16 00:31:37'),
(14, 5, NULL, 'Tony Stark', 'tony@technova.com', '$2y$10$ahkBjxBHhRrwUr1KQRfGmuNDCX/L8fUMZRSUiAMjzSAvbUynndKnW', 'user', NULL, NULL, '2026-01-06 02:55:59', '2026-01-08 01:55:48'),
(15, 7, NULL, 'Ricardo Oliveira', 'ricardo@padariasonho.com.br', '$2y$10$XiWTrYrDJKITU1ck7lf5F.oD.Hv7APyVkd2/ddEoT7lqexkF2ZXlG', 'admin', NULL, NULL, '2026-01-07 01:36:25', '2026-01-07 01:36:25'),
(16, 5, NULL, 'Ricardo Oliveira Silva', 'ricardo@gmail.com', '$2y$10$vGsxkMSQg/imRTiHBtY7quO9iui9uM2rnEoH2wKZ5dARwABPSZY6S', 'user', NULL, NULL, '2026-01-16 02:33:21', '2026-01-16 02:33:21'),
(17, 5, NULL, 'João da Silva', 'joao.silva@teste.com', '$2y$10$sUL3HFmkOqeHeJl/KSHVjefDLXg9mA.B2YTAnXp9AbjK4fcbDPQuG', 'user', NULL, NULL, '2026-01-30 21:44:06', '2026-01-30 21:44:06'),
(18, 5, NULL, 'Maria Aparecida', 'maria.aparecida@teste.com', '$2y$10$ypDP4io0ngcDEuM1kaMQfuIZkX3.tsavr5AjaSQVdh4dh2pMQG1cW', 'user', NULL, NULL, '2026-01-30 21:44:33', '2026-01-30 21:44:33'),
(19, 5, NULL, 'Carlos Eduardo Pereira', 'carlos.pereira@teste.com', '$2y$10$tqPtUE/D/cz0lbQMj4W9MuomLTgbdSkKmoO8OJl16b.FrV7GyEC6C', 'user', NULL, NULL, '2026-01-30 21:45:12', '2026-01-30 21:45:12'),
(20, 5, NULL, 'Ana Paula Ferreira', 'ana.ferreira@teste.com', '$2y$10$7rga2p9J0VmQBTVITn8WQ.uGImTqwP.FK3L5GpUnCI0mtecqdSj.q', 'user', NULL, NULL, '2026-01-30 21:45:32', '2026-01-30 21:45:32'),
(21, 5, NULL, 'Gerente Regional', 'gerente@teste.com', '$2y$10$j5nhWk0crL8az9XtAf5nYOzTgk6mvoCRsb27xy9MfyK3p7gOEC3Uu', 'admin', NULL, NULL, '2026-01-30 21:46:12', '2026-01-30 21:46:12');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `addresses`
--
ALTER TABLE `addresses`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `clients`
--
ALTER TABLE `clients`
  ADD PRIMARY KEY (`id`),
  ADD KEY `company_id` (`company_id`),
  ADD KEY `fk_clients_created_by` (`created_by`),
  ADD KEY `fk_clients_updated_by` (`updated_by`);

--
-- Índices de tabela `companies`
--
ALTER TABLE `companies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `address_id` (`address_id`);

--
-- Índices de tabela `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `company_id` (`company_id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `fk_products_created_by` (`created_by`),
  ADD KEY `fk_products_updated_by` (`updated_by`);

--
-- Índices de tabela `sales`
--
ALTER TABLE `sales`
  ADD PRIMARY KEY (`id`),
  ADD KEY `company_id` (`company_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `client_id` (`client_id`),
  ADD KEY `fk_sales_updated_by` (`updated_by`);

--
-- Índices de tabela `sale_items`
--
ALTER TABLE `sale_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sale_id` (`sale_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Índices de tabela `stock`
--
ALTER TABLE `stock`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `stock_movements`
--
ALTER TABLE `stock_movements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `company_id` (`company_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Índices de tabela `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `company_id` (`company_id`),
  ADD KEY `address_id` (`address_id`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `addresses`
--
ALTER TABLE `addresses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `clients`
--
ALTER TABLE `clients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de tabela `companies`
--
ALTER TABLE `companies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de tabela `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de tabela `sales`
--
ALTER TABLE `sales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de tabela `sale_items`
--
ALTER TABLE `sale_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de tabela `stock`
--
ALTER TABLE `stock`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `stock_movements`
--
ALTER TABLE `stock_movements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `clients`
--
ALTER TABLE `clients`
  ADD CONSTRAINT `clients_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_clients_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `fk_clients_updated_by` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`);

--
-- Restrições para tabelas `companies`
--
ALTER TABLE `companies`
  ADD CONSTRAINT `companies_ibfk_1` FOREIGN KEY (`address_id`) REFERENCES `addresses` (`id`) ON DELETE SET NULL;

--
-- Restrições para tabelas `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `fk_products_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_products_updated_by` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `products_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

--
-- Restrições para tabelas `sales`
--
ALTER TABLE `sales`
  ADD CONSTRAINT `fk_sales_clients` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_sales_updated_by` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `sales_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `sales_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Restrições para tabelas `sale_items`
--
ALTER TABLE `sale_items`
  ADD CONSTRAINT `sale_items_ibfk_1` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `sale_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL;

--
-- Restrições para tabelas `stock_movements`
--
ALTER TABLE `stock_movements`
  ADD CONSTRAINT `stock_movements_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `stock_movements_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `stock_movements_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Restrições para tabelas `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `users_ibfk_2` FOREIGN KEY (`address_id`) REFERENCES `addresses` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

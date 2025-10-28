-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 29, 2025 at 12:57 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `doceria`
--

-- --------------------------------------------------------

--
-- Table structure for table `auth_tokens`
--

CREATE TABLE `auth_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `token` varchar(64) NOT NULL,
  `name` varchar(255) NOT NULL,
  `abilities` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`abilities`)),
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cash_registers`
--

CREATE TABLE `cash_registers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `opening_balance` decimal(10,2) NOT NULL,
  `closing_balance` decimal(10,2) DEFAULT NULL,
  `opened_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `closed_at` timestamp NULL DEFAULT NULL,
  `status` enum('aberto','fechado') NOT NULL DEFAULT 'aberto',
  `opening_notes` text DEFAULT NULL,
  `closing_notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cash_registers`
--

INSERT INTO `cash_registers` (`id`, `user_id`, `opening_balance`, `closing_balance`, `opened_at`, `closed_at`, `status`, `opening_notes`, `closing_notes`, `created_at`, `updated_at`) VALUES
(1, 1, 200.00, NULL, '2025-10-25 11:00:00', NULL, 'aberto', 'Abertura do caixa para expediente', NULL, '2025-10-26 23:38:57', '2025-10-26 23:38:57'),
(2, 1, 150.00, 1850.50, '2025-10-24 11:00:00', '2025-10-25 21:00:00', 'fechado', 'Abertura do caixa', 'Caixa fechado com R$ 1850,50', '2025-10-26 23:38:57', '2025-10-26 23:38:57'),
(3, 1, 180.00, 2340.75, '2025-10-23 11:00:00', '2025-10-24 21:00:00', 'fechado', 'Abertura do caixa', 'Caixa fechado com R$ 2340,75', '2025-10-26 23:38:57', '2025-10-26 23:38:57');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`, `active`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Bolos Tradicionais', 'Bolos clássicos e tradicionais', 1, '2025-10-26 23:38:57', '2025-10-26 23:38:57', NULL),
(2, 'Bolos Especiais', 'Bolos gourmet e especiais', 1, '2025-10-26 23:38:57', '2025-10-28 22:38:18', NULL),
(3, 'Doces Finos', 'Docinhos e sobremesas finas', 1, '2025-10-26 23:38:57', '2025-10-26 23:38:57', NULL),
(4, 'Tortas', 'Tortas doces e salgadas', 1, '2025-10-26 23:38:57', '2025-10-26 23:38:57', NULL),
(5, 'Pães', 'Pães artesanais e caseiros', 1, '2025-10-26 23:38:57', '2025-10-26 23:38:57', NULL),
(6, 'Salgados', 'Salgados para lanche e festas', 1, '2025-10-26 23:38:57', '2025-10-26 23:38:57', NULL),
(7, 'Bebidas', 'Refrigerantes, sucos e bebidas', 1, '2025-10-26 23:38:57', '2025-10-26 23:38:57', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `cpf` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `neighborhood` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `zipcode` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `name`, `phone`, `email`, `cpf`, `address`, `neighborhood`, `city`, `state`, `zipcode`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Maria Silva', '(11) 99999-9999', 'maria.silva@email.com', '123.456.789-00', 'Rua das Flores, 123', 'Centro', 'São Paulo', 'SP', '01234-567', '2025-10-26 23:38:57', '2025-10-26 23:38:57', NULL),
(2, 'João Santos', '(11) 88888-8888', 'joao.santos@email.com', '987.654.321-00', 'Av. Paulista, 456', 'Bela Vista', 'São Paulo', 'SP', '01310-100', '2025-10-26 23:38:57', '2025-10-26 23:38:57', NULL),
(3, 'Ana Costa', '(11) 77777-7777', 'ana.costa@email.com', '456.789.123-00', 'Rua Augusta, 789', 'Consolação', 'São Paulo', 'SP', '01305-000', '2025-10-26 23:38:57', '2025-10-26 23:38:57', NULL),
(4, 'Carlos Oliveira', '(11) 66666-6666', 'carlos.oliveira@email.com', '789.123.456-00', 'Rua da Consolação, 321', 'Cerqueira César', 'São Paulo', 'SP', '01416-001', '2025-10-26 23:38:57', '2025-10-26 23:38:57', NULL),
(5, 'Fernanda Lima', '(11) 55555-5555', 'fernanda.lima@email.com', '321.654.987-00', 'Alameda Santos, 654', 'Jardins', 'São Paulo', 'SP', '01418-100', '2025-10-26 23:38:57', '2025-10-26 23:38:57', NULL),
(6, 'Roberto Pereira', '(11) 44444-4444', 'roberto.pereira@email.com', '654.987.321-00', 'Rua Oscar Freire, 987', 'Jardins', 'São Paulo', 'SP', '01426-001', '2025-10-26 23:38:57', '2025-10-26 23:38:57', NULL),
(7, 'Juliana Alves', '(11) 33333-3333', 'juliana.alves@email.com', '147.258.369-00', 'Av. Brigadeiro Faria Lima, 1357', 'Itaim Bibi', 'São Paulo', 'SP', '04538-133', '2025-10-26 23:38:57', '2025-10-26 23:38:57', NULL),
(8, 'Marcos Souza', '(11) 22222-2222', 'marcos.souza@email.com', '963.852.741-00', 'Rua Joaquim Floriano, 2468', 'Itaim Bibi', 'São Paulo', 'SP', '04534-004', '2025-10-26 23:38:57', '2025-10-26 23:38:57', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `encomendas`
--

CREATE TABLE `encomendas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `customer_id` bigint(20) UNSIGNED DEFAULT NULL,
  `code` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `status` enum('pendente','em_producao','pronto','entregue','cancelado') NOT NULL DEFAULT 'pendente',
  `delivery_date` date NOT NULL,
  `delivery_time` time DEFAULT NULL,
  `delivery_address` text DEFAULT NULL,
  `delivery_fee` decimal(8,2) NOT NULL DEFAULT 0.00,
  `subtotal` decimal(10,2) NOT NULL,
  `custom_costs` decimal(10,2) NOT NULL DEFAULT 0.00,
  `discount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

CREATE TABLE `expenses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `type` enum('entrada','saida') NOT NULL DEFAULT 'saida',
  `description` varchar(255) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `date` date NOT NULL,
  `payment_method` enum('dinheiro','cartao_credito','cartao_debito','pix','transferencia','boleto') DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `cash_register_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `expenses`
--

INSERT INTO `expenses` (`id`, `user_id`, `type`, `description`, `amount`, `date`, `payment_method`, `notes`, `created_at`, `updated_at`, `deleted_at`, `cash_register_id`) VALUES
(1, 1, 'saida', 'Compra de farinha de trigo - 50kg', 150.00, '2025-10-21', 'pix', 'Fornecedor: Distribuidora São Paulo', '2025-10-26 23:38:57', '2025-10-26 23:38:57', NULL, NULL),
(2, 1, 'saida', 'Compra de açúcar - 25kg', 85.00, '2025-10-22', 'dinheiro', 'Pagamento à vista', '2025-10-26 23:38:57', '2025-10-26 23:38:57', NULL, NULL),
(3, 1, 'saida', 'Caixas para bolos - 100 unidades', 45.00, '2025-10-23', 'cartao_credito', 'Entrega em 2 dias úteis', '2025-10-26 23:38:57', '2025-10-26 23:38:57', NULL, NULL),
(4, 1, 'saida', 'Conta de luz - Outubro/2025', 320.50, '2025-10-24', 'boleto', 'Vencimento: 15/11/2025', '2025-10-26 23:38:57', '2025-10-26 23:38:57', NULL, NULL),
(5, 1, 'saida', 'Gasolina para entregas - 40 litros', 220.00, '2025-10-25', 'cartao_debito', 'Posto Shell - Av. Paulista', '2025-10-26 23:38:57', '2025-10-26 23:38:57', NULL, NULL),
(6, 1, 'saida', 'Produtos de limpeza diversos', 65.30, '2025-10-26', 'dinheiro', 'Detergente, desinfetante, papel toalha', '2025-10-26 23:38:57', '2025-10-26 23:38:57', NULL, NULL),
(7, 1, 'entrada', 'Receita de vendas do dia', 1250.00, '2025-10-26', 'dinheiro', 'Total de vendas do caixa', '2025-10-26 23:38:57', '2025-10-26 23:38:57', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `menus`
--

CREATE TABLE `menus` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `day_of_week` enum('segunda','terca','quarta','quinta','sexta','sabado','domingo') NOT NULL,
  `available` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `menus`
--

INSERT INTO `menus` (`id`, `product_id`, `day_of_week`, `available`, `created_at`, `updated_at`) VALUES
(1, 1, 'segunda', 1, '2025-10-26 23:38:57', '2025-10-26 23:38:57'),
(2, 1, 'terca', 1, '2025-10-26 23:38:57', '2025-10-26 23:38:57'),
(3, 1, 'quarta', 1, '2025-10-26 23:38:57', '2025-10-26 23:38:57'),
(4, 1, 'quinta', 1, '2025-10-26 23:38:58', '2025-10-26 23:38:58'),
(5, 1, 'sexta', 1, '2025-10-26 23:38:58', '2025-10-26 23:38:58'),
(6, 1, 'sabado', 1, '2025-10-26 23:38:58', '2025-10-26 23:38:58'),
(7, 2, 'segunda', 1, '2025-10-26 23:38:58', '2025-10-26 23:38:58'),
(8, 2, 'terca', 1, '2025-10-26 23:38:58', '2025-10-26 23:38:58'),
(9, 2, 'quarta', 1, '2025-10-26 23:38:58', '2025-10-26 23:38:58'),
(10, 2, 'quinta', 1, '2025-10-26 23:38:58', '2025-10-26 23:38:58'),
(11, 2, 'sexta', 1, '2025-10-26 23:38:58', '2025-10-26 23:38:58'),
(12, 2, 'sabado', 1, '2025-10-26 23:38:58', '2025-10-26 23:38:58'),
(13, 3, 'segunda', 1, '2025-10-26 23:38:58', '2025-10-26 23:38:58'),
(14, 3, 'terca', 1, '2025-10-26 23:38:58', '2025-10-26 23:38:58'),
(15, 3, 'quarta', 1, '2025-10-26 23:38:58', '2025-10-26 23:38:58'),
(16, 3, 'quinta', 1, '2025-10-26 23:38:58', '2025-10-26 23:38:58'),
(17, 3, 'sexta', 1, '2025-10-26 23:38:58', '2025-10-26 23:38:58'),
(18, 3, 'sabado', 1, '2025-10-26 23:38:58', '2025-10-26 23:38:58'),
(19, 4, 'quarta', 1, '2025-10-26 23:38:58', '2025-10-26 23:38:58'),
(20, 4, 'quinta', 1, '2025-10-26 23:38:58', '2025-10-26 23:38:58'),
(21, 4, 'sexta', 1, '2025-10-26 23:38:58', '2025-10-26 23:38:58'),
(22, 4, 'sabado', 1, '2025-10-26 23:38:58', '2025-10-26 23:38:58'),
(23, 4, 'domingo', 1, '2025-10-26 23:38:58', '2025-10-26 23:38:58'),
(24, 5, 'quarta', 1, '2025-10-26 23:38:58', '2025-10-26 23:38:58'),
(25, 5, 'quinta', 1, '2025-10-26 23:38:58', '2025-10-26 23:38:58'),
(26, 5, 'sexta', 1, '2025-10-26 23:38:58', '2025-10-26 23:38:58'),
(27, 5, 'sabado', 1, '2025-10-26 23:38:58', '2025-10-26 23:38:58'),
(28, 5, 'domingo', 1, '2025-10-26 23:38:58', '2025-10-26 23:38:58'),
(29, 6, 'quinta', 1, '2025-10-26 23:38:58', '2025-10-26 23:38:58'),
(30, 6, 'sexta', 1, '2025-10-26 23:38:58', '2025-10-26 23:38:58'),
(31, 6, 'sabado', 1, '2025-10-26 23:38:58', '2025-10-26 23:38:58'),
(32, 6, 'domingo', 1, '2025-10-26 23:38:58', '2025-10-26 23:38:58'),
(33, 7, 'quinta', 1, '2025-10-26 23:38:58', '2025-10-26 23:38:58'),
(34, 7, 'sexta', 1, '2025-10-26 23:38:58', '2025-10-26 23:38:58'),
(35, 7, 'sabado', 1, '2025-10-26 23:38:58', '2025-10-26 23:38:58'),
(36, 7, 'domingo', 1, '2025-10-26 23:38:58', '2025-10-26 23:38:58'),
(37, 8, 'quinta', 1, '2025-10-26 23:38:58', '2025-10-26 23:38:58'),
(38, 8, 'sexta', 1, '2025-10-26 23:38:58', '2025-10-26 23:38:58'),
(39, 8, 'sabado', 1, '2025-10-26 23:38:58', '2025-10-26 23:38:58'),
(40, 8, 'domingo', 1, '2025-10-26 23:38:58', '2025-10-26 23:38:58'),
(41, 9, 'sexta', 1, '2025-10-26 23:38:58', '2025-10-26 23:38:58'),
(42, 9, 'sabado', 1, '2025-10-26 23:38:58', '2025-10-26 23:38:58'),
(43, 9, 'domingo', 1, '2025-10-26 23:38:58', '2025-10-26 23:38:58'),
(44, 10, 'sexta', 1, '2025-10-26 23:38:58', '2025-10-26 23:38:58'),
(45, 10, 'sabado', 1, '2025-10-26 23:38:58', '2025-10-26 23:38:58'),
(46, 10, 'domingo', 1, '2025-10-26 23:38:58', '2025-10-26 23:38:58'),
(47, 11, 'segunda', 1, '2025-10-26 23:38:58', '2025-10-26 23:38:58'),
(48, 11, 'terca', 1, '2025-10-26 23:38:58', '2025-10-26 23:38:58'),
(49, 11, 'quarta', 1, '2025-10-26 23:38:58', '2025-10-26 23:38:58'),
(50, 11, 'quinta', 1, '2025-10-26 23:38:58', '2025-10-26 23:38:58'),
(51, 11, 'sexta', 1, '2025-10-26 23:38:58', '2025-10-26 23:38:58'),
(52, 11, 'sabado', 1, '2025-10-26 23:38:58', '2025-10-26 23:38:58'),
(53, 11, 'domingo', 1, '2025-10-26 23:38:58', '2025-10-26 23:38:58'),
(54, 12, 'segunda', 1, '2025-10-26 23:38:58', '2025-10-26 23:38:58'),
(55, 12, 'terca', 1, '2025-10-26 23:38:58', '2025-10-26 23:38:58'),
(56, 12, 'quarta', 1, '2025-10-26 23:38:58', '2025-10-26 23:38:58'),
(57, 12, 'quinta', 1, '2025-10-26 23:38:58', '2025-10-26 23:38:58'),
(58, 12, 'sexta', 1, '2025-10-26 23:38:58', '2025-10-26 23:38:58'),
(59, 12, 'sabado', 1, '2025-10-26 23:38:58', '2025-10-26 23:38:58'),
(60, 12, 'domingo', 1, '2025-10-26 23:38:58', '2025-10-26 23:38:58'),
(61, 13, 'segunda', 1, '2025-10-26 23:38:58', '2025-10-26 23:38:58'),
(62, 13, 'terca', 1, '2025-10-26 23:38:58', '2025-10-26 23:38:58'),
(63, 13, 'quarta', 1, '2025-10-26 23:38:58', '2025-10-26 23:38:58'),
(64, 13, 'quinta', 1, '2025-10-26 23:38:58', '2025-10-26 23:38:58'),
(65, 13, 'sexta', 1, '2025-10-26 23:38:58', '2025-10-26 23:38:58'),
(66, 14, 'segunda', 1, '2025-10-26 23:38:58', '2025-10-26 23:38:58'),
(67, 14, 'terca', 1, '2025-10-26 23:38:58', '2025-10-26 23:38:58'),
(68, 14, 'quarta', 1, '2025-10-26 23:38:58', '2025-10-26 23:38:58'),
(69, 14, 'quinta', 1, '2025-10-26 23:38:58', '2025-10-26 23:38:58'),
(70, 14, 'sexta', 1, '2025-10-26 23:38:58', '2025-10-26 23:38:58'),
(71, 15, 'segunda', 1, '2025-10-26 23:38:58', '2025-10-26 23:38:58'),
(72, 15, 'terca', 1, '2025-10-26 23:38:58', '2025-10-26 23:38:58'),
(73, 15, 'quarta', 1, '2025-10-26 23:38:58', '2025-10-26 23:38:58'),
(74, 15, 'quinta', 1, '2025-10-26 23:38:58', '2025-10-26 23:38:58'),
(75, 15, 'sexta', 1, '2025-10-26 23:38:58', '2025-10-26 23:38:58'),
(76, 15, 'sabado', 1, '2025-10-26 23:38:58', '2025-10-26 23:38:58'),
(77, 15, 'domingo', 1, '2025-10-26 23:38:58', '2025-10-26 23:38:58'),
(78, 16, 'segunda', 1, '2025-10-26 23:38:58', '2025-10-26 23:38:58'),
(79, 16, 'terca', 1, '2025-10-26 23:38:58', '2025-10-26 23:38:58'),
(80, 16, 'quarta', 1, '2025-10-26 23:38:58', '2025-10-26 23:38:58'),
(81, 16, 'quinta', 1, '2025-10-26 23:38:58', '2025-10-26 23:38:58'),
(82, 16, 'sexta', 1, '2025-10-26 23:38:58', '2025-10-26 23:38:58'),
(83, 16, 'sabado', 1, '2025-10-26 23:38:58', '2025-10-26 23:38:58'),
(84, 16, 'domingo', 1, '2025-10-26 23:38:58', '2025-10-26 23:38:58');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2025_10_21_193227_create_motoboys_table', 1),
(5, '2025_10_21_193228_create_categories_table', 1),
(6, '2025_10_21_193228_create_products_table', 1),
(7, '2025_10_21_193229_create_menus_table', 1),
(8, '2025_10_21_193230_create_cash_registers_table', 1),
(9, '2025_10_21_193230_create_customers_table', 1),
(10, '2025_10_21_193230_create_tables_table', 1),
(11, '2025_10_21_193231_create_sales_table', 1),
(12, '2025_10_21_193232_create_sale_items_table', 1),
(13, '2025_10_21_193245_create_expenses_table', 1),
(14, '2025_10_21_193300_create_roles_permissions_system', 1),
(15, '2025_10_21_193330_create_custom_auth_tokens_table', 1),
(16, '2025_10_23_174857_add_cash_register_id_to_expenses_table', 1),
(17, '2025_10_24_193005_create_settings_table', 1),
(18, '2025_10_27_192637_create_encomendas_table', 2),
(19, '2025_10_28_182506_add_action_to_user_permission_table', 3);

-- --------------------------------------------------------

--
-- Table structure for table `motoboys`
--

CREATE TABLE `motoboys` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `cpf` varchar(255) DEFAULT NULL,
  `cnh` varchar(255) DEFAULT NULL,
  `placa_veiculo` varchar(255) DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `motoboys`
--

INSERT INTO `motoboys` (`id`, `name`, `phone`, `cpf`, `cnh`, `placa_veiculo`, `active`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Carlos Mendes', '(11) 99999-1111', '123.456.789-01', '12345678901', 'ABC-1234', 1, '2025-10-26 23:38:57', '2025-10-26 23:38:57', NULL),
(2, 'Roberto Lima', '(11) 99999-2222', '987.654.321-02', '98765432109', 'DEF-5678', 1, '2025-10-26 23:38:57', '2025-10-26 23:38:57', NULL),
(3, 'André Silva', '(11) 99999-3333', '456.789.123-03', '45678912304', 'GHI-9012', 1, '2025-10-26 23:38:57', '2025-10-26 23:38:57', NULL),
(4, 'Felipe Costa', '(11) 99999-4444', '789.123.456-04', '78912345605', 'JKL-3456', 1, '2025-10-26 23:38:57', '2025-10-26 23:38:57', NULL),
(5, 'Lucas Pereira', '(11) 99999-5555', '321.654.987-05', '32165498706', 'MNO-7890', 1, '2025-10-26 23:38:57', '2025-10-26 23:38:57', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `label` varchar(255) NOT NULL,
  `module` varchar(255) NOT NULL,
  `action` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `label`, `module`, `action`, `description`, `created_at`, `updated_at`) VALUES
(1, 'users.view', 'Visualizar Usuários', 'users', 'view', NULL, '2025-10-26 23:38:55', '2025-10-26 23:38:55'),
(2, 'users.create', 'Criar Usuários', 'users', 'create', NULL, '2025-10-26 23:38:55', '2025-10-26 23:38:55'),
(3, 'users.edit', 'Editar Usuários', 'users', 'edit', NULL, '2025-10-26 23:38:55', '2025-10-26 23:38:55'),
(4, 'users.delete', 'Excluir Usuários', 'users', 'delete', NULL, '2025-10-26 23:38:55', '2025-10-26 23:38:55'),
(5, 'categories.view', 'Visualizar Categorias', 'categories', 'view', NULL, '2025-10-26 23:38:55', '2025-10-26 23:38:55'),
(6, 'categories.create', 'Criar Categorias', 'categories', 'create', NULL, '2025-10-26 23:38:55', '2025-10-26 23:38:55'),
(7, 'categories.edit', 'Editar Categorias', 'categories', 'edit', NULL, '2025-10-26 23:38:55', '2025-10-26 23:38:55'),
(8, 'categories.delete', 'Excluir Categorias', 'categories', 'delete', NULL, '2025-10-26 23:38:55', '2025-10-26 23:38:55'),
(9, 'products.view', 'Visualizar Produtos', 'products', 'view', NULL, '2025-10-26 23:38:55', '2025-10-26 23:38:55'),
(10, 'products.create', 'Criar Produtos', 'products', 'create', NULL, '2025-10-26 23:38:55', '2025-10-26 23:38:55'),
(11, 'products.edit', 'Editar Produtos', 'products', 'edit', NULL, '2025-10-26 23:38:55', '2025-10-26 23:38:55'),
(12, 'products.delete', 'Excluir Produtos', 'products', 'delete', NULL, '2025-10-26 23:38:55', '2025-10-26 23:38:55'),
(13, 'sales.view', 'Visualizar Vendas', 'sales', 'view', NULL, '2025-10-26 23:38:55', '2025-10-26 23:38:55'),
(14, 'sales.create', 'Criar Vendas', 'sales', 'create', NULL, '2025-10-26 23:38:55', '2025-10-26 23:38:55'),
(15, 'sales.edit', 'Editar Vendas', 'sales', 'edit', NULL, '2025-10-26 23:38:55', '2025-10-26 23:38:55'),
(16, 'sales.delete', 'Excluir Vendas', 'sales', 'delete', NULL, '2025-10-26 23:38:55', '2025-10-26 23:38:55'),
(17, 'sales.cancel', 'Cancelar Vendas', 'sales', 'cancel', NULL, '2025-10-26 23:38:55', '2025-10-26 23:38:55'),
(18, 'sales.update_status', 'Alterar Status de Vendas', 'sales', 'update_status', NULL, '2025-10-26 23:38:55', '2025-10-26 23:38:55'),
(19, 'tables.view', 'Visualizar Mesas', 'tables', 'view', NULL, '2025-10-26 23:38:55', '2025-10-26 23:38:55'),
(20, 'tables.create', 'Criar Mesas', 'tables', 'create', NULL, '2025-10-26 23:38:55', '2025-10-26 23:38:55'),
(21, 'tables.edit', 'Editar Mesas', 'tables', 'edit', NULL, '2025-10-26 23:38:55', '2025-10-26 23:38:55'),
(22, 'tables.delete', 'Excluir Mesas', 'tables', 'delete', NULL, '2025-10-26 23:38:55', '2025-10-26 23:38:55'),
(23, 'motoboys.view', 'Visualizar Motoboys', 'motoboys', 'view', NULL, '2025-10-26 23:38:55', '2025-10-26 23:38:55'),
(24, 'motoboys.create', 'Criar Motoboys', 'motoboys', 'create', NULL, '2025-10-26 23:38:55', '2025-10-26 23:38:55'),
(25, 'motoboys.edit', 'Editar Motoboys', 'motoboys', 'edit', NULL, '2025-10-26 23:38:55', '2025-10-26 23:38:55'),
(26, 'motoboys.delete', 'Excluir Motoboys', 'motoboys', 'delete', NULL, '2025-10-26 23:38:55', '2025-10-26 23:38:55'),
(27, 'customers.view', 'Visualizar Clientes', 'customers', 'view', NULL, '2025-10-26 23:38:55', '2025-10-26 23:38:55'),
(28, 'customers.create', 'Criar Clientes', 'customers', 'create', NULL, '2025-10-26 23:38:55', '2025-10-26 23:38:55'),
(29, 'customers.edit', 'Editar Clientes', 'customers', 'edit', NULL, '2025-10-26 23:38:55', '2025-10-26 23:38:55'),
(30, 'customers.delete', 'Excluir Clientes', 'customers', 'delete', NULL, '2025-10-26 23:38:55', '2025-10-26 23:38:55'),
(31, 'expenses.view', 'Visualizar Despesas', 'expenses', 'view', NULL, '2025-10-26 23:38:55', '2025-10-26 23:38:55'),
(32, 'expenses.create', 'Criar Despesas', 'expenses', 'create', NULL, '2025-10-26 23:38:55', '2025-10-26 23:38:55'),
(33, 'expenses.edit', 'Editar Despesas', 'expenses', 'edit', NULL, '2025-10-26 23:38:55', '2025-10-26 23:38:55'),
(34, 'expenses.delete', 'Excluir Despesas', 'expenses', 'delete', NULL, '2025-10-26 23:38:55', '2025-10-26 23:38:55'),
(35, 'menu.view', 'Visualizar Cardápio', 'menu', 'view', NULL, '2025-10-26 23:38:55', '2025-10-26 23:38:55'),
(36, 'menu.create', 'Gerenciar Cardápio', 'menu', 'create', NULL, '2025-10-26 23:38:55', '2025-10-26 23:38:55'),
(37, 'menu.edit', 'Editar Cardápio', 'menu', 'edit', NULL, '2025-10-26 23:38:55', '2025-10-26 23:38:55'),
(38, 'menu.delete', 'Excluir do Cardápio', 'menu', 'delete', NULL, '2025-10-26 23:38:55', '2025-10-26 23:38:55'),
(39, 'cash_registers.view', 'Visualizar Caixas', 'cash_registers', 'view', NULL, '2025-10-26 23:38:55', '2025-10-26 23:38:55'),
(40, 'cash_registers.create', 'Abrir Caixa', 'cash_registers', 'create', NULL, '2025-10-26 23:38:55', '2025-10-26 23:38:55'),
(41, 'cash_registers.edit', 'Editar Caixa', 'cash_registers', 'edit', NULL, '2025-10-26 23:38:55', '2025-10-26 23:38:55'),
(42, 'cash_registers.close', 'Fechar Caixa', 'cash_registers', 'close', NULL, '2025-10-26 23:38:55', '2025-10-26 23:38:55'),
(43, 'sale_items.view', 'Visualizar Itens de Venda', 'sale_items', 'view', NULL, '2025-10-26 23:38:55', '2025-10-26 23:38:55'),
(44, 'sale_items.create', 'Criar Itens de Venda', 'sale_items', 'create', NULL, '2025-10-26 23:38:55', '2025-10-26 23:38:55'),
(45, 'sale_items.edit', 'Editar Itens de Venda', 'sale_items', 'edit', NULL, '2025-10-26 23:38:55', '2025-10-26 23:38:55'),
(46, 'sale_items.delete', 'Excluir Itens de Venda', 'sale_items', 'delete', NULL, '2025-10-26 23:38:55', '2025-10-26 23:38:55'),
(47, 'expense_categories.view', 'Visualizar Categorias de Despesa', 'expense_categories', 'view', NULL, '2025-10-26 23:38:55', '2025-10-26 23:38:55'),
(48, 'expense_categories.create', 'Criar Categorias de Despesa', 'expense_categories', 'create', NULL, '2025-10-26 23:38:55', '2025-10-26 23:38:55'),
(49, 'expense_categories.edit', 'Editar Categorias de Despesa', 'expense_categories', 'edit', NULL, '2025-10-26 23:38:55', '2025-10-26 23:38:55'),
(50, 'expense_categories.delete', 'Excluir Categorias de Despesa', 'expense_categories', 'delete', NULL, '2025-10-26 23:38:55', '2025-10-26 23:38:55'),
(51, 'reports.view', 'Visualizar Relatórios', 'reports', 'view', NULL, '2025-10-26 23:38:55', '2025-10-26 23:38:55'),
(52, 'analytics.view', 'Visualizar Estatísticas', 'analytics', 'view', NULL, '2025-10-26 23:38:55', '2025-10-26 23:38:55'),
(53, 'settings.view', 'Visualizar Configurações', 'settings', 'view', NULL, '2025-10-26 23:38:55', '2025-10-26 23:38:55'),
(54, 'settings.edit', 'Editar Configurações', 'settings', 'edit', NULL, '2025-10-26 23:38:55', '2025-10-26 23:38:55');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `category_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `cost_price` decimal(10,2) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `category_id`, `name`, `description`, `price`, `cost_price`, `image`, `active`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 'Bolo de Chocolate', 'Bolo fofinho de chocolate com cobertura de brigadeiro', 45.00, 15.00, NULL, 1, '2025-10-26 23:38:57', '2025-10-26 23:38:57', NULL),
(2, 1, 'Bolo de Baunilha', 'Bolo clássico de baunilha com recheio de doce de leite', 40.00, 12.00, NULL, 1, '2025-10-26 23:38:57', '2025-10-26 23:38:57', NULL),
(3, 1, 'Bolo de Cenoura', 'Bolo de cenoura com cobertura de chocolate', 35.00, 10.00, NULL, 1, '2025-10-26 23:38:57', '2025-10-26 23:38:57', NULL),
(4, 2, 'Bolo Red Velvet', 'Bolo vermelho com cream cheese e calda de frutas vermelhas', 65.00, 25.00, NULL, 1, '2025-10-26 23:38:57', '2025-10-26 23:38:57', NULL),
(5, 2, 'Bolo de Nozes', 'Bolo com nozes, castanhas e recheio de brigadeiro', 55.00, 20.00, NULL, 1, '2025-10-26 23:38:57', '2025-10-26 23:38:57', NULL),
(6, 3, 'Brigadeiro Gourmet', 'Brigadeiro tradicional com chocolate belga (100 unidades)', 80.00, 25.00, NULL, 1, '2025-10-26 23:38:57', '2025-10-26 23:38:57', NULL),
(7, 3, 'Beijinho', 'Beijinho com coco ralado (100 unidades)', 70.00, 20.00, NULL, 1, '2025-10-26 23:38:57', '2025-10-26 23:38:57', NULL),
(8, 3, 'Casadinho', 'Dois biscoitos com recheio de doce de leite (100 unidades)', 75.00, 22.00, NULL, 1, '2025-10-26 23:38:57', '2025-10-26 23:38:57', NULL),
(9, 4, 'Torta de Limão', 'Torta com mousse de limão e merengue', 50.00, 18.00, NULL, 1, '2025-10-26 23:38:57', '2025-10-26 23:38:57', NULL),
(10, 4, 'Torta de Maçã', 'Torta americana de maçã com canela', 48.00, 16.00, NULL, 1, '2025-10-26 23:38:57', '2025-10-26 23:38:57', NULL),
(11, 5, 'Pão Francês', 'Pão francês tradicional (unidade)', 1.50, 0.40, NULL, 1, '2025-10-26 23:38:57', '2025-10-26 23:38:57', NULL),
(12, 5, 'Pão de Leite', 'Pão macio com leite (unidade)', 2.00, 0.60, NULL, 1, '2025-10-26 23:38:57', '2025-10-26 23:38:57', NULL),
(13, 6, 'Coxinha de Frango', 'Coxinha de frango com catupiry (100 unidades)', 120.00, 40.00, NULL, 1, '2025-10-26 23:38:57', '2025-10-26 23:38:57', NULL),
(14, 6, 'Esfiha de Carne', 'Esfiha de carne moída (100 unidades)', 100.00, 35.00, NULL, 1, '2025-10-26 23:38:57', '2025-10-26 23:38:57', NULL),
(15, 7, 'Refrigerante Coca-Cola', 'Refrigerante Coca-Cola 2L', 8.00, 4.50, NULL, 1, '2025-10-26 23:38:57', '2025-10-26 23:38:57', NULL),
(16, 7, 'Suco Natural de Laranja', 'Suco de laranja natural 1L', 12.00, 6.00, NULL, 1, '2025-10-26 23:38:57', '2025-10-26 23:38:57', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `label` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `default_permissions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`default_permissions`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `label`, `description`, `default_permissions`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'Administrador', 'Acesso total a todas as funcionalidades do sistema', NULL, '2025-10-26 23:38:55', '2025-10-26 23:38:55'),
(2, 'gestor', 'Gerente', 'Gerenciamento completo do negócio exceto usuários', NULL, '2025-10-26 23:38:55', '2025-10-26 23:38:55'),
(3, 'atendente', 'Atendente', 'Acesso básico para operações do dia a dia', NULL, '2025-10-26 23:38:57', '2025-10-26 23:38:57');

-- --------------------------------------------------------

--
-- Table structure for table `role_permission`
--

CREATE TABLE `role_permission` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_permission`
--

INSERT INTO `role_permission` (`id`, `role_id`, `permission_id`, `created_at`, `updated_at`) VALUES
(1, 2, 5, NULL, NULL),
(2, 2, 6, NULL, NULL),
(3, 2, 7, NULL, NULL),
(4, 2, 8, NULL, NULL),
(5, 2, 9, NULL, NULL),
(6, 2, 10, NULL, NULL),
(7, 2, 11, NULL, NULL),
(8, 2, 12, NULL, NULL),
(9, 2, 13, NULL, NULL),
(10, 2, 14, NULL, NULL),
(11, 2, 15, NULL, NULL),
(12, 2, 16, NULL, NULL),
(13, 2, 17, NULL, NULL),
(14, 2, 18, NULL, NULL),
(15, 2, 19, NULL, NULL),
(16, 2, 20, NULL, NULL),
(17, 2, 21, NULL, NULL),
(18, 2, 22, NULL, NULL),
(19, 2, 23, NULL, NULL),
(20, 2, 24, NULL, NULL),
(21, 2, 25, NULL, NULL),
(22, 2, 26, NULL, NULL),
(23, 2, 27, NULL, NULL),
(24, 2, 28, NULL, NULL),
(25, 2, 29, NULL, NULL),
(26, 2, 30, NULL, NULL),
(27, 2, 31, NULL, NULL),
(28, 2, 32, NULL, NULL),
(29, 2, 33, NULL, NULL),
(30, 2, 34, NULL, NULL),
(31, 2, 35, NULL, NULL),
(32, 2, 36, NULL, NULL),
(33, 2, 37, NULL, NULL),
(34, 2, 38, NULL, NULL),
(35, 2, 39, NULL, NULL),
(36, 2, 40, NULL, NULL),
(37, 2, 41, NULL, NULL),
(38, 2, 42, NULL, NULL),
(39, 2, 43, NULL, NULL),
(40, 2, 44, NULL, NULL),
(41, 2, 45, NULL, NULL),
(42, 2, 46, NULL, NULL),
(43, 2, 51, NULL, NULL),
(44, 2, 52, NULL, NULL),
(45, 2, 53, NULL, NULL),
(46, 3, 14, NULL, NULL),
(47, 3, 13, NULL, NULL),
(48, 3, 15, NULL, NULL),
(49, 3, 17, NULL, NULL),
(50, 3, 18, NULL, NULL),
(51, 3, 19, NULL, NULL),
(52, 3, 21, NULL, NULL),
(53, 3, 27, NULL, NULL),
(54, 3, 28, NULL, NULL),
(55, 3, 29, NULL, NULL),
(56, 3, 23, NULL, NULL),
(57, 3, 35, NULL, NULL),
(58, 3, 44, NULL, NULL),
(59, 3, 45, NULL, NULL),
(60, 3, 46, NULL, NULL),
(61, 3, 32, NULL, NULL),
(62, 3, 31, NULL, NULL),
(63, 3, 51, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `sales`
--

CREATE TABLE `sales` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `cash_register_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `customer_id` bigint(20) UNSIGNED DEFAULT NULL,
  `table_id` bigint(20) UNSIGNED DEFAULT NULL,
  `motoboy_id` bigint(20) UNSIGNED DEFAULT NULL,
  `code` varchar(255) NOT NULL,
  `type` enum('balcao','delivery','encomenda') NOT NULL DEFAULT 'balcao',
  `status` enum('pendente','em_preparo','pronto','saiu_entrega','entregue','cancelado','finalizado') NOT NULL DEFAULT 'pendente',
  `subtotal` decimal(10,2) NOT NULL,
  `discount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `delivery_fee` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total` decimal(10,2) NOT NULL,
  `payment_method` enum('dinheiro','cartao_credito','cartao_debito','pix','transferencia') DEFAULT NULL,
  `delivery_date` date DEFAULT NULL,
  `delivery_time` time DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `delivery_address` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sales`
--

INSERT INTO `sales` (`id`, `cash_register_id`, `user_id`, `customer_id`, `table_id`, `motoboy_id`, `code`, `type`, `status`, `subtotal`, `discount`, `delivery_fee`, `total`, `payment_method`, `delivery_date`, `delivery_time`, `notes`, `delivery_address`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 1, 1, 1, NULL, 'VEN-68FE86E2871A2', 'balcao', 'finalizado', 85.00, 0.00, 0.00, 85.00, 'dinheiro', NULL, NULL, 'Venda no balcão', NULL, '2025-10-26 23:38:58', '2025-10-26 23:38:58', NULL),
(2, 1, 1, 2, 2, NULL, 'VEN-68FE86E2871B4', 'balcao', 'finalizado', 120.50, 5.00, 0.00, 115.50, 'pix', NULL, NULL, 'Cliente pediu desconto', NULL, '2025-10-26 23:38:58', '2025-10-26 23:38:58', NULL),
(3, 1, 1, 3, NULL, 1, 'VEN-68FE86E2871C5', 'delivery', 'entregue', 95.00, 0.00, 5.00, 100.00, 'cartao_credito', '2025-10-25', '14:30:00', 'Entrega urgente', 'Rua Augusta, 789, Centro, São Paulo - SP', '2025-10-26 23:38:58', '2025-10-26 23:38:58', NULL),
(4, 1, 1, 1, 1, NULL, 'VEN-PEND-68FE86E28DEE6', 'balcao', 'pendente', 45.00, 0.00, 0.00, 45.00, 'dinheiro', NULL, NULL, 'Pedido pendente na mesa - cliente disse que volta logo', NULL, '2025-10-26 23:38:58', '2025-10-26 23:38:58', NULL),
(5, 1, 1, 2, 2, 1, 'VEN-PEND-68FE86E28DF02', 'delivery', 'pendente', 78.90, 0.00, 5.00, 83.90, 'dinheiro', '2025-10-26', '18:30:00', 'Cliente pediu entrega urgente mas ainda não pagou', 'Rua da Mata, 456, Santana, São Paulo - SP', '2025-10-26 23:38:58', '2025-10-26 23:38:58', NULL),
(6, 1, 1, 3, 3, NULL, 'VEN-PEND-68FE86E28DF32', 'balcao', 'pendente', 35.50, 0.00, 0.00, 35.50, 'cartao_credito', NULL, NULL, 'Cliente foi atender telefone, voltará em seguida', NULL, '2025-10-26 23:38:58', '2025-10-26 23:38:58', NULL),
(7, 1, 1, NULL, NULL, 3, 'VEN-68FE87B7809A4', 'delivery', 'pendente', 40.00, 0.00, 5.00, 45.00, NULL, NULL, NULL, NULL, 'sdsd', '2025-10-26 23:42:31', '2025-10-26 23:42:31', NULL),
(8, 1, 1, NULL, NULL, 1, 'VEN-68FE87FA67C62', 'delivery', 'pendente', 35.00, 0.00, 5.00, 40.00, NULL, NULL, NULL, NULL, 'ere', '2025-10-26 23:43:38', '2025-10-26 23:43:38', NULL),
(9, 1, 1, 2, NULL, 3, 'VEN-68FE8C3493F71', 'delivery', 'entregue', 35.00, 0.00, 5.00, 40.00, 'pix', NULL, NULL, NULL, 'dfdf', '2025-10-27 00:01:40', '2025-10-27 05:46:17', NULL),
(10, 1, 1, NULL, NULL, NULL, 'VEN-68FE8FB566D52', 'balcao', 'pendente', 40.00, 0.00, 0.00, 40.00, NULL, NULL, NULL, NULL, NULL, '2025-10-27 00:16:37', '2025-10-27 00:16:37', NULL),
(11, 1, 1, NULL, 5, NULL, 'VEN-68FED8B0C5800', 'balcao', 'pendente', 45.00, 0.00, 0.00, 45.00, 'pix', NULL, NULL, NULL, NULL, '2025-10-27 05:28:00', '2025-10-27 05:28:00', NULL),
(12, 1, 1, 1, NULL, 1, 'VEN-68FED9C1E555F', 'delivery', 'entregue', 297.00, 0.00, 5.00, 302.00, 'cartao_credito', NULL, NULL, NULL, 'dfdf', '2025-10-27 05:32:33', '2025-10-27 05:46:09', NULL),
(13, 1, 1, NULL, 2, NULL, 'VEN-68FEDBDD10AF8', 'balcao', 'finalizado', 40.00, 0.00, 5.00, 45.00, 'pix', NULL, NULL, NULL, NULL, '2025-10-27 05:41:33', '2025-10-28 04:52:13', NULL),
(14, 1, 1, NULL, 2, NULL, 'VEN-68FEDCD857015', 'balcao', 'finalizado', 40.00, 0.00, 5.00, 45.00, 'pix', NULL, NULL, NULL, NULL, '2025-10-27 05:45:44', '2025-10-27 06:06:20', NULL),
(15, 1, 1, NULL, 2, NULL, 'VEN-68FEDCE658026', 'balcao', 'finalizado', 40.00, 0.00, 5.00, 45.00, 'pix', NULL, NULL, NULL, NULL, '2025-10-27 05:45:58', '2025-10-27 06:06:10', NULL),
(16, 1, 1, NULL, NULL, NULL, 'VEN-690020A167370', 'balcao', 'finalizado', 45.00, 0.00, 0.00, 45.00, 'pix', NULL, NULL, NULL, NULL, '2025-10-28 04:47:13', '2025-10-28 04:47:13', NULL),
(17, 1, 1, NULL, NULL, NULL, 'VEN-690020BD0E723', 'balcao', 'finalizado', 45.00, 0.00, 5.00, 50.00, 'pix', NULL, NULL, NULL, NULL, '2025-10-28 04:47:41', '2025-10-28 04:47:41', NULL),
(18, 1, 1, NULL, NULL, NULL, 'VEN-6900215ED8FD9', 'balcao', 'finalizado', 45.00, 0.00, 5.00, 50.00, 'pix', NULL, NULL, NULL, NULL, '2025-10-28 04:50:22', '2025-10-28 04:50:22', NULL),
(19, 1, 1, NULL, NULL, NULL, 'VEN-6900F977163D8', 'balcao', 'finalizado', 45.00, 0.00, 0.00, 45.00, 'pix', NULL, NULL, NULL, NULL, '2025-10-28 20:12:23', '2025-10-28 20:12:23', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `sale_items`
--

CREATE TABLE `sale_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `sale_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sale_items`
--

INSERT INTO `sale_items` (`id`, `sale_id`, `product_id`, `quantity`, `unit_price`, `subtotal`, `notes`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, 45.00, 45.00, NULL, '2025-10-26 23:38:58', '2025-10-26 23:38:58'),
(2, 1, 6, 50, 0.80, 40.00, 'Para festa de aniversário', '2025-10-26 23:38:58', '2025-10-26 23:38:58'),
(3, 2, 4, 1, 65.00, 65.00, NULL, '2025-10-26 23:38:58', '2025-10-26 23:38:58'),
(4, 2, 9, 1, 50.00, 50.00, 'Entrega na sexta-feira', '2025-10-26 23:38:58', '2025-10-26 23:38:58'),
(5, 2, 15, 1, 8.00, 8.00, NULL, '2025-10-26 23:38:58', '2025-10-26 23:38:58'),
(6, 3, 13, 100, 1.20, 120.00, 'Para evento corporativo', '2025-10-26 23:38:58', '2025-10-26 23:38:58'),
(7, 7, 2, 1, 40.00, 40.00, NULL, '2025-10-26 23:42:31', '2025-10-26 23:42:31'),
(8, 8, 3, 1, 35.00, 35.00, NULL, '2025-10-26 23:43:38', '2025-10-26 23:43:38'),
(9, 9, 3, 1, 35.00, 35.00, NULL, '2025-10-27 00:01:40', '2025-10-27 00:01:40'),
(10, 10, 2, 1, 40.00, 40.00, NULL, '2025-10-27 00:16:37', '2025-10-27 00:16:37'),
(11, 11, 1, 1, 45.00, 45.00, NULL, '2025-10-27 05:28:00', '2025-10-27 05:28:00'),
(12, 12, 1, 1, 45.00, 45.00, NULL, '2025-10-27 05:32:33', '2025-10-27 05:32:33'),
(13, 12, 13, 2, 120.00, 240.00, NULL, '2025-10-27 05:32:33', '2025-10-27 05:32:33'),
(14, 12, 16, 1, 12.00, 12.00, NULL, '2025-10-27 05:32:33', '2025-10-27 05:32:33'),
(18, 15, 2, 1, 40.00, 40.00, NULL, '2025-10-27 06:06:10', '2025-10-27 06:06:10'),
(19, 14, 2, 1, 40.00, 40.00, NULL, '2025-10-27 06:06:20', '2025-10-27 06:06:20'),
(20, 16, 1, 1, 45.00, 45.00, NULL, '2025-10-28 04:47:13', '2025-10-28 04:47:13'),
(21, 17, 1, 1, 45.00, 45.00, NULL, '2025-10-28 04:47:41', '2025-10-28 04:47:41'),
(23, 18, 1, 1, 45.00, 45.00, NULL, '2025-10-28 04:51:23', '2025-10-28 04:51:23'),
(25, 13, 2, 1, 40.00, 40.00, NULL, '2025-10-28 04:52:52', '2025-10-28 04:52:52'),
(26, 19, 1, 1, 45.00, 45.00, NULL, '2025-10-28 20:12:23', '2025-10-28 20:12:23');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('ayFwv8Y8jxcyBHFvoxnX70b9iSPineUjRocuYq4u', 2, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiTGdRY3RtNVgzNTdWUDI5b2pYNGJIcTUycUUyU0tZNEQ1WmJIOXp0MCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzk6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9nZXN0b3IvY2F0ZWdvcmlhcyI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjI7fQ==', 1761688668),
('pPnplG64X6vDd83sJHa7FYGrRQFr3Whsdc41s94C', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiZ3lVdkVVVmJZbGljNVhaVHNQVUtzcFljc0Z5bElmQjg5UnJrYWlObCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1761688459);

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `key` varchar(255) NOT NULL,
  `value` text DEFAULT NULL,
  `type` enum('string','boolean','json','integer') NOT NULL DEFAULT 'string',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `key`, `value`, `type`, `created_at`, `updated_at`) VALUES
(1, 'store_status', 'open', 'string', '2025-10-28 16:55:56', '2025-10-28 16:55:56'),
(2, 'banner_active', '0', 'boolean', '2025-10-28 16:55:56', '2025-10-28 19:56:19'),
(3, 'banner_message', 'Teste', 'string', '2025-10-28 16:55:56', '2025-10-28 16:55:56');

-- --------------------------------------------------------

--
-- Table structure for table `tables`
--

CREATE TABLE `tables` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `number` varchar(255) NOT NULL,
  `capacity` int(11) NOT NULL DEFAULT 4,
  `status` enum('disponivel','ocupada','reservada') NOT NULL DEFAULT 'disponivel',
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tables`
--

INSERT INTO `tables` (`id`, `number`, `capacity`, `status`, `active`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, '01', 2, 'disponivel', 1, '2025-10-26 23:38:57', '2025-10-26 23:38:57', NULL),
(2, '02', 2, 'disponivel', 1, '2025-10-26 23:38:57', '2025-10-27 06:06:10', NULL),
(3, '03', 4, 'disponivel', 1, '2025-10-26 23:38:57', '2025-10-26 23:38:57', NULL),
(4, '04', 4, 'disponivel', 1, '2025-10-26 23:38:57', '2025-10-26 23:38:57', NULL),
(5, '05', 6, 'ocupada', 1, '2025-10-26 23:38:57', '2025-10-27 05:28:00', NULL),
(6, '06', 6, 'disponivel', 1, '2025-10-26 23:38:57', '2025-10-26 23:38:57', NULL),
(7, '07', 8, 'disponivel', 1, '2025-10-26 23:38:57', '2025-10-26 23:38:57', NULL),
(8, '08', 8, 'disponivel', 1, '2025-10-26 23:38:57', '2025-10-26 23:38:57', NULL),
(9, '09', 10, 'disponivel', 1, '2025-10-26 23:38:57', '2025-10-26 23:38:57', NULL),
(10, '10', 10, 'disponivel', 1, '2025-10-26 23:38:57', '2025-10-26 23:38:57', NULL),
(11, 'BALCAO', 12, 'disponivel', 1, '2025-10-26 23:38:57', '2025-10-26 23:38:57', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','gestor','atendente') NOT NULL DEFAULT 'atendente',
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `active`, `email_verified_at`, `remember_token`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Test User', 'test@example.com', '$2y$12$KXOTNWSACdqp1puQHHeOA.eYjXYXn7dAQxzsVm2FJ2z8yaYqKe6z6', 'atendente', 1, '2025-10-26 23:38:55', 'BLLFnhrUm8atkk7LLfmfzwg0IcbxF1vhitEsdoWKkTNAhF8RaE93cScgt1Du', '2025-10-26 23:38:55', '2025-10-26 23:38:55', NULL),
(2, 'Jenifer Silva', 'jenifer@docedoce.com', '$2y$12$sZycMOCcQJcUdH39.Y9bnOOCzTMjJae2hmJ1EXmjRQIQ11.Lfeyfq', 'atendente', 1, NULL, NULL, '2025-10-28 20:51:37', '2025-10-28 20:51:37', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_permission`
--

CREATE TABLE `user_permission` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `action` enum('grant','revoke') NOT NULL DEFAULT 'grant',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_permission`
--

INSERT INTO `user_permission` (`id`, `user_id`, `permission_id`, `action`, `created_at`, `updated_at`) VALUES
(1, 1, 2, 'grant', NULL, NULL),
(2, 1, 1, 'grant', NULL, NULL),
(3, 1, 52, 'grant', NULL, NULL),
(4, 1, 42, 'grant', NULL, NULL),
(5, 1, 40, 'grant', NULL, NULL),
(6, 1, 41, 'grant', NULL, NULL),
(7, 1, 39, 'grant', NULL, NULL),
(8, 1, 6, 'grant', NULL, NULL),
(9, 1, 8, 'grant', NULL, NULL),
(10, 1, 7, 'grant', NULL, NULL),
(11, 1, 5, 'grant', NULL, NULL),
(12, 1, 28, 'grant', NULL, NULL),
(13, 1, 30, 'grant', NULL, NULL),
(14, 1, 29, 'grant', NULL, NULL),
(15, 1, 27, 'grant', NULL, NULL),
(16, 1, 48, 'grant', NULL, NULL),
(17, 1, 50, 'grant', NULL, NULL),
(18, 1, 49, 'grant', NULL, NULL),
(19, 1, 47, 'grant', NULL, NULL),
(20, 1, 32, 'grant', NULL, NULL),
(21, 1, 34, 'grant', NULL, NULL),
(22, 1, 33, 'grant', NULL, NULL),
(23, 1, 31, 'grant', NULL, NULL),
(24, 1, 36, 'grant', NULL, NULL),
(25, 1, 38, 'grant', NULL, NULL),
(26, 1, 37, 'grant', NULL, NULL),
(27, 1, 35, 'grant', NULL, NULL),
(28, 1, 24, 'grant', NULL, NULL),
(29, 1, 26, 'grant', NULL, NULL),
(30, 1, 25, 'grant', NULL, NULL),
(31, 1, 23, 'grant', NULL, NULL),
(32, 1, 10, 'grant', NULL, NULL),
(33, 1, 12, 'grant', NULL, NULL),
(34, 1, 11, 'grant', NULL, NULL),
(35, 1, 9, 'grant', NULL, NULL),
(36, 1, 51, 'grant', NULL, NULL),
(37, 1, 44, 'grant', NULL, NULL),
(38, 1, 46, 'grant', NULL, NULL),
(39, 1, 45, 'grant', NULL, NULL),
(40, 1, 43, 'grant', NULL, NULL),
(41, 1, 17, 'grant', NULL, NULL),
(42, 1, 14, 'grant', NULL, NULL),
(43, 1, 16, 'grant', NULL, NULL),
(44, 1, 15, 'grant', NULL, NULL),
(45, 1, 18, 'grant', NULL, NULL),
(46, 1, 13, 'grant', NULL, NULL),
(49, 1, 20, 'grant', NULL, NULL),
(50, 1, 22, 'grant', NULL, NULL),
(51, 1, 21, 'grant', NULL, NULL),
(52, 1, 19, 'grant', NULL, NULL),
(53, 1, 4, 'grant', NULL, NULL),
(54, 1, 3, 'grant', NULL, NULL),
(55, 1, 54, 'grant', NULL, NULL),
(56, 1, 53, 'grant', NULL, NULL),
(57, 2, 52, 'grant', NULL, NULL),
(58, 2, 42, 'grant', NULL, NULL),
(59, 2, 40, 'grant', NULL, NULL),
(60, 2, 41, 'grant', NULL, NULL),
(61, 2, 39, 'grant', NULL, NULL),
(62, 2, 6, 'grant', NULL, NULL),
(63, 2, 8, 'grant', NULL, NULL),
(64, 2, 7, 'grant', NULL, NULL),
(65, 2, 5, 'grant', NULL, NULL),
(66, 2, 28, 'grant', NULL, NULL),
(67, 2, 30, 'grant', NULL, NULL),
(68, 2, 29, 'grant', NULL, NULL),
(69, 2, 27, 'grant', NULL, NULL),
(70, 2, 36, 'grant', NULL, NULL),
(71, 2, 38, 'grant', NULL, NULL),
(72, 2, 37, 'grant', NULL, NULL),
(73, 2, 35, 'grant', NULL, NULL),
(74, 2, 24, 'grant', NULL, NULL),
(75, 2, 26, 'grant', NULL, NULL),
(76, 2, 25, 'grant', NULL, NULL),
(77, 2, 23, 'grant', NULL, NULL),
(78, 2, 10, 'grant', NULL, NULL),
(79, 2, 12, 'grant', NULL, NULL),
(80, 2, 11, 'grant', NULL, NULL),
(81, 2, 9, 'grant', NULL, NULL),
(82, 2, 44, 'grant', NULL, NULL),
(83, 2, 46, 'grant', NULL, NULL),
(84, 2, 45, 'grant', NULL, NULL),
(85, 2, 43, 'grant', NULL, NULL),
(86, 2, 17, 'grant', NULL, NULL),
(87, 2, 14, 'grant', NULL, NULL),
(88, 2, 16, 'grant', NULL, NULL),
(89, 2, 15, 'grant', NULL, NULL),
(90, 2, 18, 'grant', NULL, NULL),
(91, 2, 13, 'grant', NULL, NULL),
(92, 2, 54, 'grant', NULL, NULL),
(93, 2, 53, 'grant', NULL, NULL),
(94, 2, 20, 'grant', NULL, NULL),
(95, 2, 22, 'grant', NULL, NULL),
(96, 2, 21, 'grant', NULL, NULL),
(97, 2, 19, 'grant', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_role`
--

CREATE TABLE `user_role` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_role`
--

INSERT INTO `user_role` (`id`, `user_id`, `role_id`, `created_at`, `updated_at`) VALUES
(1, 1, 1, NULL, NULL),
(2, 2, 2, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `auth_tokens`
--
ALTER TABLE `auth_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `auth_tokens_token_unique` (`token`),
  ADD KEY `auth_tokens_user_id_foreign` (`user_id`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cash_registers`
--
ALTER TABLE `cash_registers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cash_registers_user_id_foreign` (`user_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `customers_cpf_unique` (`cpf`);

--
-- Indexes for table `encomendas`
--
ALTER TABLE `encomendas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `encomendas_code_unique` (`code`),
  ADD KEY `encomendas_user_id_foreign` (`user_id`),
  ADD KEY `encomendas_customer_id_foreign` (`customer_id`);

--
-- Indexes for table `expenses`
--
ALTER TABLE `expenses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `expenses_user_id_foreign` (`user_id`),
  ADD KEY `expenses_cash_register_id_foreign` (`cash_register_id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `menus`
--
ALTER TABLE `menus`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `menus_product_id_day_of_week_unique` (`product_id`,`day_of_week`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `motoboys`
--
ALTER TABLE `motoboys`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `motoboys_cpf_unique` (`cpf`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_unique` (`name`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `products_category_id_foreign` (`category_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_unique` (`name`);

--
-- Indexes for table `role_permission`
--
ALTER TABLE `role_permission`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `role_permission_role_id_permission_id_unique` (`role_id`,`permission_id`),
  ADD KEY `role_permission_permission_id_foreign` (`permission_id`);

--
-- Indexes for table `sales`
--
ALTER TABLE `sales`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sales_code_unique` (`code`),
  ADD KEY `sales_cash_register_id_foreign` (`cash_register_id`),
  ADD KEY `sales_user_id_foreign` (`user_id`),
  ADD KEY `sales_customer_id_foreign` (`customer_id`),
  ADD KEY `sales_table_id_foreign` (`table_id`),
  ADD KEY `sales_motoboy_id_foreign` (`motoboy_id`);

--
-- Indexes for table `sale_items`
--
ALTER TABLE `sale_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sale_items_sale_id_foreign` (`sale_id`),
  ADD KEY `sale_items_product_id_foreign` (`product_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `settings_key_unique` (`key`);

--
-- Indexes for table `tables`
--
ALTER TABLE `tables`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tables_number_unique` (`number`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Indexes for table `user_permission`
--
ALTER TABLE `user_permission`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_permission_user_id_permission_id_unique` (`user_id`,`permission_id`),
  ADD KEY `user_permission_permission_id_foreign` (`permission_id`);

--
-- Indexes for table `user_role`
--
ALTER TABLE `user_role`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_role_user_id_role_id_unique` (`user_id`,`role_id`),
  ADD KEY `user_role_role_id_foreign` (`role_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `auth_tokens`
--
ALTER TABLE `auth_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cash_registers`
--
ALTER TABLE `cash_registers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `encomendas`
--
ALTER TABLE `encomendas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `expenses`
--
ALTER TABLE `expenses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `menus`
--
ALTER TABLE `menus`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=85;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `motoboys`
--
ALTER TABLE `motoboys`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `role_permission`
--
ALTER TABLE `role_permission`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

--
-- AUTO_INCREMENT for table `sales`
--
ALTER TABLE `sales`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `sale_items`
--
ALTER TABLE `sale_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tables`
--
ALTER TABLE `tables`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `user_permission`
--
ALTER TABLE `user_permission`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=98;

--
-- AUTO_INCREMENT for table `user_role`
--
ALTER TABLE `user_role`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `auth_tokens`
--
ALTER TABLE `auth_tokens`
  ADD CONSTRAINT `auth_tokens_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `cash_registers`
--
ALTER TABLE `cash_registers`
  ADD CONSTRAINT `cash_registers_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `encomendas`
--
ALTER TABLE `encomendas`
  ADD CONSTRAINT `encomendas_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `encomendas_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `expenses`
--
ALTER TABLE `expenses`
  ADD CONSTRAINT `expenses_cash_register_id_foreign` FOREIGN KEY (`cash_register_id`) REFERENCES `cash_registers` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `expenses_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `menus`
--
ALTER TABLE `menus`
  ADD CONSTRAINT `menus_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `role_permission`
--
ALTER TABLE `role_permission`
  ADD CONSTRAINT `role_permission_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_permission_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sales`
--
ALTER TABLE `sales`
  ADD CONSTRAINT `sales_cash_register_id_foreign` FOREIGN KEY (`cash_register_id`) REFERENCES `cash_registers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `sales_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `sales_motoboy_id_foreign` FOREIGN KEY (`motoboy_id`) REFERENCES `motoboys` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `sales_table_id_foreign` FOREIGN KEY (`table_id`) REFERENCES `tables` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `sales_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sale_items`
--
ALTER TABLE `sale_items`
  ADD CONSTRAINT `sale_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `sale_items_sale_id_foreign` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_permission`
--
ALTER TABLE `user_permission`
  ADD CONSTRAINT `user_permission_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_permission_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_role`
--
ALTER TABLE `user_role`
  ADD CONSTRAINT `user_role_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_role_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

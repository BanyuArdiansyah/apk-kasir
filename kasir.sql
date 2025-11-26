-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 26, 2025 at 07:12 AM
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
-- Database: `kasir`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `role` enum('admin','kasir') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`, `nama_lengkap`, `role`, `created_at`) VALUES
(1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', 'admin', '2025-11-25 14:21:30'),
(2, 'kasir', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Kasir Toko', 'kasir', '2025-11-25 14:21:30');

-- --------------------------------------------------------

--
-- Table structure for table `detail_penjualan`
--

CREATE TABLE `detail_penjualan` (
  `id` int(11) NOT NULL,
  `penjualan_id` int(11) NOT NULL,
  `produk_id` int(11) NOT NULL,
  `qty` int(11) NOT NULL,
  `harga` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `detail_penjualan`
--

INSERT INTO `detail_penjualan` (`id`, `penjualan_id`, `produk_id`, `qty`, `harga`, `subtotal`) VALUES
(1, 1, 7, 1, 3000.00, 3000.00),
(2, 1, 4, 1, 5000.00, 5000.00),
(3, 2, 4, 1, 5000.00, 5000.00),
(4, 2, 7, 1, 3000.00, 3000.00),
(5, 2, 6, 1, 8000.00, 8000.00),
(6, 3, 4, 1, 5000.00, 5000.00),
(7, 3, 7, 1, 3000.00, 3000.00),
(8, 3, 6, 1, 8000.00, 8000.00),
(9, 4, 4, 1, 5000.00, 5000.00),
(10, 4, 7, 1, 3000.00, 3000.00),
(11, 4, 6, 1, 8000.00, 8000.00),
(12, 5, 4, 1, 5000.00, 5000.00),
(13, 5, 7, 1, 3000.00, 3000.00),
(14, 5, 6, 1, 8000.00, 8000.00),
(15, 6, 4, 1, 5000.00, 5000.00),
(16, 6, 7, 1, 3000.00, 3000.00),
(17, 6, 6, 1, 8000.00, 8000.00),
(18, 7, 4, 1, 5000.00, 5000.00),
(19, 7, 7, 1, 3000.00, 3000.00),
(20, 7, 6, 1, 8000.00, 8000.00),
(21, 8, 4, 1, 5000.00, 5000.00),
(22, 8, 7, 1, 3000.00, 3000.00),
(23, 9, 7, 1, 3000.00, 3000.00),
(24, 9, 6, 1, 8000.00, 8000.00),
(25, 10, 7, 1, 3000.00, 3000.00),
(26, 10, 4, 1, 5000.00, 5000.00),
(27, 11, 4, 1, 5000.00, 5000.00),
(28, 11, 7, 1, 3000.00, 3000.00),
(29, 12, 7, 1, 3000.00, 3000.00),
(30, 13, 7, 3, 3000.00, 9000.00),
(31, 13, 6, 3, 8000.00, 24000.00),
(32, 13, 4, 3, 5000.00, 15000.00),
(33, 14, 5, 3, 7000.00, 21000.00),
(34, 14, 4, 4, 5000.00, 20000.00),
(35, 14, 7, 2, 3000.00, 6000.00);

-- --------------------------------------------------------

--
-- Table structure for table `pelanggan`
--

CREATE TABLE `pelanggan` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `telepon` varchar(15) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pelanggan`
--

INSERT INTO `pelanggan` (`id`, `nama`, `telepon`, `created_at`) VALUES
(1, 'Banyu', NULL, '2025-11-25 15:13:48');

-- --------------------------------------------------------

--
-- Table structure for table `penjualan`
--

CREATE TABLE `penjualan` (
  `id` int(11) NOT NULL,
  `no_transaksi` varchar(20) NOT NULL,
  `pelanggan_id` int(11) DEFAULT NULL,
  `admin_id` int(11) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `bayar` decimal(10,2) NOT NULL,
  `kembalian` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `penjualan`
--

INSERT INTO `penjualan` (`id`, `no_transaksi`, `pelanggan_id`, `admin_id`, `total`, `bayar`, `kembalian`, `created_at`) VALUES
(1, 'TRX-20251125-8487', 1, 2, 3000.01, 10001.00, 7001.00, '2025-11-25 15:13:48'),
(2, 'TRX-20251125-1769', NULL, 2, 0.00, 0.00, 0.00, '2025-11-25 15:21:39'),
(3, 'TRX-20251125-5951', NULL, 2, 0.00, 0.00, 0.00, '2025-11-25 15:21:43'),
(4, 'TRX-20251125-5859', NULL, 2, 0.00, 0.00, 0.00, '2025-11-25 15:25:43'),
(5, 'TRX-20251125-4419', NULL, 2, 0.00, 0.00, 0.00, '2025-11-25 15:25:46'),
(6, 'TRX-20251125-9356', NULL, 2, 0.00, 0.00, 0.00, '2025-11-25 15:25:48'),
(7, 'TRX-20251125163052-5', NULL, 2, 16000.00, 30000.00, 14000.00, '2025-11-25 15:30:52'),
(8, 'TRX-20251125163139-8', NULL, 2, 8000.00, 12222.00, 4222.00, '2025-11-25 15:31:39'),
(9, 'TRX-20251125163725-5', NULL, 2, 11000.00, 12000.00, 1000.00, '2025-11-25 15:37:25'),
(10, 'TRX-20251125163922-5', NULL, 2, 8000.00, 12000.00, 4000.00, '2025-11-25 15:39:22'),
(11, 'TRX-20251125163937-4', NULL, 2, 8000.00, 10000.00, 2000.00, '2025-11-25 15:39:37'),
(12, 'TRX-20251125164608-2', NULL, 2, 3000.00, 5000.00, 2000.00, '2025-11-25 15:46:08'),
(13, 'TRX-20251126051941-7', NULL, 2, 48000.00, 50000.00, 2000.00, '2025-11-26 04:19:41'),
(14, 'TRX-20251126065852-8', NULL, 2, 47000.00, 50000.00, 3000.00, '2025-11-26 05:58:52');

-- --------------------------------------------------------

--
-- Table structure for table `produk`
--

CREATE TABLE `produk` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `kategori` enum('mie','minuman','snack') NOT NULL,
  `harga` decimal(10,2) NOT NULL,
  `stok` int(11) NOT NULL,
  `gambar` varchar(255) DEFAULT NULL,
  `deskripsi` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `produk`
--

INSERT INTO `produk` (`id`, `nama`, `kategori`, `harga`, `stok`, `gambar`, `deskripsi`, `is_active`, `created_at`) VALUES
(1, 'Mie Gacoan Original', 'mie', 15000.00, 50, NULL, 'Mie dengan bumbu original khas Gacoan', 1, '2025-11-25 14:21:30'),
(2, 'Mie Gacoan Pedas', 'mie', 17000.00, 45, NULL, 'Mie dengan level pedas yang bisa disesuaikan', 1, '2025-11-25 14:21:30'),
(3, 'Mie Gacoan Extra Pedas', 'mie', 19000.00, 40, NULL, 'Mie dengan level pedas maksimal', 1, '2025-11-25 14:21:30'),
(4, 'Es Teh Manis', 'minuman', 5000.00, 83, NULL, 'Es teh manis segar', 1, '2025-11-25 14:21:30'),
(5, 'Es Jeruk', 'minuman', 7000.00, 77, NULL, 'Es jeruk peras segar', 1, '2025-11-25 14:21:30'),
(6, 'Kopi Hitam', 'minuman', 8000.00, 50, NULL, 'Kopi hitam original', 1, '2025-11-25 14:21:30'),
(7, 'Kerupuk', 'snack', 3000.00, 13, NULL, 'Kerupuk sebagai pelengkap', 1, '2025-11-25 14:21:30');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `detail_penjualan`
--
ALTER TABLE `detail_penjualan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `penjualan_id` (`penjualan_id`),
  ADD KEY `produk_id` (`produk_id`);

--
-- Indexes for table `pelanggan`
--
ALTER TABLE `pelanggan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `penjualan`
--
ALTER TABLE `penjualan`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `no_transaksi` (`no_transaksi`),
  ADD KEY `pelanggan_id` (`pelanggan_id`),
  ADD KEY `admin_id` (`admin_id`);

--
-- Indexes for table `produk`
--
ALTER TABLE `produk`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `detail_penjualan`
--
ALTER TABLE `detail_penjualan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `pelanggan`
--
ALTER TABLE `pelanggan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `penjualan`
--
ALTER TABLE `penjualan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `produk`
--
ALTER TABLE `produk`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `detail_penjualan`
--
ALTER TABLE `detail_penjualan`
  ADD CONSTRAINT `detail_penjualan_ibfk_1` FOREIGN KEY (`penjualan_id`) REFERENCES `penjualan` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `detail_penjualan_ibfk_2` FOREIGN KEY (`produk_id`) REFERENCES `produk` (`id`);

--
-- Constraints for table `penjualan`
--
ALTER TABLE `penjualan`
  ADD CONSTRAINT `penjualan_ibfk_1` FOREIGN KEY (`pelanggan_id`) REFERENCES `pelanggan` (`id`),
  ADD CONSTRAINT `penjualan_ibfk_2` FOREIGN KEY (`admin_id`) REFERENCES `admin` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Dec 23, 2025 at 04:50 AM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bubur_ayam_apps`
--

-- --------------------------------------------------------

--
-- Table structure for table `detail_transaksi`
--

CREATE TABLE `detail_transaksi` (
  `id_detail` int NOT NULL,
  `id_transaksi` int DEFAULT NULL,
  `id_produk` int DEFAULT NULL,
  `jumlah` int DEFAULT NULL,
  `subtotal` int DEFAULT NULL,
  `tanggal` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `detail_transaksi`
--

INSERT INTO `detail_transaksi` (`id_detail`, `id_transaksi`, `id_produk`, `jumlah`, `subtotal`, `tanggal`) VALUES
(1, 1, 1, 2, 20000, '2025-11-01'),
(2, 1, 3, 1, 9000, '2025-11-02'),
(3, 2, 2, 3, 21000, '2025-11-03'),
(4, 3, 1, 3, 30000, '2025-11-04'),
(5, 4, 2, 2, 14000, '2025-11-05'),
(6, 7, 2, 1, 7000, '2025-12-16'),
(7, 8, 3, 1, 9000, '2025-12-16'),
(8, 9, 2, 1, 7000, '2025-12-22'),
(9, 9, 1, 1, 10000, '2025-12-22'),
(10, 9, 3, 1, 9000, '2025-12-22'),
(11, 10, 6, 1, 10000, '2025-12-23');

-- --------------------------------------------------------

--
-- Table structure for table `pendapatan_harian`
--

CREATE TABLE `pendapatan_harian` (
  `tanggal` date NOT NULL,
  `jam` time DEFAULT NULL,
  `total_transaksi` int DEFAULT NULL,
  `total_pendapatan` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `pendapatan_harian`
--

INSERT INTO `pendapatan_harian` (`tanggal`, `jam`, `total_transaksi`, `total_pendapatan`) VALUES
('2025-11-01', '07:00:00', 30, 420000),
('2025-11-02', '09:00:00', 20, 700000),
('2025-11-03', '07:30:00', 35, 490000),
('2025-11-04', '10:00:00', 25, 850000),
('2025-11-05', '08:00:00', 40, 1400000);

-- --------------------------------------------------------

--
-- Table structure for table `produk`
--

CREATE TABLE `produk` (
  `id_produk` int NOT NULL,
  `nama_produk` varchar(50) DEFAULT NULL,
  `harga` int DEFAULT NULL,
  `status_aktif` tinyint(1) DEFAULT '1',
  `deskripsi` varchar(100) DEFAULT NULL,
  `gambar` varchar(255) DEFAULT 'bubur-ayam1.jpg'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `produk`
--

INSERT INTO `produk` (`id_produk`, `nama_produk`, `harga`, `status_aktif`, `deskripsi`, `gambar`) VALUES
(6, 'Bubur Biasa', 10000, 1, NULL, 'img_69496ebb31808.jpg'),
(7, 'Bubur Ayam Spesial', 15000, 1, NULL, 'img_694971e6f20dd.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `transaksi`
--

CREATE TABLE `transaksi` (
  `id_transaksi` int NOT NULL,
  `id_users` int DEFAULT NULL,
  `order_id` varchar(50) NOT NULL,
  `id_user` int DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `jenis_transaksi` enum('offline','online') NOT NULL,
  `total_pendapatan` int DEFAULT NULL,
  `ongkir` int DEFAULT '0',
  `nama_penerima` varchar(100) DEFAULT NULL,
  `alamat_pengiriman` text,
  `no_hp_penerima` varchar(20) DEFAULT NULL,
  `snap_token` varchar(255) DEFAULT NULL,
  `metode_pembayaran` enum('cash','qris','transfer') NOT NULL DEFAULT 'cash',
  `catatan` varchar(100) DEFAULT NULL,
  `status` enum('Pending','Lunas','Batal') NOT NULL DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `transaksi`
--

INSERT INTO `transaksi` (`id_transaksi`, `id_users`, `order_id`, `id_user`, `tanggal`, `jenis_transaksi`, `total_pendapatan`, `ongkir`, `nama_penerima`, `alamat_pengiriman`, `no_hp_penerima`, `snap_token`, `metode_pembayaran`, `catatan`, `status`) VALUES
(1, NULL, '1', NULL, '2025-11-01', 'offline', 28000, 0, NULL, NULL, NULL, NULL, 'cash', 'pagi', 'Pending'),
(2, NULL, '2', NULL, '2025-11-02', 'online', 21000, 0, NULL, NULL, NULL, NULL, 'qris', 'pesan via WA', 'Pending'),
(3, NULL, '', NULL, '2025-11-03', 'offline', 36000, 0, NULL, NULL, NULL, NULL, 'cash', 'ramai jam 7', 'Pending'),
(4, NULL, '', NULL, '2025-11-04', 'online', 18000, 0, NULL, NULL, NULL, NULL, 'qris', 'pesan pelanggan tetap', 'Pending'),
(5, NULL, '', NULL, '2025-11-05', 'offline', 27000, 0, NULL, NULL, NULL, NULL, 'cash', 'pagi hari', 'Pending'),
(6, NULL, 'TEST-123', 2, '2025-12-16', 'online', 10000, 0, NULL, NULL, NULL, NULL, 'qris', NULL, 'Lunas'),
(7, NULL, 'ORD-1765826885-2', 2, '2025-12-16', 'online', 7000, 0, NULL, NULL, NULL, '16fac7aa-7719-4d54-985d-4cb9ddfd1e96', 'qris', 'Pemesanan Web', 'Pending'),
(8, NULL, 'ORD-1765871402-1', 1, '2025-12-16', 'online', 9000, 0, NULL, NULL, NULL, '119203a8-9ec4-4c34-8056-8333ad0764d3', 'qris', 'Pemesanan Web', 'Lunas'),
(9, NULL, 'ORD-1766405987-2', 2, '2025-12-22', 'online', 26000, 0, NULL, NULL, NULL, 'e529ae23-b77d-4fbc-b006-a28762a6e97b', 'qris', 'Pemesanan Web', 'Lunas'),
(10, NULL, 'OFF-1766458916', NULL, '2025-12-23', 'offline', 10000, 0, 'Warisi/Meja 1', NULL, NULL, NULL, 'cash', 'Pembelian di Kasir', 'Lunas');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id_users` int NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `no_hp` varchar(20) DEFAULT NULL,
  `alamat` text,
  `role` enum('admin','user') DEFAULT 'user',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id_users`, `nama_lengkap`, `username`, `password`, `email`, `no_hp`, `alamat`, `role`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin', '$2y$10$iu.UiqzI1pFtsRXkK.Xhu.1WsU2Cm6FXiGYTdigXJZQOQSkkBc/kC', NULL, NULL, NULL, 'admin', '2025-12-14 17:55:06', '2025-12-14 18:37:41'),
(2, 'Jihar', 'jhr-07', '$2y$10$AvTIwwI1oCOxMwSiZU8Y7uQrga1.NBvwCJ7en8n9rg8ovwkxZxx/6', 'naiuljihar@gmail.com', '085162907128', 'telang indah gang 6', 'user', '2025-12-15 14:18:32', '2025-12-22 12:18:26');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `detail_transaksi`
--
ALTER TABLE `detail_transaksi`
  ADD PRIMARY KEY (`id_detail`),
  ADD KEY `id_transaksi` (`id_transaksi`),
  ADD KEY `id_produk` (`id_produk`);

--
-- Indexes for table `pendapatan_harian`
--
ALTER TABLE `pendapatan_harian`
  ADD PRIMARY KEY (`tanggal`);

--
-- Indexes for table `produk`
--
ALTER TABLE `produk`
  ADD PRIMARY KEY (`id_produk`);

--
-- Indexes for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD PRIMARY KEY (`id_transaksi`),
  ADD KEY `fk_transaksi_users` (`id_user`),
  ADD KEY `idx_user` (`id_users`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_users`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `detail_transaksi`
--
ALTER TABLE `detail_transaksi`
  MODIFY `id_detail` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `produk`
--
ALTER TABLE `produk`
  MODIFY `id_produk` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `transaksi`
--
ALTER TABLE `transaksi`
  MODIFY `id_transaksi` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id_users` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `detail_transaksi`
--
ALTER TABLE `detail_transaksi`
  ADD CONSTRAINT `detail_transaksi_ibfk_1` FOREIGN KEY (`id_transaksi`) REFERENCES `transaksi` (`id_transaksi`),
  ADD CONSTRAINT `detail_transaksi_ibfk_2` FOREIGN KEY (`id_produk`) REFERENCES `produk` (`id_produk`);

--
-- Constraints for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD CONSTRAINT `fk_transaksi_users` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_users`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

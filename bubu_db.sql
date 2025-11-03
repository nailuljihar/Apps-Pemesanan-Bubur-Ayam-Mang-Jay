-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Nov 03, 2025 at 12:34 PM
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
-- Database: `bubu_db`
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
(5, 4, 2, 2, 14000, '2025-11-05');

-- --------------------------------------------------------

--
-- Table structure for table `pelanggan`
--

CREATE TABLE `pelanggan` (
  `id_pelanggan` int NOT NULL,
  `nama_pelanggan` varchar(50) DEFAULT NULL,
  `no_wa` varchar(15) DEFAULT NULL,
  `alamat` varchar(100) DEFAULT NULL,
  `tanggal_daftar` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `pelanggan`
--

INSERT INTO `pelanggan` (`id_pelanggan`, `nama_pelanggan`, `no_wa`, `alamat`, `tanggal_daftar`) VALUES
(1, 'Dyah', '081234567890', 'Jl. Merpati No.10', NULL),
(2, 'Kinan', '081298765432', 'Jl. Kenanga No.8', NULL),
(3, 'Fajar', '081355555555', 'Jl. Mawar No.12', NULL),
(4, 'Dwi', '081222333444', 'Jl. Anggrek No.3', NULL),
(5, 'Anti', '081777888999', 'Jl. Melati No.5', NULL);

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
  `status_aktif` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `produk`
--

INSERT INTO `produk` (`id_produk`, `nama_produk`, `harga`, `status_aktif`) VALUES
(1, 'Bubur Jumbo', 10000, 1),
(2, 'Bubur Putihan', 7000, 1),
(3, 'Bubur Biasa', 9000, 1);

-- --------------------------------------------------------

--
-- Table structure for table `transaksi`
--

CREATE TABLE `transaksi` (
  `id_transaksi` int NOT NULL,
  `tanggal` date DEFAULT NULL,
  `jenis_transaksi` enum('offline','online') DEFAULT NULL,
  `total_pendapatan` int DEFAULT NULL,
  `metode_pembayaran` enum('cash','qris') DEFAULT NULL,
  `catatan` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `transaksi`
--

INSERT INTO `transaksi` (`id_transaksi`, `tanggal`, `jenis_transaksi`, `total_pendapatan`, `metode_pembayaran`, `catatan`) VALUES
(1, '2025-11-01', 'offline', 28000, 'cash', 'pagi'),
(2, '2025-11-02', 'online', 21000, 'qris', 'pesan via WA'),
(3, '2025-11-03', 'offline', 36000, 'cash', 'ramai jam 7'),
(4, '2025-11-04', 'online', 18000, 'qris', 'pesan pelanggan tetap'),
(5, '2025-11-05', 'offline', 27000, 'cash', 'pagi hari');

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
-- Indexes for table `pelanggan`
--
ALTER TABLE `pelanggan`
  ADD PRIMARY KEY (`id_pelanggan`);

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
  ADD PRIMARY KEY (`id_transaksi`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `detail_transaksi`
--
ALTER TABLE `detail_transaksi`
  MODIFY `id_detail` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `pelanggan`
--
ALTER TABLE `pelanggan`
  MODIFY `id_pelanggan` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `produk`
--
ALTER TABLE `produk`
  MODIFY `id_produk` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `transaksi`
--
ALTER TABLE `transaksi`
  MODIFY `id_transaksi` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `detail_transaksi`
--
ALTER TABLE `detail_transaksi`
  ADD CONSTRAINT `detail_transaksi_ibfk_1` FOREIGN KEY (`id_transaksi`) REFERENCES `transaksi` (`id_transaksi`),
  ADD CONSTRAINT `detail_transaksi_ibfk_2` FOREIGN KEY (`id_produk`) REFERENCES `produk` (`id_produk`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

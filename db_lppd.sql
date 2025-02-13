-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Feb 11, 2025 at 02:46 PM
-- Server version: 8.0.30
-- PHP Version: 8.3.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_lppd`
--

-- --------------------------------------------------------

--
-- Table structure for table `indikator`
--

CREATE TABLE `indikator` (
  `id_indikator` int NOT NULL,
  `id_kategori` int NOT NULL,
  `nama_indikator` varchar(255) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `indikator`
--

INSERT INTO `indikator` (`id_indikator`, `id_kategori`, `nama_indikator`) VALUES
(9, 8, 'RSUD TERBANYAK'),
(11, 7, 'Luas Laut Di Indonesia'),
(13, 6, 'Jumlah Ikan Di PErairan Indonesia'),
(14, 12, 'Sekolah Di Indonesia'),
(15, 12, 'Apkah Program makan bergizi berjalan?'),
(16, 8, 'Auto Imun Itu penyakit'),
(17, 14, 'blalala');

-- --------------------------------------------------------

--
-- Table structure for table `kategori_ikk`
--

CREATE TABLE `kategori_ikk` (
  `id_kategori` int NOT NULL,
  `nama_kategori_ikk` varchar(255) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kategori_ikk`
--

INSERT INTO `kategori_ikk` (`id_kategori`, `nama_kategori_ikk`) VALUES
(6, 'Dinas Perikanan'),
(7, 'Dinas Kelautan'),
(8, 'Dinas Kesehatan'),
(12, 'Dinas Pendidikan'),
(13, 'Dinas Perminyakan'),
(14, 'Dinas Kominfo');

-- --------------------------------------------------------

--
-- Table structure for table `pelaporan_opd`
--

CREATE TABLE `pelaporan_opd` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `no_ikk` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `tgl_pengiriman` date NOT NULL,
  `kategori_ikk` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `ikk_output` text COLLATE utf8mb4_general_ci NOT NULL,
  `ikk_outcome` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `indikator` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `penyebut` varchar(500) COLLATE utf8mb4_general_ci NOT NULL,
  `pembilang` varchar(500) COLLATE utf8mb4_general_ci NOT NULL,
  `angka_pembilang` int NOT NULL,
  `angka_penyebut` int NOT NULL,
  `capaian` decimal(10,0) NOT NULL,
  `kategori_kendala_masalah` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `penjelasan_kendala_masalah` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `unggah_dokumen` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `status_laporan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `tanggal_persetujuan` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pelaporan_opd`
--

INSERT INTO `pelaporan_opd` (`id`, `user_id`, `no_ikk`, `tgl_pengiriman`, `kategori_ikk`, `ikk_output`, `ikk_outcome`, `indikator`, `penyebut`, `pembilang`, `angka_pembilang`, `angka_penyebut`, `capaian`, `kategori_kendala_masalah`, `penjelasan_kendala_masalah`, `unggah_dokumen`, `status_laporan`, `tanggal_persetujuan`) VALUES
(72, 0, '1', '2025-02-11', 'Dinas Perikanan', 'Ikan Laut Indonesia', 'Ikan Laut Indonesia', 'Jumlah Ikan Di PErairan Indonesia', '', '', 3, 4, 75, 'Anggaran', 'a', 'uploads/asi-05-00120 (1)-1-1.pdf', 'Setuju', '2025-02-11 20:51:42'),
(74, 17, '4', '2025-02-11', 'Dinas Pendidikan', 'Iya', 'Iya', 'Apkah Program makan bergizi berjalan?', '', '', 7, 5, 140, 'Sumber Daya Manusia', 'uuuuuu', 'uploads/275-File Utama Naskah-1712-2-10-20230201-1.pdf', 'Setuju', '2025-02-11 20:58:24'),
(75, 17, '7', '2025-02-11', 'Dinas Pendidikan', 'Tidak', 'Tidak', 'Sekolah Di Indonesia', '', '', 5, 6, 83, 'Sumber Daya Manusia', 'AS', 'uploads/275-File Utama Naskah-1712-2-10-20230201-1.pdf', 'Setuju', '2025-02-11 21:00:57'),
(76, 18, '12', '2025-02-11', 'Dinas Kesehatan', 'ASS', 'OOO', 'RSUD TERBANYAK', '', '', 3, 3, 100, 'Sumber Daya Manusia', 'Sumber Daya Manusia', 'uploads/PPT IT ASSET MANAGEMENT DAN PATCH MANAGEMENT KEL3.pdf', 'Perbaiki', NULL),
(78, 20, '1', '2025-02-11', 'Dinas Kominfo', 'eee', 'eee', 'Jumlah Ikan Di PErairan Indonesia', '', '', 8, 7, 114, 'Sumber Daya Manusia', 'HAHAHAHAH', '67ab4c5fb21fb_asi-05-00120 (1)-1-1.pdf', 'Setuju', '2025-02-11 20:51:55');

-- --------------------------------------------------------

--
-- Table structure for table `pelaporan_pem`
--

CREATE TABLE `pelaporan_pem` (
  `id` int NOT NULL,
  `tgl_pengiriman` date NOT NULL,
  `no_ikk` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `kategori_ikk` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `ikk_output` text COLLATE utf8mb4_general_ci NOT NULL,
  `ikk_outcome` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `indikator` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `penyebut` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `pembilang` varchar(200) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pelaporan_pem`
--

INSERT INTO `pelaporan_pem` (`id`, `tgl_pengiriman`, `no_ikk`, `kategori_ikk`, `ikk_output`, `ikk_outcome`, `indikator`, `penyebut`, `pembilang`) VALUES
(73, '2025-02-11', '2', 'Dinas Kelautan', 'A', 'A', 'Luas Laut Di Indonesia', 'B', 'A'),
(74, '2025-02-11', '4', 'Dinas Pendidikan', 'Iya', 'Iya', 'Apkah Program makan bergizi berjalan?', 'Tidak', 'Iya'),
(75, '2025-02-11', '7', 'Dinas Pendidikan', 'Tidak', 'Tidak', 'Sekolah Di Indonesia', 'Iya', 'Tidak'),
(76, '2025-02-11', '12', 'Dinas Kesehatan', 'ASS', 'OOO', 'RSUD TERBANYAK', '))))', 'OOOO'),
(77, '2025-02-11', '12', 'Dinas Kominfo', '123', 'gg', 'blalala', 'gg', 'kk'),
(78, '2025-02-11', '1', 'Dinas Kominfo', 'eee', 'eee', 'Jumlah Ikan Di PErairan Indonesia', 'eee', 'eee');

-- --------------------------------------------------------

--
-- Table structure for table `status_laporan`
--

CREATE TABLE `status_laporan` (
  `id` int NOT NULL,
  `no_ikk` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `tanggal_pengiriman` date NOT NULL,
  `kategori_ikk` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `ikk_output` text COLLATE utf8mb4_general_ci NOT NULL,
  `ikk_outcome` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `indikator` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `tanggal_persetujuan` date NOT NULL,
  `status_laporan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `unggah_dokumen` longblob NOT NULL,
  `perbaiki` text COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `user_id` int NOT NULL,
  `id_kategori` int NOT NULL,
  `nik` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `role` varchar(3) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `fullname` varchar(100) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `id_kategori`, `nik`, `role`, `password`, `fullname`) VALUES
(15, 6, '130504', 'opd', '$2y$10$Jqny.l.o4JI3MqnIR.uzbet86M4tneuli5Zdrlu0vYe/WULc92Ewe', '123'),
(16, 6, '13050476570987', 'opd', '$2y$10$Mwscfuf9S4gTBgQ4YdhnWe4NEV6B1gl3wWTFneAwM2ulGwhSmiJ9W', 'Raisa'),
(17, 12, '123456', 'opd', '$2y$10$XQDy2dWt7966bib/.Vu3N.Y65UHxUnfN9yvwZcYIngG/QCWaLQymW', 'Tenis'),
(18, 8, '8910', 'opd', '$2y$10$ewz35h/H0NkKCGjNSWQVEeqVjsQyFKa9WxiQtWmiH/Hotpqm3yLMK', 'Dinas Kesehatan'),
(19, 8, '2323', 'opd', '$2y$10$0RB1vOC.bseYe68zkK99N.iLhqq1ahsHf6O.s6rFFF/Di46hAvSuK', 'Sukiyem'),
(20, 14, '1310', 'opd', '$2y$10$wv9zmwHYACgk55vFGby8zOA/Xloj8xv63htOIYNTvzvaoL6AoOZv2', 'ima');

-- --------------------------------------------------------

--
-- Table structure for table `verifikasi_pem`
--

CREATE TABLE `verifikasi_pem` (
  `id` int NOT NULL,
  `no_ikk` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `tanggal_pengiriman` date NOT NULL,
  `ikk_output` text COLLATE utf8mb4_general_ci NOT NULL,
  `ikk_outcome` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `indikator` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `kategori_ikk` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `bukti_dokumen` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `penjelasan_kendala_masalah` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `status_laporan` enum('Menunggu','Setuju','Perbaiki') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Menunggu',
  `tanggal_persetujuan` date DEFAULT NULL,
  `aksi` varchar(30) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `indikator`
--
ALTER TABLE `indikator`
  ADD PRIMARY KEY (`id_indikator`);

--
-- Indexes for table `kategori_ikk`
--
ALTER TABLE `kategori_ikk`
  ADD PRIMARY KEY (`id_kategori`);

--
-- Indexes for table `pelaporan_opd`
--
ALTER TABLE `pelaporan_opd`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_opd_user` (`id`,`user_id`);

--
-- Indexes for table `pelaporan_pem`
--
ALTER TABLE `pelaporan_pem`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `status_laporan`
--
ALTER TABLE `status_laporan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `verifikasi_pem`
--
ALTER TABLE `verifikasi_pem`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `indikator`
--
ALTER TABLE `indikator`
  MODIFY `id_indikator` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `kategori_ikk`
--
ALTER TABLE `kategori_ikk`
  MODIFY `id_kategori` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `pelaporan_opd`
--
ALTER TABLE `pelaporan_opd`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=79;

--
-- AUTO_INCREMENT for table `pelaporan_pem`
--
ALTER TABLE `pelaporan_pem`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=79;

--
-- AUTO_INCREMENT for table `status_laporan`
--
ALTER TABLE `status_laporan`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `verifikasi_pem`
--
ALTER TABLE `verifikasi_pem`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

/*
SQLyog Community v13.3.1 (64 bit)
MySQL - 12.1.2-MariaDB : Database - klink_report
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`klink_report` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */;

USE `klink_report`;

/*Table structure for table `cache` */

DROP TABLE IF EXISTS `cache`;

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` bigint(20) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `cache` */

/*Table structure for table `cache_locks` */

DROP TABLE IF EXISTS `cache_locks`;

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` bigint(20) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `cache_locks` */

/*Table structure for table `clinic_profiles` */

DROP TABLE IF EXISTS `clinic_profiles`;

CREATE TABLE `clinic_profiles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nama_klinik` varchar(255) NOT NULL,
  `nama_pendek` varchar(255) DEFAULT NULL,
  `tagline` varchar(255) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `kota` varchar(255) DEFAULT NULL,
  `provinsi` varchar(255) DEFAULT NULL,
  `kode_pos` varchar(20) DEFAULT NULL,
  `telepon` varchar(50) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `penanggung_jawab` varchar(255) DEFAULT NULL,
  `jam_pelayanan` varchar(255) DEFAULT NULL,
  `deskripsi_singkat` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `clinic_profiles` */

insert  into `clinic_profiles`(`id`,`nama_klinik`,`nama_pendek`,`tagline`,`alamat`,`kota`,`provinsi`,`kode_pos`,`telepon`,`email`,`website`,`penanggung_jawab`,`jam_pelayanan`,`deskripsi_singkat`,`created_at`,`updated_at`) values 
(1,'Klinik Santa Lusia SIborongborong','Klinik ST. Lusia Siborongborong','Pelayanan yang harmonis','Siborongborong','Tapanuli Utara','Sumatera Utara',NULL,NULL,'klinikssb@gmail.com','hhttps','dr. Vera','Senin - Sabtu','Pelayanan yang holistik dengan semangat santa Lucia','2026-04-20 04:02:50','2026-04-20 04:08:04');

/*Table structure for table `failed_jobs` */

DROP TABLE IF EXISTS `failed_jobs`;

CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `failed_jobs` */

/*Table structure for table `job_batches` */

DROP TABLE IF EXISTS `job_batches`;

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
  `finished_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `job_batches` */

/*Table structure for table `jobs` */

DROP TABLE IF EXISTS `jobs`;

CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `jobs` */

/*Table structure for table `master_kategori_pengeluaran` */

DROP TABLE IF EXISTS `master_kategori_pengeluaran`;

CREATE TABLE `master_kategori_pengeluaran` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `kode_kategori` varchar(50) NOT NULL,
  `nama_kategori` varchar(255) NOT NULL,
  `urutan_laporan` int(10) unsigned NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `master_kategori_pengeluaran_kode_kategori_unique` (`kode_kategori`)
) ENGINE=InnoDB AUTO_INCREMENT=47 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `master_kategori_pengeluaran` */

insert  into `master_kategori_pengeluaran`(`id`,`kode_kategori`,`nama_kategori`,`urutan_laporan`,`is_active`,`created_at`,`updated_at`) values 
(8,'E11','Obat-obatan/pengadaan obat',1,1,NULL,NULL),
(9,'E12','Susu/yakult/bear brand',2,1,NULL,NULL),
(10,'E13','Pengadaan barang jualan lain-lain mis: Popok,bedak,minyak angin dll gurita',3,1,NULL,NULL),
(11,'E14','Ongkos kirim barang/Obat',4,1,NULL,NULL),
(12,'E15','Pustaka : buku-buku yang dibeli untuk kebutuhan Klinik, koran',5,1,NULL,NULL),
(13,'E16','Semua biaya yang berkaitan dengan kerohanian',6,1,NULL,NULL),
(14,'E21','Gaji suster/karyawan/honor dokter/honor apoteker/lembur/ uang terimakasih kepada karyawan yang mengundurkan diri atau dikeluarkan.',7,1,NULL,NULL),
(15,'E22','DHT Suster',8,1,NULL,NULL),
(16,'E23','Iuran IBI/PPNI,Perdhaki wilayah dll',9,1,NULL,NULL),
(17,'E26','Biaya penataran karyawan/pelatihan karyawan',10,1,NULL,NULL),
(18,'E27','Pajak bumi dan bangunan: SSP/RD',11,1,NULL,NULL),
(19,'E28','Izin HO(gangguan lingkungan)',12,1,NULL,NULL),
(20,'E29','Biaya mengurus izin operasional klinik, biaya mengurus SIK Perawat/bidan, Mengurus surat izin dokter praktek',13,1,NULL,NULL),
(21,'E31a','Biaya Konsumsi utk Pegawai/Karyawan/dokter',14,1,NULL,NULL),
(22,'E31b','Biaya Konsumsi Pasien(beras giling,Quaker utk pasien,buah) dll',15,1,NULL,NULL),
(23,'E32','Biaya rapat dan pertemuan; mis diutus untuk mengikuti rapat PERDHAKI',16,1,NULL,NULL),
(24,'E33','Biaya perjalanan/bensin utk sepeda motor',17,1,NULL,NULL),
(25,'E34','Biaya Pelatihan dan pengembangan',18,1,NULL,NULL),
(26,'E35','Biaya Administrasi Umum : calkulator + baterainya, kertas-kertas, pena dan kebutuhan kantor lainya, tinta print,flashdis, materai, pasphoto,plastik assoy, klip',19,1,NULL,NULL),
(27,'E36','Utilitas(air,listrik,telepon,hp + pulsanya, modem + pulsanya); bola lampu,fitting lampu,upah tukang perbaiki lampu/air dll. Elpiji',20,1,NULL,NULL),
(28,'E37','Biaya publikasi iklan/pembuatan atau mengecat pamplet',21,1,NULL,NULL),
(29,'E38','BBM untuk genset',22,1,NULL,NULL),
(30,'E39','Pajak Kendaraan, STNK, Ganti Plat sepeda motor',23,1,NULL,NULL),
(31,'E41','Biaya perawatan kendaraan/doorsmeer/service sepedamotor/ganti ban',24,1,NULL,NULL),
(32,'E42','Biaya Pengadaan Perlengkapan: Biaya perlengkapan seperti batere jam, batere alat kesehatan dll yang sifatnya habis (sanco..............) botol, piring, sendok, mangkok',25,1,NULL,NULL),
(33,'E46','Air minum(Galon)',26,1,NULL,NULL),
(34,'E51','Alat-alat kebersihan(sapu,ember,detergen,wipol,tissu,pipet dll)',27,1,NULL,NULL),
(35,'E52a','Biaya Pemeliharaan Inventaris Alkes',28,1,NULL,NULL),
(36,'E52b','Biaya Pemeliharaan Inventaris RT',29,1,NULL,NULL),
(37,'E53','Pemeliharaan gedung dan pekarangan(upah tukang kebun,bibit bunga/pohon,pupuk dll), retribusi sampah; kran-',30,1,NULL,NULL),
(38,'E82','Sewa tanah dan gedung',31,1,NULL,NULL),
(39,'E83','Biaya pengobatan Karyawan',32,1,NULL,NULL),
(40,'E85','Derma dan solidaritas : Pembangunan gereja, korban kebakaran dll',33,1,NULL,NULL),
(41,'E86','Sosial utk Pasien tidak mampu',34,1,NULL,NULL),
(42,'E87','Biaya operasional dan lain-lain(Hadiah, Ultah, Pesta St.Lusia, parcel utk dokter dan komunitas)',35,1,NULL,NULL),
(43,'A6a','Pegadaan Invetaris Alkes :Lampu sorot, Tensi meter, stetoscope,Vacum set, Set partus dll',36,1,NULL,NULL),
(44,'A6b','Pengadaan Inventaris RT: TV,Kulkas,Lemari,Meja,Kursi,T.tidur,Rak,Mesin jahit,genset,alat kebun, alat-alat tenun',37,1,NULL,NULL),
(45,'A7','Setor ke Kas Karya Luhur',38,1,NULL,NULL);

/*Table structure for table `master_layanan` */

DROP TABLE IF EXISTS `master_layanan`;

CREATE TABLE `master_layanan` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `kode_layanan` varchar(50) NOT NULL,
  `nama_layanan` varchar(255) NOT NULL,
  `simrs_kd_poli` varchar(50) DEFAULT NULL,
  `simrs_nm_poli` varchar(255) DEFAULT NULL,
  `urutan_laporan` int(10) unsigned NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `master_layanan_kode_layanan_unique` (`kode_layanan`),
  KEY `master_layanan_simrs_kd_poli_index` (`simrs_kd_poli`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `master_layanan` */

insert  into `master_layanan`(`id`,`kode_layanan`,`nama_layanan`,`simrs_kd_poli`,`simrs_nm_poli`,`urutan_laporan`,`is_active`,`created_at`,`updated_at`) values 
(1,'K1','Klinik Umum','plum','Klinik Umum',1,1,'2026-04-19 02:56:39','2026-04-19 02:56:39'),
(2,'K2','KB',NULL,NULL,2,1,'2026-04-19 02:56:39','2026-04-19 02:56:39'),
(3,'K3','ANC',NULL,NULL,3,1,'2026-04-19 02:56:39','2026-04-19 02:56:39'),
(4,'k4','Curetage',NULL,NULL,4,1,'2026-04-19 02:56:39','2026-04-19 02:56:39'),
(5,'K5','Partus','kia','KIA',5,1,'2026-04-19 02:56:39','2026-04-19 02:56:39'),
(6,'K6','Observasi',NULL,NULL,6,1,'2026-04-19 02:56:39','2026-04-19 02:56:39'),
(7,'K7','Imunisasi',NULL,NULL,7,1,'2026-04-19 02:56:39','2026-04-19 02:56:39'),
(8,'K8','Terapi',NULL,NULL,8,1,'2026-04-19 02:56:39','2026-04-19 02:56:39'),
(9,'K9','Perawatan Luka',NULL,NULL,9,1,'2026-04-19 02:56:39','2026-04-19 02:56:39'),
(10,'K10','Kontrol',NULL,NULL,10,1,'2026-04-19 02:56:39','2026-04-19 02:56:39'),
(11,'K11','Konseling',NULL,NULL,11,1,'2026-04-19 02:56:39','2026-04-19 02:56:39'),
(12,'K12','Gigi',NULL,NULL,12,1,'2026-04-19 02:56:39','2026-04-19 02:56:39'),
(13,'K13','Coba coba','u003111','coba',13,1,'2026-04-20 02:37:00','2026-04-20 02:37:00');

/*Table structure for table `migrations` */

DROP TABLE IF EXISTS `migrations`;

CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `migrations` */

insert  into `migrations`(`id`,`migration`,`batch`) values 
(1,'0001_01_01_000000_create_users_table',1),
(2,'0001_01_01_000001_create_cache_table',1),
(3,'0001_01_01_000002_create_jobs_table',1),
(4,'2026_04_19_000003_create_master_layanan_table',1),
(5,'2026_04_19_000004_create_master_kategori_pengeluaran_table',1),
(6,'2026_04_19_000005_create_rekap_pasien_table',1),
(7,'2026_04_19_000006_create_pengeluaran_table',1),
(8,'2026_04_19_000007_create_transaksi_pasien_table',2),
(9,'2026_04_19_000008_add_dokter_penjamin_to_transaksi_pasien_table',3),
(10,'2026_04_19_000009_add_simrs_mapping_to_master_layanan_table',4),
(11,'2026_04_19_000010_normalize_transaksi_pasien_layanan_to_kode_layanan',5),
(12,'2026_04_19_000011_resync_transaksi_pasien_layanan_from_simrs_kd_poli',6),
(13,'2026_04_20_000012_add_identity_columns_to_users_table',7),
(14,'2026_04_20_000013_create_pegawai_profiles_table',7),
(15,'2026_04_20_000014_add_user_id_to_operational_tables',7),
(16,'2026_04_20_000015_seed_default_admin_user',7),
(17,'2026_04_20_000016_create_clinic_profiles_table',8);

/*Table structure for table `password_reset_tokens` */

DROP TABLE IF EXISTS `password_reset_tokens`;

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `password_reset_tokens` */

/*Table structure for table `pegawai_profiles` */

DROP TABLE IF EXISTS `pegawai_profiles`;

CREATE TABLE `pegawai_profiles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `nip` varchar(255) DEFAULT NULL,
  `jabatan` varchar(255) DEFAULT NULL,
  `unit_kerja` varchar(255) DEFAULT NULL,
  `phone_number` varchar(255) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `pegawai_profiles_user_id_unique` (`user_id`),
  UNIQUE KEY `pegawai_profiles_nip_unique` (`nip`),
  CONSTRAINT `pegawai_profiles_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `pegawai_profiles` */

insert  into `pegawai_profiles`(`id`,`user_id`,`nip`,`jabatan`,`unit_kerja`,`phone_number`,`bio`,`created_at`,`updated_at`) values 
(1,1,NULL,'IT Yayasan','Yayasan / Klinik',NULL,'Akun awal lokal untuk pengelolaan dashboard dan laporan klinik.','2026-04-20 03:12:43','2026-04-20 03:12:43'),
(2,2,'1212010','Bidan','Klinik','0822','Petugas bidan','2026-04-20 03:29:56','2026-04-20 03:29:56');

/*Table structure for table `pengeluaran` */

DROP TABLE IF EXISTS `pengeluaran`;

CREATE TABLE `pengeluaran` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `master_kategori_pengeluaran_id` bigint(20) unsigned DEFAULT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `tanggal` date NOT NULL,
  `bulan` tinyint(3) unsigned NOT NULL,
  `tahun` smallint(5) unsigned NOT NULL,
  `kategori_pengeluaran` varchar(255) DEFAULT NULL,
  `deskripsi` varchar(255) NOT NULL,
  `jumlah_rp` decimal(15,2) NOT NULL DEFAULT 0.00,
  `petugas_admin` varchar(255) DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `simrs_ref` varchar(191) DEFAULT NULL,
  `synced_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pengeluaran_master_kategori_pengeluaran_id_foreign` (`master_kategori_pengeluaran_id`),
  KEY `pengeluaran_tahun_bulan_index` (`tahun`,`bulan`),
  KEY `pengeluaran_simrs_ref_index` (`simrs_ref`),
  KEY `pengeluaran_tanggal_index` (`tanggal`),
  KEY `pengeluaran_bulan_index` (`bulan`),
  KEY `pengeluaran_tahun_index` (`tahun`),
  KEY `pengeluaran_user_id_foreign` (`user_id`),
  CONSTRAINT `pengeluaran_master_kategori_pengeluaran_id_foreign` FOREIGN KEY (`master_kategori_pengeluaran_id`) REFERENCES `master_kategori_pengeluaran` (`id`) ON DELETE SET NULL,
  CONSTRAINT `pengeluaran_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `pengeluaran` */

insert  into `pengeluaran`(`id`,`master_kategori_pengeluaran_id`,`user_id`,`tanggal`,`bulan`,`tahun`,`kategori_pengeluaran`,`deskripsi`,`jumlah_rp`,`petugas_admin`,`keterangan`,`simrs_ref`,`synced_at`,`created_at`,`updated_at`) values 
(5,8,2,'2026-04-20',4,2026,'Obat-obatan/pengadaan obat','Pembelian obat untuk keperluan stok klinik',100000.00,'Mega Puspa Simanjuntak · Bidan','obat sudah habis jadi harus malalukan pemesanan',NULL,NULL,'2026-04-20 03:37:12','2026-04-20 03:37:12');

/*Table structure for table `rekap_pasien` */

DROP TABLE IF EXISTS `rekap_pasien`;

CREATE TABLE `rekap_pasien` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `master_layanan_id` bigint(20) unsigned DEFAULT NULL,
  `tanggal` date NOT NULL,
  `bulan` tinyint(3) unsigned NOT NULL,
  `tahun` smallint(5) unsigned NOT NULL,
  `harian` varchar(50) DEFAULT NULL,
  `layanan_medis` varchar(255) DEFAULT NULL,
  `no_rm` varchar(50) DEFAULT NULL,
  `nama_pasien` varchar(255) DEFAULT NULL,
  `jk` varchar(20) DEFAULT NULL,
  `statis_genap` varchar(100) DEFAULT NULL,
  `status_pasien` varchar(100) DEFAULT NULL,
  `jenis_bayar` varchar(100) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `lab` text DEFAULT NULL,
  `icd` text DEFAULT NULL,
  `diagnosa` text DEFAULT NULL,
  `farmasi` text DEFAULT NULL,
  `uang_daftar` decimal(15,2) NOT NULL DEFAULT 0.00,
  `uang_periksa` decimal(15,2) NOT NULL DEFAULT 0.00,
  `uang_obat` decimal(15,2) NOT NULL DEFAULT 0.00,
  `uang_bersalin` decimal(15,2) NOT NULL DEFAULT 0.00,
  `jasa_dokter` decimal(15,2) NOT NULL DEFAULT 0.00,
  `jml_hari` int(10) unsigned NOT NULL DEFAULT 0,
  `rawat_inap` decimal(15,2) NOT NULL DEFAULT 0.00,
  `jml_visit` int(10) unsigned NOT NULL DEFAULT 0,
  `honor_visit` decimal(15,2) NOT NULL DEFAULT 0.00,
  `oksigen` decimal(15,2) NOT NULL DEFAULT 0.00,
  `perlengkapan_bayi` decimal(15,2) NOT NULL DEFAULT 0.00,
  `jaspel_nakes` decimal(15,2) NOT NULL DEFAULT 0.00,
  `bmhp` decimal(15,2) NOT NULL DEFAULT 0.00,
  `pkl_dll` decimal(15,2) NOT NULL DEFAULT 0.00,
  `lain_lain` decimal(15,2) NOT NULL DEFAULT 0.00,
  `jumlah_rp` decimal(15,2) NOT NULL DEFAULT 0.00,
  `utang_pasien` decimal(15,2) NOT NULL DEFAULT 0.00,
  `bayar_utang_pasien` decimal(15,2) NOT NULL DEFAULT 0.00,
  `derma_solidaritas` decimal(15,2) NOT NULL DEFAULT 0.00,
  `saldo_kredit` decimal(15,2) NOT NULL DEFAULT 0.00,
  `saldo_kredit2` decimal(15,2) NOT NULL DEFAULT 0.00,
  `petugas_admin` varchar(255) DEFAULT NULL,
  `simrs_ref` varchar(191) DEFAULT NULL,
  `synced_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `rekap_pasien_master_layanan_id_foreign` (`master_layanan_id`),
  KEY `rekap_pasien_tahun_bulan_index` (`tahun`,`bulan`),
  KEY `rekap_pasien_simrs_ref_index` (`simrs_ref`),
  KEY `rekap_pasien_tanggal_index` (`tanggal`),
  KEY `rekap_pasien_bulan_index` (`bulan`),
  KEY `rekap_pasien_tahun_index` (`tahun`),
  KEY `rekap_pasien_no_rm_index` (`no_rm`),
  CONSTRAINT `rekap_pasien_master_layanan_id_foreign` FOREIGN KEY (`master_layanan_id`) REFERENCES `master_layanan` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `rekap_pasien` */

/*Table structure for table `sessions` */

DROP TABLE IF EXISTS `sessions`;

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `sessions` */

insert  into `sessions`(`id`,`user_id`,`ip_address`,`user_agent`,`payload`,`last_activity`) values 
('gaVIFvZg1nehUHvVEOElxmtUfUbRcoTFQO0moBap',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 Edg/147.0.0.0','eyJfdG9rZW4iOiJDTTRKRW13V1VXb1BEalVrVldteVRMekVsMHlZZGFyejhJUkR2NTdJIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHA6XC9cLzEyNy4wLjAuMTo4MDAwXC9pbnB1dC10cmFuc2Frc2ktcGFzaWVuIiwicm91dGUiOiJpbnB1dC1kYXRhIn0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfX0=',1776574588);

/*Table structure for table `transaksi_pasien` */

DROP TABLE IF EXISTS `transaksi_pasien`;

CREATE TABLE `transaksi_pasien` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `simrs_no_rawat` varchar(30) NOT NULL,
  `simrs_no_reg` varchar(20) DEFAULT NULL,
  `tanggal` date NOT NULL,
  `bulan` tinyint(3) unsigned NOT NULL,
  `harian` varchar(30) DEFAULT NULL,
  `layanan_medis` varchar(255) DEFAULT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `dokter` varchar(255) DEFAULT NULL,
  `penjamin` varchar(255) DEFAULT NULL,
  `no_rm` varchar(50) DEFAULT NULL,
  `nama_pasien` varchar(255) DEFAULT NULL,
  `jk` varchar(20) DEFAULT NULL,
  `statis` varchar(100) DEFAULT NULL,
  `genap` varchar(100) DEFAULT NULL,
  `status_pasien` varchar(100) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `lab` text DEFAULT NULL,
  `icd` text DEFAULT NULL,
  `diagnosa` text DEFAULT NULL,
  `farmasi` text DEFAULT NULL,
  `uang_daftar` decimal(15,2) NOT NULL DEFAULT 0.00,
  `uang_periksa` decimal(15,2) NOT NULL DEFAULT 0.00,
  `uang_obat` decimal(15,2) NOT NULL DEFAULT 0.00,
  `uang_bersalin` decimal(15,2) NOT NULL DEFAULT 0.00,
  `jasa_dokter` decimal(15,2) NOT NULL DEFAULT 0.00,
  `jml_hari` int(10) unsigned NOT NULL DEFAULT 0,
  `rawat_inap` decimal(15,2) NOT NULL DEFAULT 0.00,
  `jml_visit` int(10) unsigned NOT NULL DEFAULT 0,
  `honor_dr_visit` decimal(15,2) NOT NULL DEFAULT 0.00,
  `oksigen` decimal(15,2) NOT NULL DEFAULT 0.00,
  `perlengk_bayi` decimal(15,2) NOT NULL DEFAULT 0.00,
  `jaspel_nakes` decimal(15,2) NOT NULL DEFAULT 0.00,
  `bmhp` decimal(15,2) NOT NULL DEFAULT 0.00,
  `pkl` decimal(15,2) NOT NULL DEFAULT 0.00,
  `lain_lain` decimal(15,2) NOT NULL DEFAULT 0.00,
  `jumlah_rp` decimal(15,2) NOT NULL DEFAULT 0.00,
  `utang_pasien` decimal(15,2) NOT NULL DEFAULT 0.00,
  `utang` decimal(15,2) NOT NULL DEFAULT 0.00,
  `bayar_utang_pasien` decimal(15,2) NOT NULL DEFAULT 0.00,
  `derma_solidaritas` decimal(15,2) NOT NULL DEFAULT 0.00,
  `saldo_kredit` decimal(15,2) NOT NULL DEFAULT 0.00,
  `saldo` decimal(15,2) NOT NULL DEFAULT 0.00,
  `petugas_admin` varchar(255) DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `transaksi_pasien_simrs_no_rawat_unique` (`simrs_no_rawat`),
  KEY `transaksi_pasien_tanggal_bulan_index` (`tanggal`,`bulan`),
  KEY `transaksi_pasien_tanggal_index` (`tanggal`),
  KEY `transaksi_pasien_bulan_index` (`bulan`),
  KEY `transaksi_pasien_no_rm_index` (`no_rm`),
  KEY `transaksi_pasien_penjamin_index` (`penjamin`),
  KEY `transaksi_pasien_user_id_foreign` (`user_id`),
  CONSTRAINT `transaksi_pasien_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `transaksi_pasien` */

insert  into `transaksi_pasien`(`id`,`simrs_no_rawat`,`simrs_no_reg`,`tanggal`,`bulan`,`harian`,`layanan_medis`,`user_id`,`dokter`,`penjamin`,`no_rm`,`nama_pasien`,`jk`,`statis`,`genap`,`status_pasien`,`alamat`,`lab`,`icd`,`diagnosa`,`farmasi`,`uang_daftar`,`uang_periksa`,`uang_obat`,`uang_bersalin`,`jasa_dokter`,`jml_hari`,`rawat_inap`,`jml_visit`,`honor_dr_visit`,`oksigen`,`perlengk_bayi`,`jaspel_nakes`,`bmhp`,`pkl`,`lain_lain`,`jumlah_rp`,`utang_pasien`,`utang`,`bayar_utang_pasien`,`derma_solidaritas`,`saldo_kredit`,`saldo`,`petugas_admin`,`keterangan`,`created_at`,`updated_at`) values 
(10,'2026/04/19/000002','MG-001','2026-04-19',4,'Minggu','K5',1,'dr. Megawaty Lumbantoruan','UMUM','56022616','VERTIANUS MAGAI','L','Lama',NULL,'Ralan','ALAMAT',NULL,NULL,NULL,NULL,10000.00,0.00,0.00,0.00,0.00,0,0.00,0,0.00,0.00,0.00,0.00,0.00,0.00,0.00,10000.00,0.00,0.00,0.00,0.00,0.00,0.00,'William D.N · IT Yayasan',NULL,'2026-04-20 01:38:57','2026-04-20 04:39:26'),
(12,'2026/04/20/000001','UU-001','2026-04-20',4,'Senin','K5',2,'dr Ernest Chris Winardi Gulo','BPJS','26031200','RYAN JUNIANSYAH','L','Lama',NULL,'Ralan','DESA BOTUNG',NULL,NULL,NULL,NULL,20000.00,0.00,0.00,0.00,0.00,0,0.00,0,0.00,0.00,0.00,0.00,0.00,0.00,0.00,20000.00,0.00,0.00,0.00,0.00,0.00,0.00,'Mega Puspa Simanjuntak · Bidan',NULL,'2026-04-20 03:49:55','2026-04-20 03:49:55');

/*Table structure for table `users` */

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `username` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(50) NOT NULL DEFAULT 'staff',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `last_login_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `users_username_unique` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `users` */

insert  into `users`(`id`,`name`,`username`,`email`,`email_verified_at`,`password`,`role`,`is_active`,`last_login_at`,`remember_token`,`created_at`,`updated_at`) values 
(1,'William D.N','admin','admin@klink.local',NULL,'$2y$12$SEgGtVQxak.o4sdSa4ZkR./twk1mvpAyFpeQJbEcCJJDPEvfmnRxW','admin',1,'2026-04-20 05:48:14','ZLgkUHzldKJ3lynGD638NmbjfsamW17axudztKLyRFCOoD74HiAsvdz3KfiF','2026-04-20 03:12:43','2026-04-20 05:48:14'),
(2,'Mega Puspa Simanjuntak','megapuspa','megapuspa@gmail.com',NULL,'$2y$12$ll5edMy4jkL.FfOA1eZhoeDMt6KGnpO08K1NdbC6R/Eyq5og1HXu.','staff',1,'2026-04-20 03:30:19',NULL,'2026-04-20 03:29:56','2026-04-20 03:30:19');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

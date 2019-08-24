-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 21 Bulan Mei 2019 pada 15.23
-- Versi server: 10.1.36-MariaDB
-- Versi PHP: 7.0.32

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `projek_wk`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `serial` text NOT NULL,
  `rekening` text NOT NULL,
  `nama_bank` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `app_version`
--

CREATE TABLE `app_version` (
  `id` bigint(20) NOT NULL,
  `version_code` int(11) NOT NULL,
  `version_name` varchar(50) NOT NULL,
  `active` tinyint(1) NOT NULL,
  `created_at` bigint(20) NOT NULL,
  `last_update` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `app_version`
--

INSERT INTO `app_version` (`id`, `version_code`, `version_name`, `active`, `created_at`, `last_update`) VALUES
(3, 1, '1', 1, 1548033400415, 1548033400415);

-- --------------------------------------------------------

--
-- Struktur dari tabel `category`
--

CREATE TABLE `category` (
  `id` bigint(20) NOT NULL,
  `name` varchar(50) NOT NULL,
  `icon` varchar(100) NOT NULL,
  `draft` tinyint(1) NOT NULL,
  `brief` varchar(100) NOT NULL,
  `color` varchar(7) NOT NULL,
  `priority` int(11) NOT NULL DEFAULT '0',
  `created_at` bigint(20) NOT NULL,
  `last_update` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `category`
--

INSERT INTO `category` (`id`, `name`, `icon`, `draft`, `brief`, `color`, `priority`, `created_at`, `last_update`) VALUES
(3, 'Accesories', 'Accesories.png', 0, 'Accesories goods for your daily style', '#52c5ff', 1, 1485609656784, 1548038403401),
(4, 'Foods', 'Foods.png', 0, 'UMKM\'s Foods', '#8ddd6f', 6, 1485609722495, 1548033151203),
(5, 'Clothes', '1548033164127.png', 0, 'Bring your style', '#ef159d', 2, 1548033164127, 1548038358766);

-- --------------------------------------------------------

--
-- Struktur dari tabel `config`
--

CREATE TABLE `config` (
  `code` varchar(50) NOT NULL,
  `value` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `config`
--

INSERT INTO `config` (`code`, `value`) VALUES
('CURRENCY', 'IDR'),
('EMAIL_BCC_RECEIVER', '[\"admin1@domain.com\",\"admin2@domain.com\"]'),
('EMAIL_NOTIF_ON_ORDER', 'FALSE'),
('EMAIL_NOTIF_ON_ORDER_PROCESS', 'FALSE'),
('EMAIL_REPLY_TO', 'email.address@domain.com'),
('EMAIL_SENDER', 'your.email@domain.com'),
('EMAIL_SENDER_PASS', 'xxxxxx'),
('FEATURED_NEWS', '5'),
('SHIPPING', '[\"JNE\",\"POS Indonesia\",\"J&T\"]'),
('TAX', '2');

-- --------------------------------------------------------

--
-- Struktur dari tabel `currency`
--

CREATE TABLE `currency` (
  `id` bigint(20) NOT NULL,
  `code` varchar(3) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `currency`
--

INSERT INTO `currency` (`id`, `code`, `name`) VALUES
(1, 'AFA', 'Afghanistan afghani'),
(2, 'ALL', 'Albanian lek'),
(3, 'DZD', 'Algerian dinar'),
(4, 'AOR', 'Angolan kwanza reajustado'),
(5, 'ARS', 'Argentine peso'),
(6, 'AMD', 'Armenian dram'),
(7, 'AWG', 'Aruban guilder'),
(8, 'AUD', 'Australian dollar'),
(9, 'AZN', 'Azerbaijanian new manat'),
(10, 'BSD', 'Bahamian dollar'),
(11, 'BHD', 'Bahraini dinar'),
(12, 'BDT', 'Bangladeshi taka'),
(13, 'BBD', 'Barbados dollar'),
(14, 'BYN', 'Belarusian ruble'),
(15, 'BZD', 'Belize dollar'),
(16, 'BMD', 'Bermudian dollar'),
(17, 'BTN', 'Bhutan ngultrum'),
(18, 'BOB', 'Bolivian boliviano'),
(19, 'BWP', 'Botswana pula'),
(20, 'BRL', 'Brazilian real'),
(21, 'GBP', 'British pound'),
(22, 'BND', 'Brunei dollar'),
(23, 'BGN', 'Bulgarian lev'),
(24, 'BIF', 'Burundi franc'),
(25, 'KHR', 'Cambodian riel'),
(26, 'CAD', 'Canadian dollar'),
(27, 'CVE', 'Cape Verde escudo'),
(28, 'KYD', 'Cayman Islands dollar'),
(29, 'XOF', 'CFA franc BCEAO'),
(30, 'XAF', 'CFA franc BEAC'),
(31, 'XPF', 'CFP franc'),
(32, 'CLP', 'Chilean peso'),
(33, 'CNY', 'Chinese yuan renminbi'),
(34, 'COP', 'Colombian peso'),
(35, 'KMF', 'Comoros franc'),
(36, 'CDF', 'Congolese franc'),
(37, 'CRC', 'Costa Rican colon'),
(38, 'HRK', 'Croatian kuna'),
(39, 'CUP', 'Cuban peso'),
(40, 'CZK', 'Czech koruna'),
(41, 'DKK', 'Danish krone'),
(42, 'DJF', 'Djibouti franc'),
(43, 'DOP', 'Dominican peso'),
(44, 'XCD', 'East Caribbean dollar'),
(45, 'EGP', 'Egyptian pound'),
(46, 'SVC', 'El Salvador colon'),
(47, 'ERN', 'Eritrean nakfa'),
(48, 'EEK', 'Estonian kroon'),
(49, 'ETB', 'Ethiopian birr'),
(50, 'EUR', 'EU euro'),
(51, 'FKP', 'Falkland Islands pound'),
(52, 'FJD', 'Fiji dollar'),
(53, 'GMD', 'Gambian dalasi'),
(54, 'GEL', 'Georgian lari'),
(55, 'GHS', 'Ghanaian new cedi'),
(56, 'GIP', 'Gibraltar pound'),
(57, 'XAU', 'Gold (ounce)'),
(58, 'XFO', 'Gold franc'),
(59, 'GTQ', 'Guatemalan quetzal'),
(60, 'GNF', 'Guinean franc'),
(61, 'GYD', 'Guyana dollar'),
(62, 'HTG', 'Haitian gourde'),
(63, 'HNL', 'Honduran lempira'),
(64, 'HKD', 'Hong Kong SAR dollar'),
(65, 'HUF', 'Hungarian forint'),
(66, 'ISK', 'Icelandic krona'),
(67, 'XDR', 'IMF special drawing right'),
(68, 'INR', 'Indian rupee'),
(69, 'IDR', 'Indonesian rupiah'),
(70, 'IRR', 'Iranian rial'),
(71, 'IQD', 'Iraqi dinar'),
(72, 'ILS', 'Israeli new shekel'),
(73, 'JMD', 'Jamaican dollar'),
(74, 'JPY', 'Japanese yen'),
(75, 'JOD', 'Jordanian dinar'),
(76, 'KZT', 'Kazakh tenge'),
(77, 'KES', 'Kenyan shilling'),
(78, 'KWD', 'Kuwaiti dinar'),
(79, 'KGS', 'Kyrgyz som'),
(80, 'LAK', 'Lao kip'),
(81, 'LVL', 'Latvian lats'),
(82, 'LBP', 'Lebanese pound'),
(83, 'LSL', 'Lesotho loti'),
(84, 'LRD', 'Liberian dollar'),
(85, 'LYD', 'Libyan dinar'),
(86, 'LTL', 'Lithuanian litas'),
(87, 'MOP', 'Macao SAR pataca'),
(88, 'MKD', 'Macedonian denar'),
(89, 'MGA', 'Malagasy ariary'),
(90, 'MWK', 'Malawi kwacha'),
(91, 'MYR', 'Malaysian ringgit'),
(92, 'MVR', 'Maldivian rufiyaa'),
(93, 'MRO', 'Mauritanian ouguiya'),
(94, 'MUR', 'Mauritius rupee'),
(95, 'MXN', 'Mexican peso'),
(96, 'MDL', 'Moldovan leu'),
(97, 'MNT', 'Mongolian tugrik'),
(98, 'MAD', 'Moroccan dirham'),
(99, 'MZN', 'Mozambique new metical'),
(100, 'MMK', 'Myanmar kyat'),
(101, 'NAD', 'Namibian dollar'),
(102, 'NPR', 'Nepalese rupee'),
(103, 'ANG', 'Netherlands Antillian guilder'),
(104, 'NZD', 'New Zealand dollar'),
(105, 'NIO', 'Nicaraguan cordoba oro'),
(106, 'NGN', 'Nigerian naira'),
(107, 'KPW', 'North Korean won'),
(108, 'NOK', 'Norwegian krone'),
(109, 'OMR', 'Omani rial'),
(110, 'PKR', 'Pakistani rupee'),
(111, 'XPD', 'Palladium (ounce)'),
(112, 'PAB', 'Panamanian balboa'),
(113, 'PGK', 'Papua New Guinea kina'),
(114, 'PYG', 'Paraguayan guarani'),
(115, 'PEN', 'Peruvian nuevo sol'),
(116, 'PHP', 'Philippine peso'),
(117, 'XPT', 'Platinum (ounce)'),
(118, 'PLN', 'Polish zloty'),
(119, 'QAR', 'Qatari rial'),
(120, 'RON', 'Romanian new leu'),
(121, 'RUB', 'Russian ruble'),
(122, 'RWF', 'Rwandan franc'),
(123, 'SHP', 'Saint Helena pound'),
(124, 'WST', 'Samoan tala'),
(125, 'STD', 'Sao Tome and Principe dobra'),
(126, 'SAR', 'Saudi riyal'),
(127, 'RSD', 'Serbian dinar'),
(128, 'SCR', 'Seychelles rupee'),
(129, 'SLL', 'Sierra Leone leone'),
(130, 'XAG', 'Silver (ounce)'),
(131, 'SGD', 'Singapore dollar'),
(132, 'SBD', 'Solomon Islands dollar'),
(133, 'SOS', 'Somali shilling'),
(134, 'ZAR', 'South African rand'),
(135, 'KRW', 'South Korean won'),
(136, 'LKR', 'Sri Lanka rupee'),
(137, 'SDG', 'Sudanese pound'),
(138, 'SRD', 'Suriname dollar'),
(139, 'SZL', 'Swaziland lilangeni'),
(140, 'SEK', 'Swedish krona'),
(141, 'CHF', 'Swiss franc'),
(142, 'SYP', 'Syrian pound'),
(143, 'TWD', 'Taiwan New dollar'),
(144, 'TJS', 'Tajik somoni'),
(145, 'TZS', 'Tanzanian shilling'),
(146, 'THB', 'Thai baht'),
(147, 'TOP', 'Tongan paanga'),
(148, 'TTD', 'Trinidad and Tobago dollar'),
(149, 'TND', 'Tunisian dinar'),
(150, 'TRY', 'Turkish lira'),
(151, 'TMT', 'Turkmen new manat'),
(152, 'AED', 'UAE dirham'),
(153, 'UGX', 'Uganda new shilling'),
(154, 'XFU', 'UIC franc'),
(155, 'UAH', 'Ukrainian hryvnia'),
(156, 'UYU', 'Uruguayan peso uruguayo'),
(157, 'USD', 'US dollar'),
(158, 'UZS', 'Uzbekistani sum'),
(159, 'VUV', 'Vanuatu vatu'),
(160, 'VEF', 'Venezuelan bolivar fuerte'),
(161, 'VND', 'Vietnamese dong'),
(162, 'YER', 'Yemeni rial'),
(163, 'ZMK', 'Zambian kwacha'),
(164, 'ZWL', 'Zimbabwe dollar');

-- --------------------------------------------------------

--
-- Struktur dari tabel `doc_umkm`
--

CREATE TABLE `doc_umkm` (
  `id` int(11) NOT NULL,
  `iduser` int(11) NOT NULL,
  `noktp` int(11) NOT NULL,
  `fotoktp` text NOT NULL,
  `nama_usaha` text NOT NULL,
  `fotousaha` text NOT NULL,
  `tgldaftar` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `doc_umkm`
--

INSERT INTO `doc_umkm` (`id`, `iduser`, `noktp`, `fotoktp`, `nama_usaha`, `fotousaha`, `tgldaftar`) VALUES
(1, 7, 2302930, 'im.jpg', 'UD JAYA BARU', 'im.jpg', '20-05-2019');

-- --------------------------------------------------------

--
-- Struktur dari tabel `fcm`
--

CREATE TABLE `fcm` (
  `id` bigint(20) NOT NULL,
  `device` varchar(100) NOT NULL,
  `os_version` varchar(100) NOT NULL,
  `app_version` varchar(10) NOT NULL,
  `serial` varchar(100) NOT NULL,
  `regid` text NOT NULL,
  `created_at` bigint(20) NOT NULL,
  `last_update` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `fcm`
--

INSERT INTO `fcm` (`id`, `device`, `os_version`, `app_version`, `serial`, `regid`, `created_at`, `last_update`) VALUES
(5, 'Xiaomi Redmi 4X', '7.1.2', '1 (1.0)', 'f8f8fc3c7d04', 'fT94JZVdUFA:APA91bHKqVpAqf04FFjy6gBhVHGfLv5HXGExnlr17gcyKG0kWeO-QxBhGRDbaUk0qix5oWjsKieuF6lSqisPqjkuj45FcGh13n2wf3J-_duAgQyXR3OvOTgM3cmaTuah1ThWUTvRHF5y', 2147483647, 1553055679526),
(6, 'Xiaomi Mi A1', '9', '1 (1.0)', '81a0de460804', 'c2QtUeZno4c:APA91bGO-zaHpFwyGXGRuzx11jsNt4_dfwbE1-rvZ5Pb9gk4FBF_HidZ3D8FhvuH7IbYWx9e23BYFkRVaJyizaCiwZOUpjIvfyJthzUnhO8eAZ6yuMeVo1Rs6YLCcFhNsMn_xGOBqj4S', 2147483647, 1548079260657),
(7, 'samsung SM-A500F', '6.0.1', '1 (1.0)', 'f530eef9', 'fpXx_zpViqU:APA91bH_6I11uPiq68yOO2fN9obT3CXDHXfvN0b4AgyqYrwVvYBOhmKq4fyOs5tqqyr3LSkMJ-P_CMWHokGac9Goob4r6FKkBe2tIJApFBvN28iYpKeJO_g3ZWQrYsO_GAx9GPCW6LHT', 2147483647, 1548079921435);

-- --------------------------------------------------------

--
-- Struktur dari tabel `news_info`
--

CREATE TABLE `news_info` (
  `id` bigint(20) NOT NULL,
  `title` varchar(100) NOT NULL,
  `brief_content` varchar(200) NOT NULL,
  `full_content` text NOT NULL,
  `image` varchar(110) NOT NULL,
  `draft` tinyint(1) NOT NULL,
  `status` varchar(50) NOT NULL,
  `created_at` bigint(20) NOT NULL,
  `last_update` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `news_info`
--

INSERT INTO `news_info` (`id`, `title`, `brief_content`, `full_content`, `image`, `draft`, `status`, `created_at`, `last_update`) VALUES
(1, 'Gratis Buah di Royal Plaza', 'Quisque efficitur diam sit amet quam porttitor, at dictum arcu viverra. Vivamus dapibus ante nunc, non malesuada enim gravida in.', '<div>Aliquam sed lorem quis urna eleifend aliquam ac ut urna. Sed tempus nisl tellus, vel tempor velit rutrum ac. Ut facilisis eget augue quis pulvinar. Nam a felis eu tortor convallis dapibus efficitur vel turpis. Etiam hendrerit pulvinar leo ac venenatis. Donec non felis et massa semper convallis nec id augue. Proin sit amet mollis nisl. Sed vestibulum auctor imperdiet. Nam ut enim non lacus blandit rhoncus non at turpis. Donec vel arcu malesuada, vulputate dui a, varius leo. Proin volutpat libero ultricies est auctor, eget facilisis ligula accumsan. Sed quis sapien hendrerit, luctus augue nec, aliquam urna. Mauris vestibulum convallis malesuada. Sed et ex cursus, fringilla tellus ac, maximus ipsum.</div><div><br></div><div>Sed viverra arcu in neque pulvinar dignissim. Nunc euismod aliquam magna, non efficitur lectus vehicula quis. Suspendisse vitae consequat arcu, eget malesuada dui. Interdum et malesuada fames ac ante ipsum primis in faucibus. Donec quis imperdiet libero. Donec imperdiet quis lectus vitae dignissim. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Nam pulvinar pretium ipsum, et faucibus libero aliquet et. Quisque efficitur diam sit amet quam porttitor, at dictum arcu viverra. Vivamus dapibus ante nunc, non malesuada enim gravida in. Cras bibendum eros vitae lacus efficitur, vel pretium est rutrum.</div><div><br></div><div>Praesent id efficitur risus. Nullam dui ligula, eleifend in convallis at, porta vitae massa. Curabitur sapien lorem, congue non enim non, rutrum gravida ipsum. Duis dignissim sapien in sem finibus ultrices id nec justo. Nulla hendrerit sed eros ac rhoncus. Nullam varius tellus id venenatis cursus. Cras commodo metus mauris, eget posuere sapien blandit non. Aenean fermentum ligula a libero suscipit dictum. Nulla dictum odio ut vulputate aliquam. Suspendisse potenti. Cras pellentesque vel felis sed dignissim. Proin aliquet orci tincidunt dui convallis, facilisis accumsan urna semper.</div>', 'Buah Kaya Gizi.jpg', 0, 'FEATURED', 1482250793274, 1546482806117);

-- --------------------------------------------------------

--
-- Struktur dari tabel `product`
--

CREATE TABLE `product` (
  `id` bigint(20) NOT NULL,
  `iduser` bigint(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `image` varchar(110) NOT NULL,
  `price` decimal(12,2) NOT NULL,
  `price_discount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `stock` int(10) NOT NULL,
  `draft` tinyint(1) NOT NULL,
  `description` text NOT NULL,
  `status` varchar(50) NOT NULL,
  `created_at` bigint(20) DEFAULT NULL,
  `last_update` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `product`
--

INSERT INTO `product` (`id`, `iduser`, `name`, `image`, `price`, `price_discount`, `stock`, `draft`, `description`, `status`, `created_at`, `last_update`) VALUES
(20, 6, 'Bros Cantik Motif Bunga', 'Bros Cantik Motif Bunga.jpg', '10000.00', '0.00', 50, 0, 'Bros Cantik Motif Bunga&nbsp;', 'READY STOCK', 1548035557597, 1548038066188),
(22, 6, 'Gelang Cantik', 'Gelang Cantik.jpg', '20000.00', '0.00', 11, 0, 'Gelang Cantik Cocok Untuk Bepergian', 'READY STOCK', 1548036657240, 1548038036632),
(23, 6, 'Bros Warna Coklat', 'Bros Warna Coklat.jpg', '10000.00', '0.00', 29, 0, 'Bros Cantik Warna Varian Coklat', 'READY STOCK', 1548036756648, 1548037987914),
(24, 4, 'Batik Kontemporer Varian Hitam', 'Batik Kontemporer Varian Hitam.jpg', '200000.00', '0.00', 20, 0, 'Batik Kontemporer.<div>Kain untuk membuat busana batik, asli surabaya</div>', 'READY STOCK', 1548037445764, 1548043561962),
(25, 4, 'Batik Kontemporer Varian Putih Motif', 'Batik Kontemporer Varian Putih Motif.jpg', '200000.00', '0.00', 10, 0, 'Batik Kontemporer Varian Putih&nbsp;', 'READY STOCK', 1548037531163, 1548038281168),
(26, 4, 'Batik Kontemporer Varian Merah Muda', 'Batik Kontemporer Varian Merah Muda.jpg', '150000.00', '0.00', 46, 0, 'Varian Merah Muda Batik Kontemporer&nbsp;', 'READY STOCK', 1548037596177, 1548038260181),
(27, 4, 'Batik Biru Hijau Motif', 'Batij.jpg', '200000.00', '175000.00', 11, 0, 'Batik biru hijau, kain adem dari bahan terbaik', 'READY STOCK', 1548037596177, 1548038260181),
(28, 4, 'Batik Kontemporer Varian Motif', 'Batij.jpg', '200000.00', '170000.00', 50, 0, 'Varian batik motif bahan adem', 'READY STOCK', 1548037531163, 1548142797477);

-- --------------------------------------------------------

--
-- Struktur dari tabel `product_category`
--

CREATE TABLE `product_category` (
  `product_id` bigint(20) NOT NULL,
  `category_id` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `product_category`
--

INSERT INTO `product_category` (`product_id`, `category_id`) VALUES
(23, 3),
(22, 3),
(20, 3),
(26, 5),
(25, 5),
(27, 5),
(24, 5),
(29, 4),
(31, 5),
(32, 5),
(28, 5);

-- --------------------------------------------------------

--
-- Struktur dari tabel `product_image`
--

CREATE TABLE `product_image` (
  `product_id` bigint(20) NOT NULL,
  `name` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struktur dari tabel `product_order`
--

CREATE TABLE `product_order` (
  `id` bigint(20) NOT NULL,
  `code` varchar(20) NOT NULL,
  `buyer` varchar(50) NOT NULL,
  `address` varchar(300) NOT NULL,
  `email` varchar(50) NOT NULL,
  `shipping` varchar(20) NOT NULL,
  `date_ship` bigint(20) NOT NULL,
  `phone` varchar(50) NOT NULL,
  `comment` text NOT NULL,
  `status` varchar(50) NOT NULL,
  `total_fees` decimal(12,2) NOT NULL,
  `tax` decimal(12,2) NOT NULL,
  `serial` varchar(100) DEFAULT NULL,
  `created_at` bigint(20) NOT NULL,
  `last_update` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `product_order`
--

INSERT INTO `product_order` (`id`, `code`, `buyer`, `address`, `email`, `shipping`, `date_ship`, `phone`, `comment`, `status`, `total_fees`, `tax`, `serial`, `created_at`, `last_update`) VALUES
(2, 'EP82467WO', 'Dewi ', 'Keling RT 16 RW 005 Sukodono Sidoarjo', 'dewi.sandyislamiati@gmail.com', 'POS Indonesia', 1548044387453, '083849602001', 'segera', 'PROCESSED', '331500.00', '2.00', 'f8f8fc3c7d04', 1548044405153, 1548044405153),
(3, 'JE82806UG', 'dewi', 'keling RT 16 RW 05 Sukodono Kabupaten sidoarjo', 'dewi.sandyislamiati@gmail.com', 'JNE', 1548046266363, '083849602001', 'segera', 'PROCESSED', '739500.00', '2.00', 'f8f8fc3c7d04', 1548046277373, 1548046277373),
(4, 'GG11601VL', 'hanifah', 'Malang', 'hanifa.mustika@gmail.com', 'JNE', 1548079053709, '083849602001', 'segera', 'PROCESSED', '20400.00', '2.00', '81a0de460804', 1548079061922, 1548079061922),
(5, 'QC50168ZO', 'Dewi', 'Keling Rt 16 ', 'dewi.sandyislamiati@gmail.com', 'POS Indonesia', 1548134710630, '083849602001', 'segera', 'PROCESSED', '163200.00', '2.00', 'f8f8fc3c7d04', 1548134720341, 1548134720341);

-- --------------------------------------------------------

--
-- Struktur dari tabel `product_order_detail`
--

CREATE TABLE `product_order_detail` (
  `id` bigint(20) NOT NULL,
  `order_id` bigint(20) DEFAULT NULL,
  `product_id` bigint(20) NOT NULL,
  `product_name` varchar(100) NOT NULL,
  `amount` int(11) NOT NULL,
  `price_item` decimal(12,2) NOT NULL,
  `created_at` bigint(20) NOT NULL,
  `last_update` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `product_order_detail`
--

INSERT INTO `product_order_detail` (`id`, `order_id`, `product_id`, `product_name`, `amount`, `price_item`, `created_at`, `last_update`) VALUES
(2, 2, 26, 'Batik Kontemporer Varian Merah Muda', 1, '150000.00', 1548044405154, 1548044405154),
(3, 2, 27, 'Batik Biru Hijau Motif', 1, '175000.00', 1548044405154, 1548044405154),
(4, 3, 25, 'Batik Kontemporer Varian Putih Motif', 1, '200000.00', 1548046277374, 1548046277374),
(5, 3, 27, 'Batik Biru Hijau Motif', 3, '175000.00', 1548046277374, 1548046277374),
(6, 4, 22, 'Gelang Cantik', 1, '20000.00', 1548079061922, 1548079061922),
(7, 5, 23, 'Bros Warna Coklat', 1, '10000.00', 1548134720342, 1548134720342),
(8, 5, 26, 'Batik Kontemporer Varian Merah Muda', 1, '150000.00', 1548134720342, 1548134720342);

-- --------------------------------------------------------

--
-- Struktur dari tabel `review_product`
--

CREATE TABLE `review_product` (
  `idreview` int(11) NOT NULL,
  `iduser` int(11) NOT NULL,
  `idproduct` int(11) NOT NULL,
  `review` text NOT NULL,
  `date` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `user`
--

CREATE TABLE `user` (
  `id` bigint(11) NOT NULL,
  `name` varchar(50) CHARACTER SET utf8 NOT NULL,
  `username` varchar(20) CHARACTER SET utf8 NOT NULL,
  `email` varchar(50) CHARACTER SET utf8 NOT NULL,
  `password` text CHARACTER SET utf8 NOT NULL,
  `status` int(11) NOT NULL,
  `address` text NOT NULL,
  `ttl` text NOT NULL,
  `gender` varchar(225) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `user`
--

INSERT INTO `user` (`id`, `name`, `username`, `email`, `password`, `status`, `address`, `ttl`, `gender`) VALUES
(1, 'User Markeet', 'dewsandy', 'dewi.sandyislamiati@gmail.com', 'user1', 0, 'Keling RT 16 RW 05', '08-06-1997', 'Female'),
(3, 'admin', 'admin', 'admin@go.id', 'user', 1, '', '', ''),
(4, 'Hepy Kurnia', 'hepy', 'hepykurnia@gmail.com', 'hepy', 2, 'Jl. Babatan Timur', ' 08-12-1971', 'Female'),
(6, 'Tri Agustin Nugrahani', 'treepuspita', 'treepuspitacollection@gmail.com', 'treepuspita', 2, 'Jl. Pogot Baru Karya Bakti 12 ', '18-09-1983', 'Female'),
(7, 'tito radian', 'tito', 'titotati@gmail.com', 'titosukaayam', 2, 'Keling', '1/15/1995', 'Male');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `app_version`
--
ALTER TABLE `app_version`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique` (`name`);

--
-- Indeks untuk tabel `config`
--
ALTER TABLE `config`
  ADD PRIMARY KEY (`code`);

--
-- Indeks untuk tabel `currency`
--
ALTER TABLE `currency`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `doc_umkm`
--
ALTER TABLE `doc_umkm`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `fcm`
--
ALTER TABLE `fcm`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `news_info`
--
ALTER TABLE `news_info`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `product_unique_name` (`name`);

--
-- Indeks untuk tabel `product_category`
--
ALTER TABLE `product_category`
  ADD KEY `fk_product_category_1` (`product_id`),
  ADD KEY `fk_product_category_2` (`category_id`);

--
-- Indeks untuk tabel `product_image`
--
ALTER TABLE `product_image`
  ADD KEY `fk_table_images` (`product_id`);

--
-- Indeks untuk tabel `product_order`
--
ALTER TABLE `product_order`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `product_order_detail`
--
ALTER TABLE `product_order_detail`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_table_orders_item` (`order_id`);

--
-- Indeks untuk tabel `review_product`
--
ALTER TABLE `review_product`
  ADD PRIMARY KEY (`idreview`);

--
-- Indeks untuk tabel `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_username` (`username`),
  ADD UNIQUE KEY `unique_email` (`email`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `app_version`
--
ALTER TABLE `app_version`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `category`
--
ALTER TABLE `category`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `currency`
--
ALTER TABLE `currency`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=165;

--
-- AUTO_INCREMENT untuk tabel `doc_umkm`
--
ALTER TABLE `doc_umkm`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `fcm`
--
ALTER TABLE `fcm`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `news_info`
--
ALTER TABLE `news_info`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `product`
--
ALTER TABLE `product`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT untuk tabel `product_order`
--
ALTER TABLE `product_order`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `product_order_detail`
--
ALTER TABLE `product_order_detail`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `review_product`
--
ALTER TABLE `review_product`
  MODIFY `idreview` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `user`
--
ALTER TABLE `user`
  MODIFY `id` bigint(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `product_category`
--
ALTER TABLE `product_category`
  ADD CONSTRAINT `fk_product_category_2` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `product_category_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`);

--
-- Ketidakleluasaan untuk tabel `product_image`
--
ALTER TABLE `product_image`
  ADD CONSTRAINT `fk_table_images` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `product_order_detail`
--
ALTER TABLE `product_order_detail`
  ADD CONSTRAINT `fk_table_orders_item` FOREIGN KEY (`order_id`) REFERENCES `product_order` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

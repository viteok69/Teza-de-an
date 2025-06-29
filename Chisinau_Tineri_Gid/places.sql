-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1
-- Время создания: Июн 29 2025 г., 22:25
-- Версия сервера: 10.4.32-MariaDB
-- Версия PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `places_db`
--

-- --------------------------------------------------------

--
-- Структура таблицы `places`
--

CREATE TABLE `places` (
  `id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `category` enum('park','restaurant','cafe','museum','shopping','education','entertainment','sports','coworking','nightlife','health','transport','culture') NOT NULL,
  `address` text NOT NULL,
  `description` text NOT NULL,
  `website_url` varchar(500) DEFAULT NULL,
  `image_url` varchar(500) DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `opening_hours` varchar(100) DEFAULT NULL,
  `price_range` enum('free','budget','moderate','expensive') DEFAULT 'moderate',
  `rating` decimal(2,1) DEFAULT 0.0,
  `added_by_user` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `places`
--

INSERT INTO `places` (`id`, `name`, `category`, `address`, `description`, `website_url`, `image_url`, `latitude`, `longitude`, `phone`, `opening_hours`, `price_range`, `rating`, `added_by_user`, `created_at`, `updated_at`) VALUES
(1, 'Parcul „Valea Morilor”', 'park', 'Strada Grigore Alexandrescu', 'Un parc pitoresc cu un lac mare, ideal pentru plimbări cu barca, jogging și relaxare. Include o alee a scărilor și cascade artificiale.', 'https://visit.chisinau.md/obiective_turistice/parcul-valea-morilor/', 'https://visit.chisinau.md/wp-content/uploads/2021/09/pe33.jpg', 47.03710000, 28.80910000, NULL, '24/7', 'free', 4.7, 0, '2025-06-29 19:37:51', '2025-06-29 19:51:59'),
(2, 'Grădina Botanică din Chișinău', 'park', 'Strada Pădurii 18', 'O colecție impresionantă de plante și arbori din diverse zone climatice. Perfect pentru studiu și plimbări liniștite.', 'https://visit.chisinau.md/obiective_turistice/gradina-botanica/', 'https://upload.wikimedia.org/wikipedia/commons/6/63/Gradina_botanica_Chisinau_%2820%29.jpg', 46.97450000, 28.89500000, '+373 22 550 443', '09:00-21:00', 'budget', 4.5, 0, '2025-06-29 19:37:51', '2025-06-29 19:56:33'),
(3, 'Parcul „Dendrariu”', 'park', 'Strada Ion Creangă 20/1', 'Un parc istoric cu arbori rari și alei umbroase, perfect pentru o evadare din agitația orașului. Include un lac mic și zone de odihnă.', 'https://dendrariu.md/', 'https://pestcontrol-expert.md/wp-content/uploads/2024/03/den-1024x768.jpeg', 47.03050000, 28.81670000, '+373 22 719 029', '08:00-20:00', 'budget', 4.8, 0, '2025-06-29 19:37:51', '2025-06-29 19:54:26'),
(4, 'La Plăcinte', 'restaurant', 'Strada Pușkin 33', 'Restaurant tradițional moldovenesc, renumit pentru plăcintele sale delicioase și bucatele autentice.', 'https://laplacinte.md/', 'https://static.locals.md/2019/10/20190927-img_4430-hdr.jpg', 47.02700000, 28.83500000, '+373 22 211 211', '10:00-22:00', 'moderate', 4.3, 0, '2025-06-29 19:37:51', '2025-06-29 19:58:12'),
(5, 'Andy\'s Pizza', 'restaurant', 'Strada Calea Ieşilor 10', 'Noi prestăm clienţilor noştri servicii suplimentare, precum ar fi livrarea comenzilor, organizarea sărbătorilor pentru copii şi deservirea clienţilor ...', 'https://www.andys.md/', 'https://www.fest.md/files/places/3/image_304_1_large.jpg', 47.02500000, 28.82500000, '+373 22 210 210', '10:00-23:00', 'moderate', 4.3, 0, '2025-06-29 19:37:51', '2025-06-29 20:00:10'),
(6, 'Lake House BrewPub', 'restaurant', 'Strada Stefan Cel Mare, 182', '„Lake House Brewery” este o mică fabrică de bere deținută de o familie, fondată în 2017.', 'https://lakehouse.md/', 'https://static.tildacdn.one/tild6636-3362-4232-a231-313864323535/RDP-36.jpg', 47.02100000, 28.83100000, '+373 607 77 979', '14:00-00:00', 'expensive', 4.6, 0, '2025-06-29 19:37:51', '2025-06-29 20:01:43'),
(7, 'Tucano Coffee Brazil', 'cafe', 'Strada Alexandru cel Bun 91A', 'Lanț de cafenele cu o atmosferă vibrantă, cafea de specialitate și gustări. Ideal pentru studiu sau socializare.', 'https://www.facebook.com/tucanocoffeemoldova/?locale=ro_RO', 'https://www.fest.md/files/places/5/image_585_7_large.jpg', 47.02800000, 28.83400000, '+373 600 36 777', '08:00-21:00', 'moderate', 4.5, 0, '2025-06-29 19:37:51', '2025-06-29 20:06:04'),
(8, 'Miro Café', 'cafe', 'Strada Mitropolit Petru Movilă 14', 'Mic dejun · Gustări · Salate · Supe · Fel principal · Adaosuri · Cafea · Matcha Bar.', 'https://mirocafe.md/', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQ0EapzudTa9jrbiU3i-nHhSEVoxn7sKIBJFQ&s', 47.02550000, 28.83000000, '+373 689 90 900', '08:00-21:00', 'expensive', 4.4, 0, '2025-06-29 19:37:51', '2025-06-29 20:04:31'),
(9, 'Daily Dose', 'cafe', 'Strada Pușkin 44/1', 'Un loc colorat și vibrant, ideal pentru cocktailuri, cafea și o atmosferă relaxată. Seara devine adesea un loc de întâlnire socială.', 'https://www.dailydose.md/', 'https://static.locals.md/2023/08/dailydose.chisinau_1692450790980.jpeg', 47.02000000, 28.83200000, '+373 687 31 731', '08:00-19:00', 'moderate', 4.3, 0, '2025-06-29 19:37:51', '2025-06-29 20:07:24'),
(10, 'Muzeul Național de Artă a Moldovei', 'museum', 'Strada 31 August 1989 115', 'Un muzeu bogat în artefacte istorice, de la preistorie până în epoca modernă. O călătorie prin istoria Moldovei.', 'https://www.mnam.md', 'https://visit.chisinau.md/wp-content/uploads/2021/09/muzeu-de-arta-e1637679921204.jpg', 47.01750000, 28.83350000, '+373 22 241 312', '10:00-18:00', 'budget', 4.7, 0, '2025-06-29 19:37:51', '2025-06-29 20:08:55'),
(11, 'Muzeul Naţional de Istorie a Moldovei', 'museum', 'Strada 31 August 1989 121A', 'În prezent (2021) muzeul deține 348.619 piese de patrimoniu.', 'https://www.nationalmuseum.md/ro/programme/', 'https://visit.chisinau.md/wp-content/uploads/2021/09/pe2-1.jpg', 47.01800000, 28.83400000, '+373 22 244 325', '10:00-17:00', 'budget', 4.6, 0, '2025-06-29 19:37:51', '2025-06-29 20:11:10'),
(12, 'Muzeul Național de Etnografie și Istorie Naturală', 'museum', 'Strada Mihail Kogălniceanu 82', 'A fost creat în octombrie 1889 în baza colecției exponatelor primei expoziției agrare din Basarabia, organizată sub inițiativa baronului A. Stuart.', 'https://www.muzeu.md/', 'https://upload.wikimedia.org/wikipedia/commons/9/95/Cl%C4%83direa_Muzeului_Zooagricol_%C8%99i_de_Me%C8%99te%C8%99uguri_Populare_%28%C3%AEn_prezent_Muzeul_de_Etnografie_%C8%99i_Istorie_Natural%C4%83%29._Foto_3.jpg', 47.04500000, 28.79000000, '+373 22 240 056', '10:00-17:00', 'budget', 4.7, 0, '2025-06-29 19:37:51', '2025-06-29 20:13:08'),
(13, 'Shopping MallDova', 'shopping', 'Strada Arborilor 21', 'Unul dintre cele mai mari centre comerciale din Chișinău, cu o gamă largă de magazine, restaurante și divertisment.', 'https://shoppingmalldova.md', 'https://shoppingmalldova.md/wp-content/uploads/2023/02/Despre_noi_exterior_mall-min-1024x768.png', 46.99600000, 28.86800000, '+373 796 03 205', '10:00-22:00', 'moderate', 4.6, 0, '2025-06-29 19:37:51', '2025-06-29 20:14:18'),
(14, 'Atrium', 'shopping', 'Strada Albișoara 4, Chișinău', 'Centru comercial modern cu branduri internaționale, food court și cinematograf. Situat central.', 'https://atrium.md', 'https://visit.chisinau.md/wp-content/uploads/2021/09/Atrium2.jpg', 47.02150000, 28.84000000, '+373 22 884 669', '10:00-21:00', 'moderate', 4.4, 0, '2025-06-29 19:37:51', '2025-06-29 20:15:25'),
(15, 'Sun City', 'shopping', 'Strada Pușkin 32, Chișinău', 'Centru comercial situat în inima orașului, cu magazine variate, cafenele și spații de servicii.', 'https://www.instagram.com/suncitychisinau/', 'https://www.mold-street.com/storage/news_img/2020/04/19/news1_big.jpg', 47.02600000, 28.83300000, '+373 22 234 664', '09:00-21:00', 'moderate', 4.3, 0, '2025-06-29 19:37:51', '2025-06-29 20:21:12'),
(16, 'Universitatea Tehnică a Moldovei', 'education', 'Strada Studenților 7', 'Cea mai mare universitate tehnică din Republica Moldova, oferind programe de studiu în inginerie, IT și arhitectură.', 'https://utm.md', 'https://utm.md/wp-content/uploads/2022/03/fcim_bloc3.jpg', 47.01450000, 28.83700000, '+373 22 509 907', '07:00-22:00', 'budget', 4.5, 0, '2025-06-29 19:37:51', '2025-06-29 20:16:47'),
(17, 'Academia de Studii Economice a Moldovei', 'education', 'Strada Mitropolit Gavriil Bănulescu-Bodoni 61', 'Instituție de învățământ superior specializată în științe economice și afaceri.', 'https://ase.md', 'https://ase.md/wp-content/uploads/2020/11/Rectorii-ASEM.jpg', 47.02100000, 28.83150000, '+373 22 224 128', '08:00-16:30', 'budget', 4.7, 0, '2025-06-29 19:37:51', '2025-06-29 20:17:47'),
(18, 'Universitatea de Stat din Moldova', 'education', 'Strada Alexei Mateevici 60', 'Universitatea de Stat din Moldova este una din principalele instituții de învățământ superior din Republica Moldova, care își are sediul în Chișinău', 'https://usm.md/', 'https://upload.wikimedia.org/wikipedia/commons/thumb/9/9a/State_University_of_Moldova_%28cropped%29.jpg/1200px-State_University_of_Moldova_%28cropped%29.jpg', 47.01780000, 28.83670000, '+373 22 242 482', '08:00-17:00', 'budget', 4.6, 0, '2025-06-29 19:37:51', '2025-06-29 20:19:24'),
(19, 'Campusul Mircești al Universității Tehnice a Moldovei', 'education', 'Strada Mirceşti 42', 'Din 1 ianuarie 2023 a intrat juridic în componența Universității Tehnice din Moldova.', 'https://www.facebook.com/UASMoficial/?locale=ro_RO', 'https://agrotv.md/wp-content/uploads/2022/06/755a1e795f8743e438fa5f999fc20fa3.jpg', 47.03230000, 28.36410000, NULL, '08:00-16.00', 'budget', 4.4, 1, '2025-06-29 20:24:19', '2025-06-29 20:24:19');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `places`
--
ALTER TABLE `places`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `places`
--
ALTER TABLE `places`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

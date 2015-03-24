-- phpMyAdmin SQL Dump
-- version 4.2.11
-- http://www.phpmyadmin.net
--
-- Хост: 127.0.0.1
-- Время создания: Мар 24 2015 г., 16:42
-- Версия сервера: 5.6.21
-- Версия PHP: 5.6.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- База данных: `comm-app`
--

-- --------------------------------------------------------

--
-- Структура таблицы `chat_messages`
--

CREATE TABLE IF NOT EXISTS `chat_messages` (
`id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` varchar(255) CHARACTER SET utf8 NOT NULL,
  `stamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;

--
-- Дамп данных таблицы `chat_messages`
--

INSERT INTO `chat_messages` (`id`, `user_id`, `message`, `stamp`) VALUES
(1, 1, 'hello', '2014-11-05 12:45:28'),
(2, 3, 'hello, oli', '2014-11-05 12:52:46'),
(3, 3, 'what''s up?', '2014-11-05 16:49:24'),
(4, 3, 'where are you?', '2014-11-05 20:36:02'),
(5, 1, 'i''m afk', '2014-11-05 20:37:02'),
(6, 3, 'ok', '2014-11-05 20:37:34'),
(7, 3, 'omg, refresh alowes 5 sec', '2014-11-06 20:19:05');

-- --------------------------------------------------------

--
-- Структура таблицы `image_uploads`
--

CREATE TABLE IF NOT EXISTS `image_uploads` (
`id` int(11) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `thumbnail` varchar(255) NOT NULL,
  `label` varchar(255) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `image_uploads`
--

INSERT INTO `image_uploads` (`id`, `filename`, `thumbnail`, `label`, `user_id`) VALUES
(1, 'Я в Расеи_11-Exposure_готово_6.jpg', 'tn_Я в Расеи_11-Exposure_готово_6.jpg', 'Я в Пятигорске', 1);

-- --------------------------------------------------------

--
-- Структура таблицы `store_orders`
--

CREATE TABLE IF NOT EXISTS `store_orders` (
`id` int(11) NOT NULL,
  `store_product_id` int(255) NOT NULL,
  `qty` int(11) NOT NULL,
  `total` float(9,2) NOT NULL,
  `status` enum('new','completed','shipped','cancelled') DEFAULT NULL,
  `stamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `first_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `ship_to_street` varchar(255) DEFAULT NULL,
  `ship_to_city` varchar(255) DEFAULT NULL,
  `ship_to_state` varchar(2) DEFAULT NULL,
  `ship_to_zip` int(11) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `store_orders`
--

INSERT INTO `store_orders` (`id`, `store_product_id`, `qty`, `total`, `status`, `stamp`, `first_name`, `last_name`, `email`, `ship_to_street`, `ship_to_city`, `ship_to_state`, `ship_to_zip`) VALUES
(1, 3, 2, 862.00, 'new', '2015-03-08 16:52:44', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(2, 3, 2, 862.00, 'new', '2015-03-08 17:02:46', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(3, 3, 2, 862.00, 'new', '2015-03-08 17:04:02', NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `store_products`
--

CREATE TABLE IF NOT EXISTS `store_products` (
`id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `desc` text NOT NULL,
  `cost` float(9,2) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `store_products`
--

INSERT INTO `store_products` (`id`, `name`, `desc`, `cost`) VALUES
(3, 'Велосипед детский мульт 14 дюймов V1146K', 'Детский 2-х колесный Велосипед 14 изготовлен для детей возврастом 4 - 6 лет.\r\nЯркая окраска делает этот надежный, удобный велосипед очень стильным.\r\nВелосипед оборудован низкой рамой, что делает его более удобным для катания меленьких детей.\r\nОборудован передним ручным тормозами. Стильный хромированный руль BMX имеет возможность регулировки и оснащен защитной накладкой.\r\nВелосипед 14 укомплектован приставными пластиковыми колесиками и защитой цепи, что делает его абсолютно безопасным при эксплуатаци, а декоративная передняя панель, зеркало заднего вида делают его модным и стильным.\r\nБез сомнений, этот велосипед понравится Вашему ребенка и поможет интересно и весело провести время.\r\nРазвивает координацию движений и физическую форму ребенка.\r\nДиаметр колес - 14 дюймов.\r\nДля детей в возрасте старше 3 лет', 431.00),
(4, 'Велосипед 20 дюймов XM204', 'Характеристики велосипеда:\r\nСерия: UNION\r\nБренд: PROF1\r\nАртикул: XM204\r\nРазмер: 20"\r\nВес: 13,05\r\nЦвета: желто-черный, красно-черный, бело-салатовый\r\nРама (материал) алюминий\r\nВилка пружинно-эластомерная амортизационная вилка MODE, Sensor racing T 7 original\r\nРуль сталь, CP Black\r\nвынос руля более качественный, чем в 2012г\r\nМанетки Shimano SL-RS35-6 RevoShift\r\nЗадний амортизатор: нет\r\nНавесное оборудование одноподвесный Навесное оборудование Shimano Tourney\r\nкол-во скоростей 18\r\nКол-во звезд (передние) 3 (пластмассовая защита)\r\nКол-во звезд (задние) 6\r\nТрансмиссия и система передач:\r\nПередний переключатель Shimano Tourney, FD-TX51\r\nЗадний переключатель Shimano Tourney, SIS RD-TX35\r\nКассета (трещотка): Shimano MF-TZ-20\r\nТормоза:\r\nПередние Ободные V-Brake (Алюминий),SPARKLE\r\nЗадние Ободные V-Brake (Алюминий),SPARKLE\r\nТормозные ручки SPARKLE\r\nКолеса (Резина) 20*2,10\r\nОбода усиленные двойные обода\r\nПедали прогулочные,классические с пластиковой рамкой\r\nДоп. Комплектующие регулируемые крылья, катафоты,подножка', 1438.00);

-- --------------------------------------------------------

--
-- Структура таблицы `uploads`
--

CREATE TABLE IF NOT EXISTS `uploads` (
`id` int(11) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `label` varchar(255) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `uploads`
--

INSERT INTO `uploads` (`id`, `filename`, `label`, `user_id`) VALUES
(1, 'ІНСТРУКЦІЯ З ОХОРОНИ ПРАЦІ.doc', 'Corporate Report', 1),
(2, '576-6.doc', 'Corporate Report', 3),
(3, 'playboy.jpg', 'Corporate Report', 1),
(4, 'ОАО Ростелеком.doc', 'Corporate Report', 3),
(8, 'Проверка_Lucene_eng.docx', 'Проверка Word', 1),
(9, 'Проверка_таблиц_Lucene_eng.xlsx', 'Проверка Excel', 1),
(10, 'Проверка_Lucene.docx', 'Проверка кириллицы', 1);

-- --------------------------------------------------------

--
-- Структура таблицы `uploads_sharing`
--

CREATE TABLE IF NOT EXISTS `uploads_sharing` (
`id` int(11) NOT NULL,
  `upload_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `uploads_sharing`
--

INSERT INTO `uploads_sharing` (`id`, `upload_id`, `user_id`) VALUES
(1, 1, 3),
(3, 3, 3),
(2, 3, 4),
(4, 4, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `user`
--

CREATE TABLE IF NOT EXISTS `user` (
`id` int(10) unsigned NOT NULL,
  `name` varchar(50) CHARACTER SET utf8 NOT NULL,
  `email` varchar(255) CHARACTER SET utf8 NOT NULL,
  `password` varchar(50) CHARACTER SET utf8 NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

--
-- Дамп данных таблицы `user`
--

INSERT INTO `user` (`id`, `name`, `email`, `password`) VALUES
(1, 'OliLukoye', 'oli@yandex.ru', '202cb962ac59075b964b07152d234b70'),
(3, 'Jhon Lennon', 'jhon-l@ya.ru', '202cb962ac59075b964b07152d234b70'),
(4, 'vasya', 'vasya@pupkin.ru', '202cb962ac59075b964b07152d234b70');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `chat_messages`
--
ALTER TABLE `chat_messages`
 ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `image_uploads`
--
ALTER TABLE `image_uploads`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `filename` (`filename`);

--
-- Индексы таблицы `store_orders`
--
ALTER TABLE `store_orders`
 ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `store_products`
--
ALTER TABLE `store_products`
 ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `uploads`
--
ALTER TABLE `uploads`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `filename` (`filename`);

--
-- Индексы таблицы `uploads_sharing`
--
ALTER TABLE `uploads_sharing`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `upload_id` (`upload_id`,`user_id`);

--
-- Индексы таблицы `user`
--
ALTER TABLE `user`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `idx_email` (`email`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `chat_messages`
--
ALTER TABLE `chat_messages`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT для таблицы `image_uploads`
--
ALTER TABLE `image_uploads`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT для таблицы `store_orders`
--
ALTER TABLE `store_orders`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT для таблицы `store_products`
--
ALTER TABLE `store_products`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT для таблицы `uploads`
--
ALTER TABLE `uploads`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=11;
--
-- AUTO_INCREMENT для таблицы `uploads_sharing`
--
ALTER TABLE `uploads_sharing`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT для таблицы `user`
--
ALTER TABLE `user`
MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

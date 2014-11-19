-- phpMyAdmin SQL Dump
-- version 3.3.3
-- http://www.phpmyadmin.net
--
-- Хост: localhost
-- Время создания: Ноя 19 2014 г., 23:42
-- Версия сервера: 5.1.54
-- Версия PHP: 5.4.20

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `message` varchar(255) NOT NULL,
  `stamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;

--
-- Дамп данных таблицы `chat_messages`
--

INSERT INTO `chat_messages` (`id`, `user_id`, `message`, `stamp`) VALUES
(1, 1, 'hello', '2014-11-05 15:45:28'),
(2, 3, 'hello, oli', '2014-11-05 15:52:46'),
(3, 3, 'what''s up?', '2014-11-05 19:49:24'),
(4, 3, 'where are you?', '2014-11-05 23:36:02'),
(5, 1, 'i''m afk', '2014-11-05 23:37:02'),
(6, 3, 'ok', '2014-11-05 23:37:34'),
(7, 3, 'omg, refresh alowes 5 sec', '2014-11-06 23:19:05');

-- --------------------------------------------------------

--
-- Структура таблицы `uploads`
--

CREATE TABLE IF NOT EXISTS `uploads` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `filename` varchar(255) NOT NULL,
  `label` varchar(255) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `filename` (`filename`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Дамп данных таблицы `uploads`
--

INSERT INTO `uploads` (`id`, `filename`, `label`, `user_id`) VALUES
(1, 'ІНСТРУКЦІЯ З ОХОРОНИ ПРАЦІ.doc', 'Corporate Report', 1),
(2, '576-6.doc', 'Corporate Report', 3),
(3, 'playboy.jpg', 'Corporate Report', 1),
(4, 'ОАО Ростелеком.doc', 'Corporate Report', 3);

-- --------------------------------------------------------

--
-- Структура таблицы `uploads_sharing`
--

CREATE TABLE IF NOT EXISTS `uploads_sharing` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `upload_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `upload_id` (`upload_id`,`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

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
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_email` (`email`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Дамп данных таблицы `user`
--

INSERT INTO `user` (`id`, `name`, `email`, `password`) VALUES
(1, 'OliLukoye', 'oli@yandex.ru', '202cb962ac59075b964b07152d234b70'),
(3, 'Jhon Lennon', 'jhon-l@ya.ru', '202cb962ac59075b964b07152d234b70'),
(4, 'vasya', 'vasya@pupkin.ru', '202cb962ac59075b964b07152d234b70');

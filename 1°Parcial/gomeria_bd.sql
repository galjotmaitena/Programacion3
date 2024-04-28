-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 03-10-2022 a las 00:34:31
-- Versión del servidor: 10.4.22-MariaDB
-- Versión de PHP: 8.1.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `gomeria_bd`
--
CREATE DATABASE IF NOT EXISTS `gomeria_bd` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `gomeria_bd`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `neumaticos`
--

CREATE TABLE `neumaticos` (
  `id` int(10) UNSIGNED NOT NULL,
  `marca` varchar(30) NOT NULL,
  `medidas` varchar(30) NOT NULL,
  `precio` double NOT NULL,
  `foto` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `neumaticos`
--

INSERT INTO `neumaticos` (`id`, `marca`, `medidas`, `precio`, `foto`) VALUES
(1, 'BrideStone', '195-65-R15', 23500, NULL),
(2, 'FateO', '195-75-R14', 19800, NULL),
(3, 'Pirelli', '190-30-R18', 56900, './pirelli.105905.jpg'),
(4, 'Pirelli', '195-65-R15', 33200, './pirelli.105905.jpg');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `neumaticos`
--
ALTER TABLE `neumaticos`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `neumaticos`
--
ALTER TABLE `neumaticos`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

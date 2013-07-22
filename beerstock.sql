-- phpMyAdmin SQL Dump
-- version 3.3.9.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jul 08, 2013 at 01:08 PM
-- Server version: 5.1.53
-- PHP Version: 5.3.5

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `beerstock`
--

-- --------------------------------------------------------

--
-- Table structure for table `stock`
--

CREATE TABLE IF NOT EXISTS `stock` (
  `upc` varchar(12) COLLATE utf8_unicode_ci NOT NULL,
  `id` char(10) COLLATE utf8_unicode_ci NOT NULL,
  `thumb` varchar(160) COLLATE utf8_unicode_ci NOT NULL,
  `beer` text COLLATE utf8_unicode_ci NOT NULL,
  `brewery` varchar(160) COLLATE utf8_unicode_ci NOT NULL,
  `style` varchar(160) COLLATE utf8_unicode_ci NOT NULL,
  `abv` decimal(10,1) NOT NULL,
  `ibu` int(11) NOT NULL,
  `srm` int(11) NOT NULL,
  `type` int(11) NOT NULL,
  `qty` int(11) NOT NULL,
  PRIMARY KEY (`upc`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- phpMyAdmin SQL Dump
-- version 3.4.9
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Feb 17, 2012 at 04:26 PM
-- Server version: 5.5.20
-- PHP Version: 5.3.10

SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT=0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `htw_restaurant_oracle`
--

-- --------------------------------------------------------

--
-- Table structure for table `attributes`
--

DROP TABLE IF EXISTS `attributes`;
CREATE TABLE IF NOT EXISTS `attributes` (
  `aid` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `attribute` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`aid`),
  UNIQUE KEY `aid` (`aid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=201 ;

-- --------------------------------------------------------

--
-- Table structure for table `attribute_value_pairs`
--

DROP TABLE IF EXISTS `attribute_value_pairs`;
CREATE TABLE IF NOT EXISTS `attribute_value_pairs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `rid` bigint(20) NOT NULL,
  `aid` bigint(20) NOT NULL,
  `vid` bigint(20) NOT NULL,
  `frequency` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=228372 ;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
CREATE TABLE IF NOT EXISTS `categories` (
  `cat_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`cat_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Table of categories' AUTO_INCREMENT=77 ;

-- --------------------------------------------------------

--
-- Table structure for table `foods`
--

DROP TABLE IF EXISTS `foods`;
CREATE TABLE IF NOT EXISTS `foods` (
  `fid` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `food` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `aid` bigint(20) NOT NULL,
  PRIMARY KEY (`fid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=201 ;

-- --------------------------------------------------------

--
-- Table structure for table `groups`
--

DROP TABLE IF EXISTS `groups`;
CREATE TABLE IF NOT EXISTS `groups` (
  `gid` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'group id',
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'group name',
  PRIMARY KEY (`gid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='table of groups' AUTO_INCREMENT=32 ;

-- --------------------------------------------------------

--
-- Table structure for table `group_members`
--

DROP TABLE IF EXISTS `group_members`;
CREATE TABLE IF NOT EXISTS `group_members` (
  `gid` bigint(20) unsigned NOT NULL COMMENT 'group id',
  `uid` bigint(20) unsigned NOT NULL COMMENT 'user id',
  PRIMARY KEY (`gid`,`uid`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='maps users and groups to each other';

-- --------------------------------------------------------

--
-- Table structure for table `restaurants`
--

DROP TABLE IF EXISTS `restaurants`;
CREATE TABLE IF NOT EXISTS `restaurants` (
  `rid` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `polarity` double NOT NULL,
  `reviews` int(11) NOT NULL,
  `latitude` float DEFAULT NULL,
  `longitude` float DEFAULT NULL,
  `hours` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `price` tinyint(1) DEFAULT NULL,
  `accepts_credit_cards` tinyint(1) DEFAULT NULL,
  `reservations` tinyint(1) DEFAULT NULL,
  `attribute_count` int(11) NOT NULL,
  PRIMARY KEY (`rid`),
  KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3098 ;

-- --------------------------------------------------------

--
-- Table structure for table `restaurant_attributes`
--

DROP TABLE IF EXISTS `restaurant_attributes`;
CREATE TABLE IF NOT EXISTS `restaurant_attributes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `rid` bigint(20) unsigned NOT NULL,
  `frequency` int(5) NOT NULL,
  `aid` bigint(20) unsigned NOT NULL,
  `polarity` double NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Associates attributes to specific restaurants' AUTO_INCREMENT=67617 ;

-- --------------------------------------------------------

--
-- Table structure for table `restaurant_categories`
--

DROP TABLE IF EXISTS `restaurant_categories`;
CREATE TABLE IF NOT EXISTS `restaurant_categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `cid` bigint(20) unsigned NOT NULL,
  `rid` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3114 ;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `uid` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'user id',
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'email address',
  `fname` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'first name',
  `lname` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'last name',
  PRIMARY KEY (`uid`),
  UNIQUE KEY `uid` (`uid`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Table of users for restaurant oracle.' AUTO_INCREMENT=34 ;

-- --------------------------------------------------------

--
-- Table structure for table `user_pref_categories`
--

DROP TABLE IF EXISTS `user_pref_categories`;
CREATE TABLE IF NOT EXISTS `user_pref_categories` (
  `uid` bigint(20) unsigned NOT NULL COMMENT 'foreign key to users',
  `category` bigint(20) unsigned NOT NULL COMMENT 'categories of food (such as ''American'')',
  `rating` tinyint(3) unsigned NOT NULL COMMENT 'rating from 1 to 5 of how much the user likes this category of food',
  PRIMARY KEY (`uid`,`category`),
  KEY `category` (`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Table of users'' ratings on categories of food, such as "Amer';

-- --------------------------------------------------------

--
-- Table structure for table `user_pref_foods`
--

DROP TABLE IF EXISTS `user_pref_foods`;
CREATE TABLE IF NOT EXISTS `user_pref_foods` (
  `uid` bigint(20) unsigned NOT NULL COMMENT 'foreign key to users(uid)',
  `food` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Name of a food (like "sushi")',
  `rating` tinyint(3) unsigned NOT NULL COMMENT 'rating from 1 to 5 of how much the user likes this type of food',
  PRIMARY KEY (`uid`,`food`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Table of users'' ratings on specific types of food, such as "';

-- --------------------------------------------------------

--
-- Table structure for table `value_info`
--

DROP TABLE IF EXISTS `value_info`;
CREATE TABLE IF NOT EXISTS `value_info` (
  `vid` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `one_star` int(11) NOT NULL,
  `two_stars` int(11) NOT NULL,
  `three_stars` int(11) NOT NULL,
  `four_stars` int(11) NOT NULL,
  `five_stars` int(11) NOT NULL,
  `polarity` double NOT NULL,
  `value` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`vid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1537 ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `group_members`
--
ALTER TABLE `group_members`
  ADD CONSTRAINT `group_members_ibfk_3` FOREIGN KEY (`gid`) REFERENCES `groups` (`gid`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `group_members_ibfk_4` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `user_pref_categories`
--
ALTER TABLE `user_pref_categories`
  ADD CONSTRAINT `user_pref_categories_ibfk_3` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `user_pref_categories_ibfk_4` FOREIGN KEY (`category`) REFERENCES `categories` (`cat_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `user_pref_foods`
--
ALTER TABLE `user_pref_foods`
  ADD CONSTRAINT `user_pref_foods_ibfk_2` FOREIGN KEY (`uid`) REFERENCES `users` (`uid`) ON DELETE CASCADE ON UPDATE NO ACTION;
SET FOREIGN_KEY_CHECKS=1;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

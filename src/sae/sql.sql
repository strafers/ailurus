-- phpMyAdmin SQL Dump
-- version 3.3.8.1
-- http://www.phpmyadmin.net
--
-- 主机: w.rdc.sae.sina.com.cn:3307
-- 生成日期: 2014 年 08 月 02 日 15:20
-- 服务器版本: 5.5.23
-- PHP 版本: 5.3.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- 数据库: `app_ailurus`
--

-- --------------------------------------------------------

--
-- 表的结构 `advertisement`
--

CREATE TABLE IF NOT EXISTS `advertisement` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `image_url` varchar(100) NOT NULL,
  `weixin_app_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `advertisement`
--


-- --------------------------------------------------------

--
-- 表的结构 `ailurus_user`
--

CREATE TABLE IF NOT EXISTS `ailurus_user` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `email` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `nickname` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `salt` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `role` enum('admin','editor') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'editor',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=25 ;

--
-- 转存表中的数据 `ailurus_user`
--

INSERT INTO `ailurus_user` (`id`, `email`, `nickname`, `password`, `salt`, `role`) VALUES
(1, 'lazypeople@sina.com', 'lazypeople', '1eb89db9b500de8912d630b4361e4dc5', 'txb3kn3j4k', 'admin'),
(18, 'test@sina.com', 'test', '78dff59bc9473ae135866d575f4ba724', 'bcacximerx', 'admin'),
(21, 'admin@sina.com', 'admin', '116d2c4305c112314d575b3a6e17e2cb', 'fhruv6k6ye', 'editor'),
(23, 'edit@sina.com', 'edit', 'c3a5d4eec162aacdbea2298af35f42f7', 'dsbsbtmxv4', 'editor'),
(24, 'test1@sina.com', 'test1', '8d54d39e08cba3120bf4918616be2cb4', 'rjym8awenm', 'editor');

-- --------------------------------------------------------

--
-- 表的结构 `article`
--

CREATE TABLE IF NOT EXISTS `article` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  `content` text NOT NULL,
  `md5_id` varchar(50) NOT NULL,
  `status` tinyint(4) NOT NULL,
  `weixin_app_id` int(11) NOT NULL,
  `user_id` int(10) NOT NULL,
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `modify_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `article`
--


-- --------------------------------------------------------

--
-- 表的结构 `config`
--

CREATE TABLE IF NOT EXISTS `config` (
  `item` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `value` text COLLATE utf8_unicode_ci NOT NULL,
  UNIQUE KEY `item` (`item`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- 转存表中的数据 `config`
--

INSERT INTO `config` (`item`, `value`) VALUES
('site_open', '1'),
('use_ssl', '0'),
('site_name', '小熊猫微信开发框架'),
('keywords', '小熊猫微信开发，微信开发框架'),
('devteam', '小熊猫开发团队');

-- --------------------------------------------------------

--
-- 表的结构 `weixin_app`
--

CREATE TABLE IF NOT EXISTS `weixin_app` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `app_name` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `app_desc` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `token` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `action_class` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=18 ;

--
-- 转存表中的数据 `weixin_app`
--

INSERT INTO `weixin_app` (`id`, `app_name`, `app_desc`, `token`, `action_class`, `create_time`) VALUES
(16, 'default', 'default app', '31ee0dfd8368cdbf4691858ceb60', 'default_chat.class.php', '2014-07-23 11:17:35');

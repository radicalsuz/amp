-- phpMyAdmin SQL Dump
-- version 2.6.1-pl3
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generation Time: Aug 15, 2005 at 02:24 PM
-- Server version: 4.1.10
-- PHP Version: 4.3.11
-- 
-- Database: `unitedforpeace`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `userdata_plugins_options`
-- 

CREATE TABLE IF NOT EXISTS `userdata_plugins_options` (
  `plugin_id` int(11) NOT NULL default '0',
  `name` varchar(30) NOT NULL default '',
  `value` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`plugin_id`,`name`),
  KEY `plugin_id` (`plugin_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `userdata_plugins_options`
-- 

INSERT INTO `userdata_plugins_options` VALUES (3, 'recurring_events', '1');
INSERT INTO `userdata_plugins_options` VALUES (3, 'reg_modin', '51');

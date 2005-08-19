-- phpMyAdmin SQL Dump
-- version 2.6.1-pl3
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generation Time: Aug 15, 2005 at 03:02 PM
-- Server version: 4.1.10
-- PHP Version: 4.3.11
-- 
-- Database: `unitedforpeace`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `calendar_recur`
-- 

CREATE TABLE IF NOT EXISTS `calendar_recur` (
  `id` int(6) NOT NULL default '0',
  `name` varchar(12) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `calendar_recur`
-- 

INSERT INTO `calendar_recur` VALUES (0, 'Once');
INSERT INTO `calendar_recur` VALUES (1, 'Daily');
INSERT INTO `calendar_recur` VALUES (2, 'Weekly');
INSERT INTO `calendar_recur` VALUES (3, 'Monthly');
INSERT INTO `calendar_recur` VALUES (4, 'Yearly');

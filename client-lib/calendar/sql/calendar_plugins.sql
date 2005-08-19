-- phpMyAdmin SQL Dump
-- version 2.6.1-pl3
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generation Time: Aug 15, 2005 at 02:21 PM
-- Server version: 4.1.10
-- PHP Version: 4.3.11
-- 
-- Database: `unitedforpeace`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `userdata_plugins`
-- 

CREATE TABLE IF NOT EXISTS `userdata_plugins` (
  `id` int(11) NOT NULL auto_increment,
  `instance_id` int(11) NOT NULL default '0',
  `namespace` varchar(64) NOT NULL default '',
  `action` varchar(64) NOT NULL default '',
  `options` text,
  `active` tinyint(1) NOT NULL default '0',
  `priority` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=58 ;

-- 
-- Dumping data for table `userdata_plugins`
-- 

INSERT INTO `userdata_plugins` (instance_id, namespace, action, active, priority) VALUES (50, 'AMP', 'Save', 1, 2);
INSERT INTO `userdata_plugins` (instance_id, namespace, action, active, priority) VALUES (50, 'AMP', 'Read', 1, 1);
INSERT INTO `userdata_plugins` (instance_id, namespace, action, active, priority) VALUES (50, 'AMPCalendar', 'Save', 1, 4);
INSERT INTO `userdata_plugins` (instance_id, namespace, action, active, priority) VALUES (50, 'AMPCalendar', 'Read', 1, 3);

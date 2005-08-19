-- phpMyAdmin SQL Dump
-- version 2.6.1-pl3
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generation Time: Aug 15, 2005 at 05:03 PM
-- Server version: 4.1.10
-- PHP Version: 4.3.11
-- 
-- Database: `unitedforpeace`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `calendar`
-- 

DROP TABLE IF EXISTS `calendar`;
CREATE TABLE IF NOT EXISTS `calendar` (
  `id` int(11) NOT NULL auto_increment,
  `event` text,
  `shortdesc` text,
  `fulldesc` text,
  `contact1` text,
  `email1` text,
  `date` date default NULL,
  `location` text,
  `phone1` text,
  `areaID` int(3) default NULL,
  `datestamp` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `time` text,
  `endtime` text,
  `fname2` text,
  `lname2` text,
  `phone2` text,
  `email2` text,
  `org` text,
  `url` text,
  `typeid` int(2) default NULL,
  `publish` tinyint(4) default '0',
  `lcity` text,
  `lstate` text,
  `lcountry` text,
  `laddress` text,
  `lzip` text,
  `city2` text,
  `state2` text,
  `country2` text,
  `address2` text,
  `zip2` text,
  `endorse` text,
  `organization2` text,
  `cost` text,
  `repeat` tinyint(4) default '0',
  `recurring_options` int(11) NOT NULL default '0',
  `recurring_freq` int(11) NOT NULL default '0',
  `recurring_parent` int(11) NOT NULL default '0',
  `student` tinyint(4) default NULL,
  `fpevent` tinyint(4) default NULL,
  `fporder` int(11) default NULL,
  `enddate` text NOT NULL,
  `lat` float default NULL,
  `lon` float default NULL,
  `modin` int(11) default NULL,
  `uid` int(11) default NULL,
  `region` int(11) NOT NULL default '0',
  `section` int(11) NOT NULL default '0',
  `recurring_description` text,
  `ex_check` char(3) default NULL,
  `registration_modin` int(8) default NULL,
  PRIMARY KEY  (`id`),
  KEY `publish` (`publish`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=14638 ;

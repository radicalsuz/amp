-- phpMyAdmin SQL Dump
-- version 2.6.2
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generation Time: Dec 13, 2005 at 05:39 PM
-- Server version: 4.1.11
-- PHP Version: 4.3.10-15
-- 
-- Database: `forestethics`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `g2_AccessMap`
-- 

CREATE TABLE IF NOT EXISTS `g2_AccessMap` (
  `g_accessListId` int(11) NOT NULL default '0',
  `g_userId` int(11) default NULL,
  `g_groupId` int(11) default NULL,
  `g_permission` int(11) NOT NULL default '0',
  UNIQUE KEY `g_accessListId` (`g_accessListId`,`g_userId`,`g_groupId`),
  KEY `g2_AccessMap_83732` (`g_accessListId`),
  KEY `g2_AccessMap_69068` (`g_userId`),
  KEY `g2_AccessMap_89328` (`g_groupId`),
  KEY `g2_AccessMap_18058` (`g_permission`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `g2_AccessMap`
-- 

REPLACE INTO `g2_AccessMap` VALUES (8, 0, 4, 7);
REPLACE INTO `g2_AccessMap` VALUES (9, 0, 4, 7);
REPLACE INTO `g2_AccessMap` VALUES (9, 0, 3, 2147483647);
REPLACE INTO `g2_AccessMap` VALUES (11, 0, 3, 4);
REPLACE INTO `g2_AccessMap` VALUES (13, 0, 3, 4);

-- --------------------------------------------------------

-- 
-- Table structure for table `g2_AccessSubscriberMap`
-- 

CREATE TABLE IF NOT EXISTS `g2_AccessSubscriberMap` (
  `g_itemId` int(11) NOT NULL default '0',
  `g_accessListId` int(11) NOT NULL default '0',
  PRIMARY KEY  (`g_itemId`),
  KEY `g2_AccessSubscriberMap_83732` (`g_accessListId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `g2_AccessSubscriberMap`
-- 

REPLACE INTO `g2_AccessSubscriberMap` VALUES (1, 0);
REPLACE INTO `g2_AccessSubscriberMap` VALUES (2, 0);
REPLACE INTO `g2_AccessSubscriberMap` VALUES (3, 0);
REPLACE INTO `g2_AccessSubscriberMap` VALUES (4, 0);
REPLACE INTO `g2_AccessSubscriberMap` VALUES (5, 0);
REPLACE INTO `g2_AccessSubscriberMap` VALUES (6, 0);
REPLACE INTO `g2_AccessSubscriberMap` VALUES (7, 9);
REPLACE INTO `g2_AccessSubscriberMap` VALUES (10, 11);
REPLACE INTO `g2_AccessSubscriberMap` VALUES (12, 13);

-- --------------------------------------------------------

-- 
-- Table structure for table `g2_AlbumItem`
-- 

CREATE TABLE IF NOT EXISTS `g2_AlbumItem` (
  `g_id` int(11) NOT NULL default '0',
  `g_theme` varchar(32) default NULL,
  `g_orderBy` varchar(128) default NULL,
  `g_orderDirection` varchar(32) default NULL,
  PRIMARY KEY  (`g_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `g2_AlbumItem`
-- 

REPLACE INTO `g2_AlbumItem` VALUES (7, NULL, NULL, 'asc');

-- --------------------------------------------------------

-- 
-- Table structure for table `g2_AnimationItem`
-- 

CREATE TABLE IF NOT EXISTS `g2_AnimationItem` (
  `g_id` int(11) NOT NULL default '0',
  `g_width` int(11) default NULL,
  `g_height` int(11) default NULL,
  PRIMARY KEY  (`g_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `g2_AnimationItem`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `g2_ChildEntity`
-- 

CREATE TABLE IF NOT EXISTS `g2_ChildEntity` (
  `g_id` int(11) NOT NULL default '0',
  `g_parentId` int(11) NOT NULL default '0',
  PRIMARY KEY  (`g_id`),
  KEY `g2_ChildEntity_52718` (`g_parentId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `g2_ChildEntity`
-- 

REPLACE INTO `g2_ChildEntity` VALUES (7, 0);
REPLACE INTO `g2_ChildEntity` VALUES (10, 0);
REPLACE INTO `g2_ChildEntity` VALUES (12, 0);

-- --------------------------------------------------------

-- 
-- Table structure for table `g2_Comment`
-- 

CREATE TABLE IF NOT EXISTS `g2_Comment` (
  `g_id` int(11) NOT NULL default '0',
  `g_commenterId` int(11) NOT NULL default '0',
  `g_host` varchar(128) NOT NULL default '',
  `g_subject` varchar(128) default NULL,
  `g_comment` text,
  `g_date` int(11) NOT NULL default '0',
  PRIMARY KEY  (`g_id`),
  KEY `g_date` (`g_date`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `g2_Comment`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `g2_CustomFieldMap`
-- 

CREATE TABLE IF NOT EXISTS `g2_CustomFieldMap` (
  `g_itemId` int(11) NOT NULL default '0',
  `g_field` varchar(128) NOT NULL default '',
  `g_value` varchar(255) default NULL,
  `g_setId` int(11) default NULL,
  `g_setType` int(11) default NULL,
  KEY `g_itemId` (`g_itemId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `g2_CustomFieldMap`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `g2_DataItem`
-- 

CREATE TABLE IF NOT EXISTS `g2_DataItem` (
  `g_id` int(11) NOT NULL default '0',
  `g_mimeType` varchar(128) default NULL,
  `g_size` int(11) default NULL,
  PRIMARY KEY  (`g_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `g2_DataItem`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `g2_Derivative`
-- 

CREATE TABLE IF NOT EXISTS `g2_Derivative` (
  `g_id` int(11) NOT NULL default '0',
  `g_derivativeSourceId` int(11) NOT NULL default '0',
  `g_derivativeOperations` varchar(255) default NULL,
  `g_derivativeOrder` int(11) NOT NULL default '0',
  `g_derivativeSize` int(11) default NULL,
  `g_derivativeType` int(11) NOT NULL default '0',
  `g_mimeType` varchar(128) NOT NULL default '',
  `g_postFilterOperations` varchar(255) default NULL,
  `g_isBroken` int(1) default NULL,
  PRIMARY KEY  (`g_id`),
  KEY `g2_Derivative_85338` (`g_derivativeSourceId`),
  KEY `g2_Derivative_25243` (`g_derivativeOrder`),
  KEY `g2_Derivative_97216` (`g_derivativeType`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `g2_Derivative`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `g2_DerivativeImage`
-- 

CREATE TABLE IF NOT EXISTS `g2_DerivativeImage` (
  `g_id` int(11) NOT NULL default '0',
  `g_width` int(11) default NULL,
  `g_height` int(11) default NULL,
  PRIMARY KEY  (`g_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `g2_DerivativeImage`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `g2_DerivativePrefsMap`
-- 

CREATE TABLE IF NOT EXISTS `g2_DerivativePrefsMap` (
  `g_itemId` int(11) default NULL,
  `g_order` int(11) default NULL,
  `g_derivativeType` int(11) default NULL,
  `g_derivativeOperations` varchar(255) default NULL,
  KEY `g2_DerivativePrefsMap_75985` (`g_itemId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `g2_DerivativePrefsMap`
-- 

REPLACE INTO `g2_DerivativePrefsMap` VALUES (7, 0, 1, 'thumbnail|150');
REPLACE INTO `g2_DerivativePrefsMap` VALUES (7, 0, 2, 'scale|640');

-- --------------------------------------------------------

-- 
-- Table structure for table `g2_DescendentCountsMap`
-- 

CREATE TABLE IF NOT EXISTS `g2_DescendentCountsMap` (
  `g_userId` int(11) NOT NULL default '0',
  `g_itemId` int(11) NOT NULL default '0',
  `g_descendentCount` int(11) NOT NULL default '0',
  PRIMARY KEY  (`g_userId`,`g_itemId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `g2_DescendentCountsMap`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `g2_Entity`
-- 

CREATE TABLE IF NOT EXISTS `g2_Entity` (
  `g_id` int(11) NOT NULL default '0',
  `g_creationTimestamp` int(11) NOT NULL default '0',
  `g_isLinkable` int(1) NOT NULL default '0',
  `g_linkId` int(11) default NULL,
  `g_modificationTimestamp` int(11) NOT NULL default '0',
  `g_serialNumber` int(11) NOT NULL default '0',
  `g_entityType` varchar(32) NOT NULL default '',
  `g_onLoadHandlers` varchar(128) default NULL,
  PRIMARY KEY  (`g_id`),
  KEY `g2_Entity_76255` (`g_creationTimestamp`),
  KEY `g2_Entity_35978` (`g_isLinkable`),
  KEY `g2_Entity_63025` (`g_modificationTimestamp`),
  KEY `g2_Entity_60702` (`g_serialNumber`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `g2_Entity`
-- 

REPLACE INTO `g2_Entity` VALUES (1, 1134501525, 0, NULL, 1134501525, 1, 'GalleryEntity', NULL);
REPLACE INTO `g2_Entity` VALUES (2, 1134501526, 0, NULL, 1134501526, 1, 'GalleryGroup', NULL);
REPLACE INTO `g2_Entity` VALUES (3, 1134501526, 0, NULL, 1134501526, 1, 'GalleryGroup', NULL);
REPLACE INTO `g2_Entity` VALUES (4, 1134501526, 0, NULL, 1134501526, 1, 'GalleryGroup', NULL);
REPLACE INTO `g2_Entity` VALUES (5, 1134501526, 0, NULL, 1134501526, 1, 'GalleryUser', NULL);
REPLACE INTO `g2_Entity` VALUES (6, 1134501526, 0, NULL, 1134501526, 1, 'GalleryUser', NULL);
REPLACE INTO `g2_Entity` VALUES (7, 1134501527, 0, NULL, 1134501527, 1, 'GalleryAlbumItem', NULL);
REPLACE INTO `g2_Entity` VALUES (10, 1134501614, 0, NULL, 1134501615, 6, 'ThumbnailImage', NULL);
REPLACE INTO `g2_Entity` VALUES (12, 1134501615, 0, NULL, 1134501615, 5, 'ThumbnailImage', NULL);

-- --------------------------------------------------------

-- 
-- Table structure for table `g2_ExifPropertiesMap`
-- 

CREATE TABLE IF NOT EXISTS `g2_ExifPropertiesMap` (
  `g_property` varchar(128) default NULL,
  `g_viewMode` int(11) default NULL,
  `g_sequence` int(11) default NULL,
  UNIQUE KEY `g_property` (`g_property`,`g_viewMode`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `g2_ExifPropertiesMap`
-- 

REPLACE INTO `g2_ExifPropertiesMap` VALUES ('Make', 1, 0);
REPLACE INTO `g2_ExifPropertiesMap` VALUES ('Model', 1, 1);
REPLACE INTO `g2_ExifPropertiesMap` VALUES ('ApertureValue', 1, 2);
REPLACE INTO `g2_ExifPropertiesMap` VALUES ('ColorSpace', 1, 3);
REPLACE INTO `g2_ExifPropertiesMap` VALUES ('ExposureBiasValue', 1, 4);
REPLACE INTO `g2_ExifPropertiesMap` VALUES ('ExposureProgram', 1, 5);
REPLACE INTO `g2_ExifPropertiesMap` VALUES ('Flash', 1, 6);
REPLACE INTO `g2_ExifPropertiesMap` VALUES ('FocalLength', 1, 7);
REPLACE INTO `g2_ExifPropertiesMap` VALUES ('ISO', 1, 8);
REPLACE INTO `g2_ExifPropertiesMap` VALUES ('MeteringMode', 1, 9);
REPLACE INTO `g2_ExifPropertiesMap` VALUES ('ShutterSpeedValue', 1, 10);
REPLACE INTO `g2_ExifPropertiesMap` VALUES ('DateTime', 1, 11);
REPLACE INTO `g2_ExifPropertiesMap` VALUES ('IPTC/Caption', 1, 12);
REPLACE INTO `g2_ExifPropertiesMap` VALUES ('IPTC/CopyrightNotice', 1, 13);
REPLACE INTO `g2_ExifPropertiesMap` VALUES ('Make', 2, 0);
REPLACE INTO `g2_ExifPropertiesMap` VALUES ('Model', 2, 1);
REPLACE INTO `g2_ExifPropertiesMap` VALUES ('ApertureValue', 2, 2);
REPLACE INTO `g2_ExifPropertiesMap` VALUES ('ColorSpace', 2, 3);
REPLACE INTO `g2_ExifPropertiesMap` VALUES ('ExposureBiasValue', 2, 4);
REPLACE INTO `g2_ExifPropertiesMap` VALUES ('ExposureProgram', 2, 5);
REPLACE INTO `g2_ExifPropertiesMap` VALUES ('Flash', 2, 6);
REPLACE INTO `g2_ExifPropertiesMap` VALUES ('FocalLength', 2, 7);
REPLACE INTO `g2_ExifPropertiesMap` VALUES ('ISO', 2, 8);
REPLACE INTO `g2_ExifPropertiesMap` VALUES ('MeteringMode', 2, 9);
REPLACE INTO `g2_ExifPropertiesMap` VALUES ('ShutterSpeedValue', 2, 10);
REPLACE INTO `g2_ExifPropertiesMap` VALUES ('DateTime', 2, 11);
REPLACE INTO `g2_ExifPropertiesMap` VALUES ('IPTC/Caption', 2, 12);
REPLACE INTO `g2_ExifPropertiesMap` VALUES ('IPTC/CopyrightNotice', 2, 13);
REPLACE INTO `g2_ExifPropertiesMap` VALUES ('IPTC/Keywords', 2, 14);
REPLACE INTO `g2_ExifPropertiesMap` VALUES ('ImageType', 2, 15);
REPLACE INTO `g2_ExifPropertiesMap` VALUES ('Orientation', 2, 16);
REPLACE INTO `g2_ExifPropertiesMap` VALUES ('PhotoshopSettings', 2, 17);
REPLACE INTO `g2_ExifPropertiesMap` VALUES ('ResolutionUnit', 2, 18);
REPLACE INTO `g2_ExifPropertiesMap` VALUES ('xResolution', 2, 19);
REPLACE INTO `g2_ExifPropertiesMap` VALUES ('yResolution', 2, 20);
REPLACE INTO `g2_ExifPropertiesMap` VALUES ('Compression', 2, 21);
REPLACE INTO `g2_ExifPropertiesMap` VALUES ('BrightnessValue', 2, 22);
REPLACE INTO `g2_ExifPropertiesMap` VALUES ('Contrast', 2, 23);
REPLACE INTO `g2_ExifPropertiesMap` VALUES ('ExposureMode', 2, 24);
REPLACE INTO `g2_ExifPropertiesMap` VALUES ('FlashEnergy', 2, 25);
REPLACE INTO `g2_ExifPropertiesMap` VALUES ('Saturation', 2, 26);
REPLACE INTO `g2_ExifPropertiesMap` VALUES ('SceneType', 2, 27);
REPLACE INTO `g2_ExifPropertiesMap` VALUES ('Sharpness', 2, 28);
REPLACE INTO `g2_ExifPropertiesMap` VALUES ('SubjectDistance', 2, 29);

-- --------------------------------------------------------

-- 
-- Table structure for table `g2_ExternalIdMap`
-- 

CREATE TABLE IF NOT EXISTS `g2_ExternalIdMap` (
  `g_externalId` varchar(128) NOT NULL default '',
  `g_entityType` varchar(32) NOT NULL default '',
  `g_entityId` int(11) NOT NULL default '0',
  PRIMARY KEY  (`g_externalId`,`g_entityType`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `g2_ExternalIdMap`
-- 

REPLACE INTO `g2_ExternalIdMap` VALUES ('3', 'GalleryUser', 6);

-- --------------------------------------------------------

-- 
-- Table structure for table `g2_FactoryMap`
-- 

CREATE TABLE IF NOT EXISTS `g2_FactoryMap` (
  `g_classType` varchar(128) default NULL,
  `g_className` varchar(128) default NULL,
  `g_implId` varchar(128) default NULL,
  `g_implPath` varchar(128) default NULL,
  `g_implModuleId` varchar(128) default NULL,
  `g_hints` varchar(255) default NULL,
  `g_orderWeight` varchar(255) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `g2_FactoryMap`
-- 

REPLACE INTO `g2_FactoryMap` VALUES ('MaintenanceTask', 'OptimizeDatabaseTask', 'OptimizeDatabaseTask', 'modules/core/classes/OptimizeDatabaseTask.class', 'core', 'N;', '4');
REPLACE INTO `g2_FactoryMap` VALUES ('ItemAddOption', 'CreateThumbnailOption', 'CreateThumbnailOption', 'modules/core/CreateThumbnailOption.inc', 'core', 'N;', '4');
REPLACE INTO `g2_FactoryMap` VALUES ('ItemAddPlugin', 'ItemAddFromWeb', 'ItemAddFromWeb', 'modules/core/ItemAddFromWeb.inc', 'core', 'N;', '4');
REPLACE INTO `g2_FactoryMap` VALUES ('ItemAddPlugin', 'ItemAddFromServer', 'ItemAddFromServer', 'modules/core/ItemAddFromServer.inc', 'core', 'N;', '3');
REPLACE INTO `g2_FactoryMap` VALUES ('ItemAddPlugin', 'ItemAddFromBrowser', 'ItemAddFromBrowser', 'modules/core/ItemAddFromBrowser.inc', 'core', 'N;', '2');
REPLACE INTO `g2_FactoryMap` VALUES ('ItemEditPlugin', 'ItemEditPhotoThumbnail', 'ItemEditPhotoThumbnail', 'modules/core/ItemEditPhotoThumbnail.inc', 'core', 'N;', '4');
REPLACE INTO `g2_FactoryMap` VALUES ('ItemEditPlugin', 'ItemEditRotateAndScalePhoto', 'ItemEditRotateAndScalePhoto', 'modules/core/ItemEditRotateAndScalePhoto.inc', 'core', 'N;', '3');
REPLACE INTO `g2_FactoryMap` VALUES ('ItemEditPlugin', 'ItemEditPhoto', 'ItemEditPhoto', 'modules/core/ItemEditPhoto.inc', 'core', 'N;', '2');
REPLACE INTO `g2_FactoryMap` VALUES ('ItemEditPlugin', 'ItemEditTheme', 'ItemEditTheme', 'modules/core/ItemEditTheme.inc', 'core', 'N;', '3');
REPLACE INTO `g2_FactoryMap` VALUES ('ItemEditPlugin', 'ItemEditAlbum', 'ItemEditAlbum', 'modules/core/ItemEditAlbum.inc', 'core', 'N;', '2');
REPLACE INTO `g2_FactoryMap` VALUES ('ItemEditPlugin', 'ItemEditMovie', 'ItemEditMovie', 'modules/core/ItemEditMovie.inc', 'core', 'N;', '2');
REPLACE INTO `g2_FactoryMap` VALUES ('ItemEditPlugin', 'ItemEditItem', 'ItemEditItem', 'modules/core/ItemEditItem.inc', 'core', 'N;', '1');
REPLACE INTO `g2_FactoryMap` VALUES ('ItemEditPlugin', 'ItemEditAnimation', 'ItemEditAnimation', 'modules/core/ItemEditAnimation.inc', 'core', 'N;', '2');
REPLACE INTO `g2_FactoryMap` VALUES ('GalleryItem', 'GalleryUnknownItem', 'GalleryUnknownItem', 'modules/core/classes/GalleryUnknownItem.class', 'core', 'a:1:{i:0;s:1:"*";}', '4');
REPLACE INTO `g2_FactoryMap` VALUES ('GallerySearchInterface_1_0', 'GalleryCoreSearch', 'GalleryCoreSearch', 'modules/core/classes/GalleryCoreSearch.class', 'core', 'N;', '4');
REPLACE INTO `g2_FactoryMap` VALUES ('GalleryItem', 'GalleryAnimationItem', 'GalleryAnimationItem', 'modules/core/classes/GalleryAnimationItem.class', 'core', 'a:2:{i:0;s:22:"application/x-director";i:1;s:29:"application/x-shockwave-flash";}', '4');
REPLACE INTO `g2_FactoryMap` VALUES ('GalleryItem', 'GalleryMovieItem', 'GalleryMovieItem', 'modules/core/classes/GalleryMovieItem.class', 'core', 'a:5:{i:0;s:15:"video/x-msvideo";i:1;s:15:"video/quicktime";i:2;s:10:"video/mpeg";i:3;s:14:"video/x-ms-asf";i:4;s:14:"video/x-ms-wmv";}', '4');
REPLACE INTO `g2_FactoryMap` VALUES ('GalleryItem', 'GalleryPhotoItem', 'GalleryPhotoItem', 'modules/core/classes/GalleryPhotoItem.class', 'core', 'a:1:{i:0;s:7:"image/*";}', '4');
REPLACE INTO `g2_FactoryMap` VALUES ('GalleryEntity', 'GalleryUnknownItem', 'GalleryUnknownItem', 'modules/core/classes/GalleryUnknownItem.class', 'core', 'N;', '4');
REPLACE INTO `g2_FactoryMap` VALUES ('GalleryEntity', 'GalleryPhotoItem', 'GalleryPhotoItem', 'modules/core/classes/GalleryPhotoItem.class', 'core', 'N;', '4');
REPLACE INTO `g2_FactoryMap` VALUES ('GalleryEntity', 'GalleryAnimationItem', 'GalleryAnimationItem', 'modules/core/classes/GalleryAnimationItem.class', 'core', 'N;', '4');
REPLACE INTO `g2_FactoryMap` VALUES ('GalleryEntity', 'GalleryMovieItem', 'GalleryMovieItem', 'modules/core/classes/GalleryMovieItem.class', 'core', 'N;', '4');
REPLACE INTO `g2_FactoryMap` VALUES ('GalleryDerivative', 'GalleryDerivativeImage', 'GalleryDerivativeImage', 'modules/core/classes/GalleryDerivativeImage.class', 'core', 'a:1:{i:0;s:1:"*";}', '4');
REPLACE INTO `g2_FactoryMap` VALUES ('GalleryEntity', 'GalleryDerivativeImage', 'GalleryDerivativeImage', 'modules/core/classes/GalleryDerivativeImage.class', 'core', 'N;', '4');
REPLACE INTO `g2_FactoryMap` VALUES ('GalleryEntity', 'GalleryDerivative', 'GalleryDerivative', 'modules/core/classes/GalleryDerivative.class', 'core', 'N;', '4');
REPLACE INTO `g2_FactoryMap` VALUES ('GalleryEntity', 'GalleryGroup', 'GalleryGroup', 'modules/core/classes/GalleryGroup.class', 'core', 'N;', '4');
REPLACE INTO `g2_FactoryMap` VALUES ('GalleryEntity', 'GalleryUser', 'GalleryUser', 'modules/core/classes/GalleryUser.class', 'core', 'N;', '4');
REPLACE INTO `g2_FactoryMap` VALUES ('GalleryEntity', 'GalleryAlbumItem', 'GalleryAlbumItem', 'modules/core/classes/GalleryAlbumItem.class', 'core', 'N;', '4');
REPLACE INTO `g2_FactoryMap` VALUES ('GalleryEntity', 'GalleryEntity', 'GalleryEntity', 'modules/core/classes/GalleryEntity.class', 'core', 'N;', '4');
REPLACE INTO `g2_FactoryMap` VALUES ('GalleryEntity', 'GalleryChildEntity', 'GalleryChildEntity', 'modules/core/classes/GalleryChildEntity.class', 'core', 'N;', '4');
REPLACE INTO `g2_FactoryMap` VALUES ('MaintenanceTask', 'FlushTemplatesTask', 'FlushTemplatesTask', 'modules/core/classes/FlushTemplatesTask.class', 'core', 'N;', '4');
REPLACE INTO `g2_FactoryMap` VALUES ('MaintenanceTask', 'FlushDatabaseCacheTask', 'FlushDatabaseCacheTask', 'modules/core/classes/FlushDatabaseCacheTask.class', 'core', 'N;', '4');
REPLACE INTO `g2_FactoryMap` VALUES ('MaintenanceTask', 'BuildDerivativesTask', 'BuildDerivativesTask', 'modules/core/classes/BuildDerivativesTask.class', 'core', 'N;', '4');
REPLACE INTO `g2_FactoryMap` VALUES ('MaintenanceTask', 'ResetViewCountsTask', 'ResetViewCountsTask', 'modules/core/classes/ResetViewCountsTask.class', 'core', 'N;', '4');
REPLACE INTO `g2_FactoryMap` VALUES ('MaintenanceTask', 'SystemInfoTask', 'SystemInfoTask', 'modules/core/classes/SystemInfoTask.class', 'core', 'N;', '4');
REPLACE INTO `g2_FactoryMap` VALUES ('MaintenanceTask', 'SetOriginationTimestampTask', 'SetOriginationTimestampTask', 'modules/core/classes/SetOriginationTimestampTask.class', 'core', 'N;', '4');
REPLACE INTO `g2_FactoryMap` VALUES ('GalleryToolkit', 'ArchiveExtractToolkit', 'ArchiveUpload', 'modules/archiveupload/classes/ArchiveExtractToolkit.class', 'archiveupload', 'N;', '5');
REPLACE INTO `g2_FactoryMap` VALUES ('ExifInterface_1_0', 'ExifExtractor', 'Exif', 'modules/exif/classes/ExifExtractor.class', 'exif', 'N;', '5');
REPLACE INTO `g2_FactoryMap` VALUES ('GalleryToolkit', 'ExifToolkit', 'Exif', 'modules/exif/classes/ExifToolkit.class', 'exif', 'N;', '5');
REPLACE INTO `g2_FactoryMap` VALUES ('ItemAddOption', 'ExifDescriptionOption', 'ExifDescriptionOption', 'modules/exif/ExifDescriptionOption.inc', 'exif', 'N;', '5');
REPLACE INTO `g2_FactoryMap` VALUES ('ItemEditOption', 'ImageBlockOption', 'ImageBlockOption', 'modules/imageblock/ImageBlockOption.inc', 'imageblock', 'a:1:{i:0;s:13:"itemeditalbum";}', '5');
REPLACE INTO `g2_FactoryMap` VALUES ('SlideshowInterface_1_0', 'SlideshowImpl', 'Slideshow', 'modules/slideshow/classes/SlideshowImpl.class', 'slideshow', 'N;', '5');
REPLACE INTO `g2_FactoryMap` VALUES ('GalleryEntity', 'ThumbnailImage', 'ThumbnailImage', 'modules/thumbnail/classes/ThumbnailImage.class', 'thumbnail', 'N;', '5');
REPLACE INTO `g2_FactoryMap` VALUES ('GalleryToolkit', 'ThumbnailToolkit', 'Thumbnail', 'modules/thumbnail/classes/ThumbnailToolkit.class', 'thumbnail', 'N;', '5');
REPLACE INTO `g2_FactoryMap` VALUES ('ItemEditOption', 'CustomThumbnailOption', 'CustomThumbnailOption', 'modules/thumbnail/CustomThumbnailOption.inc', 'thumbnail', 'a:1:{i:0;s:12:"itemedititem";}', '5');
REPLACE INTO `g2_FactoryMap` VALUES ('ItemAddPlugin', 'ItemAddUploadApplet', 'ItemAddUploadApplet', 'modules/uploadapplet/ItemAddUploadApplet.inc', 'uploadapplet', 'N;', '5');
REPLACE INTO `g2_FactoryMap` VALUES ('GalleryToolkit', 'GdToolkit', 'Gd', 'modules/gd/classes/GdToolkit.class', 'gd', 'N;', '5');

-- --------------------------------------------------------

-- 
-- Table structure for table `g2_FileSystemEntity`
-- 

CREATE TABLE IF NOT EXISTS `g2_FileSystemEntity` (
  `g_id` int(11) NOT NULL default '0',
  `g_pathComponent` varchar(128) default NULL,
  PRIMARY KEY  (`g_id`),
  KEY `g2_FileSystemEntity_3406` (`g_pathComponent`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `g2_FileSystemEntity`
-- 

REPLACE INTO `g2_FileSystemEntity` VALUES (7, NULL);

-- --------------------------------------------------------

-- 
-- Table structure for table `g2_G1MigrateMap`
-- 

CREATE TABLE IF NOT EXISTS `g2_G1MigrateMap` (
  `g_itemId` int(11) NOT NULL default '0',
  `g_g1album` varchar(128) NOT NULL default '',
  `g_g1item` varchar(128) default NULL,
  PRIMARY KEY  (`g_itemId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `g2_G1MigrateMap`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `g2_Group`
-- 

CREATE TABLE IF NOT EXISTS `g2_Group` (
  `g_id` int(11) NOT NULL default '0',
  `g_groupType` int(11) NOT NULL default '0',
  `g_groupName` varchar(128) default NULL,
  PRIMARY KEY  (`g_id`),
  UNIQUE KEY `g_groupName` (`g_groupName`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `g2_Group`
-- 

REPLACE INTO `g2_Group` VALUES (2, 2, 'Registered Users');
REPLACE INTO `g2_Group` VALUES (3, 3, 'Site Admins');
REPLACE INTO `g2_Group` VALUES (4, 4, 'Everybody');

-- --------------------------------------------------------

-- 
-- Table structure for table `g2_ImageBlockCacheMap`
-- 

CREATE TABLE IF NOT EXISTS `g2_ImageBlockCacheMap` (
  `g_userId` int(11) NOT NULL default '0',
  `g_itemType` int(11) NOT NULL default '0',
  `g_itemTimestamp` int(11) NOT NULL default '0',
  `g_itemId` int(11) NOT NULL default '0',
  KEY `g_userId` (`g_userId`,`g_itemType`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `g2_ImageBlockCacheMap`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `g2_ImageBlockDisabledMap`
-- 

CREATE TABLE IF NOT EXISTS `g2_ImageBlockDisabledMap` (
  `g_itemId` int(11) NOT NULL default '0',
  PRIMARY KEY  (`g_itemId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `g2_ImageBlockDisabledMap`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `g2_Item`
-- 

CREATE TABLE IF NOT EXISTS `g2_Item` (
  `g_id` int(11) NOT NULL default '0',
  `g_canContainChildren` int(1) NOT NULL default '0',
  `g_description` text,
  `g_keywords` varchar(255) default NULL,
  `g_ownerId` int(11) NOT NULL default '0',
  `g_summary` varchar(255) default NULL,
  `g_title` varchar(128) default NULL,
  `g_viewedSinceTimestamp` int(11) NOT NULL default '0',
  `g_originationTimestamp` int(11) NOT NULL default '0',
  PRIMARY KEY  (`g_id`),
  KEY `g2_Item_99070` (`g_keywords`),
  KEY `g2_Item_21573` (`g_ownerId`),
  KEY `g2_Item_54147` (`g_summary`),
  KEY `g2_Item_90059` (`g_title`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `g2_Item`
-- 

REPLACE INTO `g2_Item` VALUES (7, 1, 'This is the main page of your Gallery', NULL, 6, NULL, 'Gallery', 1134501527, 1134501527);

-- --------------------------------------------------------

-- 
-- Table structure for table `g2_ItemAttributesMap`
-- 

CREATE TABLE IF NOT EXISTS `g2_ItemAttributesMap` (
  `g_itemId` int(11) NOT NULL default '0',
  `g_viewCount` int(11) default NULL,
  `g_orderWeight` int(11) default NULL,
  `g_parentSequence` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`g_itemId`),
  KEY `g2_ItemAttributesMap_95270` (`g_parentSequence`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `g2_ItemAttributesMap`
-- 

REPLACE INTO `g2_ItemAttributesMap` VALUES (7, 48, 0, '');

-- --------------------------------------------------------

-- 
-- Table structure for table `g2_Lock`
-- 

CREATE TABLE IF NOT EXISTS `g2_Lock` (
  `g_lockId` int(11) default NULL,
  `g_readEntityId` int(11) default NULL,
  `g_writeEntityId` int(11) default NULL,
  `g_freshUntil` int(11) default NULL,
  `g_request` int(11) default NULL,
  KEY `g2_Lock_11039` (`g_lockId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `g2_Lock`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `g2_MaintenanceMap`
-- 

CREATE TABLE IF NOT EXISTS `g2_MaintenanceMap` (
  `g_runId` int(11) NOT NULL default '0',
  `g_taskId` varchar(128) NOT NULL default '',
  `g_timestamp` int(11) default NULL,
  `g_success` int(1) default NULL,
  `g_details` text,
  PRIMARY KEY  (`g_runId`),
  KEY `g2_MaintenanceMap_21687` (`g_taskId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `g2_MaintenanceMap`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `g2_MimeTypeMap`
-- 

CREATE TABLE IF NOT EXISTS `g2_MimeTypeMap` (
  `g_extension` varchar(32) NOT NULL default '',
  `g_mimeType` varchar(32) NOT NULL default '',
  `g_viewable` int(1) default NULL,
  PRIMARY KEY  (`g_extension`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `g2_MimeTypeMap`
-- 

REPLACE INTO `g2_MimeTypeMap` VALUES ('z', 'application/x-compress', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('ai', 'application/postscript', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('aif', 'audio/x-aiff', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('aifc', 'audio/x-aiff', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('aiff', 'audio/x-aiff', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('asc', 'text/plain', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('au', 'audio/basic', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('avi', 'video/x-msvideo', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('bcpio', 'application/x-bcpio', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('bin', 'application/octet-stream', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('bmp', 'image/bmp', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('cdf', 'application/x-netcdf', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('class', 'application/octet-stream', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('cpio', 'application/x-cpio', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('cpt', 'application/mac-compactpro', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('csh', 'application/x-csh', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('css', 'text/css', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('dcr', 'application/x-director', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('dir', 'application/x-director', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('djv', 'image/vnd.djvu', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('djvu', 'image/vnd.djvu', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('dll', 'application/octet-stream', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('dms', 'application/octet-stream', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('doc', 'application/msword', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('dvi', 'application/x-dvi', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('dxr', 'application/x-director', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('eps', 'application/postscript', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('etx', 'text/x-setext', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('exe', 'application/octet-stream', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('ez', 'application/andrew-inset', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('gif', 'image/gif', 1);
REPLACE INTO `g2_MimeTypeMap` VALUES ('gtar', 'application/x-gtar', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('gz', 'application/x-gzip', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('hdf', 'application/x-hdf', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('hqx', 'application/mac-binhex40', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('htm', 'text/html', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('html', 'text/html', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('ice', 'x-conference/x-cooltalk', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('ief', 'image/ief', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('iges', 'model/iges', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('igs', 'model/iges', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('jpg', 'image/jpeg', 1);
REPLACE INTO `g2_MimeTypeMap` VALUES ('jpeg', 'image/jpeg', 1);
REPLACE INTO `g2_MimeTypeMap` VALUES ('jpe', 'image/jpeg', 1);
REPLACE INTO `g2_MimeTypeMap` VALUES ('js', 'application/x-javascript', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('kar', 'audio/midi', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('latex', 'application/x-latex', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('lha', 'application/octet-stream', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('lzh', 'application/octet-stream', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('m3u', 'audio/x-mpegurl', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('man', 'application/x-troff-man', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('me', 'application/x-troff-me', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('mesh', 'model/mesh', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('mid', 'audio/midi', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('midi', 'audio/midi', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('mif', 'application/vnd.mif', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('mov', 'video/quicktime', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('movie', 'video/x-sgi-movie', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('mp2', 'audio/mpeg', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('mp3', 'audio/mpeg', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('mpe', 'video/mpeg', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('mpeg', 'video/mpeg', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('mpg', 'video/mpeg', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('mpga', 'audio/mpeg', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('ms', 'application/x-troff-ms', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('msh', 'model/mesh', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('mxu', 'video/vnd.mpegurl', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('nc', 'application/x-netcdf', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('oda', 'application/oda', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('pbm', 'image/x-portable-bitmap', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('pdb', 'chemical/x-pdb', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('pdf', 'application/pdf', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('pgm', 'image/x-portable-graymap', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('pgn', 'application/x-chess-pgn', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('png', 'image/png', 1);
REPLACE INTO `g2_MimeTypeMap` VALUES ('pnm', 'image/x-portable-anymap', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('ppm', 'image/x-portable-pixmap', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('ppt', 'application/vnd.ms-powerpoint', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('ps', 'application/postscript', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('qt', 'video/quicktime', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('ra', 'audio/x-realaudio', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('ram', 'audio/x-pn-realaudio', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('ras', 'image/x-cmu-raster', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('rgb', 'image/x-rgb', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('rm', 'audio/x-pn-realaudio', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('roff', 'application/x-troff', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('rpm', 'audio/x-pn-realaudio-plugin', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('rtf', 'text/rtf', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('rtx', 'text/richtext', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('sgm', 'text/sgml', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('sgml', 'text/sgml', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('sh', 'application/x-sh', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('shar', 'application/x-shar', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('silo', 'model/mesh', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('sit', 'application/x-stuffit', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('skd', 'application/x-koan', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('skm', 'application/x-koan', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('skp', 'application/x-koan', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('skt', 'application/x-koan', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('smi', 'application/smil', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('smil', 'application/smil', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('snd', 'audio/basic', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('so', 'application/octet-stream', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('spl', 'application/x-futuresplash', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('src', 'application/x-wais-source', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('sv4cpio', 'application/x-sv4cpio', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('sv4crc', 'application/x-sv4crc', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('svg', 'image/svg+xml', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('swf', 'application/x-shockwave-flash', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('t', 'application/x-troff', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('tar', 'application/x-tar', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('tcl', 'application/x-tcl', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('tex', 'application/x-tex', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('texi', 'application/x-texinfo', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('texinfo', 'application/x-texinfo', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('tif', 'image/tiff', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('tiff', 'image/tiff', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('tr', 'application/x-troff', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('tsv', 'text/tab-separated-values', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('txt', 'text/plain', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('ustar', 'application/x-ustar', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('vcd', 'application/x-cdlink', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('vrml', 'model/vrml', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('vsd', 'application/vnd.visio', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('wav', 'audio/x-wav', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('wbmp', 'image/vnd.wap.wbmp', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('wbxml', 'application/vnd.wap.wbxml', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('wml', 'text/vnd.wap.wml', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('wmlc', 'application/vnd.wap.wmlc', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('wmls', 'text/vnd.wap.wmlscript', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('wmlsc', 'application/vnd.wap.wmlscriptc', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('wrl', 'model/vrml', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('xbm', 'image/x-xbitmap', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('xht', 'application/xhtml+xml', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('xhtml', 'application/xhtml+xml', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('xls', 'application/vnd.ms-excel', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('xml', 'text/xml', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('xpm', 'image/x-xpixmap', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('xsl', 'text/xml', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('xwd', 'image/x-xwindowdump', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('xyz', 'chemical/x-xyz', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('zip', 'application/zip', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('asf', 'video/x-ms-asf', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('wmv', 'video/x-ms-wmv', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('wma', 'audio/x-ms-wma', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('jp2', 'image/jp2', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('jpg2', 'image/jp2', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('jpf', 'image/jpx', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('jpx', 'image/jpx', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('mj2', 'video/mj2', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('mjp2', 'video/mj2', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('jpm', 'image/jpm', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('jpgm', 'image/jpgm', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('psd', 'application/photoshop', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('pcd', 'image/x-photo-cd', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('jpgcmyk', 'image/jpeg-cmyk', 0);
REPLACE INTO `g2_MimeTypeMap` VALUES ('tifcmyk', 'image/tiff-cmyk', 0);

-- --------------------------------------------------------

-- 
-- Table structure for table `g2_MovieItem`
-- 

CREATE TABLE IF NOT EXISTS `g2_MovieItem` (
  `g_id` int(11) NOT NULL default '0',
  `g_width` int(11) default NULL,
  `g_height` int(11) default NULL,
  `g_duration` int(11) default NULL,
  PRIMARY KEY  (`g_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `g2_MovieItem`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `g2_MultiLangItemMap`
-- 

CREATE TABLE IF NOT EXISTS `g2_MultiLangItemMap` (
  `g_itemId` int(11) NOT NULL default '0',
  `g_language` varchar(32) NOT NULL default '',
  `g_title` varchar(128) default NULL,
  `g_summary` varchar(255) default NULL,
  `g_description` text,
  PRIMARY KEY  (`g_itemId`,`g_language`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `g2_MultiLangItemMap`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `g2_PendingUser`
-- 

CREATE TABLE IF NOT EXISTS `g2_PendingUser` (
  `g_id` int(11) NOT NULL default '0',
  `g_userName` varchar(32) NOT NULL default '',
  `g_fullName` varchar(128) default NULL,
  `g_hashedPassword` varchar(128) default NULL,
  `g_email` varchar(128) default NULL,
  `g_language` varchar(128) default NULL,
  `g_registrationKey` varchar(32) default NULL,
  PRIMARY KEY  (`g_id`),
  UNIQUE KEY `g_userName` (`g_userName`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `g2_PendingUser`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `g2_PermissionSetMap`
-- 

CREATE TABLE IF NOT EXISTS `g2_PermissionSetMap` (
  `g_module` varchar(128) NOT NULL default '',
  `g_permission` varchar(128) NOT NULL default '',
  `g_description` varchar(255) default NULL,
  `g_bits` int(11) NOT NULL default '0',
  `g_flags` int(11) NOT NULL default '0',
  UNIQUE KEY `g_permission` (`g_permission`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `g2_PermissionSetMap`
-- 

REPLACE INTO `g2_PermissionSetMap` VALUES ('core', 'core.all', 'All access', 2147483647, 3);
REPLACE INTO `g2_PermissionSetMap` VALUES ('core', 'core.view', '[core] View item', 1, 0);
REPLACE INTO `g2_PermissionSetMap` VALUES ('core', 'core.viewResizes', '[core] View resized version(s)', 2, 0);
REPLACE INTO `g2_PermissionSetMap` VALUES ('core', 'core.viewSource', '[core] View original version', 4, 0);
REPLACE INTO `g2_PermissionSetMap` VALUES ('core', 'core.viewAll', '[core] View all versions', 7, 2);
REPLACE INTO `g2_PermissionSetMap` VALUES ('core', 'core.addAlbumItem', '[core] Add sub-album', 8, 4);
REPLACE INTO `g2_PermissionSetMap` VALUES ('core', 'core.addDataItem', '[core] Add sub-item', 16, 4);
REPLACE INTO `g2_PermissionSetMap` VALUES ('core', 'core.edit', '[core] Edit item', 32, 4);
REPLACE INTO `g2_PermissionSetMap` VALUES ('core', 'core.changePermissions', '[core] Change item permissions', 64, 4);
REPLACE INTO `g2_PermissionSetMap` VALUES ('core', 'core.delete', '[core] Delete item', 128, 4);

-- --------------------------------------------------------

-- 
-- Table structure for table `g2_PhotoItem`
-- 

CREATE TABLE IF NOT EXISTS `g2_PhotoItem` (
  `g_id` int(11) NOT NULL default '0',
  `g_width` int(11) default NULL,
  `g_height` int(11) default NULL,
  PRIMARY KEY  (`g_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `g2_PhotoItem`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `g2_PluginMap`
-- 

CREATE TABLE IF NOT EXISTS `g2_PluginMap` (
  `g_pluginType` varchar(32) NOT NULL default '',
  `g_pluginId` varchar(32) NOT NULL default '',
  `g_active` int(1) NOT NULL default '0',
  PRIMARY KEY  (`g_pluginType`,`g_pluginId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `g2_PluginMap`
-- 

REPLACE INTO `g2_PluginMap` VALUES ('theme', 'matrix', 1);
REPLACE INTO `g2_PluginMap` VALUES ('module', 'albumselect', 1);
REPLACE INTO `g2_PluginMap` VALUES ('module', 'archiveupload', 1);
REPLACE INTO `g2_PluginMap` VALUES ('module', 'exif', 1);
REPLACE INTO `g2_PluginMap` VALUES ('module', 'imageblock', 1);
REPLACE INTO `g2_PluginMap` VALUES ('module', 'rearrange', 1);
REPLACE INTO `g2_PluginMap` VALUES ('module', 'search', 1);
REPLACE INTO `g2_PluginMap` VALUES ('module', 'slideshow', 1);
REPLACE INTO `g2_PluginMap` VALUES ('module', 'slideshowapplet', 1);
REPLACE INTO `g2_PluginMap` VALUES ('module', 'thumbnail', 1);
REPLACE INTO `g2_PluginMap` VALUES ('module', 'uploadapplet', 1);
REPLACE INTO `g2_PluginMap` VALUES ('module', 'gd', 1);

-- --------------------------------------------------------

-- 
-- Table structure for table `g2_PluginParameterMap`
-- 

CREATE TABLE IF NOT EXISTS `g2_PluginParameterMap` (
  `g_pluginType` varchar(32) NOT NULL default '',
  `g_pluginId` varchar(32) NOT NULL default '',
  `g_itemId` int(11) NOT NULL default '0',
  `g_parameterName` varchar(128) NOT NULL default '',
  `g_parameterValue` text NOT NULL,
  UNIQUE KEY `g_pluginType` (`g_pluginType`,`g_pluginId`,`g_itemId`,`g_parameterName`),
  KEY `g2_PluginParameterMap_12808` (`g_pluginType`,`g_pluginId`,`g_itemId`),
  KEY `g2_PluginParameterMap_80596` (`g_pluginType`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `g2_PluginParameterMap`
-- 

REPLACE INTO `g2_PluginParameterMap` VALUES ('module', 'core', 0, 'permissions.directory', '755');
REPLACE INTO `g2_PluginParameterMap` VALUES ('module', 'core', 0, 'permissions.file', '644');
REPLACE INTO `g2_PluginParameterMap` VALUES ('module', 'core', 0, 'exec.expectedStatus', '0');
REPLACE INTO `g2_PluginParameterMap` VALUES ('module', 'core', 0, 'default.orderBy', 'orderWeight');
REPLACE INTO `g2_PluginParameterMap` VALUES ('module', 'core', 0, 'default.orderDirection', '1');
REPLACE INTO `g2_PluginParameterMap` VALUES ('module', 'core', 0, 'default.theme', 'matrix');
REPLACE INTO `g2_PluginParameterMap` VALUES ('module', 'core', 0, 'default.language', 'en_US');
REPLACE INTO `g2_PluginParameterMap` VALUES ('module', 'core', 0, 'default.newAlbumsUseDefaults', 'false');
REPLACE INTO `g2_PluginParameterMap` VALUES ('module', 'core', 0, 'language.selector', 'none');
REPLACE INTO `g2_PluginParameterMap` VALUES ('module', 'core', 0, 'session.lifetime', '788400000');
REPLACE INTO `g2_PluginParameterMap` VALUES ('module', 'core', 0, 'session.inactivityTimeout', '1209600');
REPLACE INTO `g2_PluginParameterMap` VALUES ('module', 'core', 0, 'misc.markup', 'bbcode');
REPLACE INTO `g2_PluginParameterMap` VALUES ('module', 'core', 0, 'lock.system', 'flock');
REPLACE INTO `g2_PluginParameterMap` VALUES ('module', 'core', 0, 'format.date', '%x');
REPLACE INTO `g2_PluginParameterMap` VALUES ('module', 'core', 0, 'format.time', '%X');
REPLACE INTO `g2_PluginParameterMap` VALUES ('module', 'core', 0, 'format.datetime', '%c');
REPLACE INTO `g2_PluginParameterMap` VALUES ('theme', 'matrix', 0, '_version', '1.0.0');
REPLACE INTO `g2_PluginParameterMap` VALUES ('theme', 'matrix', 0, '_requiredCoreApi', '6,5');
REPLACE INTO `g2_PluginParameterMap` VALUES ('theme', 'matrix', 0, '_requiredThemeApi', '2,1');
REPLACE INTO `g2_PluginParameterMap` VALUES ('theme', 'matrix', 0, 'rows', '3');
REPLACE INTO `g2_PluginParameterMap` VALUES ('theme', 'matrix', 0, 'columns', '3');
REPLACE INTO `g2_PluginParameterMap` VALUES ('theme', 'matrix', 0, 'showImageOwner', '0');
REPLACE INTO `g2_PluginParameterMap` VALUES ('theme', 'matrix', 0, 'showAlbumOwner', '0');
REPLACE INTO `g2_PluginParameterMap` VALUES ('theme', 'matrix', 0, 'showMicroThumbs', '0');
REPLACE INTO `g2_PluginParameterMap` VALUES ('theme', 'matrix', 0, 'sidebarBlocks', 'a:4:{i:0;a:2:{i:0;s:18:"search.SearchBlock";i:1;a:1:{s:16:"showAdvancedLink";b:1;}}i:1;a:2:{i:0;s:14:"core.ItemLinks";i:1;a:1:{s:11:"useDropdown";b:0;}}i:2;a:2:{i:0;s:13:"core.PeerList";i:1;a:0:{}}i:3;a:2:{i:0;s:21:"imageblock.ImageBlock";i:1;a:0:{}}}');
REPLACE INTO `g2_PluginParameterMap` VALUES ('theme', 'matrix', 0, 'albumBlocks', 'a:1:{i:0;a:2:{i:0;s:20:"comment.ViewComments";i:1;a:0:{}}}');
REPLACE INTO `g2_PluginParameterMap` VALUES ('theme', 'matrix', 0, 'photoBlocks', 'a:2:{i:0;a:2:{i:0;s:13:"exif.ExifInfo";i:1;a:0:{}}i:1;a:2:{i:0;s:20:"comment.ViewComments";i:1;a:0:{}}}');
REPLACE INTO `g2_PluginParameterMap` VALUES ('module', 'core', 0, 'id.accessListCompacterLock', '1');
REPLACE INTO `g2_PluginParameterMap` VALUES ('module', 'core', 0, 'id.allUserGroup', '2');
REPLACE INTO `g2_PluginParameterMap` VALUES ('module', 'core', 0, 'id.adminGroup', '3');
REPLACE INTO `g2_PluginParameterMap` VALUES ('module', 'core', 0, 'id.everybodyGroup', '4');
REPLACE INTO `g2_PluginParameterMap` VALUES ('module', 'core', 0, 'id.anonymousUser', '5');
REPLACE INTO `g2_PluginParameterMap` VALUES ('module', 'core', 0, 'id.rootAlbum', '7');
REPLACE INTO `g2_PluginParameterMap` VALUES ('module', 'core', 0, '_version', '1.0.0.2');
REPLACE INTO `g2_PluginParameterMap` VALUES ('module', 'core', 0, '_callbacks', 'registerEventListeners|getItemLinks|getSystemLinks|getSiteAdminViews|getUserAdminViews|getItemAdminViews');
REPLACE INTO `g2_PluginParameterMap` VALUES ('module', 'core', 0, '_requiredCoreApi', '6,7');
REPLACE INTO `g2_PluginParameterMap` VALUES ('module', 'core', 0, '_requiredModuleApi', '2,0');
REPLACE INTO `g2_PluginParameterMap` VALUES ('module', 'albumselect', 0, 'sort', 'manual');
REPLACE INTO `g2_PluginParameterMap` VALUES ('module', 'albumselect', 0, 'treeLines', '1');
REPLACE INTO `g2_PluginParameterMap` VALUES ('module', 'albumselect', 0, 'treeIcons', '0');
REPLACE INTO `g2_PluginParameterMap` VALUES ('module', 'albumselect', 0, 'treeCookies', '0');
REPLACE INTO `g2_PluginParameterMap` VALUES ('module', 'albumselect', 0, 'treeExpandCollapse', '0');
REPLACE INTO `g2_PluginParameterMap` VALUES ('module', 'albumselect', 0, 'treeCloseSameLevel', '0');
REPLACE INTO `g2_PluginParameterMap` VALUES ('module', 'albumselect', 0, '_version', '1.0.0');
REPLACE INTO `g2_PluginParameterMap` VALUES ('module', 'albumselect', 0, '_callbacks', 'getSiteAdminViews|registerEventListeners');
REPLACE INTO `g2_PluginParameterMap` VALUES ('module', 'albumselect', 0, '_requiredCoreApi', '6,6');
REPLACE INTO `g2_PluginParameterMap` VALUES ('module', 'albumselect', 0, '_requiredModuleApi', '2,0');
REPLACE INTO `g2_PluginParameterMap` VALUES ('module', 'archiveupload', 0, 'unzipPath', '/sw/bin/unzip');
REPLACE INTO `g2_PluginParameterMap` VALUES ('module', 'archiveupload', 0, '_version', '1.0.0');
REPLACE INTO `g2_PluginParameterMap` VALUES ('module', 'archiveupload', 0, '_callbacks', 'getSiteAdminViews');
REPLACE INTO `g2_PluginParameterMap` VALUES ('module', 'archiveupload', 0, '_requiredCoreApi', '6,0');
REPLACE INTO `g2_PluginParameterMap` VALUES ('module', 'archiveupload', 0, '_requiredModuleApi', '2,0');
REPLACE INTO `g2_PluginParameterMap` VALUES ('module', 'exif', 0, 'addOption', '4');
REPLACE INTO `g2_PluginParameterMap` VALUES ('module', 'exif', 0, '_version', '1.0.0');
REPLACE INTO `g2_PluginParameterMap` VALUES ('module', 'exif', 0, '_callbacks', 'getSiteAdminViews');
REPLACE INTO `g2_PluginParameterMap` VALUES ('module', 'exif', 0, '_requiredCoreApi', '6,0');
REPLACE INTO `g2_PluginParameterMap` VALUES ('module', 'exif', 0, '_requiredModuleApi', '2,0');
REPLACE INTO `g2_PluginParameterMap` VALUES ('module', 'ffmpeg', 0, 'path', '');
REPLACE INTO `g2_PluginParameterMap` VALUES ('module', 'ffmpeg', 0, '_version', '1.0.0');
REPLACE INTO `g2_PluginParameterMap` VALUES ('module', 'ffmpeg', 0, '_callbacks', 'getSiteAdminViews');
REPLACE INTO `g2_PluginParameterMap` VALUES ('module', 'ffmpeg', 0, '_requiredCoreApi', '6,0');
REPLACE INTO `g2_PluginParameterMap` VALUES ('module', 'ffmpeg', 0, '_requiredModuleApi', '2,0');
REPLACE INTO `g2_PluginParameterMap` VALUES ('module', 'imageblock', 0, 'show', 'heading|title|date');
REPLACE INTO `g2_PluginParameterMap` VALUES ('module', 'imageblock', 0, 'albumFrame', '');
REPLACE INTO `g2_PluginParameterMap` VALUES ('module', 'imageblock', 0, 'itemFrame', '');
REPLACE INTO `g2_PluginParameterMap` VALUES ('module', 'imageblock', 0, '_version', '1.0.0');
REPLACE INTO `g2_PluginParameterMap` VALUES ('module', 'imageblock', 0, '_callbacks', 'registerEventListeners|getSiteAdminViews');
REPLACE INTO `g2_PluginParameterMap` VALUES ('module', 'imageblock', 0, '_requiredCoreApi', '6,0');
REPLACE INTO `g2_PluginParameterMap` VALUES ('module', 'imageblock', 0, '_requiredModuleApi', '2,0');
REPLACE INTO `g2_PluginParameterMap` VALUES ('module', 'imagemagick', 0, 'path', '');
REPLACE INTO `g2_PluginParameterMap` VALUES ('module', 'imagemagick', 0, 'jpegQuality', '75');
REPLACE INTO `g2_PluginParameterMap` VALUES ('module', 'imagemagick', 0, 'cmykSupport', 'none');
REPLACE INTO `g2_PluginParameterMap` VALUES ('module', 'imagemagick', 0, '_version', '1.0.0');
REPLACE INTO `g2_PluginParameterMap` VALUES ('module', 'imagemagick', 0, '_callbacks', 'getSiteAdminViews');
REPLACE INTO `g2_PluginParameterMap` VALUES ('module', 'imagemagick', 0, '_requiredCoreApi', '6,0');
REPLACE INTO `g2_PluginParameterMap` VALUES ('module', 'imagemagick', 0, '_requiredModuleApi', '2,0');
REPLACE INTO `g2_PluginParameterMap` VALUES ('module', 'netpbm', 0, 'path', '');
REPLACE INTO `g2_PluginParameterMap` VALUES ('module', 'netpbm', 0, 'jpegQuality', '75');
REPLACE INTO `g2_PluginParameterMap` VALUES ('module', 'netpbm', 0, 'pnmtojpeg', 'pnmtojpeg');
REPLACE INTO `g2_PluginParameterMap` VALUES ('module', 'netpbm', 0, 'bmptopnm', 'bmptopnm');
REPLACE INTO `g2_PluginParameterMap` VALUES ('module', 'netpbm', 0, 'pnmcomp', 'pnmcomp');
REPLACE INTO `g2_PluginParameterMap` VALUES ('module', 'netpbm', 0, '_version', '1.0.0');
REPLACE INTO `g2_PluginParameterMap` VALUES ('module', 'netpbm', 0, '_callbacks', 'getSiteAdminViews');
REPLACE INTO `g2_PluginParameterMap` VALUES ('module', 'netpbm', 0, '_requiredCoreApi', '6,0');
REPLACE INTO `g2_PluginParameterMap` VALUES ('module', 'netpbm', 0, '_requiredModuleApi', '2,0');
REPLACE INTO `g2_PluginParameterMap` VALUES ('module', 'rearrange', 0, '_version', '1.0.0');
REPLACE INTO `g2_PluginParameterMap` VALUES ('module', 'rearrange', 0, '_callbacks', 'getItemLinks|getItemAdminViews');
REPLACE INTO `g2_PluginParameterMap` VALUES ('module', 'rearrange', 0, '_requiredCoreApi', '6,0');
REPLACE INTO `g2_PluginParameterMap` VALUES ('module', 'rearrange', 0, '_requiredModuleApi', '2,0');
REPLACE INTO `g2_PluginParameterMap` VALUES ('module', 'rewrite', 0, 'galleryLocation', '/gallery2/');
REPLACE INTO `g2_PluginParameterMap` VALUES ('module', 'rewrite', 0, 'status', 'a:1:{s:11:"needOptions";b:0;}');
REPLACE INTO `g2_PluginParameterMap` VALUES ('module', 'rewrite', 0, 'embeddedLocation', '');
REPLACE INTO `g2_PluginParameterMap` VALUES ('module', 'rewrite', 0, 'embeddedHtaccess', '');
REPLACE INTO `g2_PluginParameterMap` VALUES ('module', 'rewrite', 0, 'accessList', 'a:0:{}');
REPLACE INTO `g2_PluginParameterMap` VALUES ('module', 'rewrite', 0, '_version', '1.0.0');
REPLACE INTO `g2_PluginParameterMap` VALUES ('module', 'rewrite', 0, '_callbacks', 'getSiteAdminViews|registerEventListeners');
REPLACE INTO `g2_PluginParameterMap` VALUES ('module', 'rewrite', 0, '_requiredCoreApi', '6,0');
REPLACE INTO `g2_PluginParameterMap` VALUES ('module', 'rewrite', 0, '_requiredModuleApi', '2,0');
REPLACE INTO `g2_PluginParameterMap` VALUES ('module', 'search', 0, '_version', '1.0.0');
REPLACE INTO `g2_PluginParameterMap` VALUES ('module', 'search', 0, '_callbacks', '');
REPLACE INTO `g2_PluginParameterMap` VALUES ('module', 'search', 0, '_requiredCoreApi', '6,0');
REPLACE INTO `g2_PluginParameterMap` VALUES ('module', 'search', 0, '_requiredModuleApi', '2,0');
REPLACE INTO `g2_PluginParameterMap` VALUES ('module', 'slideshow', 0, '_version', '1.0.0');
REPLACE INTO `g2_PluginParameterMap` VALUES ('module', 'slideshow', 0, '_callbacks', 'getItemLinks');
REPLACE INTO `g2_PluginParameterMap` VALUES ('module', 'slideshow', 0, '_requiredCoreApi', '6,0');
REPLACE INTO `g2_PluginParameterMap` VALUES ('module', 'slideshow', 0, '_requiredModuleApi', '2,0');
REPLACE INTO `g2_PluginParameterMap` VALUES ('module', 'slideshowapplet', 0, '_version', '1.0.0');
REPLACE INTO `g2_PluginParameterMap` VALUES ('module', 'slideshowapplet', 0, '_callbacks', 'getItemLinks|getSiteAdminViews');
REPLACE INTO `g2_PluginParameterMap` VALUES ('module', 'slideshowapplet', 0, '_requiredCoreApi', '6,0');
REPLACE INTO `g2_PluginParameterMap` VALUES ('module', 'slideshowapplet', 0, '_requiredModuleApi', '2,0');
REPLACE INTO `g2_PluginParameterMap` VALUES ('module', 'squarethumb', 0, 'mode', 'crop');
REPLACE INTO `g2_PluginParameterMap` VALUES ('module', 'squarethumb', 0, 'color', '000000');
REPLACE INTO `g2_PluginParameterMap` VALUES ('module', 'squarethumb', 0, '_version', '1.0.0');
REPLACE INTO `g2_PluginParameterMap` VALUES ('module', 'squarethumb', 0, '_callbacks', 'registerEventListeners|getSiteAdminViews');
REPLACE INTO `g2_PluginParameterMap` VALUES ('module', 'squarethumb', 0, '_requiredCoreApi', '6,0');
REPLACE INTO `g2_PluginParameterMap` VALUES ('module', 'squarethumb', 0, '_requiredModuleApi', '2,0');
REPLACE INTO `g2_PluginParameterMap` VALUES ('module', 'thumbnail', 0, '_version', '1.0.0');
REPLACE INTO `g2_PluginParameterMap` VALUES ('module', 'thumbnail', 0, '_callbacks', 'getSiteAdminViews|registerEventListeners');
REPLACE INTO `g2_PluginParameterMap` VALUES ('module', 'thumbnail', 0, '_requiredCoreApi', '6,0');
REPLACE INTO `g2_PluginParameterMap` VALUES ('module', 'thumbnail', 0, '_requiredModuleApi', '2,0');
REPLACE INTO `g2_PluginParameterMap` VALUES ('module', 'uploadapplet', 0, '_version', '1.0.0');
REPLACE INTO `g2_PluginParameterMap` VALUES ('module', 'uploadapplet', 0, '_callbacks', '');
REPLACE INTO `g2_PluginParameterMap` VALUES ('module', 'uploadapplet', 0, '_requiredCoreApi', '6,0');
REPLACE INTO `g2_PluginParameterMap` VALUES ('module', 'uploadapplet', 0, '_requiredModuleApi', '2,0');
REPLACE INTO `g2_PluginParameterMap` VALUES ('module', 'gd', 0, 'jpegQuality', '75');
REPLACE INTO `g2_PluginParameterMap` VALUES ('module', 'gd', 0, '_version', '1.0.0');
REPLACE INTO `g2_PluginParameterMap` VALUES ('module', 'gd', 0, '_callbacks', 'getSiteAdminViews');
REPLACE INTO `g2_PluginParameterMap` VALUES ('module', 'gd', 0, '_requiredCoreApi', '6,0');
REPLACE INTO `g2_PluginParameterMap` VALUES ('module', 'gd', 0, '_requiredModuleApi', '2,0');
REPLACE INTO `g2_PluginParameterMap` VALUES ('module', 'core', 0, 'smtp.host', '');
REPLACE INTO `g2_PluginParameterMap` VALUES ('module', 'core', 0, 'smtp.from', '');
REPLACE INTO `g2_PluginParameterMap` VALUES ('module', 'core', 0, 'smtp.username', '');
REPLACE INTO `g2_PluginParameterMap` VALUES ('module', 'core', 0, 'cookie.path', '/');
REPLACE INTO `g2_PluginParameterMap` VALUES ('module', 'core', 0, 'cookie.domain', '');
REPLACE INTO `g2_PluginParameterMap` VALUES ('module', 'core', 0, 'smtp.password', '');

-- --------------------------------------------------------

-- 
-- Table structure for table `g2_QuotasMap`
-- 

CREATE TABLE IF NOT EXISTS `g2_QuotasMap` (
  `g_userOrGroupId` int(11) default NULL,
  `g_quotaSize` int(11) NOT NULL default '0',
  KEY `g2_QuotasMap_48775` (`g_userOrGroupId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `g2_QuotasMap`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `g2_RecoverPasswordMap`
-- 

CREATE TABLE IF NOT EXISTS `g2_RecoverPasswordMap` (
  `g_userName` varchar(32) NOT NULL default '',
  `g_authString` varchar(32) NOT NULL default '',
  `g_requestExpires` int(11) NOT NULL default '0',
  PRIMARY KEY  (`g_userName`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `g2_RecoverPasswordMap`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `g2_RewriteMap`
-- 

CREATE TABLE IF NOT EXISTS `g2_RewriteMap` (
  `g_pattern` varchar(255) NOT NULL default '',
  `g_module` varchar(32) NOT NULL default '',
  `g_ruleId` int(11) NOT NULL default '0',
  `g_match` varchar(128) default NULL,
  PRIMARY KEY  (`g_pattern`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `g2_RewriteMap`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `g2_Schema`
-- 

CREATE TABLE IF NOT EXISTS `g2_Schema` (
  `g_name` varchar(128) NOT NULL default '',
  `g_major` int(11) NOT NULL default '0',
  `g_minor` int(11) NOT NULL default '0',
  PRIMARY KEY  (`g_name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `g2_Schema`
-- 

REPLACE INTO `g2_Schema` VALUES ('Schema', 1, 0);
REPLACE INTO `g2_Schema` VALUES ('ExternalIdMap', 1, 0);
REPLACE INTO `g2_Schema` VALUES ('AccessMap', 1, 0);
REPLACE INTO `g2_Schema` VALUES ('AccessSubscriberMap', 1, 0);
REPLACE INTO `g2_Schema` VALUES ('AlbumItem', 1, 1);
REPLACE INTO `g2_Schema` VALUES ('AnimationItem', 1, 0);
REPLACE INTO `g2_Schema` VALUES ('ChildEntity', 1, 0);
REPLACE INTO `g2_Schema` VALUES ('DataItem', 1, 0);
REPLACE INTO `g2_Schema` VALUES ('Derivative', 1, 1);
REPLACE INTO `g2_Schema` VALUES ('DerivativeImage', 1, 0);
REPLACE INTO `g2_Schema` VALUES ('DerivativePrefsMap', 1, 0);
REPLACE INTO `g2_Schema` VALUES ('DescendentCountsMap', 1, 0);
REPLACE INTO `g2_Schema` VALUES ('Entity', 1, 1);
REPLACE INTO `g2_Schema` VALUES ('FactoryMap', 1, 0);
REPLACE INTO `g2_Schema` VALUES ('FileSystemEntity', 1, 0);
REPLACE INTO `g2_Schema` VALUES ('Group', 1, 1);
REPLACE INTO `g2_Schema` VALUES ('Item', 1, 1);
REPLACE INTO `g2_Schema` VALUES ('ItemAttributesMap', 1, 0);
REPLACE INTO `g2_Schema` VALUES ('MaintenanceMap', 1, 0);
REPLACE INTO `g2_Schema` VALUES ('MimeTypeMap', 1, 0);
REPLACE INTO `g2_Schema` VALUES ('MovieItem', 1, 0);
REPLACE INTO `g2_Schema` VALUES ('PermissionSetMap', 1, 0);
REPLACE INTO `g2_Schema` VALUES ('PhotoItem', 1, 0);
REPLACE INTO `g2_Schema` VALUES ('PluginMap', 1, 1);
REPLACE INTO `g2_Schema` VALUES ('PluginParameterMap', 1, 2);
REPLACE INTO `g2_Schema` VALUES ('RecoverPasswordMap', 1, 1);
REPLACE INTO `g2_Schema` VALUES ('TkOperatnMap', 1, 0);
REPLACE INTO `g2_Schema` VALUES ('TkOperatnMimeTypeMap', 1, 0);
REPLACE INTO `g2_Schema` VALUES ('TkOperatnParameterMap', 1, 0);
REPLACE INTO `g2_Schema` VALUES ('TkPropertyMap', 1, 0);
REPLACE INTO `g2_Schema` VALUES ('TkPropertyMimeTypeMap', 1, 0);
REPLACE INTO `g2_Schema` VALUES ('UnknownItem', 1, 0);
REPLACE INTO `g2_Schema` VALUES ('User', 1, 0);
REPLACE INTO `g2_Schema` VALUES ('UserGroupMap', 1, 0);
REPLACE INTO `g2_Schema` VALUES ('Lock', 1, 0);
REPLACE INTO `g2_Schema` VALUES ('ExifPropertiesMap', 1, 0);
REPLACE INTO `g2_Schema` VALUES ('ImageBlockCacheMap', 1, 0);
REPLACE INTO `g2_Schema` VALUES ('ImageBlockDisabledMap', 1, 0);
REPLACE INTO `g2_Schema` VALUES ('RewriteMap', 1, 0);
REPLACE INTO `g2_Schema` VALUES ('ThumbnailImage', 1, 1);

-- --------------------------------------------------------

-- 
-- Table structure for table `g2_SequenceId`
-- 

CREATE TABLE IF NOT EXISTS `g2_SequenceId` (
  `id` int(11) NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `g2_SequenceId`
-- 

REPLACE INTO `g2_SequenceId` VALUES (13);

-- --------------------------------------------------------

-- 
-- Table structure for table `g2_SequenceLock`
-- 

CREATE TABLE IF NOT EXISTS `g2_SequenceLock` (
  `id` int(11) NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `g2_SequenceLock`
-- 

REPLACE INTO `g2_SequenceLock` VALUES (0);

-- --------------------------------------------------------

-- 
-- Table structure for table `g2_ThumbnailImage`
-- 

CREATE TABLE IF NOT EXISTS `g2_ThumbnailImage` (
  `g_id` int(11) NOT NULL default '0',
  `g_fileName` varchar(128) NOT NULL default '',
  `g_mimeType` varchar(128) default NULL,
  `g_size` int(11) default NULL,
  `g_width` int(11) default NULL,
  `g_height` int(11) default NULL,
  `g_itemMimeTypes` varchar(128) default NULL,
  PRIMARY KEY  (`g_id`),
  UNIQUE KEY `g_fileName` (`g_fileName`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `g2_ThumbnailImage`
-- 

REPLACE INTO `g2_ThumbnailImage` VALUES (10, 'G2audio.jpg', 'image/jpeg', 15472, 400, 352, 'audio/mpeg|audio/x-wav|audio/x-aiff|audio/midi|audio/basic|audio/x-ms-wma');
REPLACE INTO `g2_ThumbnailImage` VALUES (12, 'G2video.jpg', 'image/jpeg', 15777, 400, 352, 'video/mpeg|video/quicktime|video/x-msvideo|video/x-ms-asf|video/x-ms-wmv');

-- --------------------------------------------------------

-- 
-- Table structure for table `g2_TkOperatnMap`
-- 

CREATE TABLE IF NOT EXISTS `g2_TkOperatnMap` (
  `g_name` varchar(128) NOT NULL default '',
  `g_parametersCrc` varchar(32) NOT NULL default '',
  `g_outputMimeType` varchar(128) default NULL,
  `g_description` varchar(255) default NULL,
  PRIMARY KEY  (`g_name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `g2_TkOperatnMap`
-- 

REPLACE INTO `g2_TkOperatnMap` VALUES ('extract', '0', '', 'extract files from an archive');
REPLACE INTO `g2_TkOperatnMap` VALUES ('convert-to-image/jpeg', '0', 'image/jpeg', 'Convert to a JPEG');
REPLACE INTO `g2_TkOperatnMap` VALUES ('scale', '3155881288', NULL, 'Scale the image to the target size, maintain aspect ratio');
REPLACE INTO `g2_TkOperatnMap` VALUES ('thumbnail', '3155881288', NULL, 'Scale the image to the target size, maintain aspect ratio');
REPLACE INTO `g2_TkOperatnMap` VALUES ('resize', '3155881288', NULL, 'Resize the image to the target dimensions');
REPLACE INTO `g2_TkOperatnMap` VALUES ('crop', '729751051', NULL, 'Crop the image');
REPLACE INTO `g2_TkOperatnMap` VALUES ('composite', '1204337430', NULL, 'Overlay source image with a second one');
REPLACE INTO `g2_TkOperatnMap` VALUES ('compress', '340908721', NULL, 'Reduce image quality to reach target file size');

-- --------------------------------------------------------

-- 
-- Table structure for table `g2_TkOperatnMimeTypeMap`
-- 

CREATE TABLE IF NOT EXISTS `g2_TkOperatnMimeTypeMap` (
  `g_operationName` varchar(128) NOT NULL default '',
  `g_toolkitId` varchar(128) NOT NULL default '',
  `g_mimeType` varchar(128) NOT NULL default '',
  `g_priority` int(11) NOT NULL default '0',
  KEY `g2_TkOperatnMimeTypeMap_2014` (`g_operationName`),
  KEY `g2_TkOperatnMimeTypeMap_79463` (`g_mimeType`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `g2_TkOperatnMimeTypeMap`
-- 

REPLACE INTO `g2_TkOperatnMimeTypeMap` VALUES ('extract', 'ArchiveUpload', 'application/zip', 5);
REPLACE INTO `g2_TkOperatnMimeTypeMap` VALUES ('convert-to-image/jpeg', 'Thumbnail', 'audio/mpeg', 50);
REPLACE INTO `g2_TkOperatnMimeTypeMap` VALUES ('convert-to-image/jpeg', 'Thumbnail', 'audio/x-wav', 50);
REPLACE INTO `g2_TkOperatnMimeTypeMap` VALUES ('convert-to-image/jpeg', 'Thumbnail', 'audio/x-aiff', 50);
REPLACE INTO `g2_TkOperatnMimeTypeMap` VALUES ('convert-to-image/jpeg', 'Thumbnail', 'audio/midi', 50);
REPLACE INTO `g2_TkOperatnMimeTypeMap` VALUES ('convert-to-image/jpeg', 'Thumbnail', 'audio/basic', 50);
REPLACE INTO `g2_TkOperatnMimeTypeMap` VALUES ('convert-to-image/jpeg', 'Thumbnail', 'audio/x-ms-wma', 50);
REPLACE INTO `g2_TkOperatnMimeTypeMap` VALUES ('convert-to-image/jpeg', 'Thumbnail', 'video/mpeg', 50);
REPLACE INTO `g2_TkOperatnMimeTypeMap` VALUES ('convert-to-image/jpeg', 'Thumbnail', 'video/quicktime', 50);
REPLACE INTO `g2_TkOperatnMimeTypeMap` VALUES ('convert-to-image/jpeg', 'Thumbnail', 'video/x-msvideo', 50);
REPLACE INTO `g2_TkOperatnMimeTypeMap` VALUES ('convert-to-image/jpeg', 'Thumbnail', 'video/x-ms-asf', 50);
REPLACE INTO `g2_TkOperatnMimeTypeMap` VALUES ('convert-to-image/jpeg', 'Thumbnail', 'video/x-ms-wmv', 50);
REPLACE INTO `g2_TkOperatnMimeTypeMap` VALUES ('convert-to-image/jpeg', 'Gd', 'image/gif', 21);
REPLACE INTO `g2_TkOperatnMimeTypeMap` VALUES ('convert-to-image/jpeg', 'Gd', 'image/jpeg', 21);
REPLACE INTO `g2_TkOperatnMimeTypeMap` VALUES ('convert-to-image/jpeg', 'Gd', 'image/png', 21);
REPLACE INTO `g2_TkOperatnMimeTypeMap` VALUES ('convert-to-image/jpeg', 'Gd', 'image/vnd.wap.wbmp', 21);
REPLACE INTO `g2_TkOperatnMimeTypeMap` VALUES ('scale', 'Gd', 'image/gif', 21);
REPLACE INTO `g2_TkOperatnMimeTypeMap` VALUES ('scale', 'Gd', 'image/jpeg', 21);
REPLACE INTO `g2_TkOperatnMimeTypeMap` VALUES ('scale', 'Gd', 'image/png', 21);
REPLACE INTO `g2_TkOperatnMimeTypeMap` VALUES ('scale', 'Gd', 'image/vnd.wap.wbmp', 21);
REPLACE INTO `g2_TkOperatnMimeTypeMap` VALUES ('thumbnail', 'Gd', 'image/gif', 21);
REPLACE INTO `g2_TkOperatnMimeTypeMap` VALUES ('thumbnail', 'Gd', 'image/jpeg', 21);
REPLACE INTO `g2_TkOperatnMimeTypeMap` VALUES ('thumbnail', 'Gd', 'image/png', 21);
REPLACE INTO `g2_TkOperatnMimeTypeMap` VALUES ('thumbnail', 'Gd', 'image/vnd.wap.wbmp', 21);
REPLACE INTO `g2_TkOperatnMimeTypeMap` VALUES ('resize', 'Gd', 'image/gif', 21);
REPLACE INTO `g2_TkOperatnMimeTypeMap` VALUES ('resize', 'Gd', 'image/jpeg', 21);
REPLACE INTO `g2_TkOperatnMimeTypeMap` VALUES ('resize', 'Gd', 'image/png', 21);
REPLACE INTO `g2_TkOperatnMimeTypeMap` VALUES ('resize', 'Gd', 'image/vnd.wap.wbmp', 21);
REPLACE INTO `g2_TkOperatnMimeTypeMap` VALUES ('crop', 'Gd', 'image/gif', 21);
REPLACE INTO `g2_TkOperatnMimeTypeMap` VALUES ('crop', 'Gd', 'image/jpeg', 21);
REPLACE INTO `g2_TkOperatnMimeTypeMap` VALUES ('crop', 'Gd', 'image/png', 21);
REPLACE INTO `g2_TkOperatnMimeTypeMap` VALUES ('crop', 'Gd', 'image/vnd.wap.wbmp', 21);
REPLACE INTO `g2_TkOperatnMimeTypeMap` VALUES ('composite', 'Gd', 'image/gif', 21);
REPLACE INTO `g2_TkOperatnMimeTypeMap` VALUES ('composite', 'Gd', 'image/jpeg', 21);
REPLACE INTO `g2_TkOperatnMimeTypeMap` VALUES ('composite', 'Gd', 'image/png', 21);
REPLACE INTO `g2_TkOperatnMimeTypeMap` VALUES ('composite', 'Gd', 'image/vnd.wap.wbmp', 21);
REPLACE INTO `g2_TkOperatnMimeTypeMap` VALUES ('compress', 'Gd', 'image/jpeg', 21);

-- --------------------------------------------------------

-- 
-- Table structure for table `g2_TkOperatnParameterMap`
-- 

CREATE TABLE IF NOT EXISTS `g2_TkOperatnParameterMap` (
  `g_operationName` varchar(128) NOT NULL default '',
  `g_position` int(11) NOT NULL default '0',
  `g_type` varchar(128) NOT NULL default '',
  `g_description` varchar(255) default NULL,
  KEY `g2_TkOperatnParameterMap_2014` (`g_operationName`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `g2_TkOperatnParameterMap`
-- 

REPLACE INTO `g2_TkOperatnParameterMap` VALUES ('scale', 0, 'int', 'target width');
REPLACE INTO `g2_TkOperatnParameterMap` VALUES ('scale', 1, 'int', '(optional) target height, defaults to same as width');
REPLACE INTO `g2_TkOperatnParameterMap` VALUES ('thumbnail', 0, 'int', 'target width');
REPLACE INTO `g2_TkOperatnParameterMap` VALUES ('thumbnail', 1, 'int', '(optional) target height, defaults to same as width');
REPLACE INTO `g2_TkOperatnParameterMap` VALUES ('resize', 0, 'int', 'target width');
REPLACE INTO `g2_TkOperatnParameterMap` VALUES ('resize', 1, 'int', 'target height');
REPLACE INTO `g2_TkOperatnParameterMap` VALUES ('crop', 0, 'float', 'left edge %');
REPLACE INTO `g2_TkOperatnParameterMap` VALUES ('crop', 1, 'float', 'top edge %');
REPLACE INTO `g2_TkOperatnParameterMap` VALUES ('crop', 2, 'float', 'width %');
REPLACE INTO `g2_TkOperatnParameterMap` VALUES ('crop', 3, 'float', 'height %');
REPLACE INTO `g2_TkOperatnParameterMap` VALUES ('composite', 0, 'string', 'overlay path');
REPLACE INTO `g2_TkOperatnParameterMap` VALUES ('composite', 1, 'string', 'overlay mime type');
REPLACE INTO `g2_TkOperatnParameterMap` VALUES ('composite', 2, 'int', 'overlay width');
REPLACE INTO `g2_TkOperatnParameterMap` VALUES ('composite', 3, 'int', 'overlay height');
REPLACE INTO `g2_TkOperatnParameterMap` VALUES ('composite', 4, 'string', 'alignment type');
REPLACE INTO `g2_TkOperatnParameterMap` VALUES ('composite', 5, 'int', 'alignment x %');
REPLACE INTO `g2_TkOperatnParameterMap` VALUES ('composite', 6, 'int', 'alignment y %');
REPLACE INTO `g2_TkOperatnParameterMap` VALUES ('compress', 0, 'int', 'target size in kb');

-- --------------------------------------------------------

-- 
-- Table structure for table `g2_TkPropertyMap`
-- 

CREATE TABLE IF NOT EXISTS `g2_TkPropertyMap` (
  `g_name` varchar(128) NOT NULL default '',
  `g_type` varchar(128) NOT NULL default '',
  `g_description` varchar(128) NOT NULL default ''
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `g2_TkPropertyMap`
-- 

REPLACE INTO `g2_TkPropertyMap` VALUES ('originationTimestamp', 'int', 'Get the origination timestamp');
REPLACE INTO `g2_TkPropertyMap` VALUES ('dimensions', 'int,int', 'Get the width and height of the image');

-- --------------------------------------------------------

-- 
-- Table structure for table `g2_TkPropertyMimeTypeMap`
-- 

CREATE TABLE IF NOT EXISTS `g2_TkPropertyMimeTypeMap` (
  `g_propertyName` varchar(128) NOT NULL default '',
  `g_toolkitId` varchar(128) NOT NULL default '',
  `g_mimeType` varchar(128) NOT NULL default '',
  KEY `g2_TkPropertyMimeTypeMap_52881` (`g_propertyName`),
  KEY `g2_TkPropertyMimeTypeMap_79463` (`g_mimeType`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `g2_TkPropertyMimeTypeMap`
-- 

REPLACE INTO `g2_TkPropertyMimeTypeMap` VALUES ('originationTimestamp', 'Exif', 'image/jpeg');
REPLACE INTO `g2_TkPropertyMimeTypeMap` VALUES ('originationTimestamp', 'Exif', 'image/jpeg-cmyk');
REPLACE INTO `g2_TkPropertyMimeTypeMap` VALUES ('dimensions', 'Gd', 'image/gif');
REPLACE INTO `g2_TkPropertyMimeTypeMap` VALUES ('dimensions', 'Gd', 'image/jpeg');
REPLACE INTO `g2_TkPropertyMimeTypeMap` VALUES ('dimensions', 'Gd', 'image/png');
REPLACE INTO `g2_TkPropertyMimeTypeMap` VALUES ('dimensions', 'Gd', 'image/vnd.wap.wbmp');

-- --------------------------------------------------------

-- 
-- Table structure for table `g2_UnknownItem`
-- 

CREATE TABLE IF NOT EXISTS `g2_UnknownItem` (
  `g_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`g_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `g2_UnknownItem`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `g2_User`
-- 

CREATE TABLE IF NOT EXISTS `g2_User` (
  `g_id` int(11) NOT NULL default '0',
  `g_userName` varchar(32) NOT NULL default '',
  `g_fullName` varchar(128) default NULL,
  `g_hashedPassword` varchar(128) default NULL,
  `g_email` varchar(128) default NULL,
  `g_language` varchar(128) default NULL,
  PRIMARY KEY  (`g_id`),
  UNIQUE KEY `g_userName` (`g_userName`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `g2_User`
-- 

REPLACE INTO `g2_User` VALUES (5, 'guest', 'Guest', 'ZXU1dbd281feae9e16e9e4f64f90b850c500', NULL, NULL);
REPLACE INTO `g2_User` VALUES (6, 'admin', NULL, 'aVPL2d2205be7a4d297eb1327507c10bae44', 'gallery@radicaldesigns.org', NULL);

-- --------------------------------------------------------

-- 
-- Table structure for table `g2_UserGroupMap`
-- 

CREATE TABLE IF NOT EXISTS `g2_UserGroupMap` (
  `g_userId` int(11) NOT NULL default '0',
  `g_groupId` int(11) NOT NULL default '0',
  KEY `g2_UserGroupMap_69068` (`g_userId`),
  KEY `g2_UserGroupMap_89328` (`g_groupId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `g2_UserGroupMap`
-- 

REPLACE INTO `g2_UserGroupMap` VALUES (6, 2);
REPLACE INTO `g2_UserGroupMap` VALUES (5, 4);
REPLACE INTO `g2_UserGroupMap` VALUES (6, 4);
REPLACE INTO `g2_UserGroupMap` VALUES (6, 3);

-- --------------------------------------------------------

-- 
-- Table structure for table `g2_WatermarkImage`
-- 

CREATE TABLE IF NOT EXISTS `g2_WatermarkImage` (
  `g_id` int(11) NOT NULL default '0',
  `g_applyToPreferred` int(1) default NULL,
  `g_applyToResizes` int(1) default NULL,
  `g_applyToThumbnail` int(1) default NULL,
  `g_name` varchar(128) NOT NULL default '',
  `g_fileName` varchar(128) NOT NULL default '',
  `g_mimeType` varchar(128) default NULL,
  `g_size` int(11) default NULL,
  `g_width` int(11) default NULL,
  `g_height` int(11) default NULL,
  `g_ownerId` int(11) default NULL,
  `g_xPercentage` varchar(32) default NULL,
  `g_yPercentage` varchar(32) default NULL,
  PRIMARY KEY  (`g_id`),
  UNIQUE KEY `g_fileName` (`g_fileName`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `g2_WatermarkImage`
-- 

        

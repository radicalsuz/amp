<?php
/*
 * $RCSfile: module.inc,v $
 *
 * Gallery - a web based photo album viewer and editor
 * Copyright (C) 2000-2005 Bharat Mediratta
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or (at
 * your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street - Fifth Floor, Boston, MA  02110-1301, USA.
 */

/**
 * @version $Revision: 1.18 $ $Date: 2005/09/10 20:28:15 $
 * @package SizeLimit
 * @author Felix Rabinovich <felix@rabinovich.org>
 */

/**
 * This module sets size limit for the picture
 *
 * @package
 */
class SizeLimitModule extends GalleryModule {

    function SizeLimitModule() {
	global $gallery;

	$this->setId('sizelimit');
	$this->setName($gallery->i18n('Size Limit'));
	$this->setDescription($gallery->i18n('Define picture size limit'));
	$this->setVersion('1.0.0');
	$this->setGroup('display', $this->translate('Display'));
	$this->setCallbacks('registerEventListeners');
	$this->setRequiredCoreApi(array(6, 0));
	$this->setRequiredModuleApi(array(2, 0));
    }

    /**
     * @see GalleryModule::registerEventListeners()
     */
    function registerEventListeners() {
	GalleryCoreApi::registerEventListener('GalleryEntity::save',
	    new SizelimitModule(), true);   /* true=disableForUnitTest */
    }

    /**
     * @see GalleryModule::isRecommendedDuringInstall
     */
    function isRecommendedDuringInstall() {
	return false;
    }

    /**
     * @see GalleryModule::autoConfigure
     */
    function autoConfigure() {
	/* We don't require any special configuration */
	return array(GalleryStatus::success(), true);
    }

    /**
     * @see GalleryModule::performFactoryRegistrations()
     */
    function performFactoryRegistrations() {
	$ret = GalleryCoreApi::registerFactoryImplementation(
	    'ItemEditOption', 'SizeLimitOption', 'SizeLimitOption',
	    'modules/sizelimit/SizeLimitOption.inc', 'sizelimit', array('ItemEditAlbum'));
	if ($ret->isError()) {
	    return $ret->wrap(__FILE__, __LINE__);
	}

	$ret = GalleryCoreApi::registerFactoryImplementation(
	    'ItemAddOption', 'SetSizeOption', 'SetSizeOption',
	    'modules/sizelimit/SetSizeOption.inc', 'sizelimit', null);
	if ($ret->isError()) {
	    return $ret->wrap(__FILE__, __LINE__);
	}

	return GalleryStatus::success();
    }

    /**
     * Event handler for GalleryEntity::save event
     * Copy dimension / filesize limits when the album is created
     *
     * @param object GalleryEvent the event
     * @return object GalleryStatus a status code
     */
    function handleEvent($event) {
	if ($event->getEventName() == 'GalleryEntity::save') {
	    $album = $event->getEntity();
	    if (GalleryUtilities::isA($album, 'GalleryAlbumItem') &&
		    $album->testPersistentFlag(STORAGE_FLAG_NEWLY_CREATED)) {
		$parentId = $album->getParentId();
		list ($ret, $params) = GalleryCoreApi::fetchAllPluginParameters(
		    'module', 'sizelimit', $parentId);
		if ($ret->isError()) {
		    return array($ret->wrap(__FILE__, __LINE__), null);
		}
		foreach ($params as $param => $value) {
		    $ret = GalleryCoreApi::setPluginParameter('module', 'sizelimit',
			$param, $value, $album->getId());
		    if ($ret->isError()) {
			return array($ret->wrap(__FILE__, __LINE__), null);
		    }
		}
	    }
	}
	return array(GalleryStatus::success(), null);
    }
}
?>

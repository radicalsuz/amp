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
 * @version $Revision: 1.28 $ $Date: 2005/09/10 20:28:15 $
 * @package ThumbPage
 * @author Alan Harder <alan.harder@sun.com>
 */

/**
 * Pick which page of a multi-page file to use for the thumbnail.
 *
 * @package ThumbPage
 */
class ThumbPageModule extends GalleryModule /* and GalleryEventListener */ {

    function ThumbPageModule() {
	global $gallery;

	$this->setId('thumbpage');
	$this->setName($gallery->i18n('Thumbnail Page'));
	$this->setDescription(
	    $gallery->i18n('Select page/frame from a multipage file or movie for the thumbnail'));
	$this->setVersion('1.0.0');
	$this->setGroup('display', $this->translate('Display'));
	$this->setCallbacks('registerEventListeners');
	$this->setRequiredCoreApi(array(6, 0));
	$this->setRequiredModuleApi(array(2, 0));
    }

    /**
     * @see GalleryModule::performFactoryRegistrations()
     */
    function performFactoryRegistrations() {
	$ret = GalleryCoreApi::registerFactoryImplementation(
	    'ItemEditPlugin', 'ItemEditThumbPage', 'ItemEditThumbPage',
	    'modules/thumbpage/ItemEditThumbPage.inc', 'thumbpage', null);
	if ($ret->isError()) {
	    return $ret->wrap(__FILE__, __LINE__);
	}

	$ret = GalleryCoreApi::registerFactoryImplementation(
	    'ItemEditPlugin', 'ItemEditThumbOffset', 'ItemEditThumbOffset',
	    'modules/thumbpage/ItemEditThumbOffset.inc', 'thumbpage', null);
	if ($ret->isError()) {
	    return $ret->wrap(__FILE__, __LINE__);
	}

	return GalleryStatus::success();
    }

    /**
     * @see GalleryModule::registerEventListeners();
     */
    function registerEventListeners() {
	GalleryCoreApi::registerEventListener('Gallery::DeactivatePlugin', new ThumbPageModule());
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
	return array(GalleryStatus::success(), true);
    }

    /**
     * @see GalleryModule::activate()
     */
    function activate($postActivationEvent=true) {
	list ($ret, $page) = $this->_getSupportedMultiPageTypes();
	if ($ret->isError()) {
	    return array($ret->wrap(__FILE__, __LINE__), null);
	}
	list ($ret, $movie) = $this->_getSupportedMovieTypes();
	if ($ret->isError()) {
	    return array($ret->wrap(__FILE__, __LINE__), null);
	}

	if (empty($page) && empty($movie)) {
	    /* Can't activate unless there's already toolkit support */
	    return array(GalleryStatus::success(), array('view' => 'core.SiteAdmin',
							 'subView' => 'thumbpage.CantActivate'));
	} else {
	    $ret = $this->setParameter('pageTypes', implode('|', $page));
	    if ($ret->isError()) {
		return array($ret->wrap(__FILE__, __LINE__), null);
	    }
	    $ret = $this->setParameter('movieTypes', implode('|', $movie));
	    if ($ret->isError()) {
		return array($ret->wrap(__FILE__, __LINE__), null);
	    }
	}

	list ($ret, $redirect) = parent::activate($postActivationEvent);
	if ($ret->isError()) {
	    return array($ret->wrap(__FILE__, __LINE__), null);
	}

	return array(GalleryStatus::success(), $redirect);
    }

    /**
     * @see GalleryModule::getConfigurationView()
     */
    function getConfigurationView() {
	return 'thumbpage.CantActivate';
    }

    /**
     * Find out what mime types currently have support for:
     *    thumbnail, select-page, page-count
     *
     * @return array object GalleryStatus a status code
     *               array mime types
     */
    function _getSupportedMultiPageTypes() {
	list ($ret, $thumbnail) = GalleryCoreApi::getToolkitOperationMimeTypes('thumbnail');
	if ($ret->isError()) {
	    return array($ret->wrap(__FILE__, __LINE__), null);
	}
	list ($ret, $select) = GalleryCoreApi::getToolkitOperationMimeTypes('select-page');
	if ($ret->isError()) {
	    return array($ret->wrap(__FILE__, __LINE__), null);
	}

	$mimeTypes = array();
	foreach (array_intersect(array_keys($thumbnail), array_keys($select)) as $mimeType) {
	    list ($ret, $properties) = GalleryCoreApi::getToolkitProperties($mimeType);
	    if ($ret->isError()) {
		return array($ret->wrap(__FILE__, __LINE__), null);
	    }
	    foreach ($properties as $tmp) {
		if ($tmp['name'] == 'page-count') {
		    $mimeTypes[] = $mimeType;
		    break;
		}
	    }
	}

	return array(GalleryStatus::success(), $mimeTypes);
    }

    /**
     * Find out what mime types currently have support for:
     *   convert-to-image/jpeg, select-offset, dimensions-and-duration
     *
     * @return array object GalleryStatus a status code
     *               array mime types
     */
    function _getSupportedMovieTypes() {
	list ($ret, $convert) =
	    GalleryCoreApi::getToolkitOperationMimeTypes('convert-to-image/jpeg');
	if ($ret->isError()) {
	    return array($ret->wrap(__FILE__, __LINE__), null);
	}
	list ($ret, $select) = GalleryCoreApi::getToolkitOperationMimeTypes('select-offset');
	if ($ret->isError()) {
	    return array($ret->wrap(__FILE__, __LINE__), null);
	}

	$mimeTypes = array();
	foreach (array_intersect(array_keys($convert), array_keys($select)) as $mimeType) {
	    list ($ret, $properties) = GalleryCoreApi::getToolkitProperties($mimeType);
	    if ($ret->isError()) {
		return array($ret->wrap(__FILE__, __LINE__), null);
	    }
	    foreach ($properties as $tmp) {
		if ($tmp['name'] == 'dimensions-and-duration') {
		    $mimeTypes[] = $mimeType;
		    break;
		}
	    }
	}

	return array(GalleryStatus::success(), $mimeTypes);
    }

    /**
     * Handler for Gallery::DeactivatePlugin event.
     *
     * @see GalleryEventListener::handleEvent
     */
    function handleEvent($event) {
	/*
	 * We're going to deactivate this module which will generate another
	 * Gallery::DeactivatePlugin event, so make sure that we don't handle
	 * *that* event and get into an infinite loop!
	 */
	$data = $event->getData();
	if ($event->getEventName() == 'Gallery::DeactivatePlugin' &&
		$data['pluginType'] == 'module' && $data['pluginId'] != 'thumbpage') {
	    list ($ret, $isActive) = $this->isActive();
	    if ($ret->isError()) {
		return array($ret->wrap(__FILE__, __LINE__), null);
	    }
	    if ($isActive) {
		/* Reactivate may fail if toolkit support no longer found. */
		list ($ret, $redirect) = $this->reactivate();
		if ($ret->isError()) {
		    return array($ret->wrap(__FILE__, __LINE__), null);
		}
	    }
	}
	return array(GalleryStatus::success(), null);
    }
}
?>

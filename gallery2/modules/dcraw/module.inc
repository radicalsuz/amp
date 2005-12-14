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
 * @version $Revision: 1.13 $ $Date: 2005/09/10 20:28:12 $
 * @package Dcraw
 * @author Bharat Mediratta <bharat@menalto.com>
 */

/**
 * Dcraw Graphics Module
 *
 * This module provides the Dcraw graphics toolkit for Gallery
 *
 * @package Dcraw
 */
class DcrawModule extends GalleryModule {

    function DcrawModule() {
	global $gallery;

	$this->setId('dcraw');
	$this->setName($gallery->i18n('Dcraw'));
	$this->setDescription(
	    $gallery->i18n('Graphics toolkit for processing images in raw format'));
	$this->setVersion('1.0.0');
	$this->setGroup('toolkits', $this->translate('Graphics Toolkits'));
	$this->setCallbacks('getSiteAdminViews');
	$this->setRequiredCoreApi(array(6, 0));
	$this->setRequiredModuleApi(array(2, 0));
    }

    /**
     * @see GalleryModule::performFactoryRegistrations()
     */
    function performFactoryRegistrations() {
	/* Register our graphics class with the factory */
	$ret = GalleryCoreApi::registerFactoryImplementation(
	    'GalleryToolkit', 'DcrawToolkit', 'Dcraw',
	    'modules/dcraw/classes/DcrawToolkit.class', 'dcraw', null);
	if ($ret->isError()) {
	    return $ret->wrap(__FILE__, __LINE__);
	}

	return GalleryStatus::success();
    }

    /**
     * @see GalleryModule::activate
     */
    function activate($postActivationEvent=true) {
	global $gallery;

        list ($ret, $priority) = GalleryCoreApi::getToolkitPriorityById('Dcraw');
        if ($ret->isError()) {
            return array($ret->wrap(__FILE__, __LINE__), null);
        }

        if (!$priority) {
	    list ($ret, $priority) = GalleryCoreApi::getMaximumManagedToolkitPriority();
	    if ($ret->isError()) {
		return array($ret->wrap(__FILE__, __LINE__), null);
	    }
	    $priority++;
	}

	$ret = GalleryCoreApi::registerToolkitOperation(
	    'Dcraw', array('image/x-dcraw'), 'convert-to-image/x-portable-pixmap',
	    array(), $gallery->i18n('Convert to PPM'), 'image/x-portable-pixmap', $priority);
	if ($ret->isError()) {
	    return array($ret->wrap(__FILE__, __LINE__), null);
	}

	$ret = GalleryCoreApi::registerToolkitProperty(
	    'Dcraw', array('image/x-dcraw'), 'dimensions', 'int,int',
	    $gallery->i18n('Get the width and height of the image'));
	if ($ret->isError()) {
	    return array($ret->wrap(__FILE__, __LINE__), null);
	}

	/* Assign all extensions we handle to our image mime type */
	foreach (array('bay', 'bmq', 'cr2', 'crw', 'cs1', 'dc2', 'dcr',
		       'fff', 'k25', 'kdc', 'mos', 'mrw', 'nef', 'orf',
		       'pef', 'raf', 'rdc', 'srf', 'x3f') as $extension) {
	    $ret = GalleryCoreApi::addMimeType($extension, 'image/x-dcraw', 0);
	    if ($ret->isError()) {
		if (!($ret->getErrorCode() & ERROR_COLLISION)) {
		    return array($ret->wrap(__FILE__, __LINE__), null);
		}
	    }
	}

	list ($ret, $redirect) = parent::activate($postActivationEvent);
	if ($ret->isError()) {
	    return array($ret->wrap(__FILE__, __LINE__), null);
	}

	return array(GalleryStatus::success(), $redirect);
    }

    /**
     * @see GalleryModule::deactivate
     */
    function deactivate($postDeactivationEvent=true) {
	/*
	 * Unregister all of our properties and operations.  Do this before the parent deactivates
	 * so that any event handlers triggered by the deactivation will see the world as it will
	 * be after the deactivation is done.
	 */
	$ret = GalleryCoreApi::unregisterToolkit('Dcraw');
	if ($ret->isError()) {
	    return array($ret->wrap(__FILE__, __LINE__), null);
	}

	list ($ret, $redirect) = parent::deactivate($postDeactivationEvent);
	if ($ret->isError()) {
	    return array($ret->wrap(__FILE__, __LINE__), null);
	}

	return array(GalleryStatus::success(), $redirect);
    }

    /**
     * @see GalleryModule::needsConfiguration
     */
    function needsConfiguration() {
	list ($ret, $value) = $this->getParameter('path');
	if ($ret->isError()) {
	    return array($ret->wrap(__FILE__, __LINE__), null);
	}

	return array(GalleryStatus::success(), empty($value));
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
	global $gallery;

	list ($ret, $needsConfiguration) = $this->needsConfiguration();
	if ($ret->isError()) {
	    return array($ret->wrap(__FILE__, __LINE__), false);
	}

	if (!$needsConfiguration) {
	    return array(GalleryStatus::success(), true);
	}

	/* Try a bunch of likely seeming paths to see if any of them work. */
	$platform = $gallery->getPlatform();
	$slash = $platform->getDirectorySeparator();

	/*
	 * Start with system paths.  Tack on a trailing slash if necessary,
	 * then tack on other likely paths, based on our OS
	 */
	$paths = array();
	if (GalleryUtilities::isA($platform, 'WinNtPlatform')) {
	    foreach (explode(';', getenv('PATH')) as $path) {
		$path = trim($path);
		if (empty($path)) {
		    continue;
		}
		if ($path{strlen($path)-1} != $slash) {
		    $path .= $slash;
		}
		$paths[] = $path;
	    }

	    /*
	     * Double-quoting the paths removes any ambiguity about the
	     * double-slashes being escaped or not
	     */
	    $paths[] = "C:\\Program Files\\Dcraw\\dcraw.exe";
	    $paths[] = "C:\\apps\Dcraw\\dcraw.exe";
	    $paths[] = "C:\\Dcraw\\dcraw.exe";
	} else if (GalleryUtilities::isA($platform, 'UnixPlatform')){
	    foreach (explode(':', getenv('PATH')) as $path) {
		$path = trim($path);
		if (empty($path)) {
		    continue;
		}
		if ($path{strlen($path)-1} != $slash) {
		    $path .= $slash;
		}
		$paths[] = $path;
	    }

	    $paths[] = '/usr/bin/dcraw';
	    $paths[] = '/usr/local/bin/dcraw';
	    $paths[] = '/bin/dcraw';
	    $paths[] = '/sw/bin/dcraw';
	} else {
	    return array(GalleryStatus::success(), false);
	}

	/* Load any classes we require */
	GalleryCoreApi::relativeRequireOnce('modules/dcraw/classes/DcrawToolkitHelper.class');

	/* Now try each path in turn to see which ones work */
	foreach ($paths as $path) {
	    list ($ret, $testResults) = DcrawToolkitHelper::testBinary($path);
	    if ($ret->isError()) {
		/* This path failed.  Continue with the next one */
		continue;
	    }

	    $failCount = 0;
	    foreach ($testResults as $testResult) {
		/* All tests should work, else this path is not a valid one */
		if (!$testResult['success']) {
		    $failCount++;
		}
	    }

	    if ($failCount == 0) {
		/* We have a winner */
		$ret = GalleryCoreApi::setPluginParameter('module', 'dcraw', 'path', $path);
		if ($ret->isError()) {
		    return array($ret->wrap(__FILE__, __LINE__), false);
		}

		return array(GalleryStatus::success(), true);
	    }
	}

	return array(GalleryStatus::success(), false);
    }

    /**
     * @see GalleryModule::getSiteAdminViews
     */
    function getSiteAdminViews() {
	return array(GalleryStatus::success(),
		     array(array('name' => $this->translate('Dcraw'),
				 'view' => 'dcraw.AdminDcraw')));
    }

    /**
     * @see GalleryModule::getConfigurationView
     */
    function getConfigurationView() {
	return 'dcraw.AdminDcraw';
    }
}
?>

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
 * @version $Revision: 1.45 $ $Date: 2005/09/10 20:28:12 $
 * @package Ffmpeg
 * @author Bharat Mediratta <bharat@menalto.com>
 */

/**
 * Ffmpeg Graphics Module
 *
 * This module provides the Ffmpeg graphics toolkit for Gallery
 *
 * @package Ffmpeg
 */
class FfmpegModule extends GalleryModule {

    function FfmpegModule() {
	global $gallery;

	$this->setId('ffmpeg');
	$this->setName($gallery->i18n('Ffmpeg'));
	$this->setDescription($gallery->i18n('A toolkit for processing movies'));
	$this->setVersion('1.0.0');
	$this->setGroup('toolkits', $this->translate('Graphics Toolkits'));
	$this->setCallbacks('getSiteAdminViews');
	$this->setRequiredCoreApi(array(6, 0));
	$this->setRequiredModuleApi(array(2, 0));
    }

    /**
     * @see GalleryModule::upgrade()
     */
    function upgrade($currentVersion) {
	if (!isset($currentVersion)) {
	    /* Initial install. */
	    $ret = $this->setParameter('path', '');
	    if ($ret->isError()) {
		return $ret->wrap(__FILE__, __LINE__);
	    }
	}

	return GalleryStatus::success();
    }

    /**
     * @see GalleryModule::performFactoryRegistrations()
     */
    function performFactoryRegistrations() {
	/* Register our graphics class with the factory */
	$ret = GalleryCoreApi::registerFactoryImplementation(
	    'GalleryToolkit', 'FfmpegToolkit', 'Ffmpeg',
	    'modules/ffmpeg/classes/FfmpegToolkit.class', 'ffmpeg', null);
	if ($ret->isError()) {
	    return $ret->wrap(__FILE__, __LINE__);
	}

	return GalleryStatus::success();
    }

    /**
     * @see GalleryModule::isRecommendedDuringInstall
     */
    function isRecommendedDuringInstall() {
	return true;
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
	 * then tack on other likely paths, based on our OS.
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
		$paths[] = $path . 'ffmpeg.exe';
	    }

	    $paths[] = 'C:\apps\ffmpeg\ffmpeg.exe';
	    $paths[] = 'C:\ffmpeg\ffmpeg.exe';
	} else if (GalleryUtilities::isA($platform, 'UnixPlatform')){
	    foreach (explode(':', getenv('PATH')) as $path) {
		$path = trim($path);
		if (empty($path)) {
		    continue;
		}
		if ($path{strlen($path)-1} != $slash) {
		    $path .= $slash;
		}
		$paths[] = $path . 'ffmpeg';
	    }

	    $paths[] = '/usr/bin/ffmpeg';
	    $paths[] = '/usr/local/bin/ffmpeg';
	    $paths[] = '/bin/ffmpeg';
	    $paths[] = '/sw/bin/ffmpeg';
	} else {
	    return array(GalleryStatus::success(), false);
	}

	/* Load any classes we require */
	GalleryCoreApi::relativeRequireOnce('modules/ffmpeg/classes/FfmpegToolkitHelper.class');

	/* Now try each path in turn to see which ones work */
	foreach ($paths as $path) {
	    list ($ret, $testResults) = FfmpegToolkitHelper::testBinary($path);
	    if ($ret->isError()) {
		/* Something went wrong with this path -- try the next path */
		continue;
	    }

	    $failCount = 0;
	    foreach ($testResults as $testResult) {
		/* At least one test should work, else this path is not a valid one */
		if (!$testResult['success']) {
		    $failCount++;
		}
	    }

	    if ($failCount == 0) {
		/* We have a winner */
		$ret = GalleryCoreApi::setPluginParameter('module', 'ffmpeg', 'path', $path);
		if ($ret->isError()) {
		    return array($ret->wrap(__FILE__, __LINE__), false);
		}

		return array(GalleryStatus::success(), true);
	    }
	}

	return array(GalleryStatus::success(), false);
    }

    /**
     * @see GalleryModule::activate()
     */
    function activate($postActivationEvent=true) {
	/* Load any classes we require */
	GalleryCoreApi::relativeRequireOnce('modules/ffmpeg/classes/FfmpegToolkitHelper.class');

	/* Find out what operations and properties we have available to us */
	list ($ret, $results) =
	    FfmpegToolkitHelper::getOperationsAndProperties();
	if ($ret->isError()) {
	    return array($ret->wrap(__FILE__, __LINE__), null);
	}

        list ($ret, $priority) = GalleryCoreApi::getToolkitPriorityById('Ffmpeg');
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

	foreach ($results['operations'] as $operation => $info) {
	    $ret = GalleryCoreApi::registerToolkitOperation('Ffmpeg',
							   $info['mimeTypes'],
							   $operation,
							   $info['params'],
							   $info['description'],
							   $info['outputMimeType'], $priority);
	    if ($ret->isError()) {
		return array($ret->wrap(__FILE__, __LINE__), null);
	    }
	}

	foreach ($results['properties'] as $property => $info) {
	    $ret = GalleryCoreApi::registerToolkitProperty('Ffmpeg',
							  $info['mimeTypes'],
							  $property,
							  $info['type'],
							  $info['description']);
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
     * @see GalleryModule::deactivate()
     */
    function deactivate($postDeactivationEvent=true) {
	list ($ret, $redirect) = parent::deactivate($postDeactivationEvent);
	if ($ret->isError()) {
	    return array($ret->wrap(__FILE__, __LINE__), null);
	}

	/* Unregister all of our properties and operations */
	$ret = GalleryCoreApi::unregisterToolkit('Ffmpeg');
	if ($ret->isError()) {
	    return array($ret->wrap(__FILE__, __LINE__), null);
	}

	return array(GalleryStatus::success(), $redirect);
    }

    /**
     * @see GalleryModule::needsConfiguration()
     */
    function needsConfiguration() {
	/* This module requires all fields to be filled out before it can be activated. */
	foreach (array('path') as $key) {
	    list ($ret, $value) = $this->getParameter($key);
	    if ($ret->isError()) {
		return array($ret->wrap(__FILE__, __LINE__), null);
	    }

	    if (empty($value)) {
		return array(GalleryStatus::success(), true);
	    }
	}

	return array(GalleryStatus::success(), false);
    }

    /**
     * @see GalleryModule::getSiteAdminViews()
     */
    function getSiteAdminViews() {
	return array(GalleryStatus::success(),
		     array(array('name' => $this->translate('Ffmpeg'),
				 'view' => 'ffmpeg.AdminFfmpeg')));
    }

    /**
     * @see GalleryModule::getConfigurationView()
     */
    function getConfigurationView() {
	return 'ffmpeg.AdminFfmpeg';
    }
}
?>

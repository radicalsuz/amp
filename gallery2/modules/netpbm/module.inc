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
 * @version $Revision: 1.73 $ $Date: 2005/09/10 20:28:14 $
 * @package NetPbm
 * @author Bharat Mediratta <bharat@menalto.com>
 */

/**
 * NetPBM Graphics Module
 *
 * This module provides the NetPBM graphics toolkit for Gallery
 *
 * @package NetPbm
 */
class NetPbmModule extends GalleryModule {

    function NetPbmModule() {
	global $gallery;

	$this->setId('netpbm');
	$this->setName($gallery->i18n('NetPBM'));
	$this->setDescription($gallery->i18n('NetPBM Graphics Toolkit'));
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
	list ($ret, $params) = GalleryCoreApi::fetchAllPluginParameters('module', 'netpbm');
	if ($ret->isError()) {
	    return $ret->wrap(__FILE__, __LINE__);
	}
	foreach (array('path' => '', 'jpegQuality' => '75', 'pnmtojpeg' => 'pnmtojpeg',
		       'bmptopnm' => 'bmptopnm', 'pnmcomp' => 'pnmcomp') as $key => $value) {
	    if (!isset($params[$key])) {
		$ret = $this->setParameter($key, $value);
		if ($ret->isError()) {
		    return $ret->wrap(__FILE__, __LINE__);
		}
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
	    'GalleryToolkit', 'NetPbmToolkit', 'NetPBM',
	    'modules/netpbm/classes/NetPbmToolkit.class', 'netpbm', null);
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
	    $paths[] = "C:\\Program Files\\netpbm\\";
	    $paths[] = "C:\\apps\\netpbm\\";
	    $paths[] = "C:\\apps\\jhead\\";
	    $paths[] = "C:\\netpbm\\";
	    $paths[] = "C:\\jhead\\";
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

	    $paths[] = '/usr/bin/';
	    $paths[] = '/usr/local/bin/';
	    $paths[] = '/usr/local/netpbm/bin/';
	    $paths[] = '/bin/';
	    $paths[] = '/sw/bin/';
	} else {
	    return array(GalleryStatus::success(), false);
	}

	/* Load any classes we require */
	GalleryCoreApi::relativeRequireOnce('modules/netpbm/classes/NetPbmToolkitHelper.class');

	/* Now try each path in turn to see which ones work */
	$success = false;
	foreach ($paths as $path) {
	    if (!$platform->isRestrictedByOpenBaseDir($path) && !$platform->file_exists($path)) {
		continue;
	    }

	    list ($ret, $testResults, $mimeTypes) = NetPbmToolkitHelper::testBinaries($path);
	    if ($ret->isError()) {
		return array($ret->wrap(__FILE__, __LINE__), false);
	    }

	    /* Take the first path found with at least jpeg support */
	    if (in_array('image/jpeg', $mimeTypes)) {
		$ret = $this->setParameter('path', $path);
		if ($ret->isError()) {
		    return array($ret->wrap(__FILE__, __LINE__), false);
		}
		$ret = NetPbmToolkitHelper::saveParameters($testResults);
		if ($ret->isError()) {
		    return array($ret->wrap(__FILE__, __LINE__), false);
		}
		$success = true;
		break;
	    }
	}
	/* Now try each path in turn to see which ones work for jhead */
	foreach ($paths as $path) {
	    if (!$platform->isRestrictedByOpenBaseDir($path) && !$platform->file_exists($path)) {
		continue;
	    }
	    list ($ret, $testResults) = NetPbmToolkitHelper::testJhead($path);
	    if ($ret->isError()) {
		return array($ret->wrap(__FILE__, __LINE__), false);
	    }
	    foreach ($testResults as $testResult) {
		if ($testResult['success'] && ($testResult['name'] == 'jhead')) {
		    $ret = $this->setParameter('jheadPath', $path);
		    if ($ret->isError()) {
			return array($ret->wrap(__FILE__, __LINE__), false);
		    }
		    break 2;
		}
	    }
	}

	return array(GalleryStatus::success(), $success);
    }

    /**
     * @see GalleryModule::activate()
     */
    function activate($postActivationEvent=true) {
	/* Load any classes we require */
	GalleryCoreApi::relativeRequireOnce('modules/netpbm/classes/NetPbmToolkitHelper.class');

	/* Find out what operations and properties we have available to us */
	list ($ret, $results) = NetPbmToolkitHelper::getOperationsAndProperties(true);
	if ($ret->isError()) {
	    return array($ret->wrap(__FILE__, __LINE__), null);
	}

	list ($ret, $priority) = GalleryCoreApi::getToolkitPriorityById('NetPBM');
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
	    $outputMimeType = isset($info['outputMimeType']) ? $info['outputMimeType'] : '';
	    $ret = GalleryCoreApi::registerToolkitOperation('NetPBM',
							   $info['mimeTypes'],
							   $operation,
							   $info['params'],
							   $info['description'],
							   $outputMimeType, $priority);
	    if ($ret->isError()) {
		return array($ret->wrap(__FILE__, __LINE__), null);
	    }
	}

	foreach ($results['properties'] as $property => $info) {
	    $ret = GalleryCoreApi::registerToolkitProperty('NetPBM',
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
	/*
	 * Unregister all of our properties and operations.  Do this before the parent deactivates
	 * so that any event handlers triggered by the deactivation will see the world as it will
	 * be after the deactivation is done.
	 */
	$ret = GalleryCoreApi::unregisterToolkit('NetPBM');
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
     * @see GalleryModule::needsConfiguration()
     */
    function needsConfiguration() {
	/* This module requires all fields to be filled out before it can be activated. */
	foreach (array('path', 'jpegQuality', 'pnmtojpeg', 'bmptopnm', 'pnmcomp') as $key) {
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
		     array(array('name' => $this->translate('NetPBM'),
				 'view' => 'netpbm.AdminNetPbm')));
    }

    /**
     * @see GalleryModule::getConfigurationView()
     */
    function getConfigurationView() {
	return 'netpbm.AdminNetPbm';
    }
}
?>

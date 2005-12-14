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
 * @package ArchiveUpload
 * @version $Revision: 1.14 $ $Date: 2005/09/10 20:28:11 $
 * @author Alan Harder <alan.harder@sun.com>
 */

/**
 * ArchiveUpload module
 *
 * This module provides toolkit support to extract items from
 * archive files such as a zip file
 *
 * @package ArchiveUpload
 */
class ArchiveUploadModule extends GalleryModule {

    function ArchiveUploadModule() {
	global $gallery;

	$this->setId('archiveupload');
	$this->setName($gallery->i18n('Archive Upload'));
	$this->setDescription($gallery->i18n('Extract items from uploaded zip files'));
	$this->setVersion('1.0.0');
	$this->setGroup('import', $this->translate('Import'));
	$this->setCallbacks('getSiteAdminViews');
	$this->setRequiredCoreApi(array(6, 0));
	$this->setRequiredModuleApi(array(2, 0));
    }

    /**
     * @see GalleryModule::upgrade()
     */
    function upgrade($currentVersion) {
	list ($ret, $params) = GalleryCoreApi::fetchAllPluginParameters('module', 'archiveupload');
	if ($ret->isError()) {
	    return $ret->wrap(__FILE__, __LINE__);
	}
	foreach (array('unzipPath' => '') as $key => $value) {
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
	$ret = GalleryCoreApi::registerFactoryImplementation(
	    'GalleryToolkit', 'ArchiveExtractToolkit', 'ArchiveUpload',
	    'modules/archiveupload/classes/ArchiveExtractToolkit.class', 'archiveupload', null);
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
	list ($ret, $needsConfiguration) = $this->needsConfiguration();
	if ($ret->isError()) {
	    return array($ret->wrap(__FILE__, __LINE__), false);
	}
	if (!$needsConfiguration) {
	    return array(GalleryStatus::success(), true);
	}

	GalleryCoreApi::relativeRequireOnce(
	    'modules/archiveupload/classes/ArchiveUploadHelper.class');
	$unzipPath = ArchiveUploadHelper::findUnzipBinary();
	if (empty($unzipPath)) {
	    return array(GalleryStatus::success(), false);
	}

	$this->setParameter('unzipPath', $unzipPath);
	if ($ret->isError()) {
	    return array($ret->wrap(__FILE__, __LINE__), false);
	}

	return array(GalleryStatus::success(), true);
    }

    /**
     * @see GalleryModule::needsConfiguration()
     */
    function needsConfiguration() {
	/* This module requires all fields to be filled out before it can be activated. */
	foreach (array('unzipPath') as $key) {
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
     * @see GalleryModule::activate()
     */
    function activate($postActivationEvent=true) {
	$ret = GalleryCoreApi::registerToolkitOperation('ArchiveUpload',
							array('application/zip'),
							'extract',
							array(),
							'extract files from an archive');
	if ($ret->isError()) {
	    return array($ret->wrap(__FILE__, __LINE__), null);
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
	$ret = GalleryCoreApi::unregisterToolkit('ArchiveUpload');
	if ($ret->isError()) {
	    return array($ret->wrap(__FILE__, __LINE__), null);
	}

	return array(GalleryStatus::success(), $redirect);
    }

    /**
     * @see GalleryModule::getSiteAdminViews()
     */
    function getSiteAdminViews() {
	return array(GalleryStatus::success(),
		     array(array('name' => $this->translate('Archive'),
				 'view' => 'archiveupload.ArchiveUploadSiteAdmin')));
    }

    /**
     * @see GalleryModule::getConfigurationView()
     */
    function getConfigurationView() {
	return 'archiveupload.ArchiveUploadSiteAdmin';
    }
}
?>

<?php
/*
 * $RCSfile: ErrorPage.inc,v $
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
 * @version $Revision: 1.2 $ $Date: 2005/08/23 03:49:02 $
 * @package GalleryCore
 * @subpackage UserInterface
 * @author Bharat Mediratta <bharat@menalto.com>
 */

/**
 * This view prepares all the information the theme requires to render
 * an error page.
 *
 * @package GalleryCore
 * @subpackage UserInterface
 */
class ErrorPageView extends GalleryView {

    function setError($error) {
	$this->_error = $error;
    }

    /**
     * @see GalleryView::loadTemplate
     */
    function loadTemplate(&$template, &$form) {
	global $gallery;

	$ErrorPage = array();

	list ($ret, $isAdmin) = GalleryCoreApi::isUserInSiteAdminGroup();
	$ErrorPage['isAdmin'] = ($ret->isSuccess() && $isAdmin);
	$ErrorPage['stackTrace'] = $this->_error->getAsHtml($isAdmin);

	/* Landing page for errors.. */
	$errorCode = $this->_error->getErrorCode();
	if ($errorCode & ERROR_OBSOLETE_DATA) {
	    $ErrorPage['code']['obsoleteData'] = true;
	}
	if ($errorCode & (ERROR_PERMISSION_DENIED | ERROR_BAD_PARAMETER)) {
	    $ErrorPage['code']['securityViolation'] = true;
	}
	if ($errorCode & ERROR_STORAGE_FAILURE) {
	    $ErrorPage['code']['storageFailure'] = true;
	}

	if ($ErrorPage['isAdmin']) {
	    $ErrorPage['phpversion'] = phpversion();
	    $ErrorPage['php_uname'] = php_uname();
	    $ErrorPage['php_sapi_name'] = php_sapi_name();
	    $ErrorPage['webserver'] = GalleryUtilities::getServerVar('SERVER_SOFTWARE');
	    $ErrorPage['browser'] = GalleryUtilities::getServerVar('HTTP_USER_AGENT');
	    if ($gallery->isStorageInitialized()) {
		$storage =& $gallery->getStorage();
		$ErrorPage['dbType'] = $storage->_impl->getAdoDbType();
		$ErrorPage['dbVersion'] = @$storage->_impl->getVersion();

		list ($ret, $list) = GalleryCoreApi::getToolkitOperationMimeTypes('thumbnail');
		if ($ret->isSuccess()) {
		    $toolkitList = array();
		    foreach ($list as $tmp) {
			$toolkitList = array_merge($toolkitList, $tmp);
		    }
		    $ErrorPage['toolkits'] = implode(', ', array_unique($toolkitList));
		}
	    }

	    GalleryCoreApi::relativeRequireOnce('modules/core/module.inc');
	    $installedVersions = CoreModule::getInstalledVersions();
	    $ErrorPage['version'] = $installedVersions['gallery'];
	}

	$urlGenerator =& $gallery->getUrlGenerator();
	$template->javascript($urlGenerator->generateUrl(
				  array('href' => 'lib/javascript/BlockToggle.js')));
	$template->setVariable('ErrorPage', $ErrorPage);
	return array(GalleryStatus::success(),
		     array('body' => 'modules/core/templates/ErrorPage.tpl'));
    }
}

?>

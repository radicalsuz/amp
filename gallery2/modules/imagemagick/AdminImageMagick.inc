<?php
/*
 * $RCSfile: AdminImageMagick.inc,v $
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
 * @version $Revision: 1.32 $ $Date: 2005/08/23 03:49:44 $
 * @package ImageMagick
 * @author Bharat Mediratta <bharat@menalto.com>
 */

/**
 * Required classes
 */
GalleryCoreApi::relativeRequireOnce('modules/imagemagick/classes/ImageMagickToolkitHelper.class');

/**
 * @package ImageMagick
 * @subpackage UserInterface
 */
class AdminImageMagickController extends GalleryController {

    /**
     * @see GalleryController::handleRequest
     */
    function handleRequest($form) {
	global $gallery;

	$ret = GalleryCoreApi::assertUserIsSiteAdministrator();
	if ($ret->isError()) {
	    return array($ret->wrap(__FILE__, __LINE__), null);
	}

	$error = array();
	$status = array();
	if (isset($form['action']['save'])) {

	    if (empty($form['path'])) {
		$error[] = 'form[error][path][missing]';
	    } else {
		/* Set the path.  Guarantee that it's got a trailing slash. */
		$platform = $gallery->getPlatform();
		$slash = $platform->getDirectorySeparator();
		$form['path'] = trim($form['path']);
		if (!empty($form['path'])) {
		    if ($form['path']{strlen($form['path'])-1} != $slash) {
			$form['path'] .= $slash;
		    }
		}

		list ($ret, $testResults) = ImageMagickToolkitHelper::testBinaries($form['path']);
		if ($ret->isError()) {
		    if ($ret->getErrorCode() & ERROR_BAD_PATH) {
			$error[] = 'form[error][path][badPath]';
		    } else {
			return array($ret->wrap(__FILE__, __LINE__), null);
		    }
		} else {
		    $failCount = 0;
		    foreach ($testResults as $testResult) {
			/* All tests should work, else this path is not a valid one */
			if (!$testResult['success']) {
			    $failCount++;
			}
		    }

		    if ($failCount > 0) {
			$error[] = 'form[error][path][testError]';
		    }
		}
	    }

	    if (empty($error)) {
		list ($ret, $params) =
		    GalleryCoreApi::fetchAllPluginParameters('module', 'imagemagick');
		if ($ret->isError()) {
		    return array($ret->wrap(__FILE__, __LINE__), null);
		}

		$ret = GalleryCoreApi::setPluginParameter(
		    'module', 'imagemagick', 'path', $form['path']);
		if ($ret->isError()) {
		    return array($ret->wrap(__FILE__, __LINE__), null);
		}

		/* Save some platform specific ImageMagick parameters */
		$ret = ImageMagickToolkitHelper::savePlatformParameters();
		if ($ret->isError()) {
		    return array($ret->wrap(__FILE__, __LINE__), null);
		}

		$ret = GalleryCoreApi::setPluginParameter(
		    'module', 'imagemagick', 'jpegQuality', $form['jpegQuality']);
		if ($ret->isError()) {
		    return array($ret->wrap(__FILE__, __LINE__), null);
		}
		if ($params['cmykSupport'] != 'none') {
		    $ret = GalleryCoreApi::setPluginParameter(
			'module', 'imagemagick', 'cmykSupport', $form['cmykSupport']);
		    if ($ret->isError()) {
			return array($ret->wrap(__FILE__, __LINE__), null);
		    }
		}

		list ($ret, $module) = GalleryCoreApi::loadPlugin('module', 'imagemagick');
		if ($ret->isError()) {
		    return array($ret->wrap(__FILE__, __LINE__), null);
		}
		list ($ret, $isActive) = $module->isActive();
		if ($ret->isError()) {
		    return array($ret->wrap(__FILE__, __LINE__), null);
		}
		/*
		 * If path or cmykSupport has changed and the module is active then
		 * reactivate to update supported mime types, operations, properties.
		 */
		if ($isActive && ($params['path'] != $form['path'] ||
				  $params['cmykSupport'] != $form['cmykSupport'])) {
		    list ($ret) = $module->reactivate();
		    if ($ret->isError()) {
			return array($ret->wrap(__FILE__, __LINE__), null);
		    }
		}

		$redirect['view'] = 'core.SiteAdmin';
		if ($isActive) {
		    $redirect['subView'] = 'imagemagick.AdminImageMagick';
		    $status['saved'] = 1;
		} else {
		    $redirect['subView'] = 'core.AdminModules';
		    $status['configured'] = 'imagemagick';
		}
	    }
	} else if (isset($form['action']['reset'])) {
	    $redirect['view'] = 'core.SiteAdmin';
	    $redirect['subView'] = 'imagemagick.AdminImageMagick';
	} else if (isset($form['action']['cancel'])) {
	    $redirect['view'] = 'core.SiteAdmin';
	    $redirect['subView'] = 'core.AdminModules';
	}

	if (!empty($redirect)) {
	    $results['redirect'] = $redirect;
	} else {
	    $results['delegate']['view'] = 'core.SiteAdmin';
	    $results['delegate']['subView'] = 'imagemagick.AdminImageMagick';
	}
	$results['status'] = $status;
	$results['error'] = $error;

	return array(GalleryStatus::success(), $results);
    }
}

/**
 * @package ImageMagick
 * @subpackage UserInterface
 */
class AdminImageMagickView extends GalleryView {

    /**
     * @see GalleryView::loadTemplate
     */
    function loadTemplate(&$template, &$form) {
	global $gallery;

	/* Load our default values if we didn't just come from this form. */
	if ($form['formName'] != 'AdminImageMagick') {
	    foreach (array('path', 'jpegQuality', 'cmykSupport') as $key) {
		list ($ret, $form[$key]) =
		    GalleryCoreApi::getPluginParameter('module', 'imagemagick', $key);
		if ($ret->isError()) {
		    return array($ret->wrap(__FILE__, __LINE__), null);
		}
	    }
	    $form['formName'] = 'AdminImageMagick';
	}

	/* Guarantee that the path has a trailing slash. */
	$platform = $gallery->getPlatform();
	$slash = $platform->getDirectorySeparator();
	$form['path'] = trim($form['path']);
	if (!empty($form['path'])) {
	    if ($form['path']{strlen($form['path'])-1} != $slash) {
		$form['path'] .= $slash;
	    }
	}

	/* Set up our jpeg quality selection list */
	for ($i = 100; $i >= 50; $i -= 5) {
	    $jpegQualityList[] = $i;
	}

	$platform = $gallery->getPlatform();
	$tests = array();
	$mimeTypes = array();
	$version = array();
	$debugSnippet = '';
	$failCount = 0;
	if (isset($form['action']['test'])) {
	    if (!empty($form['path'])) {
		$gallery->startRecordingDebugSnippet();
		list ($ret, $testResults) = ImageMagickToolkitHelper::testBinaries($form['path']);
		$debugSnippet = $gallery->stopRecordingDebugSnippet();

		if ($ret->isError()) {
		    if ($ret->getErrorCode() & ERROR_BAD_PATH) {
			$testResults = array();
			$form['error']['path']['bad'] = 1;
		    } else {
			return array($ret->wrap(__FILE__, __LINE__), null);
		    }
		} else {
		    $success = true;
		    foreach ($testResults as $testResult) {
			if (!$testResult['success']) {
			    $success = false;
			    $failCount++;
			}
			$tests[] = $testResult;
		    }
		    if ($success) {
			list ($ret, $mimeTypes) =
			    ImageMagickToolkitHelper::discoverMimeTypes($form['path']);
			if ($ret->isError()) {
			    return array($ret->wrap(__FILE__, __LINE__), null);
			}

			list ($ret, $version) =
			    ImageMagickToolkitHelper::discoverVersion($form['path']);
			if ($ret->isError()) {
			    return array($ret->wrap(__FILE__, __LINE__), null);
			}
		    }
		}
	    } else {
		$form['error']['path']['missing'] = 1;
	    }
	}

	list ($ret, $module) = GalleryCoreApi::loadPlugin('module', 'imagemagick');
	if ($ret->isError()) {
	    return array($ret->wrap(__FILE__, __LINE__), null);
	}
	list ($ret, $isActive) = $module->isActive();
	if ($ret->isError()) {
	    return array($ret->wrap(__FILE__, __LINE__), null);
	}

	$AdminImageMagick = array();
	$AdminImageMagick['tests'] = $tests;
	$AdminImageMagick['version'] = $version;
	$AdminImageMagick['mimeTypes'] = $mimeTypes;
	$AdminImageMagick['jpegQualityList'] = $jpegQualityList;
	$AdminImageMagick['debugSnippet'] = $debugSnippet;
	$AdminImageMagick['failCount'] = $failCount;
	$AdminImageMagick['isConfigure'] = !$isActive;

	if ($failCount > 0) {
	    $template->javascript('lib/javascript/BlockToggle.js');
	}

	$template->setVariable('AdminImageMagick', $AdminImageMagick);
	$template->setVariable('controller', 'imagemagick.AdminImageMagick');

	return array(GalleryStatus::success(),
		     array('body' => 'modules/imagemagick/templates/AdminImageMagick.tpl'));
    }
}
?>

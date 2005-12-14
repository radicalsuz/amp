<?php
/*
 * $RCSfile: AdminFfmpeg.inc,v $
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
 * @version $Revision: 1.17 $ $Date: 2005/08/23 03:49:42 $
 * @package Ffmpeg
 * @author Bharat Mediratta <bharat@menalto.com>
 */

/**
 * Required class
 */
GalleryCoreApi::relativeRequireOnce('modules/ffmpeg/classes/FfmpegToolkitHelper.class');

/**
 * @package Ffmpeg
 * @subpackage UserInterface
 */
class AdminFfmpegController extends GalleryController {

    /**
     * @see GalleryController::handleRequest()
     */
    function handleRequest(&$form) {
	global $gallery;

	$ret = GalleryCoreApi::assertUserIsSiteAdministrator();
	if ($ret->isError()) {
	    return array($ret->wrap(__FILE__, __LINE__), null);
	}

	$status = array();
	$error = array();
	if (isset($form['action']['save']) || isset($form['action']['test'])) {

	    if (empty($form['path'])) {
		$error[] = 'form[error][path][missing]';
	    } else {

		/* Verify the path */
		$platform = $gallery->getPlatform();
		$slash = $platform->getDirectorySeparator();
		if (!$platform->isRestrictedByOpenBaseDir($form['path'])) {
		    if ($platform->file_exists($form['path']) && $platform->is_dir($form['path'])) {
			$form['path'] = trim($form['path']);
			if (!empty($form['path'])) {
			    if ($form['path']{strlen($form['path'])-1} != $slash) {
				$form['path'] .= $slash;
			    }
			}

			/* Try adding on "ffmpeg" */
			$path = $form['path'] . 'ffmpeg';

			if (!$platform->file_exists($path)) {
			    $error[] = 'form[error][path][badPath]';
			} else {
			    /* Got a match */
			    $form['path'] = $path;
			    GalleryUtilities::putRequestVariable('form.path', $path);
			}
		    }

		    if (empty($error)) {
			if ($platform->is_file($form['path'])
				&& !$platform->is_executable($form['path'])) {
			    $error[] = 'form[error][path][notExecutable]';
			}
		    }
		}

		if (empty($error)) {
		    list ($ret, $testResults) =
			FfmpegToolkitHelper::testBinary($form['path']);
		    if ($ret->isError()) {
			if ($ret->getErrorCode() & ERROR_BAD_PATH) {
			    $error[] = 'form[error][path][badPath]';
			} else {
			    return array($ret->wrap(__FILE__, __LINE__), null);
			}
		    } else {
			$failCount = 0;
			foreach ($testResults as $testResult) {
			    /* At least one test should work, else this path is not a valid one */
			    if (!$testResult['success']) {
				$failCount++;
			    }
			}

			if ($failCount > 0) {
			    $error[] = 'form[error][path][testError]';
			}
		    }
		}
	    }

	    if (empty($error) && isset($form['action']['save'])) {
		$ret = GalleryCoreApi::setPluginParameter('module', 'ffmpeg',
							  'path', $form['path']);
		if ($ret->isError()) {
		    return array($ret->wrap(__FILE__, __LINE__), null);
		}

		list ($ret, $module) = GalleryCoreApi::loadPlugin('module', 'ffmpeg');
		if ($ret->isError()) {
		    return array($ret->wrap(__FILE__, __LINE__), null);
		}
		list ($ret, $isActive) = $module->isActive();
		if ($ret->isError()) {
		    return array($ret->wrap(__FILE__, __LINE__), null);
		}

		$redirect['view'] = 'core.SiteAdmin';
		if ($isActive) {
		    $redirect['subView'] = 'ffmpeg.AdminFfmpeg';
		    $status['saved'] = 1;
		} else {
		    $redirect['subView'] = 'core.AdminModules';
		    $status['configured'] = 'ffmpeg';
		}
	    }
	} else if (isset($form['action']['reset'])) {
	    $redirect['view'] = 'core.SiteAdmin';
	    $redirect['subView'] = 'ffmpeg.AdminFfmpeg';
	} else if (isset($form['action']['cancel'])) {
	    $redirect['view'] = 'core.SiteAdmin';
	    $redirect['subView'] = 'core.AdminModules';
	}

	if (!empty($redirect)) {
	    $results['redirect'] = $redirect;
	} else {
	    $results['delegate']['view'] = 'core.SiteAdmin';
	    $results['delegate']['subView'] = 'ffmpeg.AdminFfmpeg';
	}
	$results['status'] = $status;
	$results['error'] = $error;

	return array(GalleryStatus::success(), $results);
    }
}

/**
 * @package Ffmpeg
 * @subpackage UserInterface
 */
class AdminFfmpegView extends GalleryView {

    /**
     * @see GalleryView::loadTemplate
     */
    function loadTemplate(&$template, &$form) {
	global $gallery;

	/* Load our default values if we didn't just come from this form. */
	if ($form['formName'] != 'AdminFfmpeg') {
	    foreach (array('path') as $key) {
		list ($ret, $form[$key]) =
		    GalleryCoreApi::getPluginParameter('module', 'ffmpeg', $key);
		if ($ret->isError()) {
		    return array($ret->wrap(__FILE__, __LINE__), null);
		}
	    }
	    $form['formName'] = 'AdminFfmpeg';
	}

	$platform = $gallery->getPlatform();
	$tests = array();
	$mimeTypes = array();
	$debugSnippet = '';
	$failCount = 0;
	if (isset($form['action']['test'])) {
	    if (empty($form['path'])) {
		$form['error']['path']['missing'] = 1;
	    } else {
		$gallery->startRecordingDebugSnippet();
		list ($ret, $tests, $mimeTypes) = FfmpegToolkitHelper::testBinary($form['path']);
		$debugSnippet = $gallery->stopRecordingDebugSnippet();
		if ($ret->isError()) {
		    if ($ret->getErrorCode() & ERROR_BAD_PATH) {
			$form['error']['path']['badPath'] = 1;
		    } else {
			return array($ret->wrap(__FILE__, __LINE__), null);
		    }
		}

		foreach ($tests as $test) {
		    if (!$test['success']) {
			$failCount++;
		    }
		}
	    }
	}

	list ($ret, $module) = GalleryCoreApi::loadPlugin('module', 'ffmpeg');
	if ($ret->isError()) {
	    return array($ret->wrap(__FILE__, __LINE__), null);
	}
	list ($ret, $isActive) = $module->isActive();
	if ($ret->isError()) {
	    return array($ret->wrap(__FILE__, __LINE__), null);
	}

	$AdminFfmpeg = array();
	$AdminFfmpeg['tests'] = $tests;
	$AdminFfmpeg['mimeTypes'] = $mimeTypes;
	$AdminFfmpeg['debugSnippet'] = $debugSnippet;
	$AdminFfmpeg['failCount'] = $failCount;
	$AdminFfmpeg['isConfigure'] = !$isActive;

	if ($failCount > 0) {
	    $template->javascript('lib/javascript/BlockToggle.js');
	}

	$template->setVariable('AdminFfmpeg', $AdminFfmpeg);
	$template->setVariable('controller', 'ffmpeg.AdminFfmpeg');
	return array(GalleryStatus::success(),
		     array('body' => 'modules/ffmpeg/templates/AdminFfmpeg.tpl'));
    }
}
?>

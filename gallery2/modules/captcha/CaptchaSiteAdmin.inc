<?php
/*
 * $RCSfile: CaptchaSiteAdmin.inc,v $
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
 * @version $Revision: 1.4 $ $Date: 2005/08/23 03:49:00 $
 * @package Captcha
 * @author Stefan Ioachim <stefanioachim@gmail.com>
 * @author Bharat Mediratta <bharat@menalto.com>
 */

/**
 * @package Captcha
 * @subpackage UserInterface
 */
class CaptchaSiteAdminController extends GalleryController {

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
	    if (!isset($form['failedAttemptThreshold']) ||
		    !is_numeric($form['failedAttemptThreshold'])) {
		/* We don't allow free-form input in the HTML, so this should never happen. */
		return array(GalleryStatus::error(ERROR_BAD_PARAMETER, __FILE__, __LINE__), null);
	    }

	    /* Make sure that we're between 0 and 5, then store the value */
	    $form['failedAttemptThreshold'] = min($form['failedAttemptThreshold'], 5);
	    $form['failedAttemptThreshold'] = max($form['failedAttemptThreshold'], 0);
	    $ret = GalleryCoreApi::setPluginParameter(
		'module', 'captcha', 'failedAttemptThreshold', $form['failedAttemptThreshold']);
	    if ($ret->isError()) {
		return array($ret->wrap(__FILE__, __LINE__), null);
	    }

	    $redirect['view'] = 'core.SiteAdmin';
	    $redirect['subView'] = 'captcha.CaptchaSiteAdmin';
	    $status['saved'] = 1;
	} else if (isset($form['action']['cancel'])) {
	    $redirect['view'] = 'core.SiteAdmin';
	    $redirect['subView'] = 'captcha.CaptchaSiteAdmin';
	}

	if (!empty($redirect)) {
	    $results['redirect'] = $redirect;
	} else {
	    $results['delegate']['view'] = 'core.SiteAdmin';
	    $results['delegate']['subView'] = 'captcha.CaptchaSiteAdmin';
	}
	$results['status'] = $status;
	$results['error'] = $error;

	return array(GalleryStatus::success(), $results);
    }
}

/**
 * @package Captcha
 * @subpackage UserInterface
 */
class CaptchaSiteAdminView extends GalleryView {
    /**
     * @see GalleryView::loadTemplate
     */
    function loadTemplate(&$template, &$form) {
	global $gallery;

	/* Load our default values if we didn't just come from this form. */
	if ($form['formName'] != 'CaptchaSiteAdmin') {
	    $form['formName'] = 'CaptchaSiteAdmin';

	    list ($ret, $form['failedAttemptThreshold']) =
		GalleryCoreApi::getPluginParameter('module', 'captcha', 'failedAttemptThreshold');
	    if ($ret->isError()) {
		return array($ret->wrap(__FILE__, __LINE__), null);
	    }
	}

	/* Set up our failed attempts selection list */
	$CaptchaSiteAdmin = array();
	$CaptchaSiteAdmin['failedAttemptThresholdList'] = range(0, 5);

	$template->setVariable('CaptchaSiteAdmin', $CaptchaSiteAdmin);
	$template->setVariable('controller', 'captcha.CaptchaSiteAdmin');

	return array(GalleryStatus::success(),
		     array('body' => 'modules/captcha/templates/CaptchaSiteAdmin.tpl'));
    }
}
?>

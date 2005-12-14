<?php
/*
 * $RCSfile: CaptchaConfigAdmin.inc,v $
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
 * @version $Revision: 1.6 $ $Date: 2005/08/23 03:49:00 $
 * @package Captcha
 * @author Stefan Ioachim <stefanioachim@gmail.com>
 * @author Bharat Mediratta <bharat@menalto.com>
 */

/**
 * @package Captcha
 * @subpackage UserInterface
 */

class CaptchaConfigAdminController extends GalleryController {
    /**
     * @see GalleryController::handleRequest()
     */
    function handleRequest($form) {
	global $gallery;

	$ret = GalleryCoreApi::assertUserIsSiteAdministrator();
	if ($ret->isError()) {
	    return array($ret->wrap(__FILE__, __LINE__), null);
	}

	$results['status'] = array();
	$results['error'] = array();
	$results['redirect'] = array('view' => 'core.SiteAdmin', 'subView' => 'core.AdminModules');
	return array(GalleryStatus::success(), $results);
    }
}


/**
 * Show the user which required GD functions are missing.  They can't install the module, and
 * there's nothing we can do about it.
 */
class CaptchaConfigAdminView extends GalleryView {

    /**
     * @see GalleryView::loadTemplate
     */
    function loadTemplate(&$template, &$form) {
	global $gallery;

	if ($form['formName'] != 'CaptchaConfigAdmin') {
	    $form['formName'] = 'CaptchaConfigAdmin';
	}

	GalleryCoreApi::relativeRequireOnce('modules/captcha/classes/CaptchaHelper.class');
	$gdReport = CaptchaHelper::testRequiredGdFunctions();

	$CaptchaConfigAdmin = array();
	$CaptchaConfigAdmin['gdReport'] = $gdReport;
	$template->setVariable('CaptchaConfigAdmin', $CaptchaConfigAdmin);
	$template->setVariable('controller', 'captcha.CaptchaConfigAdmin');
	return array(GalleryStatus::success(),
		     array('body' => 'modules/captcha/templates/CaptchaConfigAdmin.tpl'));
    }
}
?>

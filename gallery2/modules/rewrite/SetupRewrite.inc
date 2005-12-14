<?php
/*
 * $RCSfile: SetupRewrite.inc,v $
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
 * @package Rewrite
 * @version $Revision: 1.14 $ $Date: 2005/08/23 03:49:53 $
 * @author Douglas Cau <douglas@cau.se>
 */

/**
 * Required class
 */
GalleryCoreApi::relativeRequireOnce('modules/rewrite/classes/RewriteHelper.class');

/**
 * @package Rewrite
 * @subpackage UserInterface
 */
class SetupRewriteController extends GalleryController {

    /**
     * @see GalleryController::handleRequest()
     */
    function handleRequest(&$form) {
	global $gallery;
	$platform = $gallery->getPlatform();

	$ret = GalleryCoreApi::assertUserIsSiteAdministrator();
	if ($ret->isError()) {
	    return array($ret->wrap(__FILE__, __LINE__), null);
	}

	$status = $error = array();

	if (isset($form['action']['test'])) {
	    $redirect['view'] = 'core.SiteAdmin';
	    $redirect['subView'] = 'rewrite.SetupRewrite';
	} else if (isset($form['action']['force'])) {
	    if (isset($form['force']['test1'])) {
		$ret = GalleryCoreApi::setPluginParameter('module', 'rewrite',
		    'status', serialize(array('needOptions' => false, 'forced' => 1)));
		if ($ret->isError()) {
		    return array($ret->wrap(__FILE__, __LINE__), null);
		}
	    }

	    if (isset($form['force']['test2'])) {
		$ret = GalleryCoreApi::setPluginParameter('module', 'rewrite',
		    'status', serialize(array('needOptions' => true, 'forced' => 1)));
		if ($ret->isError()) {
		    return array($ret->wrap(__FILE__, __LINE__), null);
		}
	    }

	    $redirect['view'] = 'core.SiteAdmin';
	    $redirect['subView'] = 'rewrite.SetupRewrite';
	} else if (isset($form['action']['done'])) {
	    if (isset($form['force']['test1'])) {
		$ret = GalleryCoreApi::setPluginParameter('module', 'rewrite',
		    'status', serialize(array('needOptions' => false, 'forced' => 1)));
		if ($ret->isError()) {
		    return array($ret->wrap(__FILE__, __LINE__), null);
		}
	    }

	    if (isset($form['force']['test2'])) {
		$ret = GalleryCoreApi::setPluginParameter('module', 'rewrite',
		    'status', serialize(array('needOptions' => true, 'forced' => 1)));
		if ($ret->isError()) {
		    return array($ret->wrap(__FILE__, __LINE__), null);
		}
	    }

	    $redirect['view'] = 'core.SiteAdmin';
	    $redirect['subView'] = 'core.AdminModules';
	}

	if (!empty($redirect)) {
	    $results['redirect'] = $redirect;
	} else {
	    $results['delegate']['view'] = 'core.SiteAdmin';
	    $results['delegate']['subView'] = 'rewrite.SetupRewrite';
	}
	$results['status'] = $status;
	$results['error'] = $error;

	return array(GalleryStatus::success(), $results);
    }
}

/**
 * @package Rewrite
 * @subpackage UserInterface
 */
class SetupRewriteView extends GalleryView {

    /**
     * @see GalleryView::loadTemplate
     */
    function loadTemplate(&$template, &$form) {
	global $gallery;
	$urlGenerator =& $gallery->getUrlGenerator();
	$SetupRewrite = array();

	if ($form['formName'] != 'SetupRewrite') {
	    $form['formName'] = 'SetupRewrite';
	}

	$baseUrl = parse_url($urlGenerator->generateUrl(
	    array('href' => 'modules/rewrite/data/'), false));
	$SetupRewrite['customFile'] = dirname(__FILE__) . '/data/mod_rewrite/custom/.htaccess';
	$SetupRewrite['customLine'] = 'RewriteBase ' . $baseUrl['path'] . 'mod_rewrite/custom/';
	$SetupRewrite['customFileNoOptions'] = dirname(__FILE__) .
	    '/data/mod_rewrite_no_options/custom/.htaccess';
	$SetupRewrite['customLineNoOptions'] = 'RewriteBase ' . $baseUrl['path'] .
	    'mod_rewrite_no_options/custom/';

	if (!strncmp($baseUrl['path'], '/gallery2/', 10)) {
	    $target = 'gallery2';
	} else if (!strncmp($baseUrl['path'], '/gallery/', 9)) {
	    $target = 'gallery';
	} else {
	    $target = 'custom';
	}
	$SetupRewrite['test1'] = $urlGenerator->generateUrl(
	    array('href' => 'modules/rewrite/data/mod_rewrite_no_options/'.
		$target . '/Rewrite.txt'));
	$SetupRewrite['test2'] = $urlGenerator->generateUrl(
	    array('href' => 'modules/rewrite/data/mod_rewrite/'.
		$target . '/Rewrite.txt'));

	list ($ret, $SetupRewrite['htaccessPath']) = RewriteHelper::getHtaccessPath();
	if ($ret->isError()) {
	    return array($ret->wrap(__FILE__, __LINE__), null);
	}
	list ($ret, $SetupRewrite['htaccessCode']) = RewriteHelper::checkFile();
	if ($ret->isError()) {
	    return array($ret->wrap(__FILE__, __LINE__), null);
	}
	list ($ret, $SetupRewrite['apacheCode']) = RewriteHelper::checkModRewrite();
	if ($ret->isError()) {
	    return array($ret->wrap(__FILE__, __LINE__), null);
	}

	$template->setVariable('SetupRewrite', $SetupRewrite);
	$template->setVariable('controller', 'rewrite.SetupRewrite');

	$template->javascript('lib/javascript/BlockToggle.js');
	return array(GalleryStatus::success(),
		array('body' => 'modules/rewrite/templates/SetupRewrite.tpl'));
    }
}
?>

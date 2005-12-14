<?php
/*
 * $RCSfile: SlideshowAppletSiteAdmin.inc,v $
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
 * @version $Revision: 1.1 $ $Date: 2005/08/26 05:15:14 $
 * @package SlideshowApplet
 * @author Pierre-Luc Paour <paour@users.sf.net>
 */

/**
 * @package SlideshowApplet
 * @subpackage UserInterface
 */
class SlideshowAppletSiteAdminController extends GalleryController {

    /**
     * @see GalleryController::handleRequest
     */
    function handleRequest($form) {
	/* Make sure we have adequate permissions */
	$ret = GalleryCoreApi::assertUserIsSiteAdministrator();
	if ($ret->isError()) {
	    return array($ret->wrap(__FILE__, __LINE__), null);
	}

	$status = array();
	$error = array();
	if (isset($form['action']['save'])) {
	    if (!isset($form['slideshowMaxPictures'])
	    	    || !is_numeric($form['slideshowMaxPictures'])
	    	    || ((int)$form['slideshowMaxPictures']) < 0) {
		$error[] = 'form[error][limit]';
	    } else {
		$key = 'slideshowRecursive';
		$ret = GalleryCoreApi::setPluginParameter('module', 'slideshowapplet', $key,
		    (isset($form[$key]) && $form[$key]) ? 1 : 0);
		if ($ret->isError()) {
		    return array($ret->wrap(__FILE__, __LINE__), null);
		}

		$key = 'slideshowMaxPictures';
		$ret = GalleryCoreApi::setPluginParameter('module', 'slideshowapplet', $key,
		    $form[$key]);
		if ($ret->isError()) {
		    return array($ret->wrap(__FILE__, __LINE__), null);
		}

		$status['saved'] = 1;
	    }
	}

	/* Figure out where to redirect upon success */
	$method = empty($error) ? 'redirect' : 'delegate';
	$results[$method]['view'] = 'core.SiteAdmin';
	$results[$method]['subView'] = 'slideshowapplet.SlideshowAppletSiteAdmin';
	$results['status'] = $status;
	$results['error'] = $error;

	return array(GalleryStatus::success(), $results);
    }
}

/**
 * @package Remote
 * @subpackage UserInterface
 */
class SlideshowAppletSiteAdminView extends GalleryView {

    /**
     * @see GalleryView::loadTemplate
     */
    function loadTemplate(&$template, &$form) {
	if (!isset($form['slideshowRecursive'])) {
	    $form['slideshowRecursive'] = false;
	}
	if (!isset($form['slideshowMaxPictures'])) {
	    $form['slideshowMaxPictures'] = 0;
	}
	if ($form['formName'] != 'SlideshowAppletSiteAdmin') {
	    $form['formName'] = 'SlideshowAppletSiteAdmin';
	    list ($ret, $params) = GalleryCoreApi::fetchAllPluginParameters('module', 'slideshowapplet');
	    if ($ret->isError()) {
		return array($ret->wrap(__FILE__, __LINE__), null);
	    }
	    foreach ($params as $key => $value) {
		$form[$key] = $value;
	    }
	}

	$template->setVariable('controller', 'slideshowapplet.SlideshowAppletSiteAdmin');
	return array(GalleryStatus::success(),
		     array('body' => 'modules/slideshowapplet/templates/SlideshowAppletSiteAdmin.tpl'));
    }
}
?>

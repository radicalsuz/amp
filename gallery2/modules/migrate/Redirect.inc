<?php
/*
 * $RCSfile: Redirect.inc,v $
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
 * @version $Revision: 1.15 $ $Date: 2005/08/23 03:49:45 $
 * @package Migrate
 * @subpackage Redirect
 * @author Joseph Elwell <jelwell@yahoo.com>
 */

/**
 * This controller handles rewriting URLs from Gallery1 to the new style Gallery2 locations
 *
 * @package Migrate
 * @subpackage Redirect
 */
class RedirectController extends GalleryController {

    /**
    * @see GalleryController::handleRequest()
    */
    function handleRequest($form) {
	GalleryCoreApi::relativeRequireOnce('modules/migrate/classes/G1MigrateHelper.class');
	global $gallery;

	$path = GalleryUtilities::getRequestVariables('path');
	$path = preg_split('|/|', $path, -1, PREG_SPLIT_NO_EMPTY);
	$view = 'core.ShowItem';

	if (count($path) > 1) {
	    /* Check for direct link to image file (name.type, name.thumb.type, name.sized.type) */
	    $file = explode('.', $path[1]);
	    switch (count($file)) {
	    case 3:
		if ($file[1] == 'sized') {
		    $derivative = DERIVATIVE_TYPE_IMAGE_RESIZE;
		} else if ($file[1] == 'thumb') {
		    $derivative = DERIVATIVE_TYPE_IMAGE_THUMBNAIL;
		}
	    case 2:
		$view = 'core.DownloadItem';
		$path[1] = $file[0];
		if (!isset($derivative)) {
		    $derivative = DERIVATIVE_TYPE_IMAGE_PREFERRED;
		}
	    }

	    list ($ret, $id) = G1MigrateHelper::fetchMapping($path[0], $path[1]);
	    if ($ret->isError()) {
		return array($ret->wrap(__FILE__, __LINE__), null);
	    }

	    if (isset($id) && isset($derivative)) {
		switch ($derivative) {
		case DERIVATIVE_TYPE_IMAGE_PREFERRED:
		    list ($ret, $data) = GalleryCoreApi::fetchPreferredsByItemIds(array($id));
		    if ($ret->isError()) {
			return array($ret->wrap(__FILE__, __LINE__), null);
		    }
		    break;
		case DERIVATIVE_TYPE_IMAGE_RESIZE:
		    list ($ret, $data) = GalleryCoreApi::fetchResizesByItemIds(array($id));
		    if ($ret->isError()) {
			return array($ret->wrap(__FILE__, __LINE__), null);
		    }
		    break;
		case DERIVATIVE_TYPE_IMAGE_THUMBNAIL:
		    list ($ret, $data) = GalleryCoreApi::fetchThumbnailsByItemIds(array($id));
		    if ($ret->isError()) {
			return array($ret->wrap(__FILE__, __LINE__), null);
		    }
		    break;
		}
		if (!empty($data)) {
		    $item = $data[$id];
		    if (is_array($item)) {
			$item = array_shift($item);
		    }
		    $id = $item->getId();
		}
	    }
	} else {
	    list ($ret, $id) = G1MigrateHelper::fetchMapping($path[0]);
	    if ($ret->isError()) {
		return array($ret->wrap(__FILE__, __LINE__), null);
	    }
	}

	if (!isset($id)) {
	    header('HTTP/1.0 404 Not Found');
	    exit;
	}

	/* Redirect browser */
	$results = array('redirect' => array('view' => $view, 'itemId' => $id),
			 'status' => array(), 'error' => array());

	$scriptName = GalleryUtilities::getServerVar('PHP_SELF');
	if (empty($scriptName)) {
	    /* PHP as CGI needs to use PHP_SELF; fallback to SCRIPT_NAME just in case */
	    $scriptName = GalleryUtilities::getServerVar('SCRIPT_NAME');
	}
	if (strpos($scriptName, GALLERY_MAIN_PHP) !== false) {
	    /*
	     * $scriptName may not be the right path.. see http://bugs.php.net/bug.php?id=31843
	     * If not, omit 'href' in redirect params which means we hope REQUEST_URI has been
	     * rewritten to the correct path (that is the case on php 5.0.3 cgi at least).
	     */
	    $results['redirect']['href'] = GalleryUrlGenerator::makeUrl($scriptName);
	}

	list ($full, $page) = GalleryUtilities::getRequestVariablesNoPrefix('full', 'page');
	if (!empty($full)) {
	    $results['redirect']['imageViewsIndex'] = 1;
	}
	if (!empty($page)) {
	    $results['redirect']['page'] = $page;
	}

	return array(GalleryStatus::success(), $results);
    }
}

/**
 * View returns .htaccess file
 */
class RedirectView extends GalleryView {
    /**
     * @see GalleryView::loadTemplate
     */
    function loadTemplate(&$template, &$form) {
	global $gallery;

	$ret = GalleryCoreApi::assertUserIsSiteAdministrator();
	if ($ret->isError()) {
	    return array($ret->wrap(__FILE__, __LINE__), null);
	}

	header('Content-type: text/plain');
	return array(GalleryStatus::success(),
		     array('html' => 'modules/migrate/templates/Htaccess.tpl'));
    }
}
?>
<?php
/*
 * $RCSfile: ShowItem.inc,v $
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
 * @version $Revision: 1.71 $ $Date: 2005/09/04 19:38:23 $
 * @package GalleryCore
 * @subpackage UserInterface
 * @author Bharat Mediratta <bharat@menalto.com>
 */

class ShowItemController extends GalleryController {

    /**
     * @see GalleryController::handleRequest
     */
    function handleRequest($form) {
	global $gallery;

	/*
	 * Note that this always changes user preview mode; if we add management of other
	 * variables to this controller then we should guard this properly.  (Maybe delete this
	 * comment after writing the unit test that verifies it).
	 */
	$guestPreviewMode = GalleryUtilities::getRequestVariables('guestPreviewMode');
	if ($guestPreviewMode != null) {
	    $session =& $gallery->getSession();
	    $session->put('theme.guestPreviewMode', $guestPreviewMode ? 1 : 0);
	}

	return array(GalleryStatus::success(),
		     array('return' => 1,
			   'status' => array(),
			   'error' => array()));
    }
}

/**
 * This controller will handle the rendering of an album or item.
 *
 * @package GalleryCore
 * @subpackage UserInterface
 */
class ShowItemView extends GalleryView {

    /**
     * @see GalleryView::loadTemplate
     */
    function loadTemplate(&$template, &$form) {
	global $gallery;

	list ($ret, $item) = $this->_getItem();
	if ($ret->isError()) {
	    return array($ret->wrap(__FILE__, __LINE__), null);
	}

	/* Make sure we have permission to view this item */
	$ret = GalleryCoreApi::assertHasItemPermission($item->getId(), 'core.view');
	if ($ret->isError()) {
	    if ($ret->getErrorCode() & ERROR_PERMISSION_DENIED) {
		list ($ret2, $anonymousId) = GalleryCoreApi::getPluginParameter(
		    'module', 'core', 'id.anonymousUser');
		if ($ret2->isError()) {
		    return array($ret->wrap(__FILE__, __LINE__), null);
		}
		if ($gallery->getActiveUserId() == $anonymousId) {
		    /* Redirect to login view */
		    return array(GalleryStatus::success(),
				 array('redirect' => GalleryCapabilities::get('loginRedirect')));
		}
		/* Try to redirect to root */
		list ($ret2, $rootId) = GalleryCoreApi::getPluginParameter(
		    'module', 'core', 'id.rootAlbum');
		if ($ret2->isError()) {
		    return array($ret->wrap(__FILE__, __LINE__), null);
		}
		if ($item->getId() == $rootId) {
		    /* No permission on root album; redirect to login view */
		    return array(GalleryStatus::success(),
				 array('redirect' => GalleryCapabilities::get('loginRedirect')));
		}
		return array(GalleryStatus::success(), array('redirect' =>
		    array('view' => 'core.ShowItem', 'itemId' => $rootId)));
	    }
	    return array($ret->wrap(__FILE__, __LINE__), null);
	}

	/* Increment the view count */
	$ret = GalleryCoreApi::incrementItemViewCount($item->getId());
	if ($ret->isError()) {
	    return array($ret->wrap(__FILE__, __LINE__), null);
	}

	return array(GalleryStatus::success(), array());
    }

    /**
     * @see GalleryView::getViewDescription()
     */
    function getViewDescription() {
	global $gallery;

	list ($ret, $item) = $this->_getItem();
	if ($ret->isError()) {
	    return array($ret->wrap(__FILE__, __LINE__), null);
	}

	$typeName = $item->itemTypeName(true);
	return array(GalleryStatus::success(), $typeName[1]);
    }
}
?>

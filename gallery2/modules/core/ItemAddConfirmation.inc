<?php
/*
 * $RCSfile: ItemAddConfirmation.inc,v $
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
 * @version $Revision: 1.8 $ $Date: 2005/08/23 03:49:02 $
 * @package GalleryCore
 * @subpackage UserInterface
 * @author Bharat Mediratta <bharat@menalto.com>
 */

/**
 * This view will show the confirmation that an user has been edited successfully
 *
 * @package GalleryCore
 * @subpackage UserInterface
 *
 */
class ItemAddConfirmationView extends GalleryView {

    /**
     * @see GalleryView::loadTemplate
     */
    function loadTemplate(&$template, &$form) {
	global $gallery;

	$itemId = GalleryUtilities::getRequestVariables('itemId');
	list ($ret, $item) = GalleryCoreApi::loadEntitiesById($itemId);
	if ($ret->isError()) {
	    return array($ret->wrap(__FILE__, __LINE__), null);
	}

	/*
	 * We know what kind of operation happened, but for now let's compress
	 * it down so that the template doesn't have to know.
	 */
	$status = $template->getVariable('status');
	if (!empty($status['addedFiles'])) {
	    $count = sizeof($status['addedFiles']);
	    for ($i = 0; $i < $count; $i++) {
		list ($ret, $entity) =
		    GalleryCoreApi::loadEntitiesById($status['addedFiles'][$i]['id']);
		if ($ret->isSuccess()) {
		    $status['addedFiles'][$i]['exists'] = true;
		} else if ($ret->getErrorCode() & ERROR_MISSING_OBJECT) {
		    $status['addedFiles'][$i]['exists'] = false;
		} else {
		    return array($ret->wrap(__FILE__, __LINE__), null);
		}
	    }
	} else {
	    $count = 0;
	    $status['addedFiles'] = array();
	}

	$ItemAddConfirmation = array();
	$ItemAddConfirmation['count'] = $count;
	$ItemAddConfirmation['status'] = $status;

	$template->setVariable('ItemAddConfirmation', $ItemAddConfirmation);
	$template->setVariable('controller', '');
	return array(GalleryStatus::success(),
		     array('body' => 'modules/core/templates/ItemAddConfirmation.tpl'));
    }

    /**
     * @see GalleryView::getViewDescription()
     */
    function getViewDescription() {
	list ($ret, $core) = GalleryCoreApi::loadPlugin('module', 'core');
	if ($ret->isError()) {
	    return array($ret->wrap(__FILE__, __LINE__), null);
	}

	return array(GalleryStatus::success(), $core->translate('add items confirmation'));
    }
}
?>

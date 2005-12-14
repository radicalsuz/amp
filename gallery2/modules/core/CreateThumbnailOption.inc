<?php
/*
 * $RCSfile: CreateThumbnailOption.inc,v $
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
 * This ItemAddOption allows the user to choose to create thumbnails
 * when the image is uploaded.
 *
 * @package Core
 * @subpackage UserInterface
 */
class CreateThumbnailOption extends ItemAddOption {

    /**
     * @see ItemAddOption::loadTemplate
     */
    function loadTemplate(&$template, &$form, $item) {
	return array(GalleryStatus::success(),
		     'modules/core/templates/CreateThumbnailOption.tpl',
		     'modules_core');
    }

    /**
     * @see ItemAddOption::isAppropriate
     */
    function isAppropriate() {
	return array(GalleryStatus::success(), true);
    }

    /**
     * @see ItemAddOption::handleRequestAfterAdd
     */
    function handleRequestAfterAdd($form, $items) {
	$errors = array();
	foreach ($items as $item) {
	    $itemIds[] = $item->getId();
	    $warnings[] = array();
	}
	if (empty($form['CreateThumbnailOption']['createThumbnail'])) {
	    return array(GalleryStatus::success(), $errors, $warnings);
	}

	list ($ret, $thumbTable) = GalleryCoreApi::fetchThumbnailsByItemIds($itemIds);
	if ($ret->isError()) {
	    return array($ret->wrap(__FILE__, __LINE__), null, null);
	}

	foreach ($items as $item) {
	    $itemId = $item->getId();
	    if (!empty($thumbTable[$itemId])) {
		$thumbnail = $thumbTable[$item->getId()];
		list ($ret) = GalleryCoreApi::rebuildDerivativeCacheIfNotCurrent($thumbnail->getId());
		if ($ret->isError()) {
		    return array($ret->wrap(__FILE__, __LINE__), null, null);
		}
	    }
	}
	return array(GalleryStatus::success(), $errors, $warnings);
    }
}
?>

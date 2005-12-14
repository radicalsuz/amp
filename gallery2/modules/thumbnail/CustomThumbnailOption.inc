<?php
/*
 * $RCSfile: CustomThumbnailOption.inc,v $
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
 * @version $Revision: 1.11 $ $Date: 2005/08/23 03:49:56 $
 * @package Thumbnail
 * @subpackage UserInterface
 * @author Alan Harder <alan.harder@sun.com>
 */

/**
 * ItemEditOption for uploading a custom thumbnail for an item
 *
 * @package Thumbnail
 * @subpackage UserInterface
 *
 */
class CustomThumbnailOption extends ItemEditOption {

    /**
     * @see ItemEditOption::isAppropriate
     */
    function isAppropriate($item, $thumbnail) {
	return array(GalleryStatus::success(), GalleryUtilities::isA($item, 'GalleryItem'));
    }

    /**
     * @see ItemEditOption::loadTemplate
     */
    function loadTemplate(&$template, &$form, $item, $thumbnail) {
	GalleryCoreApi::relativeRequireOnce('modules/thumbnail/classes/ThumbnailHelper.class');

	/* Is this item already using a custom thumbnail.. */
	list ($ret, $thumbnailImage) = ThumbnailHelper::fetchThumbnail($item->getId());
	if ($ret->isError()) {
	    return array($ret->wrap(__FILE__, __LINE__), null, null);
	}
	if (isset($thumbnailImage)) {
	    $thumbnailImage = $thumbnailImage->getMemberData();
	} else {
	    /* Set the form's encoding type since we're uploading binary files */
	    if ($template->hasVariable('ItemAdmin')) {
		$ItemAdmin =& $template->getVariableByReference('ItemAdmin');
		$ItemAdmin['enctype'] = 'multipart/form-data';
	    } else {
		$ItemAdmin = array('enctype' => 'multipart/form-data');
		$template->setVariable('ItemAdmin', $ItemAdmin);
	    }
	}
	$template->setVariable('CustomThumbnailOption', array('thumbnail' => $thumbnailImage));

	return array(GalleryStatus::success(),
		    'modules/thumbnail/templates/CustomThumbnail.tpl',
		    'modules_thumbnail');
    }

    /**
     * @see ItemEditOption::handleRequestAfterEdit
     */
    function handleRequestAfterEdit($form, &$item, &$preferred) {
	GalleryCoreApi::relativeRequireOnce('modules/thumbnail/classes/ThumbnailHelper.class');

	$error = array();
	$warning = array();
	if (!empty($form['tmp_name'][1])) {
	    list ($ret, $lockId) = GalleryCoreApi::acquireReadLock(array($item->getId()));
	    if ($ret->isError()) {
		return array($ret->wrap(__FILE__, __LINE__), null, null);
	    }

	    list ($ret, $thumbnailId) = ThumbnailHelper::addItem(
		null, $item->getId(), $form['name'][1], $form['tmp_name'][1], $form['type'][1]);
	    if ($ret->isError() && ($ret->getErrorCode() & ERROR_UNSUPPORTED_FILE_TYPE)) {
		$error[] = 'form[CustomThumbnailOption][error][imageMime]';
	    } else if ($ret->isError()) {
		GalleryCoreApi::releaseLocks($lockId);
		return array($ret->wrap(__FILE__, __LINE__), null, null);
	    }

	    if (empty($error)) {
		$ret = ThumbnailHelper::applyThumbnail($item, $thumbnailId);
		if ($ret->isError()) {
		    GalleryCoreApi::releaseLocks($lockId);
		    return array($ret->wrap(__FILE__, __LINE__), null, null);
		}
	    }

	    $ret = GalleryCoreApi::releaseLocks($lockId);
	    if ($ret->isError()) {
		return array($ret->wrap(__FILE__, __LINE__), null, null);
	    }
	} else if (isset($form['CustomThumbnailOption']['delete'])) {
	    list ($ret, $thumbnailImage) = ThumbnailHelper::fetchThumbnail($item->getId());
	    if ($ret->isError()) {
		return array($ret->wrap(__FILE__, __LINE__), null, null);
	    }
	    if (!isset($thumbnailImage)) {
		return array(GalleryStatus::error(ERROR_MISSING_OBJECT, __FILE__, __LINE__),
			     null, null);
	    }
	    $ret = GalleryCoreApi::deleteEntityById($thumbnailImage->getId());
	    if ($ret->isError()) {
		return array($ret->wrap(__FILE__, __LINE__), null, null);
	    }
	    $ret = ThumbnailHelper::restoreThumbnail($item);
	    if ($ret->isError()) {
		return array($ret->wrap(__FILE__, __LINE__), null, null);
	    }
	}

	return array(GalleryStatus::success(), $error, $warning);
    }
}
?>

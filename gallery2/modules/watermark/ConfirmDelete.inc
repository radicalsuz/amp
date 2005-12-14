<?php
/*
 * $RCSfile: ConfirmDelete.inc,v $
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
 * @version $Revision: 1.7 $ $Date: 2005/08/23 03:49:57 $
 * @package Watermark
 * @author Alan Harder <alan.harder@sun.com>
 */

/**
 * @package Watermark
 * @subpackage UserInterface
 */
class ConfirmDeleteController extends GalleryController {

    /**
     * @see GalleryController::handleRequest
     */
    function handleRequest($form) {
	/* Make sure we have permission to edit this item */
	$ret = GalleryCoreApi::assertHasItemPermission($form['watermarkId'], 'core.edit');
	if ($ret->isError()) {
	    return array($ret->wrap(__FILE__, __LINE__), null);
	}

	$status = array();
	if (isset($form['action']['delete'])) {
	    GalleryCoreApi::relativeRequireOnce('modules/watermark/classes/WatermarkHelper.class');

	    list ($ret) = WatermarkHelper::deleteWatermarkImageById($form['watermarkId'], true);
	    if ($ret->isError()) {
		return array($ret->wrap(__FILE__, __LINE__), null);
	    }

	    $status['delete'] = 1;
	} /* else $form['action']['cancel'] */

	$results['redirect'] = isset($form['fromAdmin'])
	    ? array('view' => 'core.SiteAdmin', 'subView' => 'watermark.WatermarkSiteAdmin')
	    : array('view' => 'core.UserAdmin', 'subView' => 'watermark.UserWatermarks');
	$results['status'] = $status;
	$results['error'] = array();

	return array(GalleryStatus::success(), $results);
    }
}

/**
 * @package Watermark
 * @subpackage UserInterface
 */
class ConfirmDeleteView extends GalleryView {

    /**
     * @see GalleryView::loadTemplate
     */
    function loadTemplate(&$template, &$form) {
	GalleryCoreApi::relativeRequireOnce('modules/watermark/classes/WatermarkHelper.class');
	if ($form['formName'] != 'ConfirmDelete') {
	    $form['formName'] = 'ConfirmDelete';
	    list ($form['watermarkId'], $form['fromAdmin']) =
		GalleryUtilities::getRequestVariables('watermarkId', 'fromAdmin');
	}

	list ($ret, $item) = GalleryCoreApi::loadEntitiesById($form['watermarkId']);
	if ($ret->isError()) {
	    return array($ret->wrap(__FILE__, __LINE__), null);
	}

	list ($ret, $itemIds) = WatermarkHelper::fetchWatermarkedItemIds($item);
	if ($ret->isError()) {
	    return array($ret->wrap(__FILE__, __LINE__), null);
	}

	$template->setVariable('ConfirmDelete',
			       array('item' => $item->getMemberData(),
				     'count' => count($itemIds)));
	$template->setVariable('controller', 'watermark.ConfirmDelete');

	return array(GalleryStatus::success(),
		     array('body' => 'modules/watermark/templates/ConfirmDelete.tpl'));
    }
}
?>

<?php
/*
 * $RCSfile: Sample.inc,v $
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
 * @version $Revision: 1.6 $ $Date: 2005/08/23 03:49:44 $
 * @package ImageFrame
 * @author Alan Harder <alan.harder@sun.com>
 */

/**
 * @package ImageFrame
 * @subpackage UserInterface
 */
class SampleView extends GalleryView {

    /**
     * @see GalleryView::loadTemplate
     */
    function loadTemplate(&$template, &$form) {
	list ($ret, $imageframe) =
	    GalleryCoreApi::newFactoryInstance('ImageFrameInterface_1_1');
	if ($ret->isError()) {
	    return array($ret->wrap(__FILE__, __LINE__), null);
	}
	if (!isset($imageframe)) {
	    return array(GalleryStatus::error(ERROR_MISSING_OBJECT, __FILE__, __LINE__), null);
	}

	list ($ret, $list) = $imageframe->getImageFrameList();
	if ($ret->isError()) {
	    return array($ret->wrap(__FILE__, __LINE__), null);
	}
	$ret = $imageframe->init($template, array_keys($list));
	if ($ret->isError()) {
	    return array($ret->wrap(__FILE__, __LINE__), null);
	}

	$ImageFrameSample = array('list' => $list);
	$itemId = GalleryUtilities::getRequestVariables('itemId');
	if (!empty($itemId)) {
	    list ($ret, $item) = GalleryCoreApi::loadEntitiesById($itemId);
	    if ($ret->isError()) {
		return array($ret->wrap(__FILE__, __LINE__), null);
	    }
	    list ($ret, $thumbnail) = GalleryCoreApi::fetchThumbnailsByItemIds(array($itemId));
	    if ($ret->isError()) {
		return array($ret->wrap(__FILE__, __LINE__), null);
	    }
	    if (isset($thumbnail[$itemId])) {
		$ImageFrameSample['item'] = $item->getMemberData();
		$ImageFrameSample['thumb'] = $thumbnail[$itemId]->getMemberData();
	    }
	}

	list ($ret, $module) = GalleryCoreApi::loadPlugin('module', 'imageframe');
	if ($ret->isError()) {
	    return array($ret->wrap(__FILE__, __LINE__), null);
	}

	$template->setVariable('ImageFrameSample', $ImageFrameSample);
	$template->title($module->translate('Image Frame Samples'));
	return array(GalleryStatus::success(),
	    array('body' => 'modules/imageframe/templates/SampleBody.tpl'));
    }
}
?>

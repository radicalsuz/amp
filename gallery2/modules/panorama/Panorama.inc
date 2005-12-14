<?php
/*
 * $RCSfile: Panorama.inc,v $
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
 * @version $Revision: 1.8 $ $Date: 2005/08/23 03:49:48 $
 * @package Panorama
 * @author Alan Harder <alan.harder@sun.com>
 */

/**
 * @package Panorama
 * @subpackage UserInterface
 */
class PanoramaView extends GalleryView {

    /**
     * @see GalleryView::loadTemplate
     */
    function loadTemplate(&$template, &$form) {
	global $gallery;
	GalleryCoreApi::relativeRequireOnce('modules/panorama/classes/PanoramaHelper.class');

	list ($ret, $item) = $this->_getItem();
	if ($ret->isError()) {
	    return array($ret->wrap(__FILE__, __LINE__), null);
	}
	list ($ret, $image) = PanoramaHelper::fetchViewableImages(array($item));
	if ($ret->isError()) {
	    return array($ret->wrap(__FILE__, __LINE__), null);
	}
	if (empty($image)) {
	    return array(GalleryStatus::error(ERROR_BAD_PARAMETER, __FILE__, __LINE__), null);
	}
	$image = $image[$item->getId()];

	list ($ret, $viewWidth) = GalleryCoreApi::getPluginParameter('module', 'panorama', 'width');
	if ($ret->isError()) {
	    return array($ret->wrap(__FILE__, __LINE__), null);
	}

	$urlGenerator =& $gallery->getUrlGenerator();
	$src = $urlGenerator->generateUrl(array('view' => 'core.DownloadItem',
						'itemId' => $image->getId(),
						'serialNumber' => $image->getSerialNumber()), true);

	list ($ret, $module) = GalleryCoreApi::loadPlugin('module', 'panorama');
	if ($ret->isError()) {
	    return array($ret->wrap(__FILE__, __LINE__), null);
	}

	$Panorama['item'] = $item->getMemberData();
	$Panorama['image'] = $image->getMemberData();
	$Panorama['imageUrl'] = $src;
	$Panorama['width'] = $viewWidth;
	$Panorama['moduleUrl'] = GalleryUtilities::convertPathToUrl(dirname(__FILE__));

	$template->setVariable('Panorama', $Panorama);
	$template->title($module->translate('Panorama'));
	return array(GalleryStatus::success(),
	    array('body' => 'modules/panorama/templates/Panorama.tpl'));
    }
}
?>

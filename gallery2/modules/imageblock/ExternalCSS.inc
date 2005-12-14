<?php
/*
 * $RCSfile: ExternalCSS.inc,v $
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
 * @version $Revision: 1.4 $ $Date: 2005/08/23 03:49:43 $
 * @package ImageBlock
 * @author Alan Harder <alan.harder@sun.com>
 */

/**
 * Generate CSS required if image frames are used with external image block.
 *
 * @package ImageBlock
 * @subpackage UserInterface
 */
class ExternalCSSController extends GalleryController {

    /**
     * @see GalleryController::handleRequest
     */
    function handleRequest($form) {
	list ($ret, $imageframe) = GalleryCoreApi::newFactoryInstance('ImageFrameInterface_1_1');
	if ($ret->isError()) {
	    return array($ret->wrap(__FILE__, __LINE__), null);
	}
	if (isset($imageframe)) {
	    $frames = GalleryUtilities::getRequestVariables('frames');
	    if (empty($frames)) {
		$frames = array();
		foreach (array('albumFrame', 'itemFrame') as $key) {
		    list ($ret, $frame) =
			GalleryCoreApi::getPluginParameter('module', 'imageblock', $key);
		    if ($ret->isError()) {
			return array($ret->wrap(__FILE__, __LINE__), null);
		    }
		    if (!empty($frame)) {
			$frames[] = $frame;
		    }
		}
		if (!empty($frames)) {
		    GalleryUtilities::putRequestVariable('frames', implode('|', $frames));
		}
	    }
	}
	return array(GalleryStatus::success(),
		     array('delegate' => array('view' =>
				empty($frames) ? 'imageblock.ExternalCSS' : 'imageframe.CSS'),
			   'status' => array(), 'error' => array()));
    }
}

/**
 * @package ImageBlock
 * @subpackage UserInterface
 */
class ExternalCSSView extends GalleryView {

    /**
     * @see GalleryView::loadTemplate()
     */
    function loadTemplate(&$template, &$form) {
	header('Content-type: text/css');
	header('Expires: ' . GalleryUtilities::getHttpDate(time() + 2592000));
	return array(GalleryStatus::success(),
		     array('html' => 'modules/imageblock/templates/NoCSS.tpl'));
    }
}
?>

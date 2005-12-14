<?php
/*
 * $RCSfile: FileNotFound.inc,v $
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
 * @package Rewrite
 * @version $Revision: 1.3 $ $Date: 2005/08/23 03:49:52 $
 * @author Douglas Cau <douglas@cau.se>
 */

/**
 * @package Rewrite
 * @subpackage UserInterface
 */
class FileNotFoundView extends GalleryView {

    /**
     * @see GalleryView::loadTemplate
     */
    function loadTemplate(&$template, &$form) {
    	global $gallery;
    	$urlGenerator =& $gallery->getUrlGenerator();

	$FileNotFound = array();
	$FileNotFound['path'] = $urlGenerator->getCurrentUrlDir() .
		GalleryUtilities::getRequestVariables('path');
	$template->setVariable('FileNotFound', $FileNotFound);

	header('HTTP/1.0 404 Not Found');

    	return array(GalleryStatus::success(),
		array('body' => 'modules/rewrite/templates/FileNotFound.tpl'));
    }
}
?>

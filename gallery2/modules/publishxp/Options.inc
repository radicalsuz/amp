<?php
/*
 * $RCSfile: Options.inc,v $
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
 * Controller to set options on how photos are uploaded.
 *
 * Allows the user to specify options that control how the photos are created
 * within Gallery.  The two options supported are for whether the file extension
 * should be used in the title and if the captions should be set automatically.
 *
 * @version $Id: Options.inc,v 1.4 2005/08/23 03:49:49 mindless Exp $
 * @package PublishXp
 * @author Timothy Webb <tiwebb@cisco.com>
 */
class OptionsController extends GalleryController {
    /**
     * @see GalleryControler::handleRequest
     */
    function handleRequest($form) {
	$results = array();
	if (isset($form['action']['setOptions'])) {
	    /* No form processing/validation required so just continue the next page */
	    $results['redirect']['view'] = 'publishxp.UploadItems';
	    $results['redirect']['stripExtensions'] = isset($form['stripExtensions']);
	    $results['redirect']['setCaptions'] = isset($form['setCaptions']);
	    $results['redirect']['albumId'] = $form['albumId'];
	} else {
	    $results['delegate']['view'] = 'publishxp.Options';
	}
	$results['status'] = array();
	$results['error'] = array();
	return array(GalleryStatus::success(), $results);
    }
}

/**
 * View to set options on how photos are uploaded.
 *
 * Allows the user to specify options that control how the photos are created
 * within Gallery.  The two options supported are for whether the file extension
 * should be used in the title and if the captions should be set automatically.
 *
 * @version $Id: Options.inc,v 1.4 2005/08/23 03:49:49 mindless Exp $
 * @package PublishXp
 * @author Timothy Webb <tiwebb@cisco.com>
 */
class OptionsView extends GalleryView {
    /**
     * @see GalleryView::loadTemplate
     */
    function loadTemplate(&$template, &$form) {
	global $gallery;
	if ($form['formName'] != 'Options') {
	    $form['formName'] = 'Options';

	    list ($form['albumId'], $form['stripExtensions'], $form['setCaptions']) =
		GalleryUtilities::getRequestVariables('albumId', 'stripExtensions', 'setCaptions');

	    /* Strip extensions defaults to true, if there was no query parameter for it */
	    if ($form['stripExtensions'] == null) {
		$form['stripExtensions'] = true;
	    }

	    if (empty($form['albumId'])) {
		return array(GalleryStatus::error(ERROR_BAD_PARAMETER, __FILE__, __LINE__), null);
	    }
	}

	$template->head('modules/publishxp/templates/Head.tpl');
	return array(GalleryStatus::success(),
		     array('body' => 'modules/publishxp/templates/Options.tpl'));
    }
}
?>

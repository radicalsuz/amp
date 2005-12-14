<?php
/*
 * $RCSfile: RemoteSiteAdmin.inc,v $
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
 * @version $Revision: 1.5 $ $Date: 2005/08/23 03:49:52 $
 * @package Remote
 * @author Alan Harder <alan.harder@sun.com>
 */

/**
 * @package Remote
 * @subpackage UserInterface
 */
class RemoteSiteAdminController extends GalleryController {

    /**
     * @see GalleryController::handleRequest
     */
    function handleRequest($form) {
	/* Make sure we have adequate permissions */
	$ret = GalleryCoreApi::assertUserIsSiteAdministrator();
	if ($ret->isError()) {
	    return array($ret->wrap(__FILE__, __LINE__), null);
	}

	$status = array();
	$error = array();
	if (isset($form['action']['save'])) {
	    foreach (array('showItemAdd') as $key) {
		$ret = GalleryCoreApi::setPluginParameter('module', 'remote', $key,
						(isset($form[$key]) && $form[$key]) ? 1 : 0);
		if ($ret->isError()) {
		    return array($ret->wrap(__FILE__, __LINE__), null);
		}
	    }
	    $status['saved'] = 1;
	}

	$results = array('redirect' => array('view' => 'core.SiteAdmin',
					     'subView' => 'remote.RemoteSiteAdmin'),
			 'status' => $status,
			 'error' => $error);

	return array(GalleryStatus::success(), $results);
    }
}

/**
 * @package Remote
 * @subpackage UserInterface
 */
class RemoteSiteAdminView extends GalleryView {

    /**
     * @see GalleryView::loadTemplate
     */
    function loadTemplate(&$template, &$form) {
	if (!isset($form['showItemAdd'])) {
	    $form['showItemAdd'] = false;
	}
	if ($form['formName'] != 'RemoteSiteAdmin') {
	    $form['formName'] = 'RemoteSiteAdmin';
	    list ($ret, $params) = GalleryCoreApi::fetchAllPluginParameters('module', 'remote');
	    if ($ret->isError()) {
		return array($ret->wrap(__FILE__, __LINE__), null);
	    }
	    foreach ($params as $key => $value) {
		$form[$key] = $value;
	    }
	}

	$template->setVariable('controller', 'remote.RemoteSiteAdmin');
	return array(GalleryStatus::success(),
		     array('body' => 'modules/remote/templates/RemoteSiteAdmin.tpl'));
    }
}
?>

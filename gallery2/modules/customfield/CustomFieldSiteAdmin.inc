<?php
/*
 * $RCSfile: CustomFieldSiteAdmin.inc,v $
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
 * @version $Revision: 1.9 $ $Date: 2005/08/23 03:49:37 $
 * @package CustomField
 * @author Alan Harder <alan.harder@sun.com>
 */

GalleryCoreApi::relativeRequireOnce('modules/customfield/classes/CustomFieldHelper.class');

/**
 * @package CustomField
 * @subpackage UserInterface
 */
class CustomFieldSiteAdminController extends GalleryController {

    /**
     * @see GalleryController::handleRequest
     */
    function handleRequest($form) {
	/* Make sure we have adequate permissions */
	$ret = GalleryCoreApi::assertUserIsSiteAdministrator();
	if ($ret->isError()) {
	    return array($ret->wrap(__FILE__, __LINE__), null);
	}

	list ($ret, $status) = CustomFieldHelper::handleAdminAction($form);
	if ($ret->isError()) {
	    return array($ret->wrap(__FILE__, __LINE__), null);
	}

	/* Figure out where to redirect upon success */
	$results['redirect']['view'] = 'core.SiteAdmin';
	$results['redirect']['subView'] = 'customfield.CustomFieldSiteAdmin';
	$results['status'] = $status;
	$results['error'] = array();

	return array(GalleryStatus::success(), $results);
    }
}

/**
 * @package CustomField
 * @subpackage UserInterface
 */
class CustomFieldSiteAdminView extends GalleryView {

    /**
     * @see GalleryView::loadTemplate
     */
    function loadTemplate(&$template, &$form) {
	if ($form['formName'] != 'CustomFieldSiteAdmin') {
	    $form['formName'] = 'CustomFieldSiteAdmin';

	    $ret = CustomFieldHelper::loadAdminForm($form);
	    if ($ret->isError()) {
		return array($ret->wrap(__FILE__, __LINE__), null);
	    }
	}
	$template->setVariable('controller', 'customfield.CustomFieldSiteAdmin');
	return array(GalleryStatus::success(),
		     array('body' => 'modules/customfield/templates/CustomFieldSiteAdmin.tpl'));
    }
}
?>

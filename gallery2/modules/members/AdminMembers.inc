<?php
/*
 * $RCSfile: AdminMembers.inc,v $
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
 * @version $Revision: 1.9 $ $Date: 2005/08/23 03:49:45 $
 * @package Members
 * @author Robert Balousek <rbalousek@hotmail.com>
 */

/**
 * @package Members
 * @subpackage UserInterface
 */
class AdminMembersController extends GalleryController {

    /**
     * @see GalleryController::handleRequest
     */
    function handleRequest($form) {
    	global $gallery;

    	$ret = GalleryCoreApi::assertUserIsSiteAdministrator();
    	if ($ret->isError()) {
    	    return array($ret->wrap(__FILE__, __LINE__), null);
    	}

    	$error = array();
    	$status = array();
    	if (isset($form['action']['save'])) {
    	    foreach (array('canViewMembersModule', 'canViewEmailAddress') as $key) {
    	        if (isset($form[$key])) {
    		    $ret = GalleryCoreApi::setPluginParameter('module',
    		                                              'members',
    		                                              $key,
    		                                              trim($form[$key]));
    		    if ($ret->isError()) {
    		        return array($ret->wrap(__FILE__, __LINE__), null);
    		    }
    		}
    	    }

    	    $status['saved'] = 1;
    	} /* else $form['action']['cancel'] */

    	if (empty($error)) {
    	    $results['redirect'] = array('view' => 'core.SiteAdmin',
    					 'subView' => 'members.AdminMembers');
    	} else {
    	    $results['delegate']['view'] = 'core.SiteAdmin';
    	    $results['delegate']['subView'] = 'members.AdminMembers';
    	}
    	$results['status'] = $status;
    	$results['error'] = $error;

    	return array(GalleryStatus::success(), $results);
    }
}

/**
 * @package Members
 * @subpackage UserInterface
 */
class AdminMembersView extends GalleryView {

    /**
     * @see GalleryView::loadTemplate
     */
    function loadTemplate(&$template, &$form) {
    	global $gallery;

    	list($ret, $module) = GalleryCoreApi::loadPlugin('module', 'members');
    	if ($ret->isError()) {
    	    return array($ret->wrap(__FILE__, __LINE__), null);
        }

    	/* Load our default values if we didn't just come from this form. */
    	if ($form['formName'] != 'AdminMembers') {
    	    foreach (array('canViewMembersModule', 'canViewEmailAddress') as $key) {
    	        list ($ret, $form[$key]) =
    	            GalleryCoreApi::getPluginParameter('module', 'members', $key);
    	        if ($ret->isError()) {
    	            return array($ret->wrap(__FILE__, __LINE__), null);
    	        }
    	    }
    	    $form['formName'] = 'AdminMembers';
    	}

    	foreach (array('id.adminGroup',
    	               'id.everybodyGroup',
    	               'id.allUserGroup') as $key) {
    	    list ($ret, $groupIds[]) =
                GalleryCoreApi::getPluginParameter('module', 'core', $key);
            if ($ret->isError()) {
                return array($ret->wrap(__FILE__, __LINE__), null);
            }
    	}
    	$viewTerms = array('Administrators', 'Everybody', 'Logged In Users');
    	$members = array();
    	$members['memberViews'] = array($groupIds[0] => $module->translate($viewTerms[0]),
    	                                $groupIds[1] => $module->translate($viewTerms[1]),
    	                                $groupIds[2] => $module->translate($viewTerms[2]));
    	$members['emailViews'] = array(true => $module->translate('Show Email Addresses'),
    	                               false => $module->translate("Don't Show Email Addresses"));
	$template->setVariable('Members', $members);
    	$template->setVariable('controller', 'members.AdminMembers');

    	return array(GalleryStatus::success(),
    		     array('body' => 'modules/members/templates/AdminMembers.tpl'));
    }
}
?>

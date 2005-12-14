<?php
/*
 * $RCSfile: Login.inc,v $
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
 * Controller to initialize the process of publishing photos.
 *
 * Allows the user to log in to Gallery to begin the process of uploading
 * photos through Windows XP's publishing mechanism.
 *
 * @version $Id: Login.inc,v 1.4 2005/08/23 03:49:49 mindless Exp $
 * @package PublishXp
 * @author Timothy Webb <tiwebb@cisco.com>
 */
class LoginController extends GalleryController {
    /**
     * @see GalleryController::handleRequest
     */
    function handleRequest($form) {
	global $gallery;
	$results = array();
	$error = array();
	if (isset($form['action']['login'])) {
	    if (empty($form['userName'])) {
		$error[] = 'form[error][userName][missing]';
	    }

	    if (empty($form['password'])) {
		$error[] = 'form[error][password][missing]';
	    }

	    if (empty($error)) {
		list($ret, $user) = GalleryCoreApi::fetchUserByUsername($form['userName']);
		if ($ret->isError() && !($ret->getErrorCode() & ERROR_MISSING_OBJECT)) {
		    return array($ret->wrap(__FILE__, __LINE__), null);
		}
		if (isset($user) && $user->isCorrectPassword($form['password'])) {
		    $gallery->setActiveUser($user);

		    $event = GalleryCoreApi::newEvent('Gallery::Login');
		    $event->setEntity($user);
		    list ($ret, $ignoreTheRedirect) = GalleryCoreApi::postEvent($event);
		    if ($ret->isError()) {
			return array($ret->wrap(__FILE__, __LINE__), null);
		    }

		    $session =& $gallery->getSession();
		    $session->regenerate();

		    $results['redirect']['view'] = 'publishxp.SelectAlbum';
		} else {
		    $error[] = 'form[error][password][invalid]';
		}
	    }
	}

	if (!isset($results['delegate']['view'])) {
	    $results['delegate']['view'] = 'publishxp.Login';
	}
	$results['status'] = array();
	$results['error'] = $error;
	return array(GalleryStatus::success(), $results);
    }
}

/**
 * View to initialize the process of publishing photos.
 *
 * Allows the user to log in to Gallery to begin the process of uploading
 * photos through Windows XP's publishing mechanism.
 *
 * @version $Id: Login.inc,v 1.4 2005/08/23 03:49:49 mindless Exp $
 * @package PublishXp
 * @author Timothy Webb <tiwebb@cisco.com>
 */
class LoginView extends GalleryView {
    /**
     * @see GalleryView::loadTemplate
     */
    function loadTemplate(&$template, &$form) {
	global $gallery;
	if ($form['formName'] != 'Login') {
	    $form['userName'] = '';
	    $form['formName'] = 'Login';
	}

	list ($ret, $anonymousUserId) =
	    GalleryCoreApi::getPluginParameter('module', 'core', 'id.anonymousUser');
	if ($ret->isError()) {
	    return array($ret->wrap(__FILE__, __LINE__), null);
	}

	if ($gallery->getActiveUserId() != $anonymousUserId) {
	    /* No need to log in */
	    return array(GalleryStatus::success(),
			 array('redirect' => array('view' => 'publishxp.SelectAlbum')));
	}

	$template->setVariable('controller', 'publishxp.Login');
	$template->head('modules/publishxp/templates/Head.tpl');
	return array(GalleryStatus::success(),
		     array('body' => 'modules/publishxp/templates/Login.tpl'));
    }
}
?>

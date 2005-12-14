<?php
/*
 * $RCSfile: module.inc,v $
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
 * @version $Revision: 1.30 $ $Date: 2005/09/10 20:28:14 $
 * @package SelfRegistration
 * @author Sebastian Eichner <mailsp@sebastian-eichner.de>
 */

/**
 * The self-registration module
 *
 * @package SelfRegistration
 */
class RegisterModule extends GalleryModule {

    function RegisterModule() {
	global $gallery;

	$this->setId('register');
	$this->setName($gallery->i18n('Registration'));
	$this->setDescription($gallery->i18n('New User Registration'));
	$this->setVersion('1.0.0');
	$this->setGroup('gallery', $this->translate('Gallery'));
	$this->setCallbacks('getSystemLinks|getUserAdminViews|getSiteAdminViews');
	$this->setRequiredCoreApi(array(6, 0));
	$this->setRequiredModuleApi(array(2, 0));
    }

    /**
     * @see GalleryModule::upgrade()
     */
    function upgrade($currentVersion) {
	if (!isset($currentVersion)) {
	    foreach (array('confirmation' => 'admin',
			   'emailadmins' => 1,
			   'subject' => $this->translate('Account activation'),
			   'adminsubject' => $this->translate('New user registration'),
			   'from' => 'webmaster@' . GalleryUtilities::getServerVar('SERVER_NAME'))
			 as $key => $value) {
		$ret = $this->setParameter($key, $value);
		if ($ret->isError()) {
		    return $ret->wrap(__FILE__, __LINE__);
		}
	    }
	}

	return GalleryStatus::success();
    }

    /**
     * @see GalleryModule::performFactoryRegistrations()
     */
    function performFactoryRegistrations() {
	$ret = GalleryCoreApi::registerFactoryImplementation(
	    'GalleryEntity', 'GalleryPendingUser', 'GalleryPendingUser',
	    'modules/register/classes/GalleryPendingUser.class', 'register', null);
	if ($ret->isError()) {
	    return $ret->wrap(__FILE__, __LINE__);
	}

	return GalleryStatus::success();
    }

    /**
     * @see GalleryModule::autoConfigure
     */
    function autoConfigure() {
	/* We don't require any special configuration */
	return array(GalleryStatus::success(), true);
    }


    /**
     * @see GalleryModule::getSystemLinks
     */
    function getSystemLinks() {
	global $gallery;
	$links = array();

	list ($ret, $anonymousUserId) =
	    GalleryCoreApi::getPluginParameter('module', 'core', 'id.anonymousUser');
	if ($ret->isError()) {
	    return array($ret->wrap(__FILE__, __LINE__), null);
	}

	/* Only anonymous users will see the register link */
	if ($gallery->getActiveUserId() == $anonymousUserId) {
	    $links['Register'] = array(
		'text' => $this->translate('Register'),
		'params' => array('view' => 'core.UserAdmin',
				  'subView' => 'register.UserSelfRegistration',
				  'return' => 1));
	}

	return array(GalleryStatus::success(), $links);
    }

    /**
     * @see GalleryModule::getUserAdminViews();
     */
    function getUserAdminViews($user) {
	global $gallery;
	$views = array();

	list ($ret, $anonymousUserId) =
	    GalleryCoreApi::getPluginParameter('module', 'core', 'id.anonymousUser');
	if ($ret->isError()) {
	    return array($ret->wrap(__FILE__, __LINE__), null);
	}

	/* Only anonymous users will see the register link */
	if ($gallery->getActiveUserId() == $anonymousUserId) {
	    $views[] = array('name' => $this->translate('Register'),
			     'view' => 'register.UserSelfRegistration');
	}

	return array(GalleryStatus::success(), $views);
    }

    /**
     * @see GalleryModule::getSiteAdminViews
     */
    function getSiteAdminViews() {
	return array(GalleryStatus::success(),
		     array(array('name' => $this->translate('Registration'),
				 'view' => 'register.AdminSelfRegistration')));
    }
}
?>

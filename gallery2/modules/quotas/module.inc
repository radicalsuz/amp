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
 * @version $Revision: 1.11 $ $Date: 2005/09/10 20:28:14 $
 * @package Quotas
 * @author Robert Balousek <volksport@users.sf.net>
 */

/**
 * The implementation of the core-module
 *
 * @package Quotas
 */
class QuotasModule extends GalleryModule {

    function QuotasModule() {
	global $gallery;

	$this->setId('quotas');
	$this->setName($gallery->i18n('Quotas'));
	$this->setDescription($gallery->i18n('User/Group Disk Quotas'));
	$this->setVersion('1.0.0');
	$this->setGroup('gallery', $this->translate('Gallery'));
	$this->setCallbacks('registerEventListeners|getSiteAdminViews');
	$this->setRequiredCoreApi(array(6, 0));
	$this->setRequiredModuleApi(array(2, 0));
    }

    /**
     * @see GalleryModule::registerEventListeners()
     */
    function registerEventListeners() {
	GalleryCoreApi::registerEventListener('GalleryEntity::delete', new QuotasModule());
    }

    /**
     * @see GalleryModule::isRecommendedDuringInstall
     */
    function isRecommendedDuringInstall() {
	return false;
    }

    /**
     * @see GalleryModule::autoConfigure
     */
    function autoConfigure() {
	/* We don't require any special configuration */
	return array(GalleryStatus::success(), true);
    }

    /**
     * @see GalleryModule::getSiteAdminViews
     */
    function getSiteAdminViews() {
	return array(GalleryStatus::success(),
		     array(array('name' => $this->translate('Quotas'),
				 'view' => 'quotas.AdminQuotas')));
    }

    /**
     * @see GalleryModule::performFactoryRegistrations()
     */
    function performFactoryRegistrations() {
	$ret = GalleryCoreApi::registerFactoryImplementation(
	    'ItemAddOption', 'DiskQuotaOption', 'DiskQuotaOption',
	    'modules/quotas/DiskQuotaOption.inc', 'quotas', null);
	if ($ret->isError()) {
	    return $ret->wrap(__FILE__, __LINE__);
	}

	return GalleryStatus::success();
    }

    /**
     * Event handler for GalleryEntity::delete events
     * Delete any group/user quotas if the associated group/user is deleted
     *
     * @see GalleryEventListener::handleEvent
     */
    function handleEvent($event) {
	$result = null;
	if ($event->getEventName() == 'GalleryEntity::delete') {
	    $entity = $event->getEntity();

	    if (GalleryUtilities::isA($entity, 'GalleryGroup') ||
		GalleryUtilities::isA($entity, 'GalleryUser')) {
		GalleryCoreApi::relativeRequireOnce('modules/quotas/classes/QuotasMap.class');
		GalleryCoreApi::relativeRequireOnce(
		    'modules/quotas/classes/GalleryQuotasHelper.class');

		/* If its a group, find if a group quota exists, if so delete it */
		if (GalleryUtilities::isA($entity, 'GalleryGroup')) {
		    list ($ret, $quotaExists, $groupQuota) =
			GalleryQuotasHelper::fetchGroupQuota($entity->getId());
		    if ($ret->isError()) {
			return array($ret->wrap(__FILE__, __LINE__), null);
		    }
		/* If its a user, find if a user quota exists, if so delete it */
		} else if (GalleryUtilities::isA($entity, 'GalleryUser')) {
		    list ($ret, $quotaExists, $userQuota) =
			GalleryQuotasHelper::fetchUserQuota($entity->getId());
		    if ($ret->isError()) {
			return array($ret->wrap(__FILE__, __LINE__), null);
		   }
		}

		if ($quotaExists) {
		    $ret = QuotasMap::removeMapEntry(array('userOrGroupId' => $entity->getId()));
		    if ($ret->isError()) {
			return array($ret->wrap(__FILE__, __LINE__), null);
		    }
		}
	    }
	}
	return array(GalleryStatus::success(), $result);
    }

}
?>

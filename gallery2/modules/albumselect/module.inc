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
 * @version $Revision: 1.29 $ $Date: 2005/09/10 20:28:11 $
 * @package AlbumSelect
 * @author Alan Harder <alan.harder@sun.com>
 * @author Jonas Forsberg <jonas@gargamel.nu> (Initial dTree integration)
 */

/**
 * The implementation of the AlbumSelect module
 *
 * @package AlbumSelect
 */
class AlbumSelectModule extends GalleryModule {

    function AlbumSelectModule() {
	global $gallery;
	$this->setId('albumselect');
	$this->setName($gallery->i18n('Album Select'));
	$this->setDescription(
	    $gallery->i18n('Jump directly to any album using a select box or tree view'));
	$this->setVersion('1.0.0');
	$this->setGroup('blocks', $this->translate('Blocks'));
	$this->setCallbacks('getSiteAdminViews|registerEventListeners');
	$this->setRequiredCoreApi(array(6, 6));
	$this->setRequiredModuleApi(array(2, 0));
    }

    /**
     * @see GalleryModule::isRecommendedDuringInstall()
     */
    function isRecommendedDuringInstall() {
	return false;
    }

    /**
     * @see GalleryModule::autoConfigure()
     */
    function autoConfigure() {
	/* We don't require any special configuration */
	return array(GalleryStatus::success(), true);
    }

    /**
     * @see GalleryModule::registerEventListeners()
     */
    function registerEventListeners() {
	$listener = new AlbumSelectModule();
	GalleryCoreApi::registerEventListener('Gallery::ViewableTreeChange', $listener);
	GalleryCoreApi::registerEventListener('Gallery::ItemOrder', $listener);
	GalleryCoreApi::registerEventListener('GalleryEntity::save', $listener);
    }

    /**
     * @see GalleryModule::upgrade()
     */
    function upgrade($currentVersion) {
	list ($ret, $params) = GalleryCoreApi::fetchAllPluginParameters('module', 'albumselect');
	if ($ret->isError()) {
	    return $ret->wrap(__FILE__, __LINE__);
	}

	/* We removed the 'show' parameter in 0.9.8 and 'type' in 0.9.11 */
	foreach (array('show', 'type') as $param) {
	    if (isset($params[$param])) {
		$ret = $this->removeParameter($param);
		if ($ret->isError()) {
		    return $ret->wrap(__FILE__, __LINE__);
		}
	    }
	}

	foreach (array('sort' => 'manual',
		       'treeLines' => 1, 'treeIcons' => 0, 'treeCookies' => 0,
		       'treeExpandCollapse' => 0, 'treeCloseSameLevel' => 0) as $key => $value) {
	    if (!isset($params[$key])) {
		$ret = $this->setParameter($key, $value);
		if ($ret->isError()) {
		    return $ret->wrap(__FILE__, __LINE__);
		}
	    } else if ($key == 'sort' && ($params[$key] == '0' || $params[$key] == '1')) {
		/* v0.9.4 changed 'sort' param from 0/1 to manual/title */
		$value = ($params[$key] == 1) ? 'title' : 'manual';
		$ret = $this->setParameter($key, $value);
		if ($ret->isError()) {
		    return $ret->wrap(__FILE__, __LINE__);
		}
	    }
	}

	/* Changed cache keys to include language code in 0.9.13 */
	if (!empty($currentVersion) && version_compare($currentVersion, '0.9.13', '<')) {
	    GalleryDataCache::removeFromDisk(
		array('type' => 'module-data', 'module' => 'albumselect'));
	}

	return GalleryStatus::success();
    }

    /**
     * @see GalleryModule::getSiteAdminViews()
     */
    function getSiteAdminViews() {
	return array(GalleryStatus::success(),
		     array(array('name' => $this->translate('Album Select'),
				 'view' => 'albumselect.AlbumSelectSiteAdmin')));
    }

    /**
     * Event handler for Gallery::ViewableTreeChange
     *
     * @see GalleryEventListener::handleEvent
     */
    function handleEvent($event) {
	if ($event->getEventName() == 'Gallery::ViewableTreeChange') {
	    GalleryDataCache::removeFromDisk(
		array('type' => 'module-data', 'module' => 'albumselect'));
	} else if ($event->getEventName() == 'Gallery::ItemOrder') {
	    list ($ret, $sort) = $this->getParameter('sort');
	    if ($ret->isError()) {
		return array($ret->wrap(__FILE__, __LINE__), null);
	    }
	    if ($sort == 'manual') {
		GalleryDataCache::removeFromDisk(
		    array('type' => 'module-data', 'module' => 'albumselect'));
	    }
	} else if ($event->getEventName() == 'GalleryEntity::save') {
	    $entity = $event->getEntity();
	    if ($entity->getEntityType() == 'GalleryAlbumItem'
		    && ($entity->getModifiedFlag('orderBy') & MEMBER_MODIFIED
			|| $entity->getModifiedFlag('orderDirection') & MEMBER_MODIFIED)) {
		list ($ret, $sort) = $this->getParameter('sort');
		if ($ret->isError()) {
		    return array($ret->wrap(__FILE__, __LINE__), null);
		}
		if ($sort == 'album') {
		    GalleryDataCache::removeFromDisk(
			array('type' => 'module-data', 'module' => 'albumselect'));
		}
	    }
	}

	return array(GalleryStatus::success(), null);
    }
}
?>

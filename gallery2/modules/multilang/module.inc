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
 * @version $Revision: 1.20 $ $Date: 2005/09/10 20:28:13 $
 * @package MultiLang
 * @author Alan Harder <alan.harder@sun.com>
 */

/**
 * Support item captions in multiple languages
 *
 * @package MultiLang
 */
class MultiLangModule extends GalleryModule /* and GalleryEventListener */ {

    function MultiLangModule() {
	global $gallery;

	$this->setId('multilang');
	$this->setName($gallery->i18n('MultiLanguage'));
	$this->setDescription($gallery->i18n('Support item captions in multiple languages'));
	$this->setVersion('1.0.0');
	$this->setGroup('data', $this->translate('Extra Data'));
	$this->setCallbacks('getSiteAdminViews|registerEventListeners');
	$this->setRequiredCoreApi(array(6, 0));
	$this->setRequiredModuleApi(array(2, 0));
    }

    /**
     * @see GalleryModule::registerEventListeners()
     */
    function registerEventListeners() {
	GalleryCoreApi::registerEventListener('GalleryEntity::delete', new MultiLangModule());
    }

    /**
     * @see GalleryModule::upgrade()
     */
    function upgrade($currentVersion) {
	if (!isset($currentVersion)) {
	    $ret = $this->setParameter('languages', '');
	    if ($ret->isError()) {
		return $ret->wrap(__FILE__, __LINE__);
	    }
	}

	return GalleryStatus::success();
    }

    /**
     * @see GalleryModule::performFactoryRegistrations()
     */
    function performFactoryRegistrations() {
	$ret = GalleryCoreApi::registerFactoryImplementation(
	    'ItemEditPlugin', 'MultiLangItemEdit', 'MultiLangItemEdit',
	    'modules/multilang/MultiLangItemEdit.inc', 'multilang', null);
	if ($ret->isError()) {
	    return $ret->wrap(__FILE__, __LINE__);
	}

	$ret = GalleryCoreApi::registerFactoryImplementation(
	    'GalleryOnLoadHandler', 'MultiLangModule', 'MultiLang',
	    'modules/multilang/module.inc', 'multilang', null);
	if ($ret->isError()) {
	    return $ret->wrap(__FILE__, __LINE__);
	}

	$ret = GalleryCoreApi::registerFactoryImplementation(
	    'GallerySearchInterface_1_0', 'MultiLangSearch', 'MultiLang',
	    'modules/multilang/classes/MultiLangSearch.class', 'multilang', null);
	if ($ret->isError()) {
	    return $ret->wrap(__FILE__, __LINE__);
	}

	return GalleryStatus::success();
    }

    /**
     * @see GalleryModule::getOnLoadHandlerIds()
     */
    function getOnLoadHandlerIds() {
	return array('MultiLang');
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
	/* This module requires gettext to be installed */
	list ($ret, $needsConfiguration) = $this->needsConfiguration();
	if ($ret->isError()) {
	    return array($ret->wrap(__FILE__, __LINE__), null);
	}
	return array(GalleryStatus::success(), !$needsConfiguration);
    }

    /**
     * @see GalleryModule::needsConfiguration
     */
    function needsConfiguration() {
	/* This module requires gettext to be installed */
	global $gallery;
	$translator =& $gallery->getTranslator();
	return array(GalleryStatus::success(), !$translator->canTranslate());
    }

    /**
     * @see GalleryModule::getConfigurationView
     */
    function getConfigurationView() {
	return 'multilang.CantActivate';
    }

    /**
     * @see GalleryModule::getSiteAdminViews()
     */
    function getSiteAdminViews() {
	return array(GalleryStatus::success(),
		     array(array('name' => $this->translate('MultiLanguage'),
				 'view' => 'multilang.MultiLangSiteAdmin')));
    }

    /**
     * Load multilang data into this item..
     */
    function onLoad(&$entity, $duringUpgrade) {
	/*
	 * Load in captions for active language.
	 * However, skip this step for core.ItemAdmin or any controller (esp core.ItemEdit)
	 * to avoid inadvertently saving multilang captions into the actual entity.
	 * Also, skip this during core module upgrades (when no session/activate language is
	 * available)
	 */
	list ($view, $controller) = GalleryUtilities::getRequestVariables('view', 'controller');
	if ($view != 'core.ItemAdmin' && empty($controller) && !$duringUpgrade) {
	    GalleryCoreApi::relativeRequireOnce('modules/multilang/classes/MultiLangHelper.class');
	    $ret = MultiLangHelper::onLoad($entity);
	    if ($ret->isError()) {
		return $ret->wrap(__FILE__, __LINE__);
	    }
	}
	return GalleryStatus::success();
    }

    /**
     * Delete MultiLang data for deleted items.
     * @see GalleryEventListener::handleEvent
     */
    function handleEvent($event) {
	$entity = $event->getEntity();
	if (GalleryUtilities::isA($entity, 'GalleryItem')) {
	    GalleryCoreApi::relativeRequireOnce('modules/multilang/classes/MultiLangItemMap.class');
	    $ret = MultiLangItemMap::removeMapEntry(array('itemId' => $entity->getId()));
	    if ($ret->isError()) {
		return array($ret->wrap(__FILE__, __LINE__), null);
	    }
	}
	return array(GalleryStatus::success(), null);
    }
}
?>

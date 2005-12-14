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
 * @version $Revision: 1.35 $ $Date: 2005/09/10 20:28:12 $
 * @package CustomField
 * @author Alan Harder <alan.harder@sun.com>
 */

/**
 * Custom fields
 *
 * This module provides custom fields for Gallery items.
 *
 * @package CustomField
 */
class CustomFieldModule extends GalleryModule {

    function CustomFieldModule() {
	global $gallery;

	$this->setId('customfield');
	$this->setName($gallery->i18n('Custom Fields'));
	$this->setDescription($gallery->i18n('Create custom data fields for Gallery items'));
	$this->setVersion('1.0.0');
	$this->setGroup('data', $this->translate('Extra Data'));
	$this->setCallbacks('registerEventListeners|getSiteAdminViews|getItemSummaries');
	$this->setRequiredCoreApi(array(6, 0));
	$this->setRequiredModuleApi(array(2, 0));
    }

    /**
     * @see GalleryModule::registerEventListeners()
     */
    function registerEventListeners() {
	GalleryCoreApi::relativeRequireOnce('modules/customfield/classes/CustomFieldHelper.class');
	GalleryCoreApi::registerEventListener('GalleryEntity::delete', new CustomFieldHelper());
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
     * @see GalleryModule::getSiteAdminViews()
     */
    function getSiteAdminViews() {
	return array(GalleryStatus::success(),
		     array(array('name' => $this->translate('Custom Fields'),
				 'view' => 'customfield.CustomFieldSiteAdmin')));
    }

    /**
     * @see GalleryModule::getItemSummaries()
     */
    function getItemSummaries($items, $permissions) {
	GalleryCoreApi::relativeRequireOnce('modules/customfield/classes/CustomFieldHelper.class');
	GalleryCoreApi::relativeRequireOnce('lib/smarty_plugins/modifier.markup.php');
	$summaries = array();

	list ($ret, $data) = CustomFieldHelper::fetchFieldValues($items, 'summary');
	if ($ret->isError()) {
	    return array($ret->wrap(__FILE__, __LINE__), null);
	}

	foreach ($data as $itemId => $fields) {
	    $list = array();
	    foreach ($fields as $field => $value) {
		$list[] = $field . ': ' . smarty_modifier_markup($value);
	    }
	    if (!empty($list)) {
		$summaries[$itemId] = implode("<br/>\n", $list);
	    }
	}
	return array(GalleryStatus::success(), $summaries);
    }

    /**
     * @see GalleryModule::performFactoryRegistrations()
     */
    function performFactoryRegistrations() {
	/* Register our item edit plugin */
	$ret = GalleryCoreApi::registerFactoryImplementation(
	    'ItemEditPlugin', 'CustomFieldItemEdit', 'CustomFieldItemEdit',
	    'modules/customfield/CustomFieldItemEdit.inc', 'customfield', null);
	if ($ret->isError()) {
	    return $ret->wrap(__FILE__, __LINE__);
	}

	/* Register interface implementation */
	$ret = GalleryCoreApi::registerFactoryImplementation(
	    'CustomFieldInterface_1_0', 'CustomFieldHelper', 'CustomField',
	    'modules/customfield/classes/CustomFieldHelper.class', 'customfield', null);
	if ($ret->isError()) {
	    return $ret->wrap(__FILE__, __LINE__);
	}

	/* Register search implementation */
	$ret = GalleryCoreApi::registerFactoryImplementation(
	    'GallerySearchInterface_1_0', 'CustomFieldSearch', 'CustomField',
	    'modules/customfield/classes/CustomFieldSearch.class', 'customfield', null);
	if ($ret->isError()) {
	    return $ret->wrap(__FILE__, __LINE__);
	}

	return GalleryStatus::success();
    }
}
?>

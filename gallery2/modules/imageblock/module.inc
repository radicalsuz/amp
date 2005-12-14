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
 * @version $Revision: 1.57 $ $Date: 2005/09/10 20:28:13 $
 * @package ImageBlock
 * @author Alan Harder <alan.harder@sun.com>
 */

/**
 * The implementation of the Image Block module
 *
 * @package ImageBlock
 */
class ImageBlockModule extends GalleryModule {

    function ImageBlockModule() {
	global $gallery;
	$this->setId('imageblock');
	$this->setName($gallery->i18n('Image Block'));
	$this->setDescription($gallery->i18n('Random, Most Recent or Most Viewed Photo or Album'));
	$this->setVersion('1.0.0');
	$this->setGroup('blocks', $this->translate('Blocks'));
	$this->setCallbacks('registerEventListeners|getSiteAdminViews');
	$this->setRequiredCoreApi(array(6, 0));
	$this->setRequiredModuleApi(array(2, 0));
    }

    /**
     * @see GalleryModule::registerEventListeners()
     */
    function registerEventListeners() {
	GalleryCoreApi::relativeRequireOnce('modules/imageblock/classes/ImageBlockHelper.class');
	$listener = new ImageBlockHelper();
	GalleryCoreApi::registerEventListener('Gallery::ViewableTreeChange', $listener);
	GalleryCoreApi::registerEventListener('GalleryEntity::save', $listener, true);
	GalleryCoreApi::registerEventListener('GalleryEntity::delete', $listener, true);
    }

    /**
     * @see GalleryModule::upgrade()
     */
    function upgrade($currentVersion) {
	list ($ret, $params) = GalleryCoreApi::fetchAllPluginParameters('module', 'imageblock');
	if ($ret->isError()) {
	    return $ret->wrap(__FILE__, __LINE__);
	}
	foreach (array('show' => 'heading|title|date|views', 'albumFrame' => '', 'itemFrame' => '')
		as $key => $value) {
	    if (!isset($params[$key])) {
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
	   'ItemEditOption', 'ImageBlockOption', 'ImageBlockOption',
	   'modules/imageblock/ImageBlockOption.inc', 'imageblock', array('ItemEditAlbum'));
	if ($ret->isError()) {
	    return $ret->wrap(__FILE__, __LINE__);
	}

	return GalleryStatus::success();
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
     * @see GalleryModule::getSiteAdminViews()
     */
    function getSiteAdminViews() {
	return array(GalleryStatus::success(),
		     array(array('name' => $this->translate('Image Block'),
				 'view' => 'imageblock.ImageBlockSiteAdmin')));
    }
}
?>

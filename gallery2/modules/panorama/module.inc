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
 *
 * Additional copyrights:
 *   Metamorphose applet Copyright (C) 2000 Rüdiger Appel
 */
/**
 * @version $Revision: 1.42 $ $Date: 2005/09/10 20:28:14 $
 * @package Panorama
 * @author Alan Harder <alan.harder@sun.com>
 * Java applet: @author Rüdiger Appel
 *              http://www.3quarks.com/Applets/Metamorphose/
 */

/**
 * The implementation of the Panorama module
 *
 * @package Panorama
 */
class PanoramaModule extends GalleryModule {

    function PanoramaModule() {
	global $gallery;
	$this->setId('panorama');
	$this->setName($gallery->i18n('Panorama'));
	$this->setDescription($gallery->i18n('View wide jpeg/gif images in a java applet viewer'));
	$this->setVersion('1.0.0');
	$this->setGroup('display', $this->translate('Display'));
	$this->setCallbacks('getSiteAdminViews|getItemLinks');
	$this->setRequiredCoreApi(array(6, 0));
	$this->setRequiredModuleApi(array(2, 0));
    }

    /**
     * @see GalleryModule::upgrade()
     */
    function upgrade($currentVersion) {
	if (!isset($currentVersion)) {
	    /* Initial install */
	    foreach (array('itemType' => 1,
			   'itemLink' => 1,
			   'width' => 800) as $key => $value) {
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
	    'GalleryEntity', 'PanoramaDerivativeImage', 'PanoramaDerivativeImage',
	    'modules/panorama/classes/PanoramaDerivativeImage.class', 'panorama', null);
	if ($ret->isError()) {
	    return $ret->wrap(__FILE__, __LINE__);
	}

	$ret = GalleryCoreApi::registerFactoryImplementation(
	    'GalleryEntity', 'PanoramaPhotoItem', 'PanoramaPhotoItem',
	    'modules/panorama/classes/PanoramaPhotoItem.class', 'panorama', null);
	if ($ret->isError()) {
	    return $ret->wrap(__FILE__, __LINE__);
	}

	$ret = GalleryCoreApi::registerFactoryImplementation(
	    'GalleryDerivative', 'PanoramaDerivativeImage', 'PanoramaDerivativeImage',
	    'modules/panorama/classes/PanoramaDerivativeImage.class', 'panorama',
	    array('PanoramaPhotoItem'));
	if ($ret->isError()) {
	    return $ret->wrap(__FILE__, __LINE__);
	}

	$ret = GalleryCoreApi::registerFactoryImplementation(
	    'ItemEditOption', 'PanoramaOption', 'PanoramaOption',
	    'modules/panorama/PanoramaOption.inc', 'panorama', array('ItemEditPhoto'));
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
     * @see GalleryModule::deactivate()
     */
    function deactivate($postDeactivationEvent=true) {
	GalleryCoreApi::relativeRequireOnce('modules/panorama/classes/PanoramaHelper.class');
	list ($ret, $count) = PanoramaHelper::fetchItemCount();
	if ($ret->isError()) {
	    return array($ret->wrap(__FILE__, __LINE__), null);
	}
	if ($count > 0) {
	    /* Can't deactivate if panorama entities exist.. */
	    return array(GalleryStatus::success(),
			 array('view' => 'core.SiteAdmin',
			       'subView' => 'panorama.PanoramaSiteAdmin'));
	}

	list ($ret, $redirect) = parent::deactivate($postDeactivationEvent);
	if ($ret->isError()) {
	    return array($ret->wrap(__FILE__, __LINE__), null);
	}
	return array(GalleryStatus::success(), $redirect);
    }

    /**
     * @see GalleryModule::getSiteAdminViews()
     */
    function getSiteAdminViews() {
	return array(GalleryStatus::success(),
		     array(array('name' => $this->translate('Panorama'),
				 'view' => 'panorama.PanoramaSiteAdmin')));
    }

    /**
     * @see GalleryModule::getItemLinks()
     */
    function getItemLinks($items, $wantsDetailedLinks, $permissions) {
	$links = array();

	list ($ret, $param) = GalleryCoreApi::fetchAllPluginParameters('module', 'panorama');
	if ($ret->isError()) {
	    return array($ret->wrap(__FILE__, __LINE__), null);
	}
	if ($param['itemLink']) {
	    GalleryCoreApi::relativeRequireOnce('modules/panorama/classes/PanoramaHelper.class');

	    /*
	     * This won't obey the acting user id, but that's ok because it's only used to
	     * determine if a panorama is even possible.  We'll still also gate on the acting
	     * user's permissions that we receive.
	     */
	    list ($ret, $images) = PanoramaHelper::fetchViewableImages($items);
	    if ($ret->isError()) {
		return array($ret->wrap(__FILE__, __LINE__), null);
	    }
	    foreach ($items as $item) {
		$itemId = $item->getId();
		if (!isset($wantsDetailedLinks[$itemId]) || !isset($images[$itemId])) {
		    continue;
		}
		$image = $images[$itemId];
		$mimeType = $item->getMimeType();
		$width = $image->getWidth();
		/* Applet supports jpeg and gif images */
		if (GalleryUtilities::isA($item, 'GalleryPhotoItem') &&
		    !GalleryUtilities::isA($item, 'PanoramaPhotoItem') &&
		    preg_match(':^image/(gif|p?jpeg)$:', $mimeType) &&
		    $width > $param['width'] &&
		    ($image->getHeight() / $width < 0.6)) {
		    $links[$itemId][] =
			array('text' => $this->translate('View Panorama'),
			      'params' => array('view' => 'panorama.Panorama',
						'itemId' => $itemId));
		}
	    }
	}
	return array(GalleryStatus::success(), $links);
    }
}
?>

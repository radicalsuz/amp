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
 * @version $Revision: 1.31 $ $Date: 2005/09/10 20:28:16 $
 * @package WebCam
 * @author Alan Harder <alan.harder@sun.com>
 */
class WebCamModule extends GalleryModule {

    function WebCamModule() {
	global $gallery;
	$this->setId('webcam');
	$this->setName($gallery->i18n('WebCam'));
	$this->setDescription($gallery->i18n('Support periodically updated images like a webcam'));
	$this->setVersion('1.0.0'); /* Update upgrade() function below too */
	$this->setGroup('import', $this->translate('Import'));
	$this->setCallbacks('getSiteAdminViews');
	$this->setRequiredCoreApi(array(6, 0));
	$this->setRequiredModuleApi(array(2, 0));
    }

    /**
     * @see GalleryModule::upgrade()
     */
    function upgrade($currentVersion) {
	global $gallery;

	list ($ret, $params) = GalleryCoreApi::fetchAllPluginParameters('module', 'webcam');
	if ($ret->isError()) {
	    return $ret->wrap(__FILE__, __LINE__);
	}
	foreach (array('duration' => 600 /* 10 minutes */) as $key => $value) {
	    if (!isset($params[$key])) {
		$ret = $this->setParameter($key, $value);
		if ($ret->isError()) {
		    return $ret->wrap(__FILE__, __LINE__);
		}
	    }
	}

	if (!empty($currentVersion)) {
	    switch ($currentVersion) {
	    case '0.8':
	    case '0.8.1':
	    case '0.8.2':
	    case '0.8.3':
	    case '0.8.4':
	    case '0.8.5':
		/* Changed from GalleryLiveImage entity type to onLoadHandler */
		$storage =& $gallery->getStorage();
		$query = 'UPDATE [GalleryEntity]
		    SET [::entityType] = \'GalleryPhotoItem\',
			[::onLoadHandlers] = \'|WebCam|\'
		  WHERE [GalleryEntity::entityType] = \'GalleryLiveImage\'
		    AND [GalleryEntity::onLoadHandlers] IS NULL';
		$ret = $storage->execute($query);
		if ($ret->isError()) {
		    return $ret->wrap(__FILE__, __LINE__);
		}
		list ($ret, $handlers) = $storage->getFunctionSql('CONCAT',
		    array('[GalleryEntity::onLoadHandlers]', "'WebCam|'"));
		if ($ret->isError()) {
		    return $ret->wrap(__FILE__, __LINE__);
		}
		$query = 'UPDATE [GalleryEntity]
		    SET [::entityType] = \'GalleryPhotoItem\',
			[::onLoadHandlers] = ' . $handlers . '
		  WHERE [GalleryEntity::entityType] = \'GalleryLiveImage\'
		    AND [GalleryEntity::onLoadHandlers] IS NOT NULL';
		$ret = $storage->execute($query);
		if ($ret->isError()) {
		    return $ret->wrap(__FILE__, __LINE__);
		}
		break;

	    case '0.9.0':
	    case '0.9.1':
	    case '0.9.2':
	    case '0.9.3':
	    case '0.9.4':
	    case '0.9.5':
	    case '0.9.6':
	    case '0.9.7':
	    case '0.9.8':

	    case 'end of upgrade path':
		break;

	    default:
		return GalleryStatus::error(ERROR_BAD_PLUGIN, __FILE__, __LINE__,
					    sprintf('Unknown module version %s', $currentVersion));
	    }
	}

	return GalleryStatus::success();
    }

    /**
     * @see GalleryModule::performFactoryRegistrations()
     */
    function performFactoryRegistrations() {
	$ret = GalleryCoreApi::registerFactoryImplementation(
	    'GalleryOnLoadHandler', 'WebCamModule', 'WebCam',
	    'modules/webcam/module.inc', 'webcam', null);
	if ($ret->isError()) {
	    return $ret->wrap(__FILE__, __LINE__);
	}

	$ret = GalleryCoreApi::registerFactoryImplementation(
	    'ItemAddPlugin', 'ItemAddWebCam', 'ItemAddWebCam',
	    'modules/webcam/ItemAddWebCam.inc', 'webcam', null);
	if ($ret->isError()) {
	    return $ret->wrap(__FILE__, __LINE__);
	}

	return GalleryStatus::success();
    }

    /**
     * @see GalleryModule::getOnLoadHandlerIds()
     */
    function getOnLoadHandlerIds() {
	return array('WebCam');
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
		     array(array('name' => $this->translate('WebCam'),
				 'view' => 'webcam.WebCamSiteAdmin')));
    }

    /**
     * Check age and refresh from external source if needed..
     */
    function onLoad(&$item, $duringUpgrade) {
	static $loop = array();
	$old = false;
	$id = $item->getId();
	/* We don't want to refresh the external source during an upgrade */
	if ($duringUpgrade) {
	    return GalleryStatus::success();
	}
	list ($ret, $duration) = $this->getParameter('duration');
	if ($ret->isError()) {
	    return $ret->wrap(__FILE__, __LINE__);
	}
	if (time() - $item->getModificationTimestamp() > $duration && !isset($loop[$id])) {
	    /* Lock and refresh to ensure another request didn't just update image.. */
	    list ($ret, $lockId) = GalleryCoreApi::acquireWriteLock($id);
	    if ($ret->isError()) {
		return $ret->wrap(__FILE__, __LINE__);
	    }
	    $loop[$id] = true;
	    list ($ret, $refresh) = $item->refresh();
	    if ($ret->isError()) {
		GalleryCoreApi::releaseLocks($lockId);
		return $ret->wrap(__FILE__, __LINE__);
	    }
	    if ($refresh->getSerialNumber() == $item->getSerialNumber()) {
		$old = true;
	    }
	}
	if ($old) {
	    global $gallery;
	    $platform = $gallery->getPlatform();
	    $tmpDir = $gallery->getConfig('data.gallery.tmp');
	    $tmpFile = $platform->tempnam($tmpDir, 'webcam');
	    list ($ret, $url) = $this->getParameter('imageUrl', $id);
	    if ($ret->isError()) {
		GalleryCoreApi::releaseLocks($lockId);
		return $ret->wrap(__FILE__, __LINE__);
	    }

	    list ($successfullyCopied, $response, $headers) =
		GalleryCoreApi::fetchWebFile($url, $tmpFile);
	    if (!$successfullyCopied || strpos($response, '404 Not Found') !== false) {
		/* Can't get the current image.. just keep what we have now */
	    } else {
		list ($ret, $path) = $item->fetchPath();
		if ($ret->isError()) {
		    GalleryCoreApi::releaseLocks($lockId);
		    return $ret->wrap(__FILE__, __LINE__);
		}
		$ret = $platform->copy($tmpFile, $path);
		if (!$ret) {
		    GalleryCoreApi::releaseLocks($lockId);
		    return GalleryStatus::error(ERROR_PLATFORM_FAILURE, __FILE__, __LINE__);
		}
		$ret = $item->rescan();
		if ($ret->isError()) {
		    GalleryCoreApi::releaseLocks($lockId);
		    return $ret->wrap(__FILE__, __LINE__);
		}
		$item->setOriginationTimestamp(time());
		$ret = $item->save();
		if ($ret->isError()) {
		    GalleryCoreApi::releaseLocks($lockId);
		    return $ret->wrap(__FILE__, __LINE__);
		}
		$ret = GalleryCoreApi::expireDerivativeTreeBySourceIds(array($id));
		if ($ret->isError()) {
		    GalleryCoreApi::releaseLocks($lockId);
		    return $ret->wrap(__FILE__, __LINE__);
		}
	    }
	    @$platform->unlink($tmpFile);
	}
	if (isset($lockId)) {
	    $ret = GalleryCoreApi::releaseLocks($lockId);
	    if ($ret->isError()) {
		return $ret->wrap(__FILE__, __LINE__);
	    }
	}

	return GalleryStatus::success();
    }
}
?>

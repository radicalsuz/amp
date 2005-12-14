<?php
/*
 * $RCSfile: GalleryRemote.inc,v $
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
 * @version $Revision: 1.35 $ $Date: 2005/08/26 05:15:13 $
 * @package Remote
 * @subpackage UserInterface
 * @author Pierre-Luc Paour <paour@users.sourceforge.net>
 */

/**
 * Required classes
 */
GalleryCoreApi::relativeRequireOnce('modules/remote/classes/GalleryRemoteProperties.class');
GalleryCoreApi::relativeRequireOnce('modules/remote/classes/GalleryRemoteConstants.class');

/**
 * This controller fields requests from Gallery Remote and perform any required changes
 * to the data model.
 *
 * @package Remote
 * @subpackage UserInterface
 */
class GalleryRemoteController extends GalleryController {
    /**
     * ItemAddOption instances to use when handling this request.  Only used by
     * test code.
     *
     * @var array (optionId => object ItemAddOption) $_optionInstances
     * @access private
     */
    var $_optionInstances;

    /**
     * Tests can use this method to hardwire a specific set of option instances to use.
     * This avoids situations where some of the option instances will do unpredictable
     * things and derail the tests.
     *
     * @param array (optionId => ItemAddOption, ...)
     */
    function setOptionInstances($optionInstances) {
	$this->_optionInstances = $optionInstances;
    }

    /**
     * @see GalleryController::handleRequest()
     */
    function handleRequest($form) {
	global $gallery;

	$status = array();
	$error = array();
	$response = new GalleryRemoteProperties();
	$grStatusCodes = GalleryRemoteConstants::getStatusCodes();

	if (!empty($form['cmd'])) {
	    switch ($form['cmd']) {
	    case 'login':
		$ret = $this->login($form, $response);
		if ($ret->isError()) {
		    $status['controllerError'] = $ret->wrap(__FILE__, __LINE__);
		}
		break;

	    case 'fetch-albums-prune':
		$ret = $this->fetchAlbums($form, $response);
		if ($ret->isError()) {
		    $status['controllerError'] = $ret->wrap(__FILE__, __LINE__);
		}
		break;

	    case 'add-item':
		$ret = $this->addDataItem($form, $response);
		if ($ret->isError()) {
		    $response->setProperty('status', $grStatusCodes['UPLOAD_PHOTO_FAIL']);
		    $response->setProperty('status_text',
			sprintf("Upload failed: '%s'.", $ret->getErrorMessage()));
		    $response->setProperty('status_debug', print_r($ret, false));
		    print_r($ret);
		}
		break;

	    case 'new-album':
		$ret = $this->newAlbum($form, $response);
		if ($ret->isError()) {
		    $status['controllerError'] = $ret->wrap(__FILE__, __LINE__);
		}
		break;

	    case 'fetch-album-images':
		$ret = $this->fetchAlbumImages($form, $response);
		if ($ret->isError()) {
		    $status['controllerError'] = $ret->wrap(__FILE__, __LINE__);
		}
		break;

	    case 'album-properties':
		$ret = $this->albumProperties($form, $response);
		if ($ret->isError()) {
		    $status['controllerError'] = $ret->wrap(__FILE__, __LINE__);
		}
		break;

	    case 'no-op':
		$response->setProperty('status', $grStatusCodes['SUCCESS']);
		$response->setProperty('status_text', 'No-op successful');
		break;

	    default:
		$response->setProperty('status', $grStatusCodes['UNKNOWN_COMMAND']);
		$response->setProperty('status_text', "Command '${form['cmd']}' unknown.");
		break;
	    }
	} else {
	    $response->setProperty('status', $grStatusCodes['UNKNOWN_COMMAND']);
	    $response->setProperty('status_text', 'No cmd passed');
	}

	$user = $gallery->getActiveUser();
	if (isset($user)) {
	    $response->setProperty('debug_user', $user->getuserName());
	} else {
	    $response->setProperty('debug_user', 'error getting user');
	}

	$status['controllerResponse'] = $response;

	$results['delegate']['view'] = 'remote.GalleryRemote';
	$results['status'] = $status;
	$results['error'] = $error;
	return array(GalleryStatus::success(), $results);
    }

    /**
     * Log into Gallery
     *
     * @param form array key value pairs from Gallery Remote
     * @param object GalleryRemoteProperties a reference to our response object
     * @return object GalleryStatus a status code
     */
    function login($form, &$response) {
	global $gallery;

	$grStatusCodes = GalleryRemoteConstants::getStatusCodes();
	$grVersionCodes = GalleryRemoteConstants::getVersionCodes();

	/* If they don't provide a username, try the anonymous user */
	if (!empty($form['uname'])) {
	    list ($ret, $user) = GalleryCoreApi::fetchUserByUsername($form['uname']);
	    if ($ret->isError() && !($ret->getErrorCode() & ERROR_MISSING_OBJECT)) {
		return $ret->wrap(__FILE__, __LINE__);
	    }

	    $password = isset($form['password']) ? $form['password'] : '';
	    if ($user != null && $user->isCorrectPassword($password)) {
		/* login successful */
		$gallery->setActiveUser($user);
		$response->setProperty('server_version',
		    sprintf('%d.%d', $grVersionCodes['MAJ'], $grVersionCodes['MIN']));
		$response->setProperty('status', $grStatusCodes['SUCCESS']);
		$response->setProperty('status_text', 'Login successful.');
		return GalleryStatus::success();
	    } else {
		/* login unsuccessful */
		$response->setProperty('status', $grStatusCodes['PASSWORD_WRONG']);
		$response->setProperty('status_text', 'Password incorrect.');
		return GalleryStatus::success();
	    }
	} else {
	    if ($gallery->getActiveUser()) {
		/* already logged in... this sounds like the applet logging in with a cookie */
		$response->setProperty('server_version',
		    sprintf('%d.%d', $grVersionCodes['MAJ'], $grVersionCodes['MIN']));
		$response->setProperty('status', $grStatusCodes['LOGIN_MISSING']);
		$response->setProperty('status_text', 'Login parameters not found.');
		return GalleryStatus::success();
	    } else {
		/* They're logged in as the guest account */
		list ($ret, $anonymousUserId) =
		    GalleryCoreApi::getPluginParameter('module', 'core', 'id.anonymousUser');
		if ($ret->isError()) {
		    return $ret->wrap(__FILE__, __LINE__);
		}

		list ($ret, $anonymousUser) = GalleryCoreApi::loadEntitiesById($anonymousUserId);
		if ($ret->isError()) {
		    return $ret->wrap(__FILE__, __LINE__);
		}

		$gallery->setActiveUser($anonymousUser);

		$response->setProperty('debug_anonymous_login', true);
		$response->setProperty('server_version',
		    sprintf('%d.%d', $grVersionCodes['MAJ'], $grVersionCodes['MIN']));
		$response->setProperty('status', $grStatusCodes['SUCCESS']);
		$response->setProperty('status_text', 'Login successful.');
		return GalleryStatus::success();
	    }
	}
    }

    /**
     * Load the album list into our response object
     *
     * @param form array key value pairs from Gallery Remote
     * @param object GalleryRemoteProperties a reference to our response object
     * @return object GalleryStatus a status code
     */
    function fetchAlbums($form, &$response) {
	global $gallery;

	$grStatusCodes = GalleryRemoteConstants::getStatusCodes();

	/* find and load the list of albums we can view */
	list ($ret, $albumIds) = GalleryCoreApi::fetchAllItemIds('GalleryAlbumItem');
	if ($ret->isError()) {
	    return $ret->wrap(__FILE__, __LINE__);
	}

	if (empty($albumIds)) {
	    $response->setProperty('status', $grStatusCodes['SUCCESS']);
	    $response->setProperty('status_text', 'No viewable albums.');
	    return GalleryStatus::success();
	}

	/* Load the permissions for all those albums */
	list ($ret, $permissionsTable) = GalleryCoreApi::fetchPermissionsForItems($albumIds);
	if ($ret->isError()) {
	    return $ret->wrap(__FILE__, __LINE__);
	}

	/* And load all the entities */
	list ($ret, $albums) = GalleryCoreApi::loadEntitiesById($albumIds);
	if ($ret->isError()) {
	    return $ret->wrap(__FILE__, __LINE__);
	}

	/* Now add all the albums to the tree */
	$i = 1;
	foreach ($albums as $album) {
	    $perms = $permissionsTable[$album->getId()];

	    /* Use id because path component is not unique (BM guessing at PLP's logic) */
	    $response->setProperty('album.name.' . $i, $album->getId());
	    $response->setProperty('album.title.' . $i, $album->getTitle());
	    $response->setProperty('album.summary.' . $i, $album->getSummary());
	    $response->setProperty('album.parent.' . $i, $album->getParentId());
	    $response->setProperty('album.perms.add.' . $i,
				   isset($perms['core.addDataItem']) ? 'true' : 'false');
	    $response->setProperty('album.perms.write.' . $i,
				   isset($perms['core.edit']) ? 'true' : 'false');
	    $response->setProperty('album.perms.del_alb.' . $i,
				   isset($perms['core.delete']) ? 'true' : 'false');
	    $response->setProperty('album.perms.create_sub.' . $i,
				   isset($perms['core.addAlbumItem']) ? 'true' : 'false');
	    $response->setProperty('album.info.extrafields.' . $i, "Summary,Description");

	    $i++;
	}

	list ($ret, $rootId) = GalleryCoreApi::getPluginParameter('module', 'core', 'id.rootAlbum');
	if ($ret->isError()) {
	    return $ret->wrap(__FILE__, __LINE__);
	}

	$perm = GalleryCoreApi::hasItemPermission($rootId, 'core.addAlbumItem');
	if ($ret->isError()) {
	    return $ret->wrap(__FILE__, __LINE__);
	}

	$response->setProperty('can_create_root', ($perm || $isSiteAdmin) ? 'true' : 'false');
	$response->setProperty('album_count', sizeof($albums));
	$response->setProperty('status', $grStatusCodes['SUCCESS']);
	$response->setProperty('status_text', 'Fetch-albums successful.');

	return GalleryStatus::success();
    }

    /**
     * Add a data item to Gallery
     *
     * @param form array key value pairs from Gallery Remote
     * @param object GalleryRemoteProperties a reference to our response object
     * @return object GalleryStatus a status code
     */
    function addDataItem($form, &$response) {
	global $gallery;

	$grStatusCodes = GalleryRemoteConstants::getStatusCodes();

	$file = GalleryUtilities::getFile('userfile');

	if (!empty($form['set_albumId'])) {
	    list ($ret, $parentItem) = GalleryCoreApi::loadEntitiesById($form['set_albumId']);
	    if ($ret->isError()) {
		return $ret->wrap(__FILE__, __LINE__);
	    }
	    $parentId = $parentItem->getId();
	    unset($parentItem);
	} else if (!empty($form['set_albumPath'])) {
	    list ($ret, $parentId) =
		GalleryCoreApi::fetchItemIdByPath(urldecode($form['set_albumName']));
	    if ($ret->isError()) {
		return $ret->wrap(__FILE__, __LINE__);
	    }
	} else if (!empty($form['set_albumName'])) {
	    /* todo: delete this G1/early G2 throwback */
	    $parentId = $form['set_albumName'];
	} else {
	    return GalleryStatus::error(ERROR_MISSING_OBJECT, __FILE__, __LINE__);
	}

	/* Make sure we have permission do edit this item */
	$ret = GalleryCoreApi::assertHasItemPermission($parentId, 'core.addDataItem');
	if ($ret->isError()) {
	    return $ret->wrap(__FILE__, __LINE__);
	}

	list ($ret, $lockIds[]) = GalleryCoreApi::acquireReadLock($parentId);
	if ($ret->isError()) {
	    return $ret->wrap(__FILE__, __LINE__);
	}

	if (empty($file['name'])) {
	    $response->setProperty('status', $grStatusCodes['NO_FILENAME']);
	    $response->setProperty('status_text', 'Filename not specified.');
	    return GalleryStatus::success();
	}

	/* Get the mime type from the upload info. */
	$mimeType = $file['type'];

	/*
	 * If we don't get useful data from that or its a type we don't
	 * recognize, take a swing at it using the file name.
	 */
	/* Note, if $ret is in error, then $mimeExtensions is empty anyway */
	list ($ret, $mimeExtensions) = GalleryCoreApi::convertMimeToExtensions($mimeType);
	if ($mimeType == 'application/octet-stream' ||
	    $mimeType == 'application/unknown' ||
	    empty($mimeExtensions)) {
	    $extension = GalleryUtilities::getFileExtension($file['name']);
	    list ($ret, $mimeType) = GalleryCoreApi::convertExtensionToMime($extension);
	    if ($ret->isError()) {
		return $ret->wrap(__FILE__, __LINE__);
	    }
	}

	if (isset($form['force_filename'])) {
	    $itemName = $form['force_filename'];
	} else {
	    $itemName = $file['name'];
	}

	$title = isset($form['caption'])?$form['caption']:NULL;
	$summary = isset($form['extrafield.Summary'])?$form['extrafield.Summary']:NULL;
	$description = isset($form['extrafield.Description'])?$form['extrafield.Description']:NULL;

	list ($ret, $newItem) = GalleryCoreApi::addItemToAlbum(
	    $file['tmp_name'], basename($itemName), $title,
	    $summary, $description, $mimeType, $parentId);
	if ($ret->isError()) {
	    return $ret->wrap(__FILE__, __LINE__);
	}

	$ret = GalleryCoreApi::releaseLocks($lockIds);
	if ($ret->isError()) {
	    return $ret->wrap(__FILE__, __LINE__);
	}

	if (isset($this->_optionInstances)) {
	    $optionInstances = $this->_optionInstances;
	} else {
	    GalleryCoreApi::relativeRequireOnce('modules/core/ItemAdd.inc');
	    list ($ret, $optionInstances) = ItemAddOption::getAllAddOptions();
	    if ($ret->isError()) {
		return $ret->wrap(__FILE__, __LINE__);
	    }
	}

	/* Allow ItemAddOptions to process added item(s) */
	foreach ($optionInstances as $option) {
	    list ($ret, $optionErrors, $optionWarnings) =
		$option->handleRequestAfterAdd($form, array($newItem));
	    if ($ret->isError()) {
		return $ret->wrap(__FILE__, __LINE__);
	    }

	    /*
	     * Swallow option warnings and errors for now.
	     *
	     * TODO: Report warnings and errors back to Gallery Remote.  If the upload is denied
	     * because of quota limitations, then we'll get an error that we should report back.
	     */
	}

	$response->setProperty('status', $grStatusCodes['SUCCESS']);
	$response->setProperty('status_text', 'Add photo successful.');

	return GalleryStatus::success();
    }

    /**
     * Create a new album
     *
     * @param form array key value pairs from Gallery Remote
     * @param object GalleryRemoteProperties a reference to our response object
     * @return object GalleryStatus a status code
     */
    function newAlbum($form, &$response) {
	global $gallery;

	$grStatusCodes = GalleryRemoteConstants::getStatusCodes();

	$itemId = $form['set_albumName'];	  /* TODO: Eliminate this throwback to G1 */

	/* Make sure we have permission do edit this item */
	$ret = GalleryCoreApi::assertHasItemPermission($itemId, 'core.addAlbumItem');
	if ($ret->isError()) {
	    return $ret->wrap(__FILE__, __LINE__);
	}

	/* Use a suitable default for the album name if none is given */
	if (!isset($form['newAlbumName']) || '' == $form['newAlbumName']) {
	    $form['newAlbumName'] = 'album';
	}

	/* Create the album */
	list ($ret, $album) = GalleryCoreApi::createAlbum($itemId, $form['newAlbumName'],
							  $form['newAlbumTitle'], null,
							  $form['newAlbumDesc'], null);
	if ($ret->isError()) {
	    return $ret->wrap(__FILE__, __LINE__);
	}

	/* Give the creator all permissions on the new album */
	/* todo: do we need to lock since we just created the album? */
	$ret = GalleryCoreApi::addUserPermission($album->getId(),
						 $album->getOwnerId(),
						 'core.all',
						 false);
	if ($ret->isError()) {
	    return $ret->wrap(__FILE__, __LINE__);
	}

	$response->setProperty('album_name', $album->getid());
	$response->setProperty('status', $grStatusCodes['SUCCESS']);
	$response->setProperty('status_text', 'New-album successful.');

	return GalleryStatus::success();
    }

    /**
     * Fetch album images
     *
     * @param form array key value pairs from Gallery Remote
     * @param object GalleryRemoteProperties a reference to our response object
     * @return object GalleryStatus a status code
     */
    function fetchAlbumImages($form, &$response) {
	global $gallery;

	$grStatusCodes = GalleryRemoteConstants::getStatusCodes();

	$albumsToo = isset($form['albums_too']) && $form['albums_too'] == 'yes';

	if (isset($form['set_albumName'])) {
	    /* list an actual album */
	    $albumId = $form['set_albumName'];
	} else {
	    /* list the root album */
	    list ($ret, $albumId) =
		GalleryCoreApi::getPluginParameter('module', 'core', 'id.rootAlbum');
	    if ($ret->isError()) {
		return $ret->wrap(__FILE__, __LINE__);
	    }
	}

	/* Get children of the album */
	list ($ret, $ids) =
	    GalleryCoreApi::fetchChildItemIdsWithPermission($albumId, 'core.view');
	if ($ret->isError()) {
	    return $ret->wrap(__FILE__, __LINE__);
	}

	if (empty($ids)) {
	    /* nothing in this album */
	    $response->setProperty('image_count', 0);
	    $response->setProperty('status', $grStatusCodes['SUCCESS']);
	    $response->setProperty(
		'status_text', 'Fetch-album-images successful, but no items found.');
	    return GalleryStatus::success();
	}

	list ($ret, $items) = GalleryCoreApi::loadEntitiesById($ids);
	if ($ret->isError()) {
	    return $ret->wrap(__FILE__, __LINE__);
	}

	/* Load the derivatives for all those items */
	list ($ret, $derivatives) = GalleryCoreApi::fetchDerivativesByItemIds($ids);
	if ($ret->isError()) {
	    return $ret->wrap(__FILE__, __LINE__);
	}

	/* Precache item permissions */
	$ret = GalleryCoreApi::studyPermissions($ids);
	if ($ret->isError()) {
	    return $ret->wrap(__FILE__, __LINE__);
	}

	$i = 1;
	foreach ($items as $item) {
	    if (GalleryUtilities::isA($item, 'GalleryPhotoItem')) {
		list ($ret, $permissions) = GalleryCoreApi::getPermissions($item->getId());
		if ($ret->isError()) {
		    return $ret->wrap(__FILE__, __LINE__);
		}
		if (isset($permissions['core.viewSource'])) {
		    $response->setProperty('image.name.' . $i, $item->getId());
		    $response->setProperty('image.raw_width.' . $i, $item->getWidth());
		    $response->setProperty('image.raw_height.' . $i, $item->getHeight());
		    $response->setProperty('image.raw_filesize.' . $i, $item->getSize());
		} else if (!isset($permissions['core.viewResizes'])) {
		    continue;
		}

		$thumb = null;
		$resizes = array();
		if (isset($derivatives[$item->getId()])) {
		    foreach ($derivatives[$item->getId()] as $index => $der) {
			if ($der->getDerivativeType() == DERIVATIVE_TYPE_IMAGE_THUMBNAIL) {
			    $thumb = $der;
			} else if ($der->getDerivativeType() == DERIVATIVE_TYPE_IMAGE_PREFERRED &&
				   isset($permissions['core.viewSource'])) {
			    $response->setProperty('image.name.' . $i, $der->getId());
			    $response->setProperty('image.raw_width.' . $i, $der->getWidth());
			    $response->setProperty('image.raw_height.' . $i, $der->getHeight());
			    $response->setProperty('image.raw_filesize.' . $i,
						   $der->getDerivativeSize());
			} else if ($der->getDerivativeType() == DERIVATIVE_TYPE_IMAGE_RESIZE &&
				   isset($permissions['core.viewResizes'])) {
			    $resizes[$index] = $der->getWidth() * $der->getHeight();
			}
		    }
		    arsort($resizes);
		    $resizes = array_keys($resizes);
		}
		if (!$response->hasProperty('image.name.' . $i)) {
		    if (empty($resizes)) {
			/* No permission on full size and no resizes found */
			continue;
		    }
		    $der = $derivatives[$item->getId()][array_shift($resizes)];
		    $response->setProperty('image.name.' . $i, $der->getId());
		    $response->setProperty('image.raw_width.' . $i, $der->getWidth());
		    $response->setProperty('image.raw_height.' . $i, $der->getHeight());
		    $response->setProperty('image.raw_filesize.' . $i, $der->getDerivativeSize());
		}
		if (!empty($resizes)) {
		    $der = $derivatives[$item->getId()][array_shift($resizes)];
		    $response->setProperty('image.resizedName.' . $i, $der->getId());
		    $response->setProperty('image.resized_width.' . $i, $der->getWidth());
		    $response->setProperty('image.resized_height.' . $i, $der->getHeight());
		}
		if (isset($thumb)) {
		    $response->setProperty('image.thumbName.' . $i, $thumb->getId());
		    $response->setProperty('image.thumb_width.' . $i, $thumb->getWidth());
		    $response->setProperty('image.thumb_height.' . $i, $thumb->getHeight());
		}

		list ($ret, $extensions) =
		    GalleryCoreApi::convertMimeToExtensions($item->getMimeType());
		if ($ret->isError()) {
		    return $ret->wrap(__FILE__, __LINE__);
		}
		if (isset($extensions) && isset($extensions[0])) {
		    $response->setProperty('image.forceExtension.' . $i, $extensions[0]);
		}

		$response->setProperty('image.caption.' . $i, $item->getTitle());
		$response->setProperty('image.title.' . $i, $item->getPathComponent());
		$response->setProperty('image.hidden.' . $i, 'no');

		$i++;
	    } else if (GalleryUtilities::isA($item, 'GalleryAlbumItem') && $albumsToo) {
		$response->setProperty('album.name.' . $i, $item->getId());

		$i++;
	    }
	}

	$urlGenerator =& $gallery->getUrlGenerator();
	$response->setProperty('baseurl', str_replace('&amp;', '&',
	    GalleryUrlGenerator::appendParamsToUrl(
		$urlGenerator->getCurrentUrlDir(true) . GALLERY_MAIN_PHP,
		array('view' => 'core.DownloadItem', 'itemId' => ''))));
	$response->setProperty('image_count', $i - 1);
	$response->setProperty('status', $grStatusCodes['SUCCESS']);
	$response->setProperty('status_text', 'Fetch-album-images successful.');

	return GalleryStatus::success();
    }

    /**
     * Album properties
     *
     * @param form array key value pairs from Gallery Remote
     * @param object GalleryRemoteProperties a reference to our response object
     * @return object GalleryStatus a status code
     */
    function albumProperties($form, &$response) {
	global $gallery;

	/*
	 * GR now only supports 1-dimension size constraints. It used to support 2-d
	 * but since G1 supported 1-d, I simplified GR a while ago, so now we have
	 * to cheat a bit and select the largest of width and height to return to GR
	 */

	$grStatusCodes = GalleryRemoteConstants::getStatusCodes();

	if (isset($form['set_albumName'])) {
	    /* list an actual album */
	    $albumId = $form['set_albumName'];
	} else {
	    /* list the root album */
	    list ($ret, $albumId) =
		GalleryCoreApi::getPluginParameter('module', 'core', 'id.rootAlbum');
	    if ($ret->isError()) {
		return $ret->wrap(__FILE__, __LINE__);
	    }
	}

	$biggestDerivative = $maxSize = 0;

	/* Find the derivative preferences for the album */
	list ($ret, $derivatives) = GalleryCoreApi::fetchDerivativePreferencesForItem($albumId);
	if ($ret->isError()) {
	    return $ret->wrap(__FILE__, __LINE__);
	}

	foreach ($derivatives as $derivative) {
	    preg_match('/scale\|(\d*),(\d*)/', $derivative['derivativeOperations'], $matches);

	    if ($derivative['derivativeType'] != DERIVATIVE_TYPE_IMAGE_THUMBNAIL &&
		    isset($matches[1])) {
		if ($matches[1] > $biggestDerivative) {
		    $biggestDerivative = $matches[1];
		}
		if ($matches[2] > $biggestDerivative) {
		    $biggestDerivative = $matches[2];
		}
	    }
	}

	/* Is the sizelimit module active? */
	list ($ret, $modules) = GalleryCoreApi::fetchPluginStatus('module');
	if ($ret->isError()) {
	    return $ret->wrap(__FILE__, __LINE__);
	}

	if (isset($modules['sizelimit']['active']) && $modules['sizelimit']['active']) {
	    /* If the sizelimit module is enabled, find the max size for the album */
	    list ($ret, $param) = GalleryCoreApi::fetchAllPluginParameters(
		'module', 'sizelimit', $albumId);
	    if ($ret->isError()) {
		return $ret->wrap(__FILE__, __LINE__);
	    }

	    if (isset($param['width'])) {
		$maxSize = $param['width'];
	    }

	    if (isset($param['height']) && $param['height'] > $maxSize) {
		$maxSize = $param['height'];
	    }
	}

	$response->setProperty('auto_resize', $biggestDerivative);
	$response->setProperty('max_size', $maxSize);

	$response->setProperty('status', $grStatusCodes['SUCCESS']);
	$response->setProperty('status_text', 'Fetch-album-images successful.');

	return GalleryStatus::success();
    }
}

/**
 * This is an immediate view that emits well formed Gallery Remote protocol 2 output
 *
 * @package Remote
 * @subpackage UserInterface
 */
class GalleryRemoteView extends GalleryView {

    /**
     * @see GalleryView::isImmediate()
     */
    function isImmediate() {
	return true;
    }

    /**
     * @see GalleryView::isImmediate()
     */
    function renderImmediate($status, $error) {
	if (!headers_sent()) {
	    header("Content-type: text/plain");
	}

	if (isset($status['controllerError'])) {
	    print 'Error: ' . $status['controllerError']->getAsText();
	}

	if (isset($status['controllerResponse'])) {
	    print $status['controllerResponse']->listProperties();
	}

	if (isset($controllerError)) {
	    return $ret->wrap(__FILE__, __LINE__);
	} else {
	    return GalleryStatus::success();
	}
    }
}
?>

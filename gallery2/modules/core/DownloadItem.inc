<?php
/*
 * $RCSfile: DownloadItem.inc,v $
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
 * @version $Revision: 1.42 $ $Date: 2005/08/23 03:49:02 $
 * @package GalleryCore
 * @subpackage UserInterface
 * @author Bharat Mediratta <bharat@menalto.com>
 */

/**
 * Provides downloading of binary items
 *
 * @package GalleryCore
 * @subpackage UserInterface
 *
 */
class DownloadItemView extends GalleryView {

    /**
     * @see GalleryView::isImmediate
     */
    function isImmediate() {
	return true;
    }

    /**
     * @see GalleryView::renderImmediate
     */
    function renderImmediate($status, $error) {
	/* Figure out which item we're talking about */
	$itemId = GalleryUtilities::getRequestVariables('itemId');
	if (empty($itemId)) {
	    return GalleryStatus::error(ERROR_BAD_PARAMETER, __FILE__, __LINE__);
	}

	/* Load the item */
	list ($ret, $item) = GalleryCoreApi::loadEntitiesById($itemId);
	if ($ret->isError()) {
	    return $ret->wrap(__FILE__, __LINE__);
	}

	/* Figure out the filename */
	list ($ret, $pseudoFileName) = GalleryUtilities::getPseudoFileName($item);
	if ($ret->isError()) {
	    return $ret->wrap(__FILE__, __LINE__);
	}

	/* Get the path to the file */
	list ($ret, $path) = $item->fetchPath();
	if ($ret->isError()) {
	    return $ret->wrap(__FILE__, __LINE__);
	}

	/* Rebuild derivative cache, if necessary */
	if (GalleryUtilities::isA($item, 'GalleryDerivative')) {
	    list ($ret, $item) = GalleryCoreApi::rebuildDerivativeCacheIfNotCurrent($item->getId());
	    if ($ret->isError()) {
		return $ret->wrap(__FILE__, __LINE__);
	    }
	    $itemForPermission = $item->getParentId();
	    $derivativeType = $item->getDerivativeType();
	} else {
	    $itemForPermission = $item->getId();
	    $derivativeType = null;
	}

	$ret = $this->_sendFile(array('derivativePath' => $path,
				      'derivativeType' => $derivativeType,
				      'mimeType' => $item->getMimeType(),
				      'pseudoFileName' => $pseudoFileName,
				      'parentId' => $itemForPermission));
	if ($ret->isError()) {
	    return $ret->wrap(__FILE__, __LINE__);
	}

	/* Fast downloads only works for thumbs/resizes right now */
	if (GalleryUtilities::isA($item, 'GalleryDerivative')) {
	    $ret = $item->createFastDownloadFile();
	    /* Ignore failures since the file has already been sent */
	}

	return GalleryStatus::success();
    }

    function _sendFile($data) {
	global $gallery;
	$platform = $gallery->getPlatform();

	/* Make sure we have permission */
	$permission = 'core.viewSource';
	switch($data['derivativeType']) {
	    case DERIVATIVE_TYPE_IMAGE_THUMBNAIL:
		$permission = 'core.view';
		break;

	    case DERIVATIVE_TYPE_IMAGE_RESIZE:
		$permission = 'core.viewResizes';
		break;

	    /* DERIVATIVE_TYPE_IMAGE_PREFERRED uses core.viewSource */
	}
	$ret = GalleryCoreApi::assertHasItemPermission($data['parentId'], $permission);
	if ($ret->isError()) {
	    return $ret->wrap(__FILE__, __LINE__);
	}

	header('Content-type: ' . $data['mimeType']);
	header('Content-Disposition: inline; filename="' . $data['pseudoFileName'] . '"');
	$stats = $platform->stat($data['derivativePath']);
 	if ($stats[7] > 0) {
 	    header('Content-length: ' . $stats[7]);
 	}
	header('Last-Modified: ' . GalleryUtilities::getHttpDate($stats[9]));
	header('Expires: Sun, 17 Jan 2038 12:00:00 GMT');

	/*
	 * Don't use readfile() because it buffers the entire file in memory
	 * Profiling shows that this approach is as efficient as fpassthru()
	 * but we get to call guaranteeTimeLimit which prevents it from failing on
	 * very large files.
	 */
	if ($fd = $platform->fopen($data['derivativePath'], 'rb')) {
	    while (true) {
		$bits = $platform->fread($fd, 65535);
		if (strlen($bits) == 0) {
		    break;
		}
		print $bits;
		$gallery->guaranteeTimeLimit(30);
	    }
	    $platform->fclose($fd);
	}

	

	return GalleryStatus::success();
    }
}
?>

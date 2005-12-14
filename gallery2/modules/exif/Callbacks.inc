<?php
/*
 * $RCSfile: Callbacks.inc,v $
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
class ExifCallbacks {
    /**
     */
    function callback($params, &$smarty, $callback, $userId=null) {
	global $gallery;

	switch ($callback) {
	case 'LoadExifInfo':
	    GalleryCoreApi::relativeRequireOnce('modules/exif/classes/ExifHelper.class');

	    list ($ret, $item) = GalleryCoreApi::loadEntitiesById($params['itemId']);
	    if ($ret->isError()) {
		return $ret->wrap(__FILE__, __LINE__);
	    }

	    /* We only work on JPEG data items */
	    if (!GalleryUtilities::isA($item, 'GalleryDataItem') ||
		!preg_match('{^image/p?jpeg(-cmyk)?$}', $item->getMimeType())) {
		return GalleryStatus::success();
	    }

	    /* Feed the file to exifer */
	    list($ret, $path) = $item->fetchPath();
	    if ($ret->isError()) {
		return $ret->wrap(__FILE__, __LINE__);
	    }

	    /* Figure out which version we're looking at */
	    $session =& $gallery->getSession();
	    if (empty($mode)) {
		$mode = $session->get('exif.module.LoadExifInfo.mode');
		if (empty($mode)) {
		    $mode = 'summary';
		}
	    }

	    if ($mode == 'detailed') {
		$filter = EXIF_DETAILED;
	    } else {
		$filter = EXIF_SUMMARY;
	    }

	    list ($ret, $exifData) = ExifHelper::getExifData($path, $filter);
	    if ($ret->isError()) {
		return $ret->wrap(__FILE__, __LINE__);
	    }
	    if (empty($exifData)) {
		if ($filter == EXIF_SUMMARY) {
		    list ($ret, $exifData) = ExifHelper::getExifData($path, EXIF_DETAILED);
		} else {
		    list ($ret, $exifData) = ExifHelper::getExifData($path, EXIF_SUMMARY);
		}
		if ($ret->isError()) {
		    return $ret->wrap(__FILE__, __LINE__);
		}
		$mode = null;
	    }
	    if (empty($exifData)) {
		return GalleryStatus::success();
	    }

	    $block =& $smarty->_tpl_vars['block'];
	    $block['exif']['LoadExifInfo']['exifData'] = $exifData;
	    $block['exif']['LoadExifInfo']['mode'] = $mode;

	    return GalleryStatus::success();
	}

	return GalleryStatus::error(ERROR_BAD_PARAMETER, __FILE__, __LINE__);
    }
}
?>

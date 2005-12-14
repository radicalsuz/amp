<?php
/*
 * $RCSfile: ItemEditAlbum.inc,v $
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
 * @version $Revision: 1.68 $ $Date: 2005/09/06 21:22:45 $
 * @package GalleryCore
 * @subpackage UserInterface
 * @author Bharat Mediratta <bharat@menalto.com>
 */

/**
 * This controller will handle the changes users make to an album.
 *
 * @package GalleryCore
 * @subpackage UserInterface
 *
 */
class ItemEditAlbum extends ItemEditPlugin {

    /**
     * @see ItemEditPlugin::handleRequest
     */
    function handleRequest($form, &$item, &$preferred) {
	global $gallery;

	$status = null;
	$error = array();
	if (isset($form['action']['undo'])) {

	    /* Take no action and we'll be redirected back to the same page which will reset the form */

	} else if (isset($form['action']['save'])) {
	    /* Validate the input data. */
	    if (!is_numeric($form['thumbnail']['size']) || $form['thumbnail']['size'] < 1) {
		$error[] = 'form[error][thumbnail][size][invalid]';
	    }

	    for ($i = 0; $i < sizeof($form['resizes']); $i++) {
		if (!empty($form['resizes'][$i]['active'])) {
		    if (empty($form['resizes'][$i]['width']) || empty($form['resizes'][$i]['height'])) {
			$error[] = 'form[error][resizes][' . $i . '][size][missing]';
		    } else if (!is_numeric($form['resizes'][$i]['width'])
			      || $form['resizes'][$i]['width'] < 1
			      || !is_numeric($form['resizes'][$i]['height'])
			      || $form['resizes'][$i]['height'] < 1) {
			$error[] = 'form[error][resizes][' . $i . '][size][invalid]';
		    }
		}
	    }

	    if (empty($error)) {
		/* Delete existing derivative preferences */
		$ret = GalleryCoreApi::removeDerivativePreferencesForItem($item->getId());
		if ($ret->isError()) {
		    return array($ret->wrap(__FILE__, __LINE__), null, null);
		}

		/* Add the thumbnail size back in */
		$ret = GalleryCoreApi::addDerivativePreference(0, $item->getId(),
		    DERIVATIVE_TYPE_IMAGE_THUMBNAIL,
		    'thumbnail|' . $form['thumbnail']['size']);
		if ($ret->isError()) {
		    return array($ret->wrap(__FILE__, __LINE__), null, null);
		}

		/* Add the resize-sizes back in */
		for ($i = 0; $i < sizeof($form['resizes']); $i++) {
		    if (empty($form['resizes'][$i]['active'])) {
			continue;
		    }

		    $ret = GalleryCoreApi::addDerivativePreference($i, $item->getId(),
			DERIVATIVE_TYPE_IMAGE_RESIZE, 'scale|' .
			$form['resizes'][$i]['width'] . ',' . $form['resizes'][$i]['height']);
		    if ($ret->isError()) {
			return array($ret->wrap(__FILE__, __LINE__), null, null);
		    }
		}

		/* Load the children if we might need them */
		if (isset($form['recreateThumbnails']) || isset($form['recreateResizes']) ) {
		    /* Get the child id => child types */
		    list ($ret, $childIds) = GalleryCoreApi::fetchChildItemIds($item);
		    if ($ret->isError()) {
			return array($ret->wrap(__FILE__, __LINE__), null, null);
		    }

		    $childItems = array();
		    if (!empty($childIds)) {
			/* Load the children */
			list ($ret, $childItems) = GalleryCoreApi::loadEntitiesById($childIds);
			if ($ret->isError()) {
			    return array($ret->wrap(__FILE__, __LINE__), null, null);
			}
		    }
		}

		$lockIds = array();

		/* Recreate the thumbnails, if requested to do so */
		if (isset($form['recreateThumbnails'])) {
		    /* Load the thumbnail of the children */
		    list ($ret, $thumbTable) = GalleryCoreApi::fetchThumbnailsByItemIds($childIds);
		    if ($ret->isError()) {
			return array($ret->wrap(__FILE__, __LINE__), null, null);
		    }

		    foreach ($childItems as $child) {
			$childId = $child->getId();
			$thumbnail = null;
			if (isset($thumbTable[$childId])) {
			    /* We already have a thumbnail */
			    $thumbnail = $thumbTable[$childId];
			    $sourceId = $thumbnail->getDerivativeSourceId();

			    /* Load the source of the thumbnail */
			    list($ret, $source) = GalleryCoreApi::loadEntitiesById($sourceId);
			    if ($ret->isError() && $ret->getErrorCode() & ERROR_MISSING_OBJECT) {
				/* Someone deleted the source image, we can do
				   nothing but delete the thumbnail */
				list ($ret, $lockIds[]) = GalleryCoreApi::acquireWriteLock($thumbnail->getId());
				if ($ret->isError()) {
				    return array($ret->wrap(__FILE__, __LINE__), null, null);
				}
				$ret = $thumbnail->delete();
				if ($ret->isError()) {
				    return array($ret->wrap(__FILE__, __LINE__), null, null);
				}
				continue;
			    } else if ($ret->isError()) {
				return array($ret->wrap(__FILE__, __LINE__), null, null);
			    }

			    $operation = preg_replace('/((^|;)thumbnail)\|\d+/',
						      '$1|' . $form['thumbnail']['size'],
						      $thumbnail->getDerivativeOperations());
			} else {
			    /*
			     * There is no thumbnail yet (maybe the file was uploaded
			     * when there was no graphic toolkit). Build new thumbnail
			     * from source if its a GalleryDataItem (with mimeType)
			     */
			    if (!GalleryUtilities::isA($child, 'GalleryDataItem')) {
				/* Its an album or something else, we can't make a thumbnail */
				continue;
			    }

			    list ($ret, $source) = GalleryCoreApi::fetchPreferredSource($child);
			    if ($ret->isError()) {
				return array($ret->wrap(__FILE__, __LINE__), null, null);
			    }

			    list ($ret, $thumbnail) =
				GalleryCoreApi::newFactoryInstanceByHint('GalleryDerivative',
									 $source->getEntityType());
			    if ($ret->isError()) {
				return array($ret->wrap(__FILE__, __LINE__), null, null);
			    }

			    $ret = $thumbnail->create($child->getId(), DERIVATIVE_TYPE_IMAGE_THUMBNAIL);
			    if ($ret->isError()) {
				return array($ret->wrap(__FILE__, __LINE__), null, null);
			    }

			    $operation = 'thumbnail|' . $form['thumbnail']['size'];
			}

			if ($thumbnail == null) {
			    return array(GalleryStatus::error(ERROR_MISSING_OBJECT, __FILE__, __LINE__), null, null);
			}

			/* Change the thumbnail */
			list ($ret, $operation, $outputMimeType) =
			    GalleryCoreApi::makeSupportedViewableOperationSequence(
				$source->getMimeType(), $operation);
			if ($ret->isError()) {
			    return array($ret->wrap(__FILE__, __LINE__), null, null);
			}

			if (!empty($operation)) {
			    $thumbnail->setMimeType($outputMimeType);
			    $thumbnail->setDerivativeSourceId($source->getId());
			    $thumbnail->setDerivativeOperations($operation);
			    $thumbnail->expireCache();
			    if ($thumbnail->isModified()) {
				list ($ret, $lockIds[]) = GalleryCoreApi::acquireWriteLock($thumbnail->getId());
				if ($ret->isError()) {
				    return array($ret->wrap(__FILE__, __LINE__), null, null);
				}
				$ret = GalleryCoreApi::estimateDerivativeDimensions($thumbnail, $source);
				if ($ret->isError()) {
				    return array($ret->wrap(__FILE__, __LINE__), null, null);
				}

				$ret = $thumbnail->save();
				if ($ret->isError()) {
				    return array($ret->wrap(__FILE__, __LINE__), null, null);
				}
			    }
			}
		    }
		}

		/*
		 * Recreate the resizes, if requested to do so.
		 * TODO: We should not delete resizes that we will be
		 *       adding again moments later again
		 */
		if (isset($form['recreateResizes'])) {
		    /* Get and delete all current resizes */
		    list ($ret, $resizesTable) = GalleryCoreApi::fetchResizesByItemIds($childIds);
		    if ($ret->isError()) {
			return array($ret->wrap(__FILE__, __LINE__), null, null);
		    }
		    foreach ($resizesTable as $resizes) {
			foreach ($resizes as $resize) {
			    $ret = GalleryCoreApi::deleteEntityById($resize->getId());
			}
		    }

		    /* Add the new resizes */
		    foreach ($childItems as $child) {
			if (!GalleryUtilities::isA($child, 'GalleryDataItem')) {
			    continue;
			}

			list ($ret, $source) = GalleryCoreApi::fetchPreferredSource($child);
			if ($ret->isError()) {
			    return array($ret->wrap(__FILE__, __LINE__), null, null);
			}

			/* Make sure that we have a toolkit before adding back the resizes */
			list ($ret, $toolkit, $outputMimeType) =
			    GalleryCoreApi::getToolkitByOperation($source->getMimeType(), 'scale');
			if ($ret->isError()) {
			    return array($ret->wrap(__FILE__, __LINE__), null, null);
			}

			if (isset($toolkit)) {
			    for ($i = 0; $i < sizeof($form['resizes']); $i++) {
				if (empty($form['resizes'][$i]['active'])) {
				    continue;
				}

				/* Special case to make sure that we don't upsample photos */
				if (GalleryUtilities::isA($child, 'GalleryPhotoItem')) {
				    if ($child->getWidth() <= $form['resizes'][$i]['width']
					  && $child->getHeight() <= $form['resizes'][$i]['height']) {
					continue;
				    }
				}

				list ($ret, $derivative) =
				    GalleryCoreApi::newFactoryInstanceByHint('GalleryDerivative',
									     $source->getEntityType());
				if ($ret->isError()) {
				    return array($ret->wrap(__FILE__, __LINE__), null, null);
				}

				if (!isset($derivative)) {
				    return array(GalleryStatus::error(ERROR_MISSING_OBJECT, __FILE__, __LINE__),
						 null, null);
				}

				$ret = $derivative->create($child->getId(), DERIVATIVE_TYPE_IMAGE_RESIZE);
				if ($ret->isError()) {
				    return array($ret->wrap(__FILE__, __LINE__), null, null);
				}

				$derivative->setMimeType($outputMimeType);
				$derivative->setDerivativeSourceId($source->getId());
				$derivative->setDerivativeOperations('scale|' .
				    $form['resizes'][$i]['width'] . ',' . $form['resizes'][$i]['height']);

				$ret = GalleryCoreApi::estimateDerivativeDimensions($derivative, $source);
				if ($ret->isError()) {
				    return array($ret->wrap(__FILE__, __LINE__), null, null);
				}

				$ret = $derivative->save();
				if ($ret->isError()) {
				    return array($ret->wrap(__FILE__, __LINE__), null, null);
				}
			    }
			}
		    }
		}

		$ret = GalleryCoreApi::releaseLocks($lockIds);
		if ($ret->isError()) {
		    return array($ret->wrap(__FILE__, __LINE__), null, null);
		}

		/* Save basic settings */
		$item->setTheme($form['theme']);
		if (!empty($form['presort'])) {
		    $item->setOrderBy($form['presort'] . '|' . $form['orderBy']);
		} else {
		    $item->setOrderBy($form['orderBy']);
		}
		if (isset($form['orderDirection'])) {
		    $item->setOrderDirection($form['orderDirection']);
		}
		$item->setSerialNumber($form['serialNumber']);

		list ($ret, $lockId) = GalleryCoreApi::acquireWriteLock($item->getId());
		if ($ret->isError()) {
		    return array($ret->wrap(__FILE__, __LINE__), null, null);
		}

		$ret = $item->save();
		if ($ret->isError()) {
		    GalleryCoreApi::releaseLocks($lockId);
		    return array($ret->wrap(__FILE__, __LINE__), null, null);
		}

		$ret = GalleryCoreApi::releaseLocks($lockId);
		if ($ret->isError()) {
		    return array($ret->wrap(__FILE__, __LINE__), null, null);
		}

		/* Prepare our status message */
		list ($ret, $module) = GalleryCoreApi::loadPlugin('module', 'core');
		if ($ret->isError()) {
		    return array($ret->wrap(__FILE__, __LINE__), null, null);
		}

		$status = $module->translate('Settings saved successfully.');
	    }
	}

	return array(GalleryStatus::success(), $error, $status);
    }

    /**
     * @see ItemEditPlugin::loadTemplate
     */
    function loadTemplate(&$template, &$form, $item, $thumbnail) {
	global $gallery;

	if ($form['formName'] != 'ItemEditAlbum') {
	    $tmp = explode('|', $item->getOrderBy(), 2);
	    if (count($tmp) < 2) {
		$form['orderBy'] = $item->getOrderBy();
		$form['presort'] = '';
	    } else {
		$form['orderBy'] = $tmp[1];
		$form['presort'] = $tmp[0];
	    }
	    $form['orderDirection'] = $item->getOrderDirection();
	    $form['theme'] = $item->getTheme();
	    $form['theme'] = $item->getTheme();
	    $form['formName'] = 'ItemEditAlbum';

	    /* Load up the data for the resizes table */
	    list ($ret, $preferences) = GalleryCoreApi::fetchDerivativePreferencesForItem($item->getId());
	    if ($ret->isError()) {
		return array($ret->wrap(__FILE__, __LINE__), null, null);
	    }

	    foreach ($preferences as $preference) {
		if (preg_match('/(?:resize|scale|thumbnail)\|(\d+)(?:,(\d+))?/',
			       $preference['derivativeOperations'],
			       $matches)) {
		    $size = $matches[1];
		    $height = empty($matches[2]) ? $size : $matches[2];
		}

		switch ($preference['derivativeType']) {
		case DERIVATIVE_TYPE_IMAGE_THUMBNAIL:
		    $form['thumbnail']['size'] = $size;
		    break;

		case DERIVATIVE_TYPE_IMAGE_RESIZE:
		    if (empty($size)) {
			$form['resizes'][] = array('active' => 0, 'width' => '', 'height' => '');
		    } else {
			$form['resizes'][] = array('active' => 1, 'width' => $size, 'height' => $height);
		    }
		    break;
		}
	    }

	    /* Tag on a few form blanks */
	    if (empty($form['resizes'])) {
		$extraBlanks = 3;
	    } else {
		$extraBlanks = max(2 - sizeof($form['resizes']), 0) + 1;
	    }

	    while ($extraBlanks-- > 0) {
		$form['resizes'][] = array('active' => 0, 'width' => '', 'height' => '');
	    }

	    /* Always force these to be false */
	    $form['recreateThumbnails'] = false;
	    $form['recreateResizes'] = false;
	}

	/* Checkboxes are annoying in that they are empty if false */
	$form['recreateThumbnails'] = !empty($form['recreateThumbnails']);
	$form['recreateResizes'] = !empty($form['recreateThumbnails']);
	for ($i = 0; $i < sizeof($form['resizes']); $i++) {
	    $form['resizes'][$i]['active'] = !empty($form['resizes'][$i]['active']);
	}

	list ($ret, $module) = GalleryCoreApi::loadPlugin('module', 'core');
	if ($ret->isError()) {
	    return array($ret->wrap(__FILE__, __LINE__), null, null);
	}

	/* Set up our sort order selection list */
	GalleryCoreApi::relativeRequireOnce('modules/core/classes/GallerySortInterface_1_1.class');
	list ($ret, $orderByList, $presortList, $orderDirectionList) =
	    GallerySortInterface_1_1::getAllSortOrders();
	if ($ret->isError()) {
	    return array($ret->wrap(__FILE__, __LINE__), null, null);
	}

	/* Set up our theme selection list */
	list ($ret, $themeStatusList) = GalleryCoreApi::fetchPluginStatus('theme');
	if ($ret->isError()) {
	    return array($ret->wrap(__FILE__, __LINE__), null, null);
	}
	$themeList = array('' => $module->translate('&laquo; default theme &raquo;'));
	foreach ($themeStatusList as $themeId => $themeStatus) {
	    if (empty($themeStatus['active'])) {
		continue;
	    }

	    list ($ret, $theme) = GalleryCoreApi::loadPlugin('theme', $themeId);
	    if ($ret->isError()) {
		return array($ret->wrap(__FILE__, __LINE__), null, null);
	    }
	    $themeList[$themeId] = $theme->translate($theme->getName());
	}

	$ItemEditAlbum['themeList'] = $themeList;
	$ItemEditAlbum['orderByList'] = $orderByList;
	$ItemEditAlbum['presortList'] = $presortList;
	$ItemEditAlbum['orderDirectionList'] = $orderDirectionList;

	$template->setVariable('ItemEditAlbum', $ItemEditAlbum);
	$template->setVariable('controller', 'core.ItemEditAlbum');
	return array(GalleryStatus::success(),
		     'modules/core/templates/ItemEditAlbum.tpl', 'modules_core');
    }

    /**
     * @see ItemEditPlugin::isSupported
     */
    function isSupported($item, $thumbnail) {
	return (GalleryUtilities::isA($item, 'GalleryAlbumItem'));
    }

    /**
     * @see ItemEditPlugin::getTitle
     */
    function getTitle() {
	list ($ret, $module) = GalleryCoreApi::loadPlugin('module', 'core');
	if ($ret->isError()) {
	    return array($ret->wrap(__FILE__, __LINE__), null);
	}

	return array(GalleryStatus::success(), $module->translate('Album'));
    }
}
?>

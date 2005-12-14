<?php
/*
 * $RCSfile: SearchShowAll.inc,v $
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
 * @version $Revision: 1.31 $ $Date: 2005/08/23 03:49:53 $
 * @package Search
 * @author Bharat Mediratta <bharat@menalto.com>
 */

/**
 * Required classes
 */
GalleryCoreApi::relativeRequireOnce('modules/search/classes/SearchUtilities.class');

/**
 * @package Search
 * @subpackage UserInterface
 */
class SearchShowAllController extends GalleryController {

    /**
     * @see GalleryController::handleRequest
     */
    function handleRequest($form) {
	global $gallery;

	if (isset($form['action']['scan'])) {
	    $results['delegate']['view'] = 'search.SearchScan';
	} else {
	    $results['delegate']['view'] = 'search.SearchShowAll';
	}

	$results['status'] = array();
	$results['error'] = array();
	return array(GalleryStatus::success(), $results);
    }
}

/**
 * @package Search
 * @subpackage UserInterface
 */
class SearchShowAllView extends GalleryView {

    /**
     * @see GalleryView::loadTemplate
     */
    function loadTemplate(&$template, &$form) {
	global $gallery;

	/* TODO: Parameterize this */
	$pageSize = 20;

	$items = array();
	$thumbnails = array();
	$results = array();
	$searchResultThumbnails = array();
	if ($form['formName'] == 'SearchShowAll' ||
	    $form['formName'] == 'SearchScan') {

	    if (isset($form['searchCriteria'])) {
		$form['searchCriteria'] = trim($form['searchCriteria']);
	    }

	    if (empty($form['searchCriteria'])) {
		$form['error']['searchCriteria']['missing'] = 1;
	    } else {

		/* If we click next/previous, make sure we don't change search criteria */
		if (!empty($form['lastSearchCriteria']) &&
		    $form['lastSearchCriteria'] != $form['searchCriteria'] &&
		    (isset($form['action']['nextPage']) ||
		     isset($form['action']['previousPage']))) {
		    $form['searchCriteria'] = $form['lastSearchCriteria'];
		}

		if (isset($form['action']['showAll'])) {
		    $showAll = array_keys($form['action']['showAll']);
		    $form['moduleId'] = array_pop($showAll);
		    $form['page'] = 1;
		}

		/* Reset the page number if we start a new search */
		if (isset($form['action']['search'])) {
		    $form['page'] = 1;
		}

		if (isset($form['action']['nextPage'])) {
		    $form['page']++;
		} else if (isset($form['action']['previousPage'])) {
		    $form['page']--;
		}

		if ($form['page'] < 1) {
		    $form['page'] = 1;
		}

		$moduleId = $form['moduleId'];
		GalleryCoreApi::relativeRequireOnce(
		    'modules/search/classes/GallerySearchInterface_1_0.class');
		list ($ret, $searchInstance) =
		    GalleryCoreApi::newFactoryInstanceById('GallerySearchInterface_1_0',
							   $form['moduleId']);
		if ($ret->isError()) {
		    return array($ret->wrap(__FILE__, __LINE__), null);
		}

		if (!isset($searchInstance)) {
		    return array(GalleryStatus::error(ERROR_MISSING_OBJECT, __FILE__, __LINE__),
				 null);
		}

		list ($ret, $moduleInfo) = $searchInstance->getSearchModuleInfo();
		if ($ret->isError()) {
		    return array($ret->wrap(__FILE__, __LINE__), null);
		}

		if (isset($form['options'][$moduleId])) {
		    list ($ret, $results) =
			$searchInstance->search($form['options'][$moduleId],
			    SearchUtilities::sanitizeSearchCriteria($form['searchCriteria']),
			    ($form['page']-1) * $pageSize,
			    $pageSize);
		    if ($ret->isError()) {
			return array($ret->wrap(__FILE__, __LINE__), null);
		    }
		}
		$maxPages = ceil($results['count'] / $pageSize);
	    }

	    $itemIds = array();
	    $thumbnails = array();
	    if (!empty($results['results'])) {
		foreach ($results['results'] as $result) {
		    if (isset($result['itemId'])) {
			$itemIds[$result['itemId']] = 1;
		    }
		}
		$itemIds = array_keys($itemIds);

		if (!empty($itemIds)) {
		    /* Fetch items */
		    list ($ret, $itemList) = GalleryCoreApi::loadEntitiesById($itemIds);
		    if ($ret->isError()) {
			return array($ret->wrap(__FILE__, __LINE__), null);
		    }
		    foreach ($itemList as $item) {
			$items[$item->getId()] = $item->getMemberData();
		    }

		    /* Fetch thumbnails */
		    list ($ret, $thumbnailList) =
			GalleryCoreApi::fetchThumbnailsByItemIds($itemIds);
		    if ($ret->isError()) {
			return array($ret->wrap(__FILE__, __LINE__), null);
		    }
		    foreach ($thumbnailList as $thumbnail) {
			$thumbnails[$thumbnail->getParentId()] = $thumbnail->getMemberData();
		    }
		}
	    }
	} else {
	    $form['searchCriteria'] = '';
	}

	list ($ret, $rootAlbumId) =
	    GalleryCoreApi::getPluginParameter('module', 'core', 'id.rootAlbum');
	if ($ret->isError()) {
	    return array($ret->wrap(__FILE__, __LINE__), null);
	}
	list ($ret, $rootAlbum) = GalleryCoreApi::loadEntitiesById($rootAlbumId);
	if ($ret->isError()) {
	    return array($ret->wrap(__FILE__, __LINE__), null);
	}

	/* Render the HTML body */
	$template->setVariable('SearchShowAll', array(
	    'controller' => 'search.SearchShowAll',
	    'maxPages' => $maxPages,
	    'moduleInfo' => $moduleInfo,
	    'moduleId' => $moduleId,
	    'results' => $results,
	    'thumbnails' => $thumbnails,
	    'items' => $items,
	    'rootAlbum' => $rootAlbum->getMemberData()));

	list ($ret, $module) = GalleryCoreApi::loadPlugin('module', 'search');
	if ($ret->isError()) {
	    return array($ret->wrap(__FILE__, __LINE__), null);
	}

	$template->title($module->translate('Gallery Search'));
	return array(GalleryStatus::success(),
		     array('body' => 'modules/search/templates/SearchShowAll.tpl'));
    }
}
?>

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
/**
 * @version $Revision: 1.6 $ $Date: 2005/09/07 02:33:15 $
 * @package AlbumSelect
 * @author Alan Harder <alan.harder@sun.com>
 */
class AlbumSelectCallbacks {

    function callback($params, &$smarty, $callback, $userId) {
	global $gallery;
	static $idCount = '';
	switch($callback) {
	case 'LoadAlbumData':
	    if (isset($params['albumTree']) && $params['albumTree']) {
		/* Set unique name for javascript object */
		$albumTreeName = 'albumTree' . $idCount++;
	    }
	    list ($ret, $params) =
		GalleryCoreApi::fetchAllPluginParameters('module', 'albumselect');
	    if ($ret->isError()) {
		return $ret->wrap(__FILE__, __LINE__);
	    }

	    /* Try to load it from the cache */
	    list ($ret, $languageCode) = $gallery->getActiveLanguageCode();
	    if ($ret->isError()) {
		return $ret->wrap(__FILE__, __LINE__);
	    }
	    $cachePathInfo = array('type' => 'module-data',
				   'module' => 'albumselect',
				   'itemId' => $userId . '_' . $languageCode);
	    list ($treeList, $titles) = GalleryDataCache::getFromDisk($cachePathInfo);

	    if (!isset($titles) || !isset($treeList)) {
		list ($ret, $rootAlbumId) =
		    GalleryCoreApi::getPluginParameter('module', 'core', 'id.rootAlbum');
		if ($ret->isError()) {
		    return $ret->wrap(__FILE__, __LINE__);
		}
		list ($ret, $rootAlbum) = GalleryCoreApi::loadEntitiesById($rootAlbumId);
		if ($ret->isError()) {
		    return $ret->wrap(__FILE__, __LINE__);
		}
		$titles = array('root' => preg_replace('/\r\n/', ' ', $rootAlbum->getTitle()));
		$treeList = array();

		if ($params['sort'] != 'album') {
		    list ($ret, $tree) = GalleryCoreApi::fetchAlbumTree(null, null, $userId);
		    if ($ret->isError()) {
			if ($ret->getErrorCode() & ERROR_PERMISSION_DENIED) {
			    $tree = null;
			} else {
			    return $ret->wrap(__FILE__, __LINE__);
			}
		    }
		    if (empty($tree)) {
			return GalleryStatus::success();
		    }

		    list ($ret, $items) = GalleryCoreApi::loadEntitiesById(
			GalleryUtilities::arrayKeysRecursive($tree));
		    if ($ret->isError()) {
			return $ret->wrap(__FILE__, __LINE__);
		    }
		    foreach ($items as $item) {
			$title = $item->getTitle() ? $item->getTitle() : $item->getPathComponent();
			$titles[$item->getId()] = preg_replace('/\r\n/', ' ', $title);
		    }

		    $sorter = ($params['sort'] == 'title') ?
			new AlbumSelectTreeSorter($titles) : null;
		    $nodeId = 0;
		    $this->_parseTree($tree, $treeList, $sorter, $nodeId);

		} else {
		    list ($ret, $canView) =
			GalleryCoreApi::hasItemPermission($rootAlbumId, 'core.view', $userId);
		    if ($ret->isError()) {
			return $ret->wrap(__FILE__, __LINE__);
		    }
		    if ($canView) {
			$ret = $this->_buildTree($rootAlbum, $treeList, $titles, $nodeId, $userId);
			if ($ret->isError()) {
			    return $ret->wrap(__FILE__, __LINE__);
			}
		    }
		    if (empty($treeList)) {
			return GalleryStatus::success();
		    }
		}
		GalleryDataCache::putToDisk($cachePathInfo,
					    $dataToCache = array($treeList, $titles));
	    }

	    $block =& $smarty->_tpl_vars['block'];
	    $block['albumselect']['LoadAlbumData'] = array(
		'tree' => $treeList,
		'titles' => $titles,
		'params' => $params);
	    if (isset($albumTreeName)) {
		$block['albumselect']['LoadAlbumData']['albumTreeName'] = $albumTreeName;
	    }

	    return GalleryStatus::success();
	}

	return GalleryStatus::error(ERROR_BAD_PARAMETER, __FILE__, __LINE__);
    }

    /**
     * Build template data for subalbum tree (manual and title sorting)
     * @access private
     */
    function _parseTree($tree, &$treeList, &$sorter, &$nodeId, $parentNode=0, $depth=0) {
	if (isset($sorter)) {
	    uksort($tree, array($sorter, 'sort'));
	}
	foreach ($tree as $id => $list) {
	    $treeList[] = array('id' => $id, 'nodeId' => ++$nodeId,
				'parentNode' => $parentNode, 'depth' => $depth);
	    if (!empty($list)) {
		$this->_parseTree($list, $treeList, $sorter, $nodeId, $nodeId, $depth + 1);
	    }
	}
    }

    /**
     * Build template data for subalbum tree (apply sort preference of each album)
     * @return object GalleryStatus a status code
     * @access private
     */
    function _buildTree($album, &$treeList, &$titles, &$nodeId, $userId, $parentNode=0, $depth=0) {
	list ($ret, $subAlbumIds) =
	    GalleryCoreApi::fetchChildAlbumItemIds($album, null, null, $userId);
	if ($ret->isError()) {
	    return $ret->wrap(__FILE__, __LINE__);
	}
	if (!empty($subAlbumIds)) {
	    list ($ret, $subAlbums) = GalleryCoreApi::loadEntitiesById($subAlbumIds);
	    if ($ret->isError()) {
		return $ret->wrap(__FILE__, __LINE__);
	    }
	    foreach ($subAlbums as $subAlbum) {
		$treeList[] = array('id' => $subAlbum->getId(), 'nodeId' => ++$nodeId,
				    'parentNode' => $parentNode, 'depth' => $depth);
		$title = $subAlbum->getTitle() ? $subAlbum->getTitle()
					       : $subAlbum->getPathComponent();
		$titles[$subAlbum->getId()] = preg_replace('/\r\n/', ' ', $title);
		$ret = $this->_buildTree($subAlbum, $treeList, $titles,
					 $nodeId, $userId, $nodeId, $depth + 1);
		if ($ret->isError()) {
		    return $ret->wrap(__FILE__, __LINE__);
		}
	    }
	}
	return GalleryStatus::success();
    }
}

/**
 * Sort albums on a specific field
 */
class AlbumSelectTreeSorter {
    var $_titles;
    function AlbumSelectTreeSorter(&$titles) {
	$this->_titles =& $titles;
    }
    function sort($a, $b) {
	return strcmp($this->_titles[$a], $this->_titles[$b]);
    }
}
?>

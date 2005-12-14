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
 * @version $Revision: 1.8 $ $Date: 2005/08/23 03:49:02 $
 * @package Core
 * @author Bharat Mediratta <bharat@menalto.com>
 */
class CoreCallbacks {
    function callback($params, &$smarty, $callback, $userId=null) {
	global $gallery;
	$block =& $smarty->_tpl_vars['block'];
	$theme =& $smarty->_tpl_vars['theme'];

	switch($callback) {
	case 'LoadLanguageSelector':
	    $languageList = array();
	    $translator =& $gallery->getTranslator();
	    $supportedLanguages = $translator->getSupportedLanguages();
	    foreach ($supportedLanguages as $language => $countryList) {
		foreach ($countryList as $country => $languageData) {
		    $languageList[$language . '_' . $country] = $languageData['description'];
		}
	    }

	    list ($ret, $language) = $gallery->getActiveLanguageCode();
	    if ($ret->isError()) {
		return $ret->wrap(__FILE__, __LINE__);
	    }

	    $block['core']['LanguageSelector'] =
		array('list' => $languageList, 'language' => $language);
	    return GalleryStatus::success();

	case 'ShouldShowEmergencyEditItemLink':
	    $block['core']['ShouldShowEmergencyEditItemLink'] = false;
	    if (isset($params['permissions']['core_edit'])) {
		$block['core']['ShouldShowEmergencyEditItemLink'] = true;
		foreach (array('checkSidebarBlocks' => 'sidebarBlocks',
			       'checkAlbumBlocks' => 'albumBlocks',
			       'checkPhotoBlocks' => 'photoBlocks')
			as $key => $value) {
		    if (!empty($params[$key])) {
			foreach ($theme['params'][$value] as $entry) {
			    if ($entry[0] == 'core.ItemLinks') {
				$block['core']['ShouldShowEmergencyEditItemLink'] = false;
				break 2;
			    }
			}
		    }
		}
	    }
	    return GalleryStatus::success();

	case 'LoadPeers':
	    $item = $params['item'];
	    $canViewParent = false;
	    if ($item['parentId'] > 0) {
		list ($ret, $canViewParent) = GalleryCoreApi::hasItemPermission(
		    $item['parentId'], 'core.view', $theme['actingUserId']);
		if ($ret->isError()) {
		    return $ret->wrap(__FILE__, __LINE__);
		}
	    }
	    if ($canViewParent) {
		list ($ret, $parent) = GalleryCoreApi::loadEntitiesById($item['parentId']);
		if ($ret->isError()) {
		    return $ret->wrap(__FILE__, __LINE__);
		}
		list ($ret, $peerIds) =
		    GalleryCoreApi::fetchChildItemIds($parent, null, null, $theme['actingUserId']);
		if ($ret->isError()) {
		    return $ret->wrap(__FILE__, __LINE__);
		}
		foreach ($peerIds as $i => $id) {
		    if ($id == $item['id']) {
			$peerItemIndex = $i;
			break;
		    }
		}
	    }
	    if (isset($peerItemIndex)) {
		$windowSize = isset($params['windowSize']) ? $params['windowSize'] : 6;
		$peerLast = count($peerIds) - 1;
		$peerNeighborStart = max($peerItemIndex - (int)($windowSize/2), 0);
		$peerNeighborEnd = min($peerItemIndex + (int)($windowSize/2), $peerLast);
		/* If the window is pinned to one end, expand it to the entire windowSize */
		if ($peerNeighborStart == 0) {
		    $peerNeighborEnd = min($peerLast, $windowSize-1);
		} else if ($peerNeighborEnd == $peerLast) {
		    $peerNeighborStart = max($peerLast - $windowSize, 0);
		}
		if ($peerNeighborStart > 0) {
		    $peerMap[0] = $peerIds[0];
		}
		for ($i = $peerNeighborStart; $i <= $peerNeighborEnd; $i++) {
		    $peerMap[$i] = $peerIds[$i];
		}
		if ($peerNeighborEnd < $peerLast) {
		    $peerMap[$peerLast] = $peerIds[$peerLast];
		}

		list ($ret, $peerItems) = GalleryCoreApi::loadEntitiesById($peerMap);
                if ($ret->isError()) {
                    return $ret->wrap(__FILE__, __LINE__);
                }
		if (!empty($params['loadThumbnails'])) {
		    list ($ret, $thumbTable) = GalleryCoreApi::fetchThumbnailsByItemIds($peerMap);
		    if ($ret->isError()) {
			return $ret->wrap(__FILE__, __LINE__);
		    }
		}
		$j = 0;
		foreach ($peerMap as $i => $id) {
		    $peer = $peerItems[$j++]->getMemberData();
		    $peer['peerIndex'] = $i + 1;
		    if (isset($thumbTable[$id])) {
			$peer['thumbnail'] = $thumbTable[$id]->getMemberData();
		    }
		    $peers[] = $peer;
		}
		$block['core']['LoadPeers'] = array('peers' => $peers,
		    'peerCount' => count($peerIds), 'thisPeerIndex' => $peerItemIndex + 1,
		    'parent' => $parent->getMemberData());
	    } else {
		$block['core']['LoadPeers'] =
		    array('peers' => array(), 'peerCount' => 0);
	    }
	    return GalleryStatus::success();
	}

	return GalleryStatus::error(ERROR_BAD_PARAMETER, __FILE__, __LINE__);
    }
}
?>

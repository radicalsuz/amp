<?php
/*
 * $RCSfile: RandomHighlightOption.inc,v $
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
 * @version $Revision: 1.7 $ $Date: 2005/08/23 03:49:50 $
 * @package RandomHighlight
 * @subpackage UserInterface
 * @author Alan Harder <alan.harder@sun.com>
 */

/**
 * This ItemEditOption allows the user to activate random highlight for an album.
 *
 * @package RandomHighlight
 * @subpackage UserInterface
 */
class RandomHighlightOption extends ItemEditOption {

    /**
     * @see ItemEditOption::isAppropriate
     */
    function isAppropriate($item, $thumbnail) {
	return array(GalleryStatus::success(), isset($thumbnail));
    }

    /**
     * @see ItemEditOption::loadTemplate
     */
    function loadTemplate(&$template, &$form, $item, $thumbnail) {
	$form['RandomHighlightOption']['isRandomHighlight'] =
	    $thumbnail->hasOnLoadHandler('RandomHighlight');
	return array(GalleryStatus::success(),
		     'modules/randomhighlight/templates/RandomHighlightOption.tpl',
		     'modules_randomhighlight');
    }

    /**
     * @see ItemEditOption::handleRequestAfterEdit
     */
    function handleRequestAfterEdit($form, &$item, &$preferred) {
	list ($ret, $thumbnail) = GalleryCoreApi::fetchThumbnailsByItemIds(array($item->getId()));
	if ($ret->isError()) {
	    return array($ret->wrap(__FILE__, __LINE__), null, null);
	}
	$thumbnail = array_shift($thumbnail);
	$isRandomHighlight = isset($form['RandomHighlightOption']['isRandomHighlight'])
			     && $form['RandomHighlightOption']['isRandomHighlight'];

	if (isset($thumbnail) &&
	        $isRandomHighlight != $thumbnail->hasOnLoadHandler('RandomHighlight')) {
	    list ($ret, $lockId) = GalleryCoreApi::acquireWriteLock($thumbnail->getId());
	    if ($ret->isError()) {
		return array($ret->wrap(__FILE__, __LINE__), null, null);
	    }
	    if ($isRandomHighlight) {
		$thumbnail->addOnLoadHandler('RandomHighlight');
	    } else {
		$thumbnail->removeOnLoadHandler('RandomHighlight');
	    }
	    $ret = $thumbnail->save();
	    if ($ret->isError()) {
		GalleryCoreApi::releaseLocks($lockId);
		return array($ret->wrap(__FILE__, __LINE__), null, null);
	    }
	    $ret = GalleryCoreApi::releaseLocks($lockId);
	    if ($ret->isError()) {
		return array($ret->wrap(__FILE__, __LINE__), null, null);
	    }
	}

	return array(GalleryStatus::success(), array(), array());
    }
}
?>

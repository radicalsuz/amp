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
 * @package Comment
 * @version $Revision: 1.75 $ $Date: 2005/09/10 20:28:11 $
 * @author Bharat Mediratta <bharat@menalto.com>
 */

/**
 * Comment Module
 *
 * This module provides support for adding comments to items
 *
 * @package Comment
 */
class CommentModule extends GalleryModule {

    function CommentModule() {
	global $gallery;

	$this->setId('comment');
	$this->setName($gallery->i18n('Comments'));
	$this->setDescription($gallery->i18n('User commenting system'));
	$this->setVersion('1.0.0');
	$this->setGroup('data', $this->translate('Extra Data'));
	$this->setCallbacks('getItemLinks|getItemSummaries|getItemAdminViews');
	$this->setRequiredCoreApi(array(6, 0));
	$this->setRequiredModuleApi(array(2, 0));
    }

    /**
     * @see GalleryModule::upgrade()
     */
    function upgrade($currentVersion) {
	global $gallery;

	if (!isset($currentVersion)) {
	    /* Initial install.  Register our permissions */
	    $permissions[] = array('add', $gallery->i18n('[comment] Add comments'), 0, array());
	    $permissions[] = array('edit', $gallery->i18n('[comment] Edit comments'), 0, array());
	    $permissions[] = array('delete', $gallery->i18n('[comment] Delete comments'),
				   0, array());
	    $permissions[] = array('view', $gallery->i18n('[comment] View comments'), 0, array());
	    $permissions[] = array('all', $gallery->i18n('[comment] All access'),
		GALLERY_PERMISSION_COMPOSITE,
		array('comment.add', 'comment.edit', 'comment.delete', 'comment.view'));
	    foreach ($permissions as $p) {
		$ret = GalleryCoreApi::registerPermission($this->getId(),
							  'comment.' . $p[0],
							  $p[1], $p[2], $p[3]);
		if ($ret->isError()) {
		    return $ret->wrap(__FILE__, __LINE__);
		}
	    }
	} else if (version_compare($currentVersion, '0.9.7', '<')) {
	    /*
	     * Remove comment.search permission included in older module versions.
	     * As this is a composite permission we can simply remove its entry in the
	     * PermissionSet table and not touch any item permissions.
	     */
	    GalleryCoreApi::relativeRequireOnce(
		'modules/core/classes/GalleryPermissionSetMap.class');
	    $ret = GalleryPermissionSetMap::removeMapEntry(
		array('module' => 'comment', 'permission' => 'comment.search'));
	    if ($ret->isError()) {
		return $ret->wrap(__FILE__, __LINE__);
	    }
	}

	return GalleryStatus::success();
    }

    /**
     * @see GalleryModule::performFactoryRegistrations()
     */
    function performFactoryRegistrations() {
	$ret = GalleryCoreApi::registerFactoryImplementation(
	    'GalleryEntity', 'GalleryComment', 'GalleryComment',
	    'modules/comment/classes/GalleryComment.class', 'comment', null);
	if ($ret->isError()) {
	    return $ret->wrap(__FILE__, __LINE__);
	}

	$ret = GalleryCoreApi::registerFactoryImplementation(
	    'GallerySearchInterface_1_0', 'GalleryCommentSearch', 'comment',
	    'modules/comment/classes/GalleryCommentSearch.class', 'comment', null);
	if ($ret->isError()) {
	    return $ret->wrap(__FILE__, __LINE__);
	}

	return GalleryStatus::success();
    }

    /**
     * @see GalleryModule::isRecommendedDuringInstall
     */
    function isRecommendedDuringInstall() {
	return true;
    }

    /**
     * @see GalleryModule::autoConfigure
     */
    function autoConfigure() {
	/* We don't require any special configuration */
	return array(GalleryStatus::success(), true);
    }

    /**
     * @see GalleryModule::getItemLinks()
     */
    function getItemLinks($items, $wantsDetailedLinks, $permissions) {
	$links = array();
	foreach ($items as $item) {
	    if (isset($wantsDetailedLinks[$item->getId()])) {
		if (isset($permissions[$item->getId()]['comment.add'])) {
		    $params['view'] = 'comment.AddComment';
		    $params['itemId'] = $item->getId();
		    $params['return'] = 1;
		    $links[$item->getId()][] =
			array('text' => $this->translate('Add Comment'),
			      'params' => $params);
		}
	    }
	}

	return array(GalleryStatus::success(), $links);
    }

    /**
     * @see GalleryModule::getItemSummaries()
     */
    function getItemSummaries($items, $permissions) {
	$ids = array();
	foreach ($items as $item) {
	    $ids[] = $item->getId();
	}

	GalleryCoreApi::relativeRequireOnce('modules/comment/classes/GalleryCommentHelper.class');
	list ($ret, $commentCounts) = GalleryCommentHelper::fetchCommentCounts($ids);
	if ($ret->isError()) {
	    return array($ret->wrap(__FILE__, __LINE__), null);
	}

	$summaries = array();
	foreach ($items as $item) {
	    $message = array();

	    if (isset($permissions[$item->getId()]['comment.view'])) {
		if (!empty($commentCounts[$item->getId()])) {
		    $summaries[$item->getId()] =
			$this->translate(array('text' => 'Comments: %d',
					       'arg1' => $commentCounts[$item->getId()]));
		}
	    }
	}

	return array(GalleryStatus::success(), $summaries);
    }

    /**
     * @see GalleryModule::getItemAdminViews();
     */
    function getItemAdminViews($item) {
	$views = array();
	list ($ret, $permissions) = GalleryCoreApi::getPermissions($item->getId());
	if ($ret->isError()) {
	    return array($ret->wrap(__FILE__, __LINE__), null);
	}

	if (isset($permissions['comment.edit']) ||
	    isset($permissions['comment.delete']) ||
	    isset($permissions['comment.view'])) {
		$views[] = array('name' => $this->translate('View Comments'),
				 'view' => 'comment.ShowComments');
	}

	return array(GalleryStatus::success(), $views);
    }

    /**
     * @see GalleryModule::getRewriteRules
     */
    function getRewriteRules() {
	$rules = array();

	$rule = array();
	$rule['match'] = array('view' => 'core.ItemAdmin', 'subView' => 'comment.AddComment');
	$rule['pattern'] = 'c/add/%itemId%.html';
	$rule['comment'] = $this->translate('Add Comment');
	$rules[] = $rule;

	$rule = array();
	$rule['match'] = array('view' => 'core.ItemAdmin', 'subView' => 'comment.ShowComments');
	$rule['pattern'] = 'c/view/%itemId%.html';
	$rule['comment'] = $this->translate('View Comments');
	$rules[] = $rule;

	return $rules;
    }
}
?>

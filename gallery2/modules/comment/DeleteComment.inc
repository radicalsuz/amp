<?php
/*
 * $RCSfile: DeleteComment.inc,v $
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
 * @version $Revision: 1.19 $ $Date: 2005/08/23 03:49:01 $
 * @package Comment
 * @author Bharat Mediratta <bharat@menalto.com>
 */

/**
 * This controller will handle the deletion of a comment
 *
 * @package Comment
 * @subpackage UserInterface
 *
 */
class DeleteCommentController extends GalleryController {

    /**
     * @see GalleryController::handleRequest()
     */
    function handleRequest($form) {
	global $gallery;

	list ($itemId, $commentId) = GalleryUtilities::getRequestVariables('itemId', 'commentId');

	/* Make sure we have permission to delete a comment */
	$ret = GalleryCoreApi::assertHasItemPermission($itemId, 'comment.delete');
	if ($ret->isError()) {
	    return array($ret->wrap(__FILE__, __LINE__), null);
	}

	$status = array();
	$error = array();
	if (isset($form['action']['delete'])) {
	    /* Delete the comment */
	    list ($ret, $comment) = GalleryCoreApi::loadEntitiesById($commentId);
	    if ($ret->isError()) {
		return array($ret->wrap(__FILE__, __LINE__), null);
	    }

	    /* Make sure that the comment<->parent relationship is right */
	    if ($comment->getParentId() != $itemId) {
		return array(GalleryStatus::error(ERROR_BAD_PARAMETER, __FILE__, __LINE__), null);
	    }

	    $ret = GalleryCoreApi::deleteEntityById($comment->getId());
	    if ($ret->isError()) {
		return array($ret->wrap(__FILE__, __LINE__), null);
	    }

	    $redirect['view'] = 'comment.CommentChangeConfirmation';
	    $redirect['itemId'] = $itemId;
	    $status['deleted'] = 1;
	} else if (isset($form['action']['cancel'])) {
	    /* Where to go on a cancel?  Go to item admin.*/
	    $redirect['view'] = 'core.ShowItem';
	    $redirect['itemId'] = (int)$itemId;
	}

	$results = array();
	if (!empty($redirect)) {
	    $results['return'] = 1;
	    $results['redirect'] = $redirect;
	} else {
	    $results['delegate']['view'] = 'core.ItemAdmin';
	    $results['delegate']['subView'] = 'comment.DeleteComment';
	}
	$results['status'] = $status;
	$results['error'] = $error;

	return array(GalleryStatus::success(), $results);
    }
}

/**
 * This view will ask for a confirmation to delete a comment
 *
 * @package Comment
 * @subpackage UserInterface
 *
 */
class DeleteCommentView extends GalleryView {

    /**
     * @see GalleryView::loadTemplate()
     */
    function loadTemplate(&$template, &$form) {
	global $gallery;

	/* Load our item */
	list ($ret, $item) = $this->_getItem();
	if ($ret->isError()) {
	    return array($ret->wrap(__FILE__, __LINE__), null);
	}

	$commentId = GalleryUtilities::getRequestVariables('commentId');

	/* Make sure we have permission to delete a comment */
	$ret = GalleryCoreApi::assertHasItemPermission($item->getId(), 'comment.delete');
	if ($ret->isError()) {
	    return array($ret->wrap(__FILE__, __LINE__), null);
	}

	if ($form['formName'] != 'DeleteComment') {
	    $form['formName'] = 'DeleteComment';
	}

	/* Load the specific comment */
	list ($ret, $comment) = GalleryCoreApi::loadEntitiesById($commentId);
	if ($ret->isError()) {
	    return array($ret->wrap(__FILE__, __LINE__), null);
	}

	/* Associate the commenter's info */
	$commenterId = $comment->getCommenterId();
	list ($ret, $commenter) = GalleryCoreApi::loadEntitiesById($commenterId);
	if ($ret->isError()) {
	    return array($ret->wrap(__FILE__, __LINE__), null);
	}

	$DeleteComment = array();
	$DeleteComment['comment'] = $comment->getMemberData();
	$DeleteComment['commenter'] = $commenter->getMemberData();
	$DeleteComment['item'] = $item->getMemberData();
	$DeleteComment['can'] = array('edit' => false, 'delete' => false);

	$template->setVariable('DeleteComment', $DeleteComment);
	$template->setVariable('controller', 'comment.DeleteComment');

	return array(GalleryStatus::success(),
		     array('body' => 'modules/comment/templates/DeleteComment.tpl'));
    }
}
?>

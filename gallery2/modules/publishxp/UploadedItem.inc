<?php
/*
 * $RCSfile: UploadedItem.inc,v $
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
 * Empty view used when the photo is successfully uploaded.
 *
 * Each photo is uploaded via a seperate HTTP request.  The initial response from
 * this page is to return JavaScript code that instructs Windows how to upload
 * the photos.  Subsequent requests from Windows include the photo data and text.
 * This page processes the data and applies the options to be used while uploading.
 *
 * @version $Id: UploadedItem.inc,v 1.3 2005/08/23 03:49:49 mindless Exp $
 * @package PublishXp
 * @author Timothy Webb <tiwebb@cisco.com>
 */
class UploadedItemView extends GalleryView {
    /**
     * @see GalleryView::loadTemplate()
     */
    function loadTemplate(&$template, &$form) {
	return array(GalleryStatus::success(),
		     array('body' => 'modules/publishxp/templates/EmptyPage.tpl'));
    }
}
?>

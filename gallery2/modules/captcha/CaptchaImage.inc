<?php
/*
 * $RCSfile: CaptchaImage.inc,v $
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
 * @version $Revision: 1.3 $ $Date: 2005/08/23 03:49:00 $
 * @package Captcha
 * @subpackage UserInterface
 * @author Stefan Ioachim <stefanioachim@gmail.com>
 * @author Bharat Mediratta <bharat@menalto.com>
 */

/**
 * Display the given captcha image
 *
 * @package Captcha
 * @subpackage UserInterface
 */
class CaptchaImageView extends GalleryView {

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
	global $gallery;

	header('Content-type: image/jpeg');
	header('Pragma: No-cache');

	$session =& $gallery->getSession();
	$code = $session->get('captcha.key');

	$image = @ImageCreateFromJPEG(dirname(__FILE__) . '/data/code_bg.jpg');
	$textColor = ImageColorAllocate($image, 80, 80, 80);
	$width = (imagesx($image) - 10 * strlen($code)) / 2;
	ImageString($image, 5, $width, 40, $code, $textColor);
	ImageJPEG($image, '', 75);
	ImageDestroy($image);

	return GalleryStatus::success();
    }
}
?>

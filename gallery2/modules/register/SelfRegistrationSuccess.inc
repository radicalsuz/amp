<?php
/*
 * $RCSfile: SelfRegistrationSuccess.inc,v $
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
 * @version $Revision: 1.7 $ $Date: 2005/08/23 03:49:52 $
 * @package SelfRegistration
 * @subpackage UserInterface
 * @author Sebastian Eichner <mailsp@sebastian-eichner.de>
 */

/**
 * This controller will handle the success-confirmation for the selfregistration of an user
 *
 * @package SelfRegistration
 * @subpackage UserInterface
 *
 */
class SelfRegistrationSuccessController extends GalleryController {

    /**
     * @see GalleryController::handleRequest
     */
    function handleRequest($form) {
	global $gallery;
	$results = array();
	$results['delegate']['view'] = 'core.UserAdmin';
	$results['delegate']['subView'] = 'register.SelfRegistrationSuccess';

	return array(GalleryStatus::success(), $results);
    }
}

/**
 * This view will show a confirmation for the user
 *
 * @package SelfRegistration
 * @subpackage UserInterface
 */
class SelfRegistrationSuccessView extends GalleryView {

    /**
     * @see GalleryView::loadTemplate
     */
    function loadTemplate(&$template, &$form) {
	global $gallery;

	$selfregistration = array('email' => 'e@mail.de');
	$template->setVariable('selfregistration', $selfregistration);
	$template->setVariable('controller', 'register.SelfRegistrationSuccess');
	return array(GalleryStatus::success(),
		     array('body' => 'modules/register/templates/SelfRegistrationSuccess.tpl'));
    }
}
?>

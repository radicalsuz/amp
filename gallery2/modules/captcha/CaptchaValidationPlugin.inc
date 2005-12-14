<?php
/*
 * $RCSfile: CaptchaValidationPlugin.inc,v $
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
 * @version $Revision: 1.2 $ $Date: 2005/08/23 03:49:00 $
 * @package Captcha
 * @author Stefan Ioachim <stefanioachim@gmail.com>
 * @author Bharat Mediratta <bharat@menalto.com>  
 */

/**
 * Implement ValidationPlugin to present the HTML for the captcha image
 * and an input box for the user to type in the correct value.
 *
 * @package Captcha
 */
class CaptchaValidationPlugin extends ValidationPlugin {

    /**
     * @see ValidationPlugin::performValidation
     */
    function performValidation(&$form) {
	global $gallery;

	$session =& $gallery->getSession();
	$code = $session->get('captcha.key');

	$error = array();
	$success = false;
	if (isset($form['CaptchaValidationPlugin']['word']) &&
	        empty($form['CaptchaValidationPlugin']['word'])) {
	    $error[] = 'form[error][CaptchaValidationPlugin][missing]';
	} else if (isset($form['CaptchaValidationPlugin']['word']) &&
		   $form['CaptchaValidationPlugin']['word'] != $code) {
	    $error[] = 'form[error][CaptchaValidationPlugin][invalid]';
	} else {
	    $success = true;
	}

	return array(GalleryStatus::success(), $error, $success);
    }

    /**
     * @see ValidationPlugin::loadTemplate
     */
    function loadTemplate(&$template, &$form, $securityLevel) {
	global $gallery;

	$session =& $gallery->getSession();

	$useCaptcha = false;

	switch (strtoupper($securityLevel)) {
	case 'HIGH':
		/* always require captcha to be enabled */
		$useCaptcha = true;
		break;
	case 'MEDIUM':
		/*
		 * Use the module's failedAttemptThreshold parameter to match against the
		 * captcha.failedAttempts stored in the session to figure out whether or not we need to
		 * show the captcha.
		 */
		list ($ret, $failedAttemptThreshold) =
		    GalleryCoreApi::getPluginParameter('module', 'captcha', 'failedAttemptThreshold');
		if ($ret->isError()) {
		    return array($ret->wrap(__FILE__, __LINE__), null, null);
		}
		
		$failedAttempts = $session->get('captcha.failedAttempts');
		
		if (!isset($failedAttempts)) {
		    $failedAttempts = 0;
		}

		if (isset($form['error'])) {
		    $failedAttempts++;
		    $session->put('captcha.failedAttempts', $failedAttempts);
		}


		if ($failedAttempts > $failedAttemptThreshold) {
			$useCaptcha = true;
		}
		break;
	case 'LOW':
		/* Captcha does nothing for the LOW method */
		break;
	default:
		/* Bad Parameter */
		return array(GalleryStatus::error(ERROR_BAD_PARAMETER, __FILE__, __LINE__), 
			     null, null);
	}

	if ($useCaptcha) {
	    /* Generate a new code */
	    list($usec, $sec) = explode(' ', microtime());
	    srand((float) $sec + ((float) $usec * 100000));
	    $random_num = rand ();
	    $datekey = date('H i s');
	    $rcode = hexdec(md5(GalleryUtilities::getServerVar('HTTP_USER_AGENT') .
				$random_num . $datekey));
	    $code = substr($rcode, 2, 6);
	    $session->put('captcha.key', $code);

	    return array(GalleryStatus::success(),
			 'modules/captcha/templates/CaptchaValidationPlugin.tpl',
			 'modules_captcha');
	} else {
	    /* They haven't failed enough to require the captcha yet */
	    return array(GalleryStatus::success(), null, null);
	}
    }
}
?>

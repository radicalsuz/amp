<?php
/*
 * ATTENTION:
 *
 * If you're seeing this in your browser, and are trying to upgrade Gallery,
 * you either do not have PHP installed, or if it is installed, it is not
 * properly enabled. Please visit the following page for assistance:
 *
 *    http://gallery.sourceforge.net/
 *
 * ----------------------------------------------------------------------------
 *
 * $Id: index.php,v 1.21 2005/08/23 03:50:01 mindless Exp $
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

/* Show all errors. */
@ini_set('display_errors', 1);

/*
 * Disable magic_quotes runtime -- it causes problems with legitimate quotes
 * in our SQL, as well as reading/writing the config.php
 */
@ini_set('magic_quotes_runtime', 0);

require_once(dirname(__FILE__) . '/UpgradeStep.class');
require_once(dirname(__FILE__) . '/StatusTemplate.class');

/*
 * If gettext isn't enabled, subvert the _() text translation function
 * and just pass the string on through in English
 */
if (!function_exists('_')) {
    function _($s) {
	return $s;
    }
}

$error = false;

/* Our install steps, in order */
$stepOrder[] = 'Welcome';
$stepOrder[] = 'Authenticate';
$stepOrder[] = 'SystemChecks';
$stepOrder[] = 'UpgradeCoreModule';
$stepOrder[] = 'UpgradeOtherModules';
$stepOrder[] = 'CleanCache';
$stepOrder[] = 'Finished';

foreach ($stepOrder as $stepName) {
    $className = $stepName . 'Step';
    require("steps/$className.class");
}

if (!ini_get('session.auto_start')) {
    session_start();
}

if (!isset($_SESSION['path'])) {
    $_SESSION['path'] = __FILE__;
} else if ($_SESSION['path'] != __FILE__) {
    /* Security error!  This session is not valid for this copy of the upgrader. Start over. */
    session_unset();
    $_SESSION['path'] = __FILE__;
}

require_once(dirname(__FILE__) . '/../bootstrap.inc');
require_once(dirname(__FILE__) . '/../init.inc');
/* Check if config.php is ok */
$storageConfig = @$gallery->getConfig('storage.config');
if (!empty($storageConfig)) {
    $ret = GalleryInitFirstPass(array('debug' => 'buffered', 'noDatabase' => 1));
    if ($ret->isError()) {
	print $ret->getAsHtml();
	return;
    }

    $translator = $gallery->getTranslator();
    if (!$translator->canTranslate()) {
	unset($translator);
    } else {
	if (empty($_SESSION['language'])) {
	    $_SESSION['language'] = GalleryTranslator::getLanguageCodeFromRequest();
	}
	$translator->init($_SESSION['language']);
	/* Select domain for translation */
	bindtextdomain('gallery2_upgrade', dirname(__FILE__) . '/locale');
	textdomain('gallery2_upgrade');
	if (function_exists('bind_textdomain_codeset')) {
	    bind_textdomain_codeset('gallery2_upgrade', 'UTF-8');
	}
    }
    
    /* We want to avoid using the cache */
    GalleryDataCache::setFileCachingEnabled(false);
    GalleryDataCache::setMemoryCachingEnabled(false);
    
    /* Preallocate at least 5 minutes for the upgrade */
    $gallery->guaranteeTimeLimit(300);
    
    /* Check to see if we have a database.  If we don't, then go to the installer */
    $storage =& $gallery->getStorage();
    list ($ret, $isInstalled) = $storage->isInstalled();
    if ($ret->isError() || !$isInstalled) {
	$error = true;
    }
} else {
    $error = true;
}

/* If we don't have our steps in our session, initialize them now. */
if (!isset($_GET['startOver']) && !empty($_SESSION['upgrade_steps'])) {
    $steps = unserialize($_SESSION['upgrade_steps']);
}

if (empty($steps) || !is_array($steps)) {
    $steps = array();
    for ($i = 0; $i < sizeof($stepOrder); $i++) {
	$className = $stepOrder[$i] . 'Step';
	$step = new $className();
	if ($step->isRelevant()) {
	    $step->setIsLastStep(false);
	    $step->setStepNumber($i);
	    $step->setInError(false);
	    $step->setComplete(false);
	    $steps[] = $step;
	}
    }

    /* Don't do this in the loop, since not all steps are relevant */
    $steps[sizeof($steps)-1]->setIsLastStep(true);
}

if (isset($_GET['step'])) {
    $stepNumber = (int)$_GET['step'];
} else {
    $stepNumber = 0;
}

/* Make sure all steps up to the current one are ok */
for ($i = 0; $i < $stepNumber; $i++) {
    if (!$steps[$i]->isComplete() && ! $steps[$i]->isOptional()) {
	$stepNumber = $i;
	break;
    }
}

if (!$error) {
    $currentStep =& $steps[$stepNumber];
} else {
    require_once(dirname(__FILE__) . '/steps/RedirectToInstallerStep.class');
    $currentStep =& new RedirectToInstallerStep();
}

if (!empty($_GET['doOver'])) {
    $currentStep->setComplete(false);
}

/* If the current step is incomplete, the rest of the steps can't be complete either */
if (!$currentStep->isComplete()) {
    for ($i = $stepNumber+1; $i < sizeof($steps); $i++) {
	$steps[$i]->setComplete(false);
	$steps[$i]->setInError(false);
    }
}

if ($currentStep->processRequest()) {
    /* Load up template data from the current step */
    $templateData = array();

    /* Round percentage to the nearest 5 */
    $templateData['errors'] = array();
    $currentStep->loadTemplateData($templateData);

    /* Fetch our page into a variable */
    ob_start();
    $template = new StatusTemplate();
    $template->renderHeaderBodyAndFooter($templateData);
    $html = ob_get_contents();
    ob_end_clean();

    /* Add session ids if we don't have cookies */
    $html = addSessionIdToUrls($html);
    print $html;
}

/**
 * Add the session id to our url, if necessary
 */
function addSessionIdToUrls($html) {
    /*
     * SID is empty if we have a session cookie.
     * If session.use_trans_sid is on then it will add the session id.
     */
    $sid = SID;
    if (!empty($sid) && !ini_get('session.use_trans_sid')) {
	$html = preg_replace('/href="(.*\?.*)"/', 'href="$1&amp;' . $sid . '"', $html);
    }
    return $html;
}

/**
 * Mini url generator for upgrader
 */
function generateUrl($uri, $print=true) {
    if (strncmp($uri, 'index.php', 9) && strncmp($uri, '../' . GALLERY_MAIN_PHP, 11)) {
	global $gallery;
	/* Add @ here in case we haven't yet upgraded config.php to include galleryBaseUrl */
	$baseUrl = @$gallery->getConfig('galleryBaseUrl');
	if (!empty($baseUrl)) {
	     $uri = $baseUrl . 'upgrade/' . $uri;
	}
    }
    if ($print) {
	print $uri;
    }
    return $uri;
}

/*
 * We don't store the steps in the session in raw form because that
 * will break in environments where session.auto_start is on since
 * it will try to instantiate the classes before they've been defined
 */
$_SESSION['upgrade_steps'] = serialize($steps);
?>

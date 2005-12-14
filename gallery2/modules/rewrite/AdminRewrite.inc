<?php
/*
 * $RCSfile: AdminRewrite.inc,v $
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
 * @package Rewrite
 * @version $Revision: 1.19 $ $Date: 2005/08/23 03:49:52 $
 * @author Douglas Cau <douglas@cau.se>
 */

/**
 * Required class
 */
GalleryCoreApi::relativeRequireOnce('modules/rewrite/classes/RewriteHelper.class');
GalleryCoreApi::relativeRequireOnce('modules/rewrite/classes/RewriteMap.class');

/**
 * @package Rewrite
 * @subpackage UserInterface
 */
class AdminRewriteController extends GalleryController {

    /**
     * @see GalleryController::handleRequest()
     */
    function handleRequest(&$form) {
	global $gallery;
	$platform = $gallery->getPlatform();

	$ret = GalleryCoreApi::assertUserIsSiteAdministrator();
	if ($ret->isError()) {
	    return array($ret->wrap(__FILE__, __LINE__), null);
	}

	$status = $error = $dupe = array();

	if (isset($form['action']['saveRules'])) {
	    $results['delegate']['mode'] = 'rules';

	    $activeRules = array();
	    if (isset($form['shortUrls'])) {
		foreach (array_keys($form['shortUrls']) as $moduleId) {
		    $activeRules[$moduleId] = array();
		    foreach (array_keys($form['shortUrls'][$moduleId]) as $ruleId) {
			if (isset($form['shortUrls'][$moduleId][$ruleId]['active'])) {
			    if (empty($form['shortUrls'][$moduleId][$ruleId]['pattern'])) {
				$error[] = 'form[error][empty][' . $moduleId . '][' . $ruleId . ']';
				break 2;
			    }

			    if (isset($dupe[$form['shortUrls'][$moduleId][$ruleId]['pattern']])) {
				$error[] = 'form[error][dupe][' .
					$form['shortUrls'][$moduleId][$ruleId]['pattern'] . ']';
				break 2;
			    }

			    $activeRules[$moduleId][$ruleId] = array();
			    $activeRules[$moduleId][$ruleId]['pattern'] =
				    $form['shortUrls'][$moduleId][$ruleId]['pattern'];
			    if (substr($activeRules[$moduleId][$ruleId]['pattern'], 0, 1) == '/') {
				$activeRules[$moduleId][$ruleId]['pattern'] =
					substr($activeRules[$moduleId][$ruleId]['pattern'], 1);
			    }

			    if (isset($form['shortUrls'][$moduleId][$ruleId]['match'])) {
				$activeRules[$moduleId][$ruleId]['match'] =
					$form['shortUrls'][$moduleId][$ruleId]['match'];
			    }

			    $dupe[$form['shortUrls'][$moduleId][$ruleId]['pattern']] = 1;
			}
		    }

		    if (empty($activeRules[$moduleId])) {
			unset($activeRules[$moduleId]);
		    }
		}
	    }

	    if (empty($error)) {
		list($ret, $code, $rewriteRules) = RewriteHelper::parseActiveRules($activeRules);
		if ($ret->isError()) {
		    return array($ret->wrap(__FILE__, __LINE__), null);
		}
		if ($code != REWRITE_STATUS_OK) {
		    $error[] = 'form[error][badKeyword]';
		}

		list($ret, $code) = RewriteHelper::writeFile($rewriteRules);
		if ($ret->isError()) {
		    return array($ret->wrap(__FILE__, __LINE__), null);
		}
		if ($code != REWRITE_STATUS_OK) {
		    $error[] = 'form[error][cantWrite][gallery]';
		}

		if (GalleryUtilities::isEmbedded()) {
		    list($ret, $code) = RewriteHelper::writeFile($rewriteRules, true);
		    if ($ret->isError()) {
			return array($ret->wrap(__FILE__, __LINE__), null);
		    }
		    if ($code != REWRITE_STATUS_OK) {
			$error[] = 'form[error][cantWrite][embedded]';
		    }
		}

		if (empty($error)) {
		    $ret = RewriteMap::setActiveRules($activeRules);
		    if ($ret->isError()) {
			return array($ret->wrap(__FILE__, __LINE__), null);
		    }

		    $redirect['view'] = 'core.SiteAdmin';
		    $redirect['subView'] = 'rewrite.AdminRewrite';
		    $redirect['mode'] = 'rules';
		    $status['rulesSaved'] = 1;
		}
	    }
	} else if (isset($form['action']['test'])) {
	    $redirect['view'] = 'core.SiteAdmin';
	    $redirect['subView'] = 'rewrite.AdminRewrite';
	    $redirect['mode'] = 'test';
	} else if (isset($form['action']['saveSetup'])) {
	    $results['delegate']['mode'] = 'setup';

	    if (isset($form['embedded']['save'])) {
		if (!$platform->is_dir($form['embedded']['htaccessPath'])) {
		    $error[] = 'form[error][invalidDir]';
		}
		if (empty($form['embedded']['publicPath'])) {
		    $error[] = 'form[error][invalidPath]';
		}

		if (substr($form['embedded']['htaccessPath'], -1) == '/') {
		    $form['embedded']['htaccessPath'] =
			substr($form['embedded']['htaccessPath'], 0, -1);
		}
		if (substr($form['embedded']['publicPath'], 0, 1) != '/') {
		    $form['embedded']['publicPath'] = '/' . $form['embedded']['publicPath'];
		}
		if (substr($form['embedded']['publicPath'], -1) != '/') {
		    $form['embedded']['publicPath'] .= '/';
		}
	    }

	    $allow = array();
	    foreach ($form['allow'] as $host) {
		if (!empty($host)) {
		    $allow[] = $host;
		}
	    }

	    list ($ret, $code) = RewriteHelper::checkFile();
	    if ($ret->isError()) {
		return array($ret->wrap(__FILE__, __LINE__), null);
	    }
	    if ($code != REWRITE_STATUS_HTACCESS_READY) {
		$error[] = 'form[error][cantWrite][gallery]';
	    }

	    if (empty($error)) {
		if (isset($form['embedded']['save'])) {
		    $ret = GalleryCoreApi::setPluginParameter('module', 'rewrite',
			    'embeddedHtaccess', $form['embedded']['htaccessPath']);
		    if ($ret->isError()) {
			return array($ret->wrap(__FILE__, __LINE__), null);
		    }

		    $ret = GalleryCoreApi::setPluginParameter('module', 'rewrite',
			    'embeddedLocation', $form['embedded']['publicPath']);
		    if ($ret->isError()) {
			return array($ret->wrap(__FILE__, __LINE__), null);
		    }
		}

		$ret = GalleryCoreApi::setPluginParameter('module', 'rewrite',
			'accessList', serialize($allow));
		if ($ret->isError()) {
		    return array($ret->wrap(__FILE__, __LINE__), null);
		}

		list ($ret, $activeRules) = RewriteMap::getActiveRules();
		if ($ret->isError()) {
		    return array($ret->wrap(__FILE__, __LINE__), null);
		}
		list($ret, $code, $rewriteRules) = RewriteHelper::parseActiveRules($activeRules);
		if ($ret->isError()) {
		    return array($ret->wrap(__FILE__, __LINE__), null);
		}
		if ($code != REWRITE_STATUS_OK) {
		    return array(GalleryStatus::error(ERROR_UNKNOWN, __FILE__, __LINE__), null);
		}

		list($ret, $code) = RewriteHelper::writeFile($rewriteRules);
		if ($ret->isError()) {
		    return array($ret->wrap(__FILE__, __LINE__), null);
		}
		if ($code != REWRITE_STATUS_OK) {
		    $error[] = 'form[error][cantWrite][gallery]';
		}

		if (GalleryUtilities::isEmbedded()) {
		    list($ret, $code) = RewriteHelper::writeFile($rewriteRules, true);
		    if ($ret->isError()) {
			return array($ret->wrap(__FILE__, __LINE__), null);
		    }
		    if ($code != REWRITE_STATUS_OK) {
			$error[] = 'form[error][cantWrite][embedded]';
		    }
		}

		if (empty($error)) {
		    $status['setupSaved'] = 1;
		    $redirect['view'] = 'core.SiteAdmin';
		    $redirect['subView'] = 'rewrite.AdminRewrite';
		    $redirect['mode'] = 'setup';
		}
	    }
	}

	if (!empty($redirect)) {
	    $results['redirect'] = $redirect;
	    unset($results['delegate']);
	} else {
	    $results['delegate']['view'] = 'core.SiteAdmin';
	    $results['delegate']['subView'] = 'rewrite.AdminRewrite';
	}
	$results['status'] = $status;
	$results['error'] = $error;

	return array(GalleryStatus::success(), $results);
    }
}

/**
 * @package Rewrite
 * @subpackage UserInterface
 */
class AdminRewriteView extends GalleryView {

    /**
     * @see GalleryView::loadTemplate
     */
    function loadTemplate(&$template, &$form) {
	global $gallery;
	$urlGenerator =& $gallery->getUrlGenerator();
	$AdminRewrite = array();

	$ret = GalleryCoreApi::assertUserIsSiteAdministrator();
	if ($ret->isError()) {
	    return array($ret->wrap(__FILE__, __LINE__), null);
	}

	$AdminRewrite['isEmbedded'] = GalleryUtilities::isEmbedded();
	$AdminRewrite['mode'] = GalleryUtilities::getRequestVariables('mode');
	if (empty($AdminRewrite['mode'])) {
	    list ($ret, $embeddedLocation) =
		    GalleryCoreApi::getPluginParameter('module', 'rewrite', 'embeddedLocation');
	    if ($ret->isError()) {
		return array($ret->wrap(__FILE__, __LINE__), null);
	    }

	    list ($ret, $code) = RewriteHelper::checkFile();
	    if ($ret->isError()) {
		return array($ret->wrap(__FILE__, __LINE__), null);
	    }

	    if (GalleryUtilities::isEmbedded() && empty($embeddedLocation)) {
		$AdminRewrite['mode'] = 'setup';
	    } else if ($code != REWRITE_STATUS_HTACCESS_READY) {
		$AdminRewrite['mode'] = 'test';
	    } else {
		$AdminRewrite['mode'] = 'rules';
	    }
	}

	switch ($AdminRewrite['mode']) {
	case 'rules':
	    list ($ret, $activeRules) = RewriteMap::getActiveRules();
	    if ($ret->isError()) {
		return array($ret->wrap(__FILE__, __LINE__), null);
	    }

	    $AdminRewrite['shortUrls'] = array();
	    if ($form['formName'] != 'AdminRewrite') {
		$form['shortUrls'] = array();
	    }

	    list($ret, $modules) = GalleryCoreAPi::fetchPluginStatus('module');
	    if ($ret->isError()) {
		return array($ret->wrap(__FILE__, __LINE__), null);
	    }

	    foreach ($modules as $moduleId => $status) {
		if (isset($status['active']) && $status['active']) {
		    list($ret, $module) = GalleryCoreApi::loadPlugin('module', $moduleId);
		    if ($ret->isError()) {
			if ($ret->getErrorCode() & ERROR_PLUGIN_VERSION_MISMATCH) {
			    continue;
			}
			return array($ret->wrap(__FILE__, __LINE__), null);
		    }

		    $AdminRewrite['shortUrls'][$moduleId] = array(
			'name' => $module->translate($module->getName()));

		    $rules = $module->getRewriteRules();

		    foreach ($rules as $ruleId => $rule) {
			if ($form['formName'] != 'AdminRewrite') {
			    $form['shortUrls'][$moduleId][$ruleId] = array();

			    if (isset($activeRules[$moduleId][$ruleId])) {
				$form['shortUrls'][$moduleId][$ruleId]['active'] = 1;
				$form['shortUrls'][$moduleId][$ruleId]['pattern'] =
					$activeRules[$moduleId][$ruleId]['pattern'];
			    } else {
				$form['shortUrls'][$moduleId][$ruleId]['pattern'] =
					$rule['pattern'];
			    }

			    if (isset($rule['match'])) {
				$form['shortUrls'][$moduleId][$ruleId]['match'] =
					join('|', $rule['match']);
			    }
			}

			if (isset($rule['locked'])) {
			    $AdminRewrite['shortUrls'][$moduleId]['rules'][$ruleId]['locked'] = 1;
			}

			if (isset($rule['comment'])) {
			    $AdminRewrite['shortUrls'][$moduleId]['rules'][$ruleId]['name'] =
				    $rule['comment'];
			} else if (isset($rule['match'])) {
			    $AdminRewrite['shortUrls'][$moduleId]['rules'][$ruleId]['name'] =
				    (isset($rule['match']['subView'])) ? $rule['match']['subView']
					    : $rule['match']['view'];
			}

			if (isset($rule['keywords'])) {
			    $AdminRewrite['shortUrls'][$moduleId]['rules'][$ruleId]['keywords'] =
				    $rule['keywords'];
			} else  {
			    $AdminRewrite['shortUrls'][$moduleId]['rules'][$ruleId]['keywords'] =
				    array();
			}

			if (isset($rule['help'])) {
			    $AdminRewrite['shortUrls'][$moduleId]['rules'][$ruleId]['help'] =
				    $rule['help'];
			}
		    }

		    if (!sizeof($rules)) {
			unset($AdminRewrite['shortUrls'][$moduleId]);
			unset($form['shortUrls'][$moduleId]);
		    }
		}
	    }
	    break;

	case 'test':
	    $baseUrl = parse_url($urlGenerator->generateUrl(
		array('href' => 'modules/rewrite/data/'), false));
	    $AdminRewrite['customFile'] = dirname(__FILE__) . '/data/mod_rewrite/custom/.htaccess';
	    $AdminRewrite['customLine'] = 'RewriteBase ' . $baseUrl['path'] . 'mod_rewrite/custom/';
	    $AdminRewrite['customFileNoOptions'] = dirname(__FILE__) .
		'/data/mod_rewrite_no_options/custom/.htaccess';
	    $AdminRewrite['customLineNoOptions'] = 'RewriteBase ' . $baseUrl['path'] .
		'mod_rewrite_no_options/custom/';

	    list ($ret, $AdminRewrite['htaccessPath']) = RewriteHelper::getHtaccessPath();
	    if ($ret->isError()) {
		return array($ret->wrap(__FILE__, __LINE__), null);
	    }
	    list ($ret, $AdminRewrite['htaccessCode']) = RewriteHelper::checkFile();
	    if ($ret->isError()) {
		return array($ret->wrap(__FILE__, __LINE__), null);
	    }
	    list ($ret, $AdminRewrite['apacheCode']) = RewriteHelper::checkModRewrite();
	    if ($ret->isError()) {
		return array($ret->wrap(__FILE__, __LINE__), null);
	    }

	    if ($AdminRewrite['isEmbedded']) {
		list ($ret, $AdminRewrite['embeddedCode']) = RewriteHelper::checkFile(true);
		if ($ret->isError()) {
		    return array($ret->wrap(__FILE__, __LINE__), null);
		}

		list ($ret, $AdminRewrite['embeddedPath']) = RewriteHelper::getHtaccessPath(true);
		if ($ret->isError()) {
		    return array($ret->wrap(__FILE__, __LINE__), null);
		}

		list ($ret, $AdminRewrite['embeddedSync']) = RewriteHelper::checkSync();
		if ($ret->isError()) {
		    return array($ret->wrap(__FILE__, __LINE__), null);
		}
	    }
	    break;

	case 'setup':
	    if ($form['formName'] != 'AdminRewrite') {
		list ($ret, $form['embedded']['publicPath']) =
		    GalleryCoreApi::getPluginParameter('module', 'rewrite', 'embeddedLocation');
		if ($ret->isError()) {
		    return array($ret->wrap(__FILE__, __LINE__), null);
		}

		list ($ret, $form['embedded']['htaccessPath']) =
		    GalleryCoreApi::getPluginParameter('module', 'rewrite', 'embeddedHtaccess');
		if ($ret->isError()) {
		    return array($ret->wrap(__FILE__, __LINE__), null);
		}

		list ($ret, $form['allow']) = GalleryCoreApi::getPluginParameter(
			'module', 'rewrite', 'accessList');
		if ($ret->isError()) {
		    return array($ret->wrap(__FILE__, __LINE__), null);
		}
		$form['allow'] = unserialize($form['allow']);
	    }

	    $AdminRewrite['host'] = substr($urlGenerator->makeUrl('/'), 0, -1);
	    $AdminRewrite['serverName'] = GalleryUtilities::getServerVar('SERVER_NAME');

	    break;
	}

	if ($form['formName'] != 'AdminRewrite') {
	    $form['formName'] = 'AdminRewrite';
	}

	$template->setVariable('AdminRewrite', $AdminRewrite);
	$template->setVariable('controller', 'rewrite.AdminRewrite');

	$template->javascript('lib/javascript/BlockToggle.js');
	return array(GalleryStatus::success(),
		array('body' => 'modules/rewrite/templates/AdminRewrite.tpl'));
    }
}
?>

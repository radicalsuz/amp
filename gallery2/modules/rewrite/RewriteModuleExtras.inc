<?php
/*
 * $RCSfile: RewriteModuleExtras.inc,v $
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
 * @version $Revision: 1.29 $ $Date: 2005/09/10 20:28:14 $
 * @author Douglas Cau <douglas@cau.se>
 */

class RewriteModuleExtras {

    /**
     * @see GalleryModule::upgrade
     */
    function upgrade($module, $currentVersion) {
	switch ($currentVersion) {
	case null:
	    /* Initial install */
	    foreach (array('galleryLocation' => '',
			   'status' => serialize(array()),
			   'embeddedLocation' => '',
			   'embeddedHtaccess' => '',
			   'accessList' => serialize(array()))
		     as $key => $value) {
		$ret = $module->setParameter($key, $value);
		if ($ret->isError()) {
		    return $ret->wrap(__FILE__, __LINE__);
		}
	    }
	    break;

	case '0.0.1':
	case '0.0.2':
	case '0.0.3':
	    foreach (array('prefixes' => '', 'views' => '')
		    as $key => $value) {
		$ret = $module->setParameter($key, $value);
		if ($ret->isError()) {
		    return $ret->wrap(__FILE__, __LINE__);
		}
	    }

	case '0.0.4':
	    $prefixMap = array();
	    list ($ret, $views) =
		    GalleryCoreApi::getPluginParameter('module', 'rewrite', 'views');
	    if ($ret->isError()) {
		return $ret->wrap(__FILE__, __LINE__);
	    }
	    $views = explode('|', $views);

	    list ($ret, $prefixes) =
		    GalleryCoreApi::getPluginParameter('module', 'rewrite', 'prefixes');
	    if ($ret->isError()) {
		return $ret->wrap(__FILE__, __LINE__);
	    }
	    $prefixes = explode('|', $prefixes);

	    for ($i = 0; $i < sizeof($views); $i++) {
		if (strlen($prefixes[$i])) {
		    $prefixMap[$views[$i]] = $prefixes[$i];
		}
	    }

	    foreach (array('prefixMap' => serialize($prefixMap))
		    as $key => $value) {
		$ret = $module->setParameter($key, $value);
		if ($ret->isError()) {
		    return $ret->wrap(__FILE__, __LINE__);
		}
	    }

	    $ret = $module->removeParameter('views');
	    if ($ret->isError()) {
		return $ret->wrap(__FILE__, __LINE__);
	    }
	    $ret = $module->removeParameter('prefixes');
	    if ($ret->isError()) {
		return $ret->wrap(__FILE__, __LINE__);
	    }

	    $currentVersion = '0.0.5';

	case '0.0.5':
	case '0.1.0':
	case '0.1.1':
	case '0.1.2':
	case '0.1.3':
	    foreach (array('usePrefixes' => 'true',
			   'rewriteLocation' => '',
			   'rewriteBase' => '')
		    as $key => $value) {
		$ret = $module->setParameter($key, $value);
		if ($ret->isError()) {
		    return $ret->wrap(__FILE__, __LINE__);
		}
	    }

	case '0.1.4':
	case '0.1.5':
	    foreach (array('galleryLocation' => '',
			   'step' => 1)
		    as $key => $value) {
		$ret = $module->setParameter($key, $value);
		if ($ret->isError()) {
		    return $ret->wrap(__FILE__, __LINE__);
		}
	    }

	case '0.1.6':
	case '0.1.7':
	case '0.1.8':
	    foreach (array('rewriteLocation', 'rewriteBase') as $key) {
		$ret = $module->removeParameter($key);
		if ($ret->isError()) {
		    return $ret->wrap(__FILE__, __LINE__);
		}
	    }

	case '0.1.9':
	case '0.1.10':
	case '0.8.0':
	case '0.8.1':
	    foreach (array('prefixMap', 'usePrefixes') as $key) {
		$ret = $module->removeParameter($key);
		if ($ret->isError()) {
		    return $ret->wrap(__FILE__, __LINE__);
		}
	    }

	    /*
	     * We changed the .htaccess format so deactivate the module and make
	     * the user reconfigure and reactivate.
	     */
	    list ($ret, $unused) = $module->deactivate();
	    if ($ret->isError()) {
		return $ret->wrap(__FILE__, __LINE__);
	    }

	case '0.9.0':
	case '0.9.1':
	case '0.9.2':
	    /* Reset Gallery location, needs to be reconfigured. */
	    foreach (array('galleryLocation' => '',
			   'needOptions' => '',
			   'embeddedLocation' => '',
			   'embeddedHtaccess' => '')
		     as $key => $value) {
		$ret = $module->setParameter($key, $value);
		if ($ret->isError()) {
		    return $ret->wrap(__FILE__, __LINE__);
		}
	    }

	    list ($ret, $code) = $module->autoConfigure();
	    if ($ret->isError()) {
		return $ret->wrap(__FILE__, __LINE__);
	    }

	case '0.9.3':
	case '0.9.4':
	    foreach (array('accessList' => serialize(array()))
		     as $key => $value) {
		$ret = $module->setParameter($key, $value);
		if ($ret->isError()) {
		    return $ret->wrap(__FILE__, __LINE__);
		}
	    }

	case '0.9.5':
	    /* Update view names in match strings to new . separator; core.ShowItem, etc */
	    GalleryCoreApi::relativeRequireOnce('modules/rewrite/classes/RewriteMap.class');
	    list ($ret, $activeRules) = RewriteMap::getActiveRules();
	    if ($ret->isError()) {
		return $ret->wrap(__FILE__, __LINE__);
	    }
	    foreach ($activeRules as $moduleId => $rules) {
		for ($i = 0; $i < count($rules); $i++) {
		    if (!empty($rules[$i]['match'])) {
			$activeRules[$moduleId][$i]['match'] =
			    str_replace(':', '.', $rules[$i]['match']);
		    }
		}
	    }
	    $ret = RewriteMap::setActiveRules($activeRules);
	    if ($ret->isError()) {
		return $ret->wrap(__FILE__, __LINE__);
	    }

	case '0.9.6':
	case '0.9.7':
	case '0.9.8':
	case '0.9.9':
	case '0.9.10':
	    foreach (array('needOptions') as $key) {
		$ret = $module->removeParameter($key);
		if ($ret->isError()) {
		    return $ret->wrap(__FILE__, __LINE__);
		}
	    }

	    foreach (array('status' => serialize(array()))
		     as $key => $value) {
		$ret = $module->setParameter($key, $value);
		if ($ret->isError()) {
		    return $ret->wrap(__FILE__, __LINE__);
		}
	    }

	case '0.9.11':
	case '0.9.12':
	case '0.9.13':
	case '0.9.14':
	case '0.9.15':
	case '0.9.16':
	case '0.9.17':
	case '0.9.18':

	case 'end of upgrade path':
	    break;

	default:
	    return GalleryStatus::error(ERROR_BAD_PLUGIN, __FILE__, __LINE__,
					sprintf('Unknown module version %s', $currentVersion));
	}

	return GalleryStatus::success();
    }

    /**
     * @see GalleryModule::handleEvent
     */
    function handleDeactivatePlugin($module, $pluginId) {
	list ($ret, $activeRules) = RewriteMap::getActiveRules();
	if ($ret->isError()) {
	    return $ret->wrap(__FILE__, __LINE__);
	}

	/* Don't update the rules if not necissary */
	if (isset($activeRules[$pluginId])) {
	    /* Save the old patterns for later use if module gets reactivated */
	    $i = 0;
	    foreach (array_keys($activeRules[$pluginId]) as $ruleId) {
		$ret = $module->setParameter('history.' . $pluginId . '.' . $i++,
		    serialize(array(
			'pattern' => $activeRules[$pluginId][$ruleId]['pattern'],
			'ruleId' => $ruleId)));
	        if ($ret->isError()) {
		    return $ret->wrap(__FILE__, __LINE__);
	        }
	    }
	    unset($activeRules[$pluginId]);

	    list($ret, $code, $rewriteRules) =
		RewriteHelper::parseActiveRules($activeRules);
	    if ($ret->isError()) {
		return $ret->wrap(__FILE__, __LINE__);
	    }
	    /* We're just removing so anything but status OK would be an unknown error */
	    if ($code != REWRITE_STATUS_OK) {
		return GalleryStatus::error(ERROR_UNKNOWN, __FILE__, __LINE__);
	    }

	    /*
	     * Ignore the return code. No big deal if we fail to write the new file,
	     * atleast we won't generate the short URLs as long as the DB is updated.
	     */
	    list($ret, $code) = RewriteHelper::writeFile($rewriteRules);
	    if ($ret->isError()) {
		return $ret->wrap(__FILE__, __LINE__);
	    }
	    if (GalleryUtilities::isEmbedded()) {
		list($ret, $code) = RewriteHelper::writeFile($rewriteRules, true);
		if ($ret->isError()) {
		    return $ret->wrap(__FILE__, __LINE__);
		}
	    }

	    $ret = RewriteMap::setActiveRules($activeRules);
    	    if ($ret->isError()) {
		return $ret->wrap(__FILE__, __LINE__);
	    }
	}

	return GalleryStatus::success();
    }

    /**
     * @see GalleryModule::handleEvent
     */
    function handleActivatePlugin($module, $pluginId) {
	/* Get saved patterns */
	list ($ret, $plugin) = GalleryCoreApi::loadPlugin('module', $pluginId);
	if ($ret->isError()) {
	    return $ret->wrap(__FILE__, __LINE__);
	}
	$rules = $plugin->getRewriteRules();

	list ($ret, $params) = $module->fetchParameters();
	if ($ret->isError()) {
	    return $ret->wrap(__FILE__, __LINE__);
	}

	/* Build the dupe-list and get current rules active */
	if (isset($params['history.' . $pluginId . '.0'])) {
	    list ($ret, $activeRules) = RewriteMap::getActiveRules();
	    if ($ret->isError()) {
		return $ret->wrap(__FILE__, __LINE__);
	    }

	    $dupe = array();
	    foreach (array_keys($activeRules) as $moduleId) {
		foreach (array_keys($activeRules[$moduleId]) as $ruleId) {
		    $dupe[$activeRules[$moduleId][$ruleId]['pattern']] = 1;
		}
	    }
	}

	for ($i = 0; isset($params['history.' . $pluginId . '.' . $i]); $i++) {
	    $ret = $module->removeParameter('history.' . $pluginId . '.' . $i);
	    if ($ret->isError()) {
		return $ret->wrap(__FILE__, __LINE__);
	    }

	    $history = unserialize($params['history.' . $pluginId . '.' . $i]);
	    if (isset($rules[$history['ruleId']]) && !isset($dupe[$history['pattern']])) {
		$activeRules[$pluginId][$history['ruleId']]['pattern'] =
		    $history['pattern'];

		if (isset($rules[$history['ruleId']]['match'])) {
		    $activeRules[$pluginId][$history['ruleId']]['match'] =
			join('|', $rules[$history['ruleId']]['match']);
		}
	    }
	}

	/* If we added new rules - update active rules and the .htaccess file. */
	if (isset($activeRules[$pluginId])) {
	    list($ret, $code, $rewriteRules) = RewriteHelper::parseActiveRules($activeRules);
	    if ($ret->isError()) {
		return $ret->wrap(__FILE__, __LINE__);
	    }
	    if ($code != REWRITE_STATUS_OK) {
		return GalleryStatus::success();
	    }

	    list($ret, $code) = RewriteHelper::writeFile($rewriteRules);
	    if ($ret->isError()) {
		return $ret->wrap(__FILE__, __LINE__);
	    }
	    if ($code != REWRITE_STATUS_OK) {
		return GalleryStatus::success();
	    }
	    if (GalleryUtilities::isEmbedded()) {
		list($ret, $code) = RewriteHelper::writeFile($rewriteRules, true);
		if ($ret->isError()) {
		    return $ret->wrap(__FILE__, __LINE__);
		}
		if ($code != REWRITE_STATUS_OK) {
		    return GalleryStatus::success();
		}
	    }

	    $ret = RewriteMap::setActiveRules($activeRules);
	    if ($ret->isError()) {
		return $ret->wrap(__FILE__, __LINE__);
	    }
	}

	return GalleryStatus::success();
    }

    /**
     * @see GalleryModule::handleEvent
     */
    function handleUninstallPlugin($module, $pluginId) {
	list ($ret, $params) = $module->fetchParameters();
	if ($ret->isError()) {
	    return $ret->wrap(__FILE__, __LINE__);
	}

	for ($i = 0; isset($params['history.' . $pluginId . '.' . $i]); $i++) {
	    $ret = $module->removeParameter('history.' . $pluginId . '.' . $i);
	    if ($ret->isError()) {
		return $ret->wrap(__FILE__, __LINE__);
	    }
	}

	return GalleryStatus::success();
    }
}
?>

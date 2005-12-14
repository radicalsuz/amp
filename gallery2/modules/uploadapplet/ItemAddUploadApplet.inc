<?php
/*
 * $RCSfile: ItemAddUploadApplet.inc,v $
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
 * @version $Revision: 1.17 $ $Date: 2005/08/23 03:49:56 $
 * @package uploadapplet
 * @author Pierre-Luc Paour <gallery@paour.com>
 */

class ItemAddUploadApplet extends ItemAddPlugin {

    /**
     * @see ItemAdd:loadTemplate
     */
    function loadTemplate(&$template, &$form, $item) {
	global $gallery;

	list ($ret, $module) = GalleryCoreApi::loadPlugin('module', 'uploadapplet');
	if ($ret->isError()) {
	    return array($ret->wrap(__FILE__, __LINE__), null, null);
	}

	list ($ret, $modules) = GalleryCoreApi::fetchPluginStatus('module');
	if ($ret->isError()) {
	    return array($ret->wrap(__FILE__, __LINE__), null, null);
	}

	$ItemAddUploadApplet = array();
	if (!isset($modules['remote']['active']) || !$modules['remote']['active']) {
	    $ItemAddUploadApplet['NoProtocolError'] = 1;
	} else {
	    $session =& $gallery->getSession();
	    $urlGenerator =& $gallery->getUrlGenerator();
	    /*
	     * Force the use of the G2 base for the url and the cookie path as the applet will
	     * communicate to G2 directly, even when G2 is embedded
	     */
	    $ItemAddUploadApplet['g2BaseUrl'] = $urlGenerator->getCurrentUrlDir(true);
	    list ($ret, $cookiePath) = $urlGenerator->getCookiePath(true);
	    if ($ret->isError()) {
		return array($ret->wrap(__FILE__, __LINE__), null, null);
	    }
	    $ItemAddUploadApplet['NoProtocolError'] = 0;
	    $ItemAddUploadApplet['cookieName'] = $session->getKey();
	    $ItemAddUploadApplet['cookieValue'] = $session->getId();
	    $ItemAddUploadApplet['cookieDomain'] = $session->getCookieDomain();
	    $ItemAddUploadApplet['cookiePath'] = $cookiePath;
	    $ItemAddUploadApplet['album'] = $item->getId();
	    $ItemAddUploadApplet['defaults'] = array('htmlEscapeCaptions' => 'false');
	    $ItemAddUploadApplet['overrides'] = array('toSysOut' => 'true');
	    $ItemAddUploadApplet['userAgent'] = GalleryUtilities::getServerVar('HTTP_USER_AGENT');
	    $ItemAddUploadApplet['galleryVersion'] = 2;
	    $ItemAddUploadApplet['code'] = 'com.gallery.GalleryRemote.ItemAddUploadAppletMini';
	}

	$template->setVariable('ItemAddUploadApplet', $ItemAddUploadApplet);
	return array(GalleryStatus::success(),
		     'modules/uploadapplet/templates/ItemAddUploadApplet.tpl',
		     'modules_uploadapplet');
    }

    /**
     * @see ItemAddPlugin::getTitle
     */
    function getTitle() {
	list ($ret, $module) = GalleryCoreApi::loadPlugin('module', 'uploadapplet');
	if ($ret->isError()) {
	    return array($ret->wrap(__FILE__, __LINE__), null);
	}

	return array(GalleryStatus::success(), $module->translate('Upload Applet'));
    }
}
?>

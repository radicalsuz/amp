<?php
/*
 * $RCSfile: SlideshowApplet.inc,v $
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
 * @version $Revision: 1.7 $ $Date: 2005/08/26 05:15:14 $
 * @package uploadapplet
 * @author Pierre-Luc Paour <gallery@paour.com>
 */

class SlideshowAppletView extends GalleryView {

    /**
     * @see GalleryView::loadTemplate()
     */
    function loadTemplate(&$template, &$form) {
	global $gallery;

	list ($ret, $module) = GalleryCoreApi::loadPlugin('module', 'slideshowapplet');
	if ($ret->isError()) {
	    return array($ret->wrap(__FILE__, __LINE__), null);
	}

	list ($ret, $modules) = GalleryCoreApi::fetchPluginStatus('module');
	if ($ret->isError()) {
	    return array($ret->wrap(__FILE__, __LINE__), null);
	}

	$startId = null;

	/* Build slideshow for an album.. */
	list ($ret, $item) = $this->_getItem();
	if ($ret->isError()) {
	    return array($ret->wrap(__FILE__, __LINE__), null);
	}

	if (!$item->getCanContainChildren()) {
	    /* Photo: do slideshow of parent album, start at this photo */
	    $startId = $item->getId();
	    list ($ret, $item) = GalleryCoreApi::loadEntitiesById($item->getParentId());
	    if ($ret->isError()) {
		return array($ret->wrap(__FILE__, __LINE__), null);
	    }
	}

	$SlideshowApplet = array();
	$SlideshowApplet['extra'] = array();

	if (!isset($modules['remote']['active']) || !$modules['remote']['active']) {
	    $SlideshowApplet['NoProtocolError'] = 1;
	} else {
	    $urlGenerator =& $gallery->getUrlGenerator();
	    $session =& $gallery->getSession();
	    /*
	     * Force the use of the G2 base for the url and the cookie path as the applet will
	     * communicate to G2 directly, even when G2 is embedded
	     */
	    $SlideshowApplet['g2BaseUrl'] = $urlGenerator->getCurrentUrlDir(true);
	    list ($ret, $cookiePath) = $urlGenerator->getCookiePath(true);
	    if ($ret->isError()) {
		return array($ret->wrap(__FILE__, __LINE__), null);
	    }
	    $SlideshowApplet['NoProtocolError'] = 0;
	    $SlideshowApplet['cookieName'] = $session->getKey();
	    $SlideshowApplet['cookieValue'] = $session->getId();
	    $SlideshowApplet['cookieDomain'] = $session->getCookieDomain();
	    $SlideshowApplet['cookiePath'] = $cookiePath;
	    $SlideshowApplet['album'] = $item->getId();
	    //$SlideshowApplet['defaults'] = array();
	    $SlideshowApplet['overrides'] = array('toSysOut' => 'true');
	    $SlideshowApplet['userAgent'] = GalleryUtilities::getServerVar('HTTP_USER_AGENT');
	    $SlideshowApplet['galleryVersion'] = 2;
	    $SlideshowApplet['code'] = 'com.gallery.GalleryRemote.GRAppletSlideshow';

	    if (isset($startId)) {
	    	/* format the start_from as album_picture, that's what GR expects */
	    	$SlideshowApplet['extra']['gr_slideshow_start_from'] = $item->getId().'_'.$startId;
	    }

	    list ($ret, $params) = GalleryCoreApi::fetchAllPluginParameters('module', 'slideshowapplet');
	    if ($ret->isError()) {
		return array($ret->wrap(__FILE__, __LINE__), null);
	    }
	    $SlideshowApplet['defaults'] = $params;
	}

	$form['formName'] = '';
	$template->setVariable('controller', '');
	$template->setVariable('SlideshowApplet', $SlideshowApplet);
	return array(GalleryStatus::success(),
		     array('body' => 'modules/slideshowapplet/templates/SlideshowApplet.tpl'));
    }

    /**
     * @see GalleryView::getViewDescription()
     */
    function getViewDescription() {
	list ($ret, $module) = GalleryCoreApi::loadPlugin('module', 'slideshowapplet');
	if ($ret->isError()) {
	    return array($ret->wrap(__FILE__, __LINE__), null);
	}

	return array(GalleryStatus::success(), $module->translate('slideshow (fullscreen)'));
    }
}
?>

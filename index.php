<?php
/**
 *  index.php
 *
 *  Display Page
 *
 *  @author Austin Putman <austin@radicaldesigns.org>
 *  @version AMP 3.5.3
 *  @copyright Radical Designs 2005, released under GPL 2+
 *  @package AMP::Content
 */

/*******************************************
    Activist Mobilization Platform (AMP)
    Copyright (C) 2000-2005  David Taylor & Radical Designs

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

    For further information, contact Radical Designs at info@radicaldesigns.org

*******************************************/


$mod_id = 2 ;
include("AMP/BaseDB.php");

/*
if (AMP_SITE_MEMCACHE_ON) {
    require_once( "AMP/Content/Page/Cached.inc.php" );
    $cached_page = &new AMPContent_Page_Cached();
    if ($cached_page->execute()) exit;
}
*/
if ( $cached_output = AMP_cached_request( AMP_SYSTEM_CACHE_TIMEOUT_FRONTPAGE )) {
    print $cached_output;
    exit;
}
require_once ("AMP/BaseTemplate.php");
if ( 'index.php' != AMP_CONTENT_URL_FRONTPAGE ) ampredirect( AMP_CONTENT_URL_FRONTPAGE );

$currentPage = &AMPContent_Page::instance();
$currentPage->setListType( AMP_CONTENT_LISTTYPE_FRONTPAGE );

require_once( 'AMP/Content/Class.inc.php');
$currentClass = &new ContentClass( AMP_Registry::getDbcon( ), AMP_CONTENT_CLASS_FRONTPAGE );
$display = &$currentClass->getDisplay( );
$currentPage->contentManager->addDisplay( $display );
require_once( 'AMP/BaseFooter.php' );

?>

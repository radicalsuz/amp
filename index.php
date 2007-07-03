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


$intro_id = 2 ;
require_once("AMP/BaseDB.php");

//if the frontpage timeout is actually set it will have a different value from the CACHE_TIMEOUT
//otherwise no one cares about this case
if ( 
    //everything is normal
     ( ( $cached_output = AMP_cached_request( ))
         && ( AMP_SYSTEM_CACHE_TIMEOUT_FRONTPAGE == AMP_SYSTEM_CACHE_TIMEOUT ))
     || 
     //stuff is weird but the timestamp is cool
     ( $cached_output 
        && ( $cached_frontpage_stamp = AMP_cache_get( AMP_CACHE_TOKEN_URL_CONTENT.'_TIMESTAMP_FRONTPAGE'))
        && ( AMP_SYSTEM_CACHE_TIMEOUT_FRONTPAGE >= ( time( ) - $cached_frontpage_stamp ))
     )
   ) {
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

AMP_cache_set( AMP_CACHE_TOKEN_URL_CONTENT .'_TIMESTAMP_FRONTPAGE', time( ));
require_once( 'AMP/BaseFooter.php' );

?>

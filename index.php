<?php

/*******************************************
    Activist Mobilization Platform (AMP)
    Copyright (C) 2000-2005  David Taylor & Radical Designs

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
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
if (!defined( 'AMP_USE_OLD_CONTENT_ENGINE' )) define ('AMP_USE_OLD_CONTENT_ENGINE', false );

if (AMP_USE_OLD_CONTENT_ENGINE) {
    require_once( 'index2.php' );

} else {
    if (AMP_SITE_MEMCACHE_ON) {
        require_once( "AMP/Content/Page/Cached.inc.php" );
        $cached_page = &new AMPContent_Page_Cached();
        $cached_page->execute();
    }
    require_once ("AMP/BaseTemplate.php");
    require_once ("AMP/Content/Class/Display_FrontPage.inc.php");
    $currentPage = &AMPContent_Page::instance();
    $currentPage->setListType( AMP_CONTENT_LISTTYPE_FRONTPAGE );

    $display = &new ContentClass_Display_FrontPage( $dbcon ); 
    $currentPage->contentManager->addDisplay( $display );
    require_once( 'AMP/BaseFooter.php' );
}

?>

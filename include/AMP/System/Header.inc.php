<?php
if ( !defined( 'AMP_TEXT_ADMINISTRATION')) define ( 'AMP_TEXT_ADMINISTRATION', 'Administration');
require_once( 'AMP/Content/Header.inc.php');

/**
 * AMPSystem_Header 
 * 
 * @uses AMPContent_Header
 * @package 
 * @version 3.5.8
 * @copyright 2006 Radical Designs
 * @author Austin Putman <austin@radicaldesigns.org> 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
class AMPSystem_Header extends AMPContent_Header {

    var $javaScripts = array(
        'system_header' => 'scripts/system_header.js',
        'calendar'      => 'system/Connections/popcalendar.js',
        'functions'     => 'scripts/functions.js', 
        'prototype'     => 'scripts/ajax/prototype.js',
        'scriptaculous' => 'scripts/ajax/scriptaculous.js' );

    var $styleSheets = array( 'default'     =>  'system/system_interface.css' );
    var $_path_favicon = '/system/images/amp_admin.ico';
    
    function AMPSystem_Header( ){
        
    }

    function &instance( ){
        static $system_header = false;
        if ( !$system_header ) $system_header = new AMPSystem_Header( );
        return $system_header;
    }

    function getPageTitle( ){
        $pageTitle = array( AMP_SITE_NAME . ' ' . AMP_TEXT_ADMINISTRATION );
        // someday we should be able to put current admin-side location here
        
        $this->_pageTitle = join( $this->_title_separator, $pageTitle ); 
        return $this->_pageTitle;
    }

    function _HTML_feed( ){
        // RSS feeds not supported for admin users at this time.
        return false;
    }

    function _HTML_header () {
        return  $this->_HTML_linkRels() . 
                $this->_HTML_pageTitle() . 
                $this->_HTML_javaScripts() .
                $this->_HTML_extra();
    }

    function _getFaviconPath( ){
        if ( file_exists( AMP_BASE_PATH . $this->_path_favicon ) ) return $this->_path_favicon; 
        return false;
    }


}
?>

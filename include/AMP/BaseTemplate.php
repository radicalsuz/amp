<?php
/**
 * Initializes the template for the current page
 *
 * @package Content
 * @since 3.5.3
 * @access public
 * @author Austin Putman <austin@radicaldesigns.org>
 * @copyright 2005 Radical Designs
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

require_once( 'AMP/BaseDB.php' );
require_once( 'AMP/Content/Map.inc.php' );
require_once( 'AMP/Content/Page.inc.php' );

/**
 * Filelink is used by link navs
 * TODO remove this when built-in navs are upgraded
 */
if (isset($_GET['filelink']) && $_GET['filelink']) {
    ampredirect ($_GET['filelink']);

}

/* TODO: check the cache for the current page, once all front-end pages support caching */
   
/**
 * Initialize the current Page object  
 *
 * When the page is initialized, it is given a context within the current site hierarchy.  This
 * tells it which template to use, which navs to display, and what kind of metadata is needed in the header.
 *
 * @var AMPContent_Page
 */

$currentPage = & AMPContent_Page::instance();

/**
 *  If an intro_id or mod_id value is set to a non-default value , the page is not a content page 
 */
if ((isset($mod_id) && $mod_id) && (! (isset($intro_id) && $intro_id))) $intro_id = $mod_id; 
if (! ( isset( $intro_id ) && $intro_id )) $intro_id = AMP_CONTENT_INTRO_ID_DEFAULT;

if ( $intro_id == AMP_CONTENT_INTRO_ID_DEFAULT ) {

    /**
     * Initialize Content System Pages  
     */
    if ( isset( $_GET['id']   ) && $_GET['id']   ) $currentPage->setArticle( $_GET['id'] );
    if ( isset( $_GET['class']) && $_GET['class']) $currentPage->setClass(   $_GET['class'] );
    if ( isset( $_GET['type'] ) && $_GET['type'] ) $currentPage->setSection( $_GET['type'] );
    if ( isset( $_GET['list'] ) && $_GET['list'] ) $currentPage->setListType($_GET['list'] );
    if ( isset( $_GET['region'])&& $_GET['region'])$currentPage->setRegion(  $_GET['region'] ); 

    /**
     * Translate readable dates 
     */
    $date_values = AMP_date_from_url( );
    foreach( $date_values as $token => $value ) {
        $_GET['date'][$token] = $value;
    }
     
    /**
     * Activate Preview Mode if requested
     * TODO: Preview mode should only be available to authenticated admin users
     */
    define( 'AMP_DISPLAYMODE_PREVIEW', ( isset( $_GET['preview'] ) && $_GET['preview'] ) );

} else {

    /**
     * Initialize all other kinds of pages 
     */
    $currentPage->setIntroText( $intro_id );

} 

$currentPage->initLocation();

/**
 * Retrieve tools settings from the modules_control table  
 */
if (isset($modid) && $modid ) {
    require_once( 'AMP/System/Tool/Control/Set.inc.php' );
    $controls = &new ToolControlSet( AMP_Registry::getDbcon( ), $modid );
    $controls->globalizeSettings();
}

/**
 * Start Output Buffering 
 */
ob_start();
?>

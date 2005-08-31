<?php
require_once( 'AMP/BaseDB.php' );

if (!defined( 'AMP_USE_OLD_CONTENT_ENGINE' )) define ('AMP_USE_OLD_CONTENT_ENGINE', false );

if ( AMP_USE_OLD_CONTENT_ENGINE ) {
    require_once( 'AMP/BaseTemplate2.php' );
} else {

    require_once('AMP/Content/Map.inc.php');
    require_once('AMP/System/Tool/Controls.inc.php');
    require_once('AMP/Content/Page.inc.php' );

    //this seems not to be used anywhere - leaving it active for 3.5.3
    //but this should be removed by 3.5.5 unless we know who uses it
    if (isset($_GET['filelink']) && $_GET["filelink"]) ampredirect ($_GET["filelink"]);

    /*
        // check the cache for the current page
        // this is not in use until all front-end pages support caching
        if (!( AMP_SITE_MEMCACHE_ON && $cache_output = &AMP_getCachedPageItem( MEMCACHE_KEY_PAGEHTML ) )) {
        define ('AMP_DISPLAYMODE_USECACHED', false );
    */
       
    ### create Content Map ###
    if ( !isset($MX_top) ) $MX_top = AMP_CONTENT_MAP_ROOT_SECTION;
    $content_map = & AMPContent_Map_instance( $MX_top );

    $currentPage = & AMPContent_Page::instance();

### FIND CURRENT LOCATION ##

    //confirm intro_id values
    if ((isset($mod_id) && $mod_id) && (! (isset($intro_id) && $intro_id))) { $intro_id = $mod_id; }
    if (! (isset($intro_id) && $intro_id)) { $intro_id = AMP_CONTENT_INTRO_ID_DEFAULT; }


    if ( $intro_id == AMP_CONTENT_INTRO_ID_DEFAULT ) {

### CONTENT SYSTEM PAGES  ###
        if ( isset( $_GET['id']   ) && $_GET['id']   ) $currentPage->setArticle( $_GET['id'] );
        if ( isset( $_GET['class']) && $_GET['class']) $currentPage->setClass(   $_GET['class'] );
        if ( isset( $_GET['type'] ) && $_GET['type'] ) $currentPage->setSection( $_GET['type'] );
        if ( isset( $_GET['list'] ) && $_GET['list'] ) $currentPage->setListType($_GET['list'] );
        if ( isset( $_GET['region'])&& $_GET['region'])$currentPage->setRegion(  $_GET['region'] ); 

    } else {

        ### TOOL OR FORM PAGES ####
        $currentPage->setIntroText( $intro_id );

    } 

    $currentPage->initSection();

    ### Check if section requires user login ###
    if ($currentPage->requiresLogin() ) require ( "AMP/Auth/UserRequire.inc.php" );

    $currentPage->initTemplate();

    #SET MODULE SPECIFIC VARS
    if (isset($modid) && $modid ) {
        $controls = &new AMPSystem_Tool_Controls( $dbcon, $modid );
        $controls->globalizeSettings();
    }

    // Activate Preview Mode
    define( 'AMP_DISPLAYMODE_PREVIEW', ( isset( $_GET['preview'] ) && $_GET['preview'] ) );

    # Start Output Buffering
    ob_start();
}
?>

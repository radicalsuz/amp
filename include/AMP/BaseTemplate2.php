<?php
require_once('AMP/Content/Map.inc.php');
require_once('AMP/System/Tool/Controls.inc.php');
require_once('AMP/Content/Page.inc.php' );

if (!defined( 'AMP_CONTENT_MAP_ROOT_SECTION' )) define( 'AMP_CONTENT_MAP_ROOT_SECTION' , 1 );
if (!defined( 'AMP_CONTENT_INTRO_ID_DEFAULT' )) define( 'AMP_CONTENT_INTRO_ID_DEFAULT' , 1 );

$registry =& AMP_Registry::instance();

### create Content Map ###
if ( !isset($MX_top) ) $MX_top = AMP_CONTENT_MAP_ROOT_SECTION;
$content_map = & AMPContent_Map::instance();
$content_map->init( $dbcon, $MX_top );

$currentPage = & AMPContent_Page::instance();

### FIND CURRENT LOCATION ##

//confirm intro_id values
if (! (isset($intro_id) && $intro_id)) { $intro_id = $mod_id; }
if (! (isset($intro_id) && $intro_id)) { $intro_id = AMP_CONTENT_INTRO_ID_DEFAULT; }
	
if ( $intro_id == AMP_CONTENT_INTRO_ID_DEFAULT ) {
    
    ### CONTENT SYSTEM PAGES  ###
    if ( isset( $_GET['id']   ) && $_GET['id']   ) $currentPage->setArticle( $_GET['id'] );
    if ( isset( $_GET['type'] ) && $_GET['type'] ) $currentPage->setSection( $_GET['type'] );
    if ( isset( $_GET['class']) && $_GET['class']) $currentPage->setClass(   $_GET['class'] );
    if ( isset( $_GET['list'] ) && $_GET['list'] ) $currentPage->setListType($_GET['list'] );

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

# Start Output Buffering
ob_start();

?>

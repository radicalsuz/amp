<?php

/*****
 *
 * Groups Listing Page
 * 
 * Allows search for records 
 *
 * 
 *
 *****/
$modid=5;
if (!defined( 'AMP_FORM_ID_GROUPS' )) define( 'AMP_FORM_ID_GROUPS', 2 );
require_once( 'AMP/BaseDB.php' );
require_once('AMP/UserData/Set.inc.php');
require_once( 'Modules/Groups/Display/Config.inc.php');

$intro_id = defined( 'AMP_CONTENT_PUBLICPAGE_ID_GROUPS_DISPLAY') ? AMP_CONTENT_PUBLICPAGE_ID_GROUPS_DISPLAY : AMP_CONTENT_INTROTEXT_ID_GROUPS;

$modin=AMP_FORM_ID_GROUPS;
if (isset($_REQUEST['modin']) && $_REQUEST['modin']) {
    $modin= intval( $_REQUEST['modin'] );
}

$admin=false;
$userlist=&new UserDataSet($dbcon, $modin, $admin);

$sub = isset($_REQUEST['btnUDMSubmit']);
$uid= isset($_REQUEST['uid']) ? intval( $_REQUEST['uid'] ):false;
if( isset($_REQUEST['gid']) && $_REQUEST['gid'] ) {
    $uid = intval( $_REQUEST['gid'] );
}

if (isset($modid) && $modid ) {
    require_once( 'AMP/System/Tool/Control/Set.inc.php' );
    $controls = &new ToolControlSet( $dbcon, $modid );
    $controls->globalizeSettings();
}

if ( isset( $gdisplay )) AMP_legacy_groups_get_display( $gdisplay );

if ($uid && $modin) {

    if( is_array( $list_options )) {
        $list_options['detail_format'] = 'groups_detail_display';
    } else {
        $list_options = array( );
    }
    $userlist->uid = $uid;
    $output= $userlist->output('DisplayHTML', $list_options); 

} else { 

    if( is_array( $list_options )) {
        $list_options['display_format'] = 'groups_layout_display';
    } else {
        $list_options = array( );
    }
    #$userlist->registerPlugin("Output", "Index");
    if (is_array($sort_options)) {
        $sort = $userlist->getPlugins("Sort");
        $sort_plugin = current($sort);
        $sort_plugin->setOptions($sort_options);
    }

    //require searching to be possible
    $search = $userlist->getPlugins('Search');
    if (!$search) $userlist->registerPlugin('AMP', 'Search');
    $searchform = $userlist->getPlugins('SearchForm');
    if (!$searchform) $userlist->registerPlugin('Output', 'SearchForm');

    //display result list
    $order = null;
    $output=$userlist->output_list('DisplayHTML', $list_options, $order, $srch_options); 
}

$intro_id = $userlist->getIntrotextId( );
require_once( 'AMP/BaseTemplate.php' );
if ($intro_id != 1) require_once( 'AMP/BaseModuleIntro.php' );

print $output;

        
// Append the footer and clean up.
require_once( 'AMP/BaseFooter.php' );

?>

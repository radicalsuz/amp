<?php

/*****
 *
 * UserData Listing Page
 * 
 * Allows search for records 
 *
 * 
 *
 *****/
$modid=1;
require_once( 'AMP/BaseDB.php' );

/**
 *  Check for a cached page
 */
if (AMP_SITE_MEMCACHE_ON) {
    require_once( "AMP/Content/Page/Cached.inc.php" );
    $cached_page = &new AMPContent_Page_Cached();
    if ($cached_page->execute()) exit;
}

require_once('AMP/UserData/Set.inc.php');

//bounce to the index if modin isn't set
if (isset($_REQUEST['modin']) && $_REQUEST['modin']) {
    $modin=$_REQUEST['modin'];
} else {
    header ("Location: index.php");
}

#$intro_id=1;
$admin=false;

$userlist=&new UserDataSet($dbcon, $modin, $admin);

//Check if publishing of data has been authorized

if (!$userlist->_module_def['publish']) {
    header ("Location: index.php");
}

$sub = isset($_REQUEST['btnUDMSubmit']);
$uid= isset($_REQUEST['uid'])?$_REQUEST['uid']:false;

if ($uid && $modin) {

    $userlist->uid = $uid;
    $output= $userlist->output('DisplayHTML'); 

} else { 

    //display result list
    if (!$userlist->getPlugins('SearchForm')) $srch_options['criteria']=array('value'=>array("modin=".$modin));

    #$userlist->doAction('Search', $srch_options);
    $output = $userlist->output_list();
                
}

$intro_id = $userlist->modTemplateID;

require_once( 'AMP/BaseTemplate.php' );
if ($intro_id != 1) require_once( 'AMP/BaseModuleIntro.php' );

print $output;

        
// Append the footer and clean up.
require_once( 'AMP/BaseFooter.php' );

?>

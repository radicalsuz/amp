<?php

/*****
 *
 * UserData Listing Page
 * 
 * Allows search for records admin side
 *
 * 
 *
 *****/
$modid=1;
require_once( 'AMP/BaseDB.php' );
require_once('AMP/UserData/Set.inc.php');

if (isset($_REQUEST['modin']) && $_REQUEST['modin']) {
    $modin=$_REQUEST['modin'];
} else {
    header ("Location: modinput4_list.php");
}

$modidselect=$dbcon->CacheExecute("SELECT id, perid from modules where publish=1 and userdatamodid=" . $modin) or DIE($dbcon->ErrorMsg());
$modid=$modidselect->Fields("id");
$modin_permission=$modidselect->Fields("perid");

/*
if (!$userper[$modin_permission]) {

    print "<script type=\"text/javascript\">
        alert (\"You don't have permission to view this list\");
        window.location.url=\"index.php\";
        </script>";
}
$admin=($userper[54]&&$userper[$modin_permission]);
*/
$admin=true;
$userlist=&new UserDataSet($dbcon, $modin, $admin);
$searchform=&$userlist->registerPlugin('Output', 'SearchForm');
$pager=&$userlist->registerPlugin('Output', 'Pager');
$userlist->registerPlugin('AMP', 'Search');
$userlist->registerPlugin('Output', 'TableHTML');
$userlist->registerPlugin('AMP', 'Sort');
$actionbar=&$userlist->registerPlugin('Output', 'Actions');

$sub = isset($_REQUEST['btnUDMSubmit']);
$uid= isset($_REQUEST['uid'])?$uid:false;

if ($uid && $modin) {

    $list_options['_userid']= array('value'=> $_REQUEST['_userid']);
    $output= $userlist->output('DisplayHTML', $list_options); 

} else { 

    //display result list
    /*
    $searchform=&$userlist->getPlugin('Output', 'SearchForm');
    $pager=&$userlist->getPlugin('Output','Pager');
    $actionbar=&$userlist->getPlugin('Output','Actions');
    */    
    if ($userlist->doAction('Search')) {
        $output= (isset($userlist->error)? $userlist->error.'<BR>':"").
                ($searchform?   $searchform->search_text_header()
                                .$userlist->output('SearchForm'):"").
                ($pager?$pager->execute():"").
                ($actionbar?$actionbar->execute():"").
                $userlist->output('TableHTML').
                ($pager?$pager->execute():"").
                $userlist->output('Index');
    } else {
        $output=$userlist->error.'<BR>'.$userlist->output('SearchForm');
    }
}

require_once( 'header.php' );

print $output;

        
// Append the footer and clean up.
require_once( 'footer.php' );

?>
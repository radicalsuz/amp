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
require_once( 'header.php' );

if (isset($_REQUEST['modin']) && $_REQUEST['modin']) {
    $modin=$_REQUEST['modin'];
} else {
    header ("Location: modinput4_list.php");
}

$sub = isset($_REQUEST['btnUDMSubmit']);
$uid= isset($_REQUEST['uid'])?$uid:false;
if ($uid && $modin) {
    $list_options['_userid']= array('value'=> $_REQUEST['_userid']);
    $userlist=new UserDataSet($dbcon, $modin, $admin=true);
    $output= $userlist->output('DisplayHTML', $list_options); 

} else { 
    //display result list
    $admin=true;
    $userlist=&new UserDataSet($dbcon, $modin, $admin);
    $searchform=&$userlist->getPlugin('Output', 'SearchForm');
    $pager=&$userlist->getPlugin('Output','Pager');
    $actionbar=&$userlist->getPlugin('Output','Actions');
    
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
print $output;

        
// Append the footer and clean up.
require_once( 'footer.php' );

?>

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
require_once('AMP/UserData/Set.inc.php');

if (isset($_REQUEST['modin']) && $_REQUEST['modin']) {
    $modin=$_REQUEST['modin'];
} else {
    header ("Location: index.php");
}


$admin=false;
$userlist=&new UserDataSet($dbcon, $modin, $admin);
if ($userlist->_module_def['publish']) {
    #$searchform=&$userlist->getPlugin('Output', 'SearchForm');
    $searchform=&$userlist->registerPlugin('Output', 'SearchFormLum');
    $pager=&$userlist->registerPlugin('Output', 'Pager');
    $userlist->registerPlugin('AMP', 'Search');
    if ($display=&$userlist->getPlugin('Output', 'DisplayHTML')) {
        $mod_id=$display->options['header_text'];
    } else {
       $display=&$userlist->registerPlugin('Output', 'DisplayHTML');
       $mod_id=1;
    }
    $userlist->registerPlugin('AMP', 'Sort');
} else {
    header ("Location: index.php");
}

$sub = isset($_REQUEST['btnUDMSubmit']);
$uid= isset($_REQUEST['uid'])?$uid:false;

if ($uid && $modin) {

    $list_options['_userid']= $_REQUEST['_userid'];
    $output= $userlist->output('DisplayHTML', $list_options); 

} else { 

    //display result list
    /*
    $searchform=&$userlist->getPlugin('Output', 'SearchForm');
    $pager=&$userlist->getPlugin('Output','Pager');
    $actionbar=&$userlist->getPlugin('Output','Actions');
    */    
    $list_options['display_format']='user_photo_layout';
    if (!isset($searchform)||$searchform==false) $srch_options['criteria']=array('value'=>array("modin=".$modin));

    if ($userlist->doAction('Search', $srch_options)) {
        $output= (isset($userlist->error)? $userlist->error.'<BR>':"").
                ($searchform?   $searchform->search_text_header()
                                .$searchform->execute():"").
                ($pager?$pager->execute():"").
                ($actionbar?$actionbar->execute():"").
                $userlist->output('DisplayHTML', $list_options);#.
                #($pager?$pager->execute():"").
                #$userlist->output('Index');
    } else {
        $output=join('<BR>',$userlist->errors).'<P>'.($searchform?$searchform->execute():"");
    }
}

require_once( 'AMP/BaseTemplate.php' );

print $output;

        
// Append the footer and clean up.
require_once( 'AMP/BaseFooter.php' );

?>

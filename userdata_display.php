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
    #$searchform=&$userlist->registerPlugin('Output', 'SearchForm');
    $pager=&$userlist->registerPlugin('Output', 'Pager');
    $userlist->registerPlugin('AMP', 'Search');
    if ($display=&$userlist->getPlugin('Output', 'DisplayHTML')) {
        
        $headr=$display->getOptions();
        $intro_id=$headr['header_text'];
    } else {
       $display=&$userlist->registerPlugin('Output', 'DisplayHTML');
       $mod_id=1;
    }
    $userlist->registerPlugin('AMP', 'Sort');
} else {
    header ("Location: index.php");
}

$sub = isset($_REQUEST['btnUDMSubmit']);
$uid= isset($_REQUEST['uid'])?$_REQUEST['uid']:false;

if ($uid && $modin) {

    $list_options['_userid']= $uid;
    $list_options['detail_format'] = 'groups_detail_display';
    $output= $userlist->doAction('DisplayHTML', $list_options); 

} else { 

    //display result list
    $list_options['display_format']='groups_layout_display';
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

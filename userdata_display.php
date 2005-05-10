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

$intro_id=1;
$display_options=0;
$admin=false;
$userlist=&new UserDataSet($dbcon, $modin, $admin);
if ($userlist->_module_def['publish']) {

	#get all registered SearchForm plugins
	$searchforms=$userlist->getPlugins('SearchForm');
	#use only the first one (NULL if not an array or empty)
	$searchform = array_shift($searchforms);

    $pager=&$userlist->registerPlugin('Output', 'Pager');
    $userlist->registerPlugin('AMP', 'Search');
    if ($display=&$userlist->getPlugin('Output', 'DisplayHTML')) {
        $display_options = $display->getOptions();

    } else {
       $display=&$userlist->registerPlugin('Output', 'DisplayHTML');
    }
    $userlist->registerPlugin('AMP', 'Sort');
} else {
    header ("Location: index.php");
}

$sub = isset($_REQUEST['btnUDMSubmit']);
$uid= isset($_REQUEST['uid'])?$_REQUEST['uid']:false;

if ($uid && $modin) {
/*
	if(isset($display_options['header_text_detail'])) {
		$intro_id=$display_options['header_text_detail'];
	}
*/
    $userlist->uid = $uid;
    $output= $userlist->output('DisplayHTML'); 

} else { 

/*
	if(isset($display_options['header_text_list'])) {
		$intro_id=$display_options['header_text_list'];
	}
*/
    //display result list
    if (!isset($searchform)||$searchform==false) $srch_options['criteria']=array('value'=>array("modin=".$modin));

    if ($userlist->doAction('Search', $srch_options)) {
        $output= (isset($userlist->error)? $userlist->error.'<BR>':"").
                ($searchform?   $searchform->search_text_header()
                                .$searchform->execute():"").
                ($pager?$pager->execute():"").
                ($actionbar?$actionbar->execute():"").
                $userlist->output('DisplayHTML', $list_options).
                ($pager?$pager->execute():"");#.
                #$userlist->output('Index');
    } else {
        $output=join('<BR>',$userlist->errors).'<P>'.($searchform?$searchform->execute():"");
    }
}
if ($userlist->modTemplateID) $intro_id = $userlist->modTemplateID;
require_once( 'AMP/BaseTemplate.php' );
require_once( 'AMP/BaseModuleIntro.php' );

print $output;

        
// Append the footer and clean up.
require_once( 'AMP/BaseFooter.php' );

?>

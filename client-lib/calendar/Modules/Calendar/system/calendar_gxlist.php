<?php

/*****
 *
 * Calendar Event Listing Page
 * 
 * Allows search for events, admin side
 *
 * 
 *
 *****/
$modid=1;
$mod_id=67;
$mod_name = "calendar";
ob_start();

require_once( 'AMP/System/Base.php' );
require_once('Modules/Calendar/Calendar.inc.php');
//require_once( 'header.php' );
require_once("AMP/System/BaseTemplate.php");
$template =& AMPSystem_BaseTemplate::instance();
$template->setTool($modid);
$template->setToolName($mod_name);
$template->useFormNav(false);

$output = $template->outputHeader();

	if ($_REQUEST['id']) {//display single event, specify calid value
        $list_options['calid']= array('value'=> $_REQUEST['id']);
        $eventsearch=new Calendar($dbcon,null,$admin);
        $eventlist=$eventsearch->find_events("publish=1", $searchform->sortby);
        $output .= $eventlist->output('DisplayHTML', $list_options); 

	} else { 
        //display result list
        $admin=true;
        $eventsearch=&new Calendar($dbcon, null, $admin);
        $searchform=&$eventsearch->getPlugin('Output', 'SearchForm');
        $pager=&$eventsearch->getPlugin('Output','Pager');
        $actionbar=&$eventsearch->getPlugin('Output','Actions');
        
        if ($eventsearch->doAction('Search')) {
            $output .= (isset($eventsearch->error)? $eventsearch->error.'<BR>':"").
                    ($searchform?   $searchform->search_text_header()
                                    .$eventsearch->output('SearchForm'):"").
                    ($pager?$pager->execute():"").
                    ($actionbar?$actionbar->execute():"").
                    $eventsearch->output('TableHTML').
                    ($pager?$pager->execute():"").
                    $eventsearch->output('Index');
        } else {
            $output .= $eventsearch->error.'<BR>'.$eventsearch->output('SearchForm');
        }
    }
    print $output;

        
// Append the footer and clean up.
require_once( 'footer.php' );

?>

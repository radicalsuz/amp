<?php

/*****
 *
 * Calendar Event Listing Page
 * 
 * Allows search for events
 *
 * 
 *
 *****/
$modid=1;
$mod_id=57;
ob_start();


#require_once('Modules/Calendar/Output/list_rsvp_html4.inc.php');
require_once( 'AMP/BaseDB.php' );


require_once('Modules/Calendar/Calendar.inc.php');
require_once( 'AMP/BaseTemplate.php' );
require_once( 'AMP/BaseModuleIntro.php' );
$admin=false;
$calendar=&new Calendar($dbcon, null, $admin);

if ($calid = $_REQUEST['calid']) {
	$options['calid'] = array('value' => $calid);
}

if ($_REQUEST['output'] == 'rss') {
	$output = $calendar->output('RSS', $options);

//display single event, specify calid value
} elseif ($calid) {
	$output= $calendar->output('DisplayHTML', $options); 

} else { 
	//display result list
	$searchform=&$calendar->getPlugin('Output','SearchForm');
	$pager =&$calendar->getPlugin('Output','Pager');
	$actionbar=&$calendar->getPlugin('Output','Actions');

	if ($calendar->doAction('Search')) {
		$output= (isset($calendar->error)? $calendar->error.'<BR>':"").
				($searchform?   $searchform->search_text_header()
								.$calendar->output('SearchForm'):"").
				($pager?$pager->execute():"").
				$calendar->output('DisplayHTML').
				($pager?$pager->execute():"").
				$calendar->output('Index');
	} else {
		$output=$calendar->error.'<BR>'.$calendar->output('SearchForm');
	}
}

print $output;

        
// Append the footer and clean up.
require_once( 'AMP/BaseFooter.php' );

?>

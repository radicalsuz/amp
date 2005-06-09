<?php

/*****
 *
 * Calendar Event Email Page
 * 
 * Email Out to selected contacts
 *
 *
 * 
 *
 *****/
$modid=1;
$mod_id=67;
ob_start();
require_once( 'AMP/BaseDB.php' );

require_once('AMP/Calendar.inc.php');
require_once( 'header.php' );

$admin=true;
$eventsearch=&new Calendar($dbcon, null, $admin);
/*
$searchform=&$eventsearch->getPlugin('Output', 'SearchForm');
$pager=&$eventsearch->getPlugin('Output','Pager');
$actionbar=&$eventsearch->getPlugin('Output','Actions');
*/
$sub=($_REQUEST['email_send']=='Send')?true:false;
$idset=$_REQUEST['list_action_id'];

if (!$sub && $idset) {
    //Email form hasn't submitted
    //Display Email form
    $options['criteria']="id in (".$idset.")";
    if ($eventsearch->doAction('Search', $options)) {
        $myset=$eventsearch->results;
        foreach ($myset as $set_bb=>$set_info) {
            print $set_info['email1']."<BR>";
        }
        $eventsearch->registerPlugin("Output", "EmailForm");
        $output= (isset($eventsearch->error)? $eventsearch->error.'<BR>':"").
                $eventsearch->output('EmailForm');
    } else {
        $output=$eventsearch->error.'<BR>'.$eventsearch->output('SearchForm');
    }
} else {
    $output="Form submitted, email sender engaged!";
}
print $output;

        
// Append the footer and clean up.
require_once( 'footer.php' );

?>

<?php

require_once ( 'Modules/Schedule/Appointment/ComponentMap.inc.php' );
require_once ( 'AMP/System/Page.inc.php' );

$map = &new ComponentMap_Appointment();
$page = &new AMPSystem_Page( $dbcon, $map );

if (isset( $_GET[ 'action' ] ) && $_GET[ 'action' ]=='list' ) $page->showList( true );

// for single schedule items
if (isset( $_GET[ 'scheduleitem_id' ]) && ($item_id = $_GET[ 'scheduleitem_id' ])) {
    $page->addCallback( 'form', 'setDefaultValue', array( 'action_id', $item_id ));
    $page->addCallback( 'list', 'addCriteria', 'action_id='.$item_id );
}

// for single users
if (isset( $_GET[ 'userdata_id' ]) && ($user_id = $_GET[ 'userdata_id' ])) {
    $page->addCallback( 'form', 'setDefaultValue', array( 'userdata_id', $user_id ));
    $page->addCallback( 'list', 'addCriteria', 'userdata_id='.$user_id );
}

// for single schedules
if (isset( $_GET[ 'schedule_id' ]) && ($schedule_id = $_GET[ 'schedule_id' ])) {

    $page->addCallback( 'form', 'setSchedule', $schedule_id );
    $page->addCallback( 'list', 'appendEditLinkVar', 'schedule_id='.$schedule_id );

    $itemset = &ScheduleLookup_ItemSet::instance( $schedule_id );
    $itemcrit = count($itemset)? "(" . join( ",", $itemset ) .")" : false;
    if ($itemcrit) $page->addCallback( 'list', 'addCriteria', array ('scheduleitem_id in'.$itemcrit, false ));
}

$page->execute();

print $page->output();

?>

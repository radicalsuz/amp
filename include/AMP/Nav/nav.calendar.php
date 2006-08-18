<?php

require_once( 'AMP/System/Lookups.inc.php');

function AMP_navCalendarSearchState( ){
    $renderer = new AMPDisplay_HTML;
    $add_link = $renderer->link( AMP_CONTENT_URL_EVENT_ADD, 'Post an Event', array( 'class' => 'homeeventslink') );
    $search_link = $renderer->link( AMP_CONTENT_URL_EVENT_SEARCH, 'Search Events', array( 'class' => 'homeeventslink'));

    $state_values = AMPSystem_Lookup::instance( 'Regions_US');
    $select_values = array( '' => 'Select Your State');

    if ( $state_values ) {
        $select_values = $select_values + $state_values;
    }
    return 
        $search_link
        .$renderer->newline( )
        .'<form method="GET" action="'.AMP_CONTENT_URL_EVENT_LIST.'">'
        .AMP_buildSelect( 'state', $select_values, null, $renderer->makeAttributes( array( 'onChange' => 'if ( this.value != "") this.form.submit( );')))
        .'<input name="search" value="Search" type="hidden">'
        .'</form>'
        .$renderer->newline( )
        .$add_link
        .$renderer->newline( );
}

?>

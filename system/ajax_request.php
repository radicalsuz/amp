<?php
require_once("AMP/BaseDB.php");
require_once( 'AMP/Content/Display/HTML.inc.php');

#declare what is needed for each request
$allowed_lookups = array( 'author', 'source', 'title', 'event' );

foreach( $allowed_lookups as $test_lookup ){
    if ( !( isset( $_REQUEST[ $test_lookup ]) && $_REQUEST[ $test_lookup ])) continue;
    
    $partial_value = $_REQUEST[ $test_lookup ];
    $desired_lookup = 'AMPContentLookup_' . ucfirst( $test_lookup );
    
}

$result_set = call_user_func( array( $desired_lookup, 'instance'), $partial_value );

$display = &new AMPDisplay_HTML( );
print $display->_HTML_UL( $result_set );
?>

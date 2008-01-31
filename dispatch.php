<?php

$request = ( isset( $_GET['q_url'] ) && $_GET['q_url'] ) ? $_GET['q_url'] : false;
require_once( 'AMP/Base/Config.php');

if( !( $request && $include_file = AMP_request_to_include( $request ))) AMP_make_404( );
$url_vars = AMP_request_to_vars( $request );
$_GET = array_merge( $_GET, $url_vars );
include_once( $include_file );

?>

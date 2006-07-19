<?php
require_once( 'AMP/System/Base.php');
require_once( 'AMP/System/Page/Menu.inc.php');
require_once( 'AMP/System/Page/Display.php');

$id = isset( $_GET['id']) && $_GET['id'] ? $_GET['id'] : 'home';
$menu = &new AMP_System_Page_Menu( $id );

$display = &new AMP_System_Page_Display( AMP_Registry::instance( ) );
$display->add( $menu );
print $display->execute( );



?>

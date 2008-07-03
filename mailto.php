<?php
/*********************
05-07-2003  v3.01
Module:  Mailto
Description:  popup that sends email link to page
SYS VARS:  $SiteName
To Do:  make multiple var pages send
			  add design and css to page

*********************/ 
require_once( 'AMP/Base/Config.php' );
require_once( 'Modules/Share/Public/ComponentMap.inc.php' );
$intro_id = 22;
$map = & new ComponentMap_Share_Public( );

$controller = $map->get_controller( );
$controller->_form->action = "mailto.php";

$header = AMP_get_header( );
$flash  = &AMP_System_Flash::instance( );
$header->addStylesheet( 'custom/styles.css' );

print $header->output( );
print $flash->execute( ) ;
print $controller->execute( ) ;

?>

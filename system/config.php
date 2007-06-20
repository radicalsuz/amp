<?php

require_once( 'AMP/BaseDB.php');
require_once( 'AMP/System/Config/Form.php');
require_once( 'AMP/System/Page/Display.php');

$config_form = new AMP_System_Config_Form( 'public');
$config_form->Build( );
$config_form->applyDefaults( );

$display = AMP_System_Page_Display::instance( $config_form );
$flash = AMP_System_Flash::instance( );
$display->add( $flash, AMP_CONTENT_DISPLAY_KEY_FLASH );
$display->add( $config_form );

print $display->execute( );

?>

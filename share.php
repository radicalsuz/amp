<?php

require_once( 'AMP/Base/Config.php' );
require_once( 'Modules/Share/Public/ComponentMap.inc.php' );
//require_once( 'AMP/Display/Form.php' );
require_once( 'AMP/Content/Page.inc.php');

$intro_id = 22;
//$form = &new AMP_Display_Form( );
//$form->read_xml_fields( 'Modules/Share/Public/Fields.xml' );
//AMP_directDisplay( $form->execute( ));

$map = & new ComponentMap_Share_Public( );

$controller = $map->get_controller( );
AMP_directDisplay( $controller->execute( ) );

require_once( 'AMP/BaseFooter.php');

?>

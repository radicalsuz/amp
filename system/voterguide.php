<?php 
/*****
 *
 * AMP VoterGuide Edit View
 *
 * @copyright 2005 Radical Designs
 * @author Austin Putman <austin@radicaldesigns.org>
 *
 *****/

require_once( 'Modules/VoterGuide/ComponentMap.inc.php' );
//$modin = (isset($_GET['modin']) ? $_GET[ 'modin' ] : AMP_FORM_ID_VOTERGUIDES );

$map = &new ComponentMap_VoterGuide();
$controller =& $map->get_controller();
print $controller->execute();

?>

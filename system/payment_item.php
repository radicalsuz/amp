<?php

require_once('Modules/Payment/Item/ComponentMap.inc.php');

$map = &new ComponentMap_PaymentItem();
$controller = & $map->get_controller( );
print $controller->execute( );

?>

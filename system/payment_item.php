<?php

require_once('AMP/System/Page.inc.php');
require_once('Modules/Payment/Item/ComponentMap.inc.php');

$map = &new ComponentMap_PaymentItem();
$page = &new AMPSystem_Page( $dbcon, $map );
if (isset( $_GET['action'] ) && $_GET['action'] == 'list') $page->showList( true );

$page->execute();

print $page->output();


?>

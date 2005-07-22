<?php

require_once ('AMP/System/ComponentMap.inc.php' );

class ComponentMap_PaymentItem extends AMPSystem_ComponentMap {

		var $heading = "Payment Item";
		var $nav_name = "payments";

		var $paths = array(
			"form" => "Modules/Payment/Item/Form.inc.php",
			"list" => "Modules/Payment/Item/List.inc.php",
			"source" => "Modules/Payment/Item.inc.php" );

		var $components = array(
			"form" => "PaymentItem_Form",
			"list" => "PaymentItem_List",
			"source" => "PaymentItem" );
}
?>

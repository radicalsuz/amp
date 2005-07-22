<?php

require_once ('AMP/System/List.inc.php');
require_once ('Modules/Payment/Item/Set.inc.php');

class PaymentItem_List extends AMPSystem_List {
		var $col_headers = array("ID"=>"id", "Name"=>"name", "Amount"=>"Amount");
		var $editlink = "payment_item.php";

		function PaymentItem_List( &$dbcon ) {
				$source = &new PaymentItemSet( $dbcon );
				$this->init( $source );
		}
}
?>

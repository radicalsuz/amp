<?php
require_once( 'unit_tests/config.php' );
require_once( 'Modules/Payment/Payment.php' );

class TestPayment extends UnitTestCase {
	var $dbcon;
	var $payment;

	function TestPayment() {
        $this->UnitTestCase('Payment Plugin Test');
    }  

	function setUp() {
		$this->dbcon = &new MockADODB_mysql($this);	
	}

	function testcreate() {
		$payment = & new Payment ( $this->dbcon );
	}	

	function testsetPaymentType() {
		$payment = & new Payment ($this->dbcon, 'Check');
		#$this->assertNotNull( $payment->paymentType );
	}
		
}

UnitRunner_instantiate( __FILE__ );
?>

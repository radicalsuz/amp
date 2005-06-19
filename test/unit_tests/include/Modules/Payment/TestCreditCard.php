<?php
require_once( 'unit_tests/config.php' );
require_once( 'Modules/Payment/CreditCard.inc.php' );

Mock::generate('Payment');

class TestCreditCard extends UnitTestCase {

	function TestCreditCard() {
		$this->UnitTestCase('Credit Card Payment Plugin Test');
	}

	function test_setMerchantNoData() {
		$payment =& new MockPayment($this);
		#$payment->expectOnce('addError', array('No merchant specified'));

	}

	function test_confirmTransactionisReady() {
		$payment = & new MockPayment($this);
		$payment->expectOnce('addError', array('No merchant specified'));
		$cc =& new PaymentType_CreditCard($payment);

		$data = array();
		$cc->_setMerchant( $data );
		$return = $cc->_confirmTransactionisReady();
		$this->assertFalse($return);
		$this->assertNull($cc->merchant);
		$payment->tally();
	}

}

UnitRunner_instantiate(__FILE__);
?>

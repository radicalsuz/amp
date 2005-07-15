<?php
require_once( 'unit_tests/config.php' );
require_once( 'Modules/Payment/CreditCard.inc.php' );
require_once( 'Modules/Payment/Item.inc.php' );
require_once( 'Modules/Payment/Receipt.inc.php' );

Mock::generate('Payment');
Mock::generate('PaymentType_CreditCard');


class TestReceipt extends UnitTestCase {

    var $dbcon;

    function TestReceipt() {
        $this->UnitTestCase('Payment Receipt Test');
    }
    function setUp() {
		$this->dbcon = &new MockADODB_mysql($this);	
    }

    function test_readPayment() {
        $payment = new MockPayment($this);
        $payment->setReturnValue( 'readData', true );
        $payment->setReturnValue( 'getData', array('Amount'=>'5', 'description'=>'shiny object', 'payment_item_ID'=>105, 'Status'=>'test', 'Payment_Type'=>'MonopolyMoney' ));
        $payment->expectCallCount( 'getData',2);
        $payment->setReturnValue( 'getData', '190', array('user_ID'));
        #$$payment->expectOnce( 'getData', array('user_ID') );
        $cc = new MockPaymentType_CreditCard( $this );
        $cc->setReturnValue( 'getData', array('Number'=>'56', 'Type'=>'Diner', 'Expiration'=>'20/02') );
        $cc->expectOnce( 'getData', array());
        $payment->paymentType = &$cc;
        $testobj = new PaymentReceipt( $this->dbcon);
        $testobj->payment = &$payment;
        $testobj->readPayment(1);
        $payment->tally();
        $cc->tally();
    }
}
UnitRunner_instantiate(__FILE__);
?>

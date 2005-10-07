<?php
require_once( 'unit_tests/config.php' );

require_once( 'AMP/BaseDB.php' );
require_once( 'AMP/UserData.php' );

class ThrowsErrorsClass {

	var $classvar;

	function ThrowsErrorsClass($var = null) {
		if(isset($var)) {
			$this->classvar = $var;
		}
	}

	function getVar() {
		return $this->classvar;
	}

	function error($message = null) {
		if(isset($message)) {
			return $message;
		}
		return 'Threw an error';
	}
}

function throwsErrorFunc($var1, $var2) {
	return $var1.$var2;
}

function throwsErrorFuncRef(&$var1, &$var2) {
	foreach($var1 as $value) {
		$output .= $value;
	}
	$output .= $var2->getVar();
	return $output;
}

class TestUserData extends UnitTestCase {

	var $udm;

    function TestUserData () {
        $this->UnitTestCase('UserData Test');
    }

	function setUP() {
		$registry =& AMP_Registry::instance();
		$dbcon =& $registry->getDbcon();
		$this->udm =& new UserData($dbcon, 1);
	}

	function tearDown() {
		unset($this->udm);
	}

	function testAddErrorMessageString() {
		$this->udm->addError('test', __FUNCTION__);
		$errors = $this->udm->outputErrors();
		$this->assertEqual($errors, __FUNCTION__."<BR>");
	}

	function testAddErrorMessageArray() {
		$this->udm->addError('test', array(__FUNCTION__));
		$errors = $this->udm->outputErrors();
		$this->assertEqual($errors, "");
	}

	function testErrorFunctionCallback() {
		$var1 = 'Hello';
		$var2 = 'Goodbye';
		$callback = 'throwsErrorFunc';
		$this->udm->setErrorHandler('test',$callback);
		$this->assertTrue($this->udm->getErrorHandler('test'));
		$this->assertFalse($this->udm->getErrorHandler('noexist'));
		$this->udm->addError('test', array($var1, $var2));
		$errors = $this->udm->outputErrors();
		$this->assertEqual($errors, throwsErrorFunc($var1, $var2)."<BR>");
	}

	function testErrorFunctionCallbackRef() {
		$var1 = 'Hello';
		$var2 = 'Goodbye';
		$var3 =& new ThrowsErrorsClass($var2);
		$callback = 'throwsErrorFuncRef';
		$this->udm->setErrorHandler('test',$callback);
		$this->assertTrue($this->udm->getErrorHandler('test'));
		$this->assertFalse($this->udm->getErrorHandler('noexist'));
		$array = array($var1, $var2);
		$this->udm->addError('test', array($array,$var3));
		$errors = $this->udm->outputErrors();
		$array = array($var1, $var2);
		$this->assertEqual($errors, throwsErrorFuncRef($array,$var3)."<BR>");
	}

	function testErrorFunctionCallbackClass() {
		$var1 =& new ThrowsErrorsClass('Hello');
		$var2 = "my error message";
		$this->udm->setErrorHandler('test', array(get_class($var1), 'error'));
		$this->udm->addError('test', array($var2));
		$errors = $this->udm->outputErrors();
		$this->assertEqual($errors, $var1->error($var2)."<BR>");
	}

	function testErrorFunctionCallbackClassRef() {
		$var1 =& new ThrowsErrorsClass('Hello');
		$var2 = "my error message";
		$this->udm->setErrorHandler('test', array(&$var1, 'getVar'));
		$this->udm->addError('test', null);
		$errors = $this->udm->outputErrors();
		$this->assertEqual($errors, $var1->getVar()."<BR>");
	}
}

UnitRunner_instantiate( __FILE__ );
?>

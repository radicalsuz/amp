<?php
require_once( 'unit_tests/config.php' );
require_once( 'AMP/BaseDB.php' );
require_once( 'AMP/UserData/Input.inc.php' );
require_once( 'Modules/UDM/AMPCalendar/Read.inc.php' );

require_once( 'Modules/Calendar/Calendar.inc.php' );
class TestCalendarRead extends UnitTestCase {
	var $_udm;
	var $_plugin;

	var $_now = false;

    function TestCalendarRead() {
        $this->UnitTestCase('Dia Event Read');
    }

	function setUp() {
		$dbcon =& AMP_Registry::getDbcon();
		$this->_udm =& new UserDataInput($dbcon, 50, true);
		$this->_plugin =& $this->_udm->registerPlugin('AMPCalendar', 'Read');
		$this->_dia_plugin =& $this->_udm->registerPlugin('DIAEvent', 'Read');
	}

	function tearDown() {
		unset( $this->_udm );
		unset( $this->_plugin );
	}

	function testExecute() {
//		$this->dump($this->_plugin->cal->readData(15426));

		$this->_udm->doPlugin('Read', array('dia_event_key' => 10915));
		$this->dump($this->_plugin->cal->results());
	}

}

UnitRunner_instantiate( __FILE__ );
?>

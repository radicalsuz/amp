<?php
require_once( 'unit_tests/config.php' );
require_once( 'AMP/BaseDB.php' );
require_once( 'AMP/UserData/Input.inc.php' );
require_once( 'Modules/UDM/DIAEvent/Read.inc.php' );

require_once( 'Modules/Calendar/Calendar.inc.php' );
class TestEventRead extends UnitTestCase {
	var $_udm;
	var $_plugin;

	var $_now = false;

    function TestEventRead() {
        $this->UnitTestCase('Dia Event Read');
    }

	function setUp() {
		$dbcon =& AMP_Registry::getDbcon();
		$this->_udm =& new UserDataInput($dbcon, 50, true);
		$this->_udm->registerPlugin('AMPCalendar', 'Read');
		$this->_plugin =& $this->_udm->registerPlugin( 'DIAEvent', 'Read');

//        $this->_populateUDM( );
//		$this->_udm->doPlugin('QuickForm', 'Build');
	}

	function tearDown() {
		unset( $this->_udm );
		unset( $this->_plugin );
	}

    function testExecute( ) {
		$this->assertTrue($this->_plugin->execute(array('dia_event_key' => 10915)));
		$this->dump($this->_plugin->getData());
    }

	function testStrToTime() {
//		$this->dump(strtotime('2006-03-03 13:30:00.0'));
//		preg_match('/(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})/', '2006-03-03 13:30:00.0', $matches);
//		$this->dump($matches);
	}

	function testDoRead() {
		$this->_udm->doPlugin('AMPCalendar', 'Read', array('dia_event_key' => 10915));
		$this->dump($this->_udm->getData());
	}

	function testDoActionRead() {
		$this->dump(array_keys($this->_udm->getPlugins('Read')));
		$this->_udm->doAction('Read', array('dia_event_key' => 10915));
		$this->dump($this->_udm->getData());
	}

    function _populateUDM( ){
//        $calendar_dummy = &new Calendar( $this->_udm->dbcon );
		#$dates = array('plugin_AMPCalendar_endtime', 'plugin_AMPCalendar_time', 'plugin_AMPCalendar_enddate', 'plugin_AMPCalendar_date');
		$dates = array('endtime', 'time', 'enddate', 'date');
        #$field_defs = $this->_udm->fields;
        $field_defs = $this->_plugin->_calendar_plugin->getSaveFields();
        foreach( $field_defs as $fieldName => $values){
            if( $values['type'] == 'checkbox') {
                $dummy_data[$fieldName] = true;
            } elseif( array_search($fieldName,$dates) !== false ) {
                $dummy_data[$fieldName] = $this->now();
            } else {
                $dummy_data[$fieldName] = $fieldName."_DUMMY";
            } 
        }
        #$this->_udm->setData( $dummy_data );
		$this->_plugin->_calendar_plugin->setData($dummy_data);
		
    }

    function now( ) {
       if( !$this->_now ) {
           $this->_now = date('r' );
       }
       return $this->_now;
    }

}

UnitRunner_instantiate( __FILE__ );
?>

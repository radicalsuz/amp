<?php
require_once( 'unit_tests/config.php' );
require_once( 'AMP/BaseDB.php' );
require_once( 'AMP/UserData/Input.inc.php' );
require_once( 'Modules/UDM/DIAEvent/Read.inc.php' );
require_once('DIA/API.php');

require_once( 'Modules/Calendar/Calendar.inc.php' );
class TestEventRead extends UnitTestCase {
	var $_udm;
	var $_plugin;

	var $_now = false;

	var $event;
	var $event_key;
	var $dia_account = array('user' => 'test', 'password' => 'test', 'organization_key' => 962);

    function TestEventRead() {
        $this->UnitTestCase('Dia Event Read');
		$api =& DIA_API::create(null, $this->dia_account);
		$now = time();
		$now -= $now % 60; //round to nearest minute
		$event = array(
				'Status' => 'Active',
				'Event_Name' => 'my cool event',
				'Start' => dia_formatdate($now),
				'End' => dia_formatdate($now+360),
				'This_Event_Costs_Money' => true,
				'Ticket_Price' => 5,
				'Contact_Email' => 'event_test@radicaldesigns.org',
				'Directions' => 'take a left at the tree',
				'Address' => '123 sesame st',
				'City' => 'san francisco',
				'State' => 'CA',
				'Zip' => 94110,
				'Description' => 'this is the description',
				'distributed_event_KEY' => 142);
		$this->event_key = $api->addEvent($event);
		$this->event = $api->getEvent($this->event_key);
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
//		$this->dump($this->_plugin->getData());
    }

	function testDoRead() {
		$this->_udm->doPlugin('AMPCalendar', 'Read', array('dia_event_key' => 10915));
//		$this->dump($this->_udm->getData());
	}

	function testDoActionRead() {
		@$this->_udm->doPlugin('AMPCalendar', 'Read', array('dia_event_key' => 10915));
		@$this->_udm->doPlugin('DIAEvent', 'Read', array('dia_event_key' => 10915));
//		$this->dump($this->_udm->getData());
	}

	function testCheckData() {
		@$this->_udm->doPlugin('DIAEvent', 'Read', array('dia_event_key' => $this->event_key));
		$data = $this->_plugin->getData();

		foreach($this->_plugin->translation as $key => $value) {
			if('event_KEY' == $key) continue;
			$this->assertNotNull($this->event[$key], "event[$key] is null");
			$this->assertNotNull($data[$value], "data[$value] is null");
			$this->assertEqual($this->event[$key], $data[$value], "event[$key] != data[$value]");
		}
		$this->assertNotNull($this->event['Start']);
		$this->assertNotNull($data['date']);
		$this->assertNotNull($data['time']);
		$this->assertEqual(dia_datetotime($this->event['Start']), strtotime($data['date'].' '.$data['time']), "event['Start'] == ".$this->event['Start']."; data['date'] data['time'] == ".$data['date'].' '.$data['time']);
		$this->assertEqual($data['publish'], 1);
		$this->assertEqual($data['typeid'], 39);
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

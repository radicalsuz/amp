<?php
require_once( 'unit_tests/config.php' );
require_once( 'AMP/BaseDB.php' );
require_once( 'AMP/UserData/Input.inc.php' );
require_once( 'Modules/UDM/DIA/Save.inc.php' );
require_once( 'Modules/UDM/DIAEvent/Save.inc.php' );

require_once( 'Modules/Calendar/Calendar.inc.php' );
class TestEventSave extends UnitTestCase {
	var $_udm;
	var $_plugin;

	var $_now = false;

    function TestEventSave() {
        $this->UnitTestCase('Dia Event Save');
    }

	function setUp() {
		$dbcon =& AMP_Registry::getDbcon();
		$this->_udm =& new UserDataInput($dbcon, 50, true);
        $this->_udm->registerPlugin( 'DIA', 'Save');
		$this->_udm->registerPlugin('AMPCalendar', 'Save');
		$this->_plugin =& $this->_udm->registerPlugin( 'DIAEvent', 'Save');

        $this->_populateUDM( );
		$this->_udm->doPlugin('QuickForm', 'Build');
	}

	function tearDown() {
		unset( $this->_udm );
		unset( $this->_plugin );
	}

    function testWholeSave( ) {
		define('DIA_API_DEBUG', true);
        $save =& $this->_udm->getPlugin( 'DIA', 'Save');
        @$this->assertTrue($save->execute( ));
		$this->assertTrue($save->getSupporterKey());

		$dia_save =& $this->_udm->getPlugin('DIAEvent', 'Save');

		$data = $dia_save->translate($dia_save->getData());

        @$event_key = $dia_save->execute( );
		$this->assertTrue($this->_plugin->getEventKey());
		$this->assertTrue($event_key);
//		$this->dump($event_key);

        $api =& DIA_API::create( );
        @$event = $api->getEvent( $event_key );
		$this->assertNotNull($event);

//		$this->dump($event);

        //check that $event fields are the same as those we populated the udm with
		
		foreach($data as $key => $value) {
			if(!array_search($key, $this->_plugin->translation)) continue;
			$this->assertEqual($data[$key], $event[$key]);
		}
        //foreach ( translated field) {
        //  $this->assertEqual( translated field, dia retrieved field))
        //}
    }

    function testDiaKeyUpdate( ){
        /*
        $save =& $this->_udm->getPlugin( 'AMPCalendar', 'Save');
        $this->assertTrue($save->execute( ));
		$this->assertTrue($save->getSupporterKey());

		$dia_save =& $this->_udm->getPlugin('DIAEvent', 'Save');
        $event_key = $dia_save->execute( );
		$this->assertTrue($this->_plugin->getEventKey());
		$this->assertTrue($event_key);
		#$this->dump($event_key);

        $api =& DIA_API::create( );
        */

    }

    function _populateUDM( ){
//        $calendar_dummy = &new Calendar( $this->_udm->dbcon );
		#$dates = array('plugin_AMPCalendar_endtime', 'plugin_AMPCalendar_time', 'plugin_AMPCalendar_enddate', 'plugin_AMPCalendar_date');
		$ignore = array('endtime', 'time', 'enddate', 'date');
        $numbers = array( 'cost', 'lzip', 'distributed_event_KEY');
        $field_defs = array_combine_key( 
                            $this->_plugin->_calendar_plugin->getSaveFields( ), 
                            $this->_plugin->_calendar_plugin->getFields( )
                            );
        foreach( $field_defs as $fieldName => $values){
            if( $values['type'] == 'checkbox') {
                $dummy_data[$fieldName] = true;
            //dates
            } elseif( array_search($fieldName,$ignore) !== false ) {
            //numbers
            } elseif( array_search($fieldName,$numbers) !== false ) {
                $dummy_data[$fieldName] = 9;
            //default
            } else {
                $dummy_data[$fieldName] = $fieldName."_DUMMY";
            } 
        }
		$dummy_data['date'] = '2006-01-20';
		$dummy_data['time'] = '4 PM';
		$dummy_data['endtime'] = '5 PM';
		$dummy_data['enddate'] = '2006-01-21';
		$dummy_data['lstate'] = 'CA';
        #$this->_udm->setData( $dummy_data );
		$this->_plugin->_calendar_plugin->setData($dummy_data);
//		AMP_varDump( $this->_plugin->_calendar_plugin->getData());

		
    }

    function now( ) {
       if( !$this->_now ) {
           $this->_now = '2006-01-20';
           #$this->_now = date('r' );
       }
       return $this->_now;
    }

}

UnitRunner_instantiate( __FILE__ );
?>

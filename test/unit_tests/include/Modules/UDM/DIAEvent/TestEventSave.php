<?php
require_once( 'unit_tests/config.php' );
require_once( 'AMP/BaseDB.php' );
require_once( 'AMP/UserData/Input.inc.php' );
require_once( 'Modules/UDM/DIA/Save.inc.php' );
require_once( 'Modules/UDM/DIAEvent/Save.inc.php' );

require_once( 'Modules/Calendar/Calendar.inc.php' );
class TestDIAEventSave extends UnitTestCase {
	var $_udm;
	var $_plugin;

	var $_now = false;

    function TestDIAEventSave() {
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
        $this->assertTrue($save->execute( ));
		$this->assertTrue($save->getSupporterKey());

		$dia_save =& $this->_udm->getPlugin('DIAEvent', 'Save');
        $event_key = $dia_save->execute( );
		$this->assertTrue($this->_plugin->getEventKey());
		$this->assertTrue($event_key);
		$this->dump($event_key);

        $api =& DIA_API::create( );
        $event = $api->getEvent( $event_key );
		$this->assertNotNull($event);
		$this->dump($event);

        //check that $event fields are the same as those we populated the udm with
        //foreach ( translated field) {
        //  $this->assertEqual( translated field, dia retrieved field))
        //}
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

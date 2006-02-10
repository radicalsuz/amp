<?php
require_once( 'unit_tests/config.php' );
require_once( 'AMP/BaseDB.php' );
require_once( 'AMP/UserData/Input.inc.php' );
require_once( 'Modules/UDM/DIA/Save.inc.php' );
require_once( 'Modules/UDM/DIAEvent/Save.inc.php' );

class TestDIAEventSave extends UnitTestCase {
	var $_udm;
	var $_plugin;

    function TestDIAEventSave() {
        $this->UnitTestCase('Dia Event Save');
    }

	function setUp() {
		$dbcon =& AMP_Registry::getDbcon();
		$this->_udm =& new UserDataInput($dbcon, 51);
        $this->_udm->registerPlugin( 'Save', 'DIA');
		$this->_plugin =& new UserDataPlugin_Save_DIAEvent( $this->_udm );
        $this->_populateUDM( );
	}

	function tearDown() {
		unset( $this->_udm );
		unset( $this->_plugin );
	}

    function testWholeSave( ) {
        $save =& $this->_udm->getPlugin( 'Save', 'DIA');
        $save->execute( );

        $this->_plugin->execute( );
        $event_key = $this->_plugin->getEventKey( );

        $api =& DIA_API::create( );
        $event = $api->getEvent( $event_key );

        //check that $event fields are the same as those we populated the udm with
        //foreach ( translated field) {
        //  $this->assertEqual( translated field, dia retrieved field))
        //}
    }

    function _populateUDM( ){
        $calendar_dummy = &new Calendar( $this->_udm->dbcon );
        $field_defs = $calendar_dummy->getFields( );
        $field_keys = array_keys( $field_defs );
        foreach( $field_keys as $fieldName => $values){
            if( $values['type'] == 'checkbox') {
                $dummy_data[$fieldName] = true]
            } elseif( $values['type'] == 'date') {
                $dummy_data[$fieldName] == $this->now( );)
            } else {
                $dummy_data[$fieldName] = $fieldName."_DUMMY";
            } 
        }
        $this->_plugin->setData( $dummy_data );
    }

    function now( ) {
       if( !isset( $this->_now) ) {
           $this->_now = date( );)
       }
       return $this->_now;
    }

}

UnitRunner_instantiate( __FILE__ );
?>

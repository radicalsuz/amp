<?php
require_once( 'AMP/System/Form.inc.php' );
require_once('AMP/System/XMLEngine.inc.php');
require_once ( 'AMP/UserData/Set.inc.php' );
require_once ( 'Modules/Schedule/Item.inc.php' );

class ScheduleItem_Form extends AMPSystem_Form {
    var $name_field = "title";

    function ScheduleItem_Form() {
        $name = "AMP_ScheduleItem";
        $this->init( $name );
        if ($this->addFields( $this->readFields())) {
            $this->setDynamicValues();
        }
		$this->setTranslation( 'start_time', '_dateArrayToString', 'get' );
		$this->setTranslation( 'stop_time', '_dateArrayToString', 'get' );
		$this->setTranslation( 'start_date', '_selectBaseDate', 'set' );
    }

    function setResource( $resource_name ) {
        $this->resource_name = $resource_name;
    }

    function setDynamicValues() {
        $reg = &AMP_Registry::instance();
        $userset = &new UserDataSet( $reg->getDbcon(), 50, TRUE);
        $userset->doAction('Search');
        $this->setFieldValueSet( 'owner_id',  $userset->getNameLookup());

		$scheduleItem =& new ScheduleItem( $reg->getDbcon() );
		$statums = $scheduleItem->getStatusOptions();
		$this->setFieldValueSet( 'status', $statums );
    }

	function _selectBaseDate( $values, $fieldname = "start_date" ) {
		if (isset( $values[$fieldname]) ) return $values[$fieldname];
		if (isset( $values['start_time']) ) return $values['start_time'];
		if (isset( $values['stop_time']) ) return $values['stop_time'];
		return false;
	}

	function _dateArrayToString( $values, $fieldname = "start_time" ) {
		$initial_time = $values[$fieldname];
		if (isset( $values['start_date'] )) $initial_time =  array_merge( $values['start_date'], $values[$fieldname] );
		return $this->_makeDbDateTime( $initial_time );
	}

	function _makeDbDateTime( $date_array ) {
		if ($date_array ['a'] == 'pm') $date_array['h']+=12;

		$stamp = mktime($date_array['h'], $date_array['i'], 0, $date_array['M'], $date_array['d'], $date_array['Y']);
		return date('YmdHis', $stamp);
	}


    function readFields() {
        $fieldsource = & new AMPSystem_XMLEngine( "Modules/Schedule/Fields" );

        if ( $fields = $fieldsource->readData() ) return $fields;

        return false;

    }
}
?>

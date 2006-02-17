<?php

require_once('AMP/UserData/Plugin.inc.php');
require_once('Modules/Calendar/Calendar.inc.php');

class UserDataPlugin_Read_AMPCalendar extends UserDataPlugin {

    // Basic descriptive data.
    var $short_name  = 'udm_AMPCalendar_read';
    var $long_name   = 'Calendar Read Plugin';
    var $description = 'Reads Calendar Data from the AMP database';

    // We take one option, a calid, and no fields.
    var $cal;
    var $options     = array( 'calid' => array(   'available' => false,
                                                    'value' => null),
							  'dia_event_key' => array( 'available' => false,
													'value' => null) );

    // Available for use in forms.
    var $available   = true;
    var $_field_prefix = "plugin_AMPCalendar";

    function UserDataPlugin_Read_AMPCalendar ( &$udm, $plugin_instance=null ) {
        $this->cal = &new Calendar( $udm->dbcon, null, $udm->admin );
        $this->init( $udm, $plugin_instance );
    }
   /* 
    function _register_fields_dynamic() {
        return;

    }
    */
    
    function execute( $options = null ) {
        $options = array_merge($this->getOptions(), $options);

		if(isset($options['dia_event_key'])) {
            $options['calid'] = $this->lookupCalid( $options['dia_event_key']);
		}

        // Check for the existence of a userid.
        if (!(isset( $options['calid'] )&&$options['calid'])) return false;

        if ($calData = $this->cal->readData( $options['calid'])) {
            $this->setData( $calData );
			
            return $options['calid'];
        }
        return false;

    }

    function lookupCalid( $foreign_key, $type = 'CalendarDiaKey'){
        $keys = AMPSystem_Lookup::instance($type);
            
	    if(isset($keys[$foreign_key])) return $keys[$foreign_key];
        return false;

    }
}

 
?>

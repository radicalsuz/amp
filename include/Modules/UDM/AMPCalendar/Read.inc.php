<?php

require_once('AMP/UserData/Plugin.inc.php');
require_once('Modules/Calendar/Calendar.inc.php');

class UserDataPlugin_Read_AMPCalendar extends UserDataPlugin {

    // Basic descriptive data.
    var $short_name  = 'udm_AMPCalendar_read';
    var $long_name   = 'Calendar Read Plugin';
    var $description = 'Reads Calendar Data from the AMP database';

    // We take one option, a calid, and no fields.
    var $options     = array( 'calid' => array(   'available' => false,
                                                    'value' => null) );
    var $fields      = array();

    // Available for use in forms.
    var $available   = true;

    function UserDataPlugin_Read_AMPCalendar ( &$udm, $plugin_instance=null ) {
        $this->init( $udm, $plugin_instance );
    }
    
    function _register_fields_dynamic() {
        $cal=new Calendar( $this->dbcon, null, $this->udm->admin );
        $cal_fieldnames=array_keys($cal->fields);

        $prefix = ($this->_field_prefix?$this->_field_prefix:'plugin_AMPCalendar').'_';
        foreach ($cal_fieldnames as $calfield) {
            $cal_fieldorder .= $prefix.$calfield.",";
        }

        $cal_fieldorder = substr($cal_fieldorder, 0, strlen($cal_fieldorder)-1);

        $this->udm->_module_def[ 'field_order' ] = join(",", array($cal_fieldorder, $this->udm->_module_def[ 'field_order']));
        $this->fields=$this->fields + $cal->fields;
    }
    
    function execute( $options = null ) {
        $options = array_merge($this->getOptions(), $options);
        // Check for the existence of a userid.
        if (!(isset( $options['calid'] )&&$options['calid'])) {
            print 'no calid';
            return false;
        } else {
            print $options['calid']."#AA#";
        }
        $calid = $options['calid'];
	
        //Read Calendar Record
        $sql  = "SELECT * FROM calendar WHERE "; 
        $sql .= "id='" . $calid . "'";      
    
        $calDataSet = $this->dbcon->CacheExecute( $sql );
        if ($calData = $calDataSet->FetchRow()) {
            $this->setData( $calData );
            return true;
        }
        return false;

    }
}

 
?>

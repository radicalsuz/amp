<?php

require_once('AMP/UserData/Plugin.inc.php');
require_once('Modules/Volunteer/Volunteer.inc.php');

class UserDataPlugin_Read_Volunteer extends UserDataPlugin {

    // Basic descriptive data.
    var $short_name  = 'udm_volunteer_read';
    var $long_name   = 'Volunteer Read Plugin';
    var $description = 'Reads Volunteer Data from the AMP database';

    // We take one option, a userid, and no fields.
    var $options     = array( '_userid' => array('value'=>null) );
    var $fields      = array();

    // Available for use in forms.
    var $available   = true;

    function UserDataPlugin_Read_Volunteer ( &$udm, $options=null ) {
        $this->init( $udm );
    }
    
    function _register_fields_dynamic() {
        $vol=new Volunteer( $this->dbcon );
        $this->fields=$this->fields + $vol->fields;
    }
    
    function execute( $options = null ) {
        // Check for the existence of a userid.
        if (!isset( $options['_userid'] ) &&
            !isset($this->options['_userid']['value'] )) return false;

        $userid = (isset($options['_userid'])) ? $options['_userid'] : $this->options['_userid']['value'];
        print $userid;
	
        //Read Interests
        $sql  = "SELECT * FROM vol_relinterest WHERE "; 
        $sql .= "personid='" . $userid . "'";      
    
        if ($volData = $this->dbcon->GetAssoc( $sql )) {
			
            foreach ($volData as $int_id=>$interest) {
                $fieldname='vol_interest'.$interest['interestid'];
                if ($this->fields[$fieldname]) {
                    $voldata[ $fieldname ][ 'value' ] = 1;
                }
            }	
        }

        //Read Skills

        $sql  = "SELECT * FROM vol_relskill WHERE ";
        $sql .= "personid='" . $userid . "'";
        if ($volData = $this->dbcon->GetAssoc( $sql )) {
            foreach ($volData as $int_id=>$interest) {
                $fieldname='vol_skill'.$interest['skillid'];
                if ($this->fields[$fieldname]) {
                    $voldata[ $fieldname ][ 'value' ] = 1;
                }
            }	
        }

        //Read Availability

        $sql  = "SELECT * FROM vol_relavailability WHERE "; 
        $sql .= "personid='" . $userid . "'";
        if ($volData = $this->dbcon->GetAssoc( $sql )) {
            foreach ($volData as $int_id=>$interest) {
                $fieldname='vol_avail'.$interest['availabilityid'];
                if ($this->fields[$fieldname]) {
                    $voldata[ $fieldname ][ 'value' ] = 1;
                }
            }	
        }
        
        $this->setData( $voldata );
    }
}

 
?>

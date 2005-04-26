<?php

require_once ('Modules/Calendar/Calendar.inc.php');
require_once ('AMP/UserData/Plugin/Save.inc.php');

class UserDataPlugin_Save_AMPCalendar extends UserDataPlugin_Save {
    var $name = 'Save Calendar Data';
    var $description = 'Save event data into the AMP database';

    var $options = array(
        'reg_modin' => array(
            'description'=>'Registration Form',
            'name'=>'Registration',
            'values'=>'Lookup(userdata_fields, name, id)',
            'default'=>null,
            'type'=>'select',
            'available'=>true),
        'recurring_events' =>array(
            'name'=>'Use Recurring Events',
            'type'=>'checkbox',
            'default'=>false,
            'available'=>true));

    var $available = true;
    var $cal; #the Calendar Object

    function UserdataPlugin_Save_AMPCalendar ( &$udm , $plugin_instance=null){
        $this->init( $udm, $plugin_instance );
    }


    function getSaveFields(){
        $this->cal =new Calendar( $this->dbcon, null, $this->udm->admin );
        $save_fields=array();
        $fieldset = $this->cal->fields + $this->fields;
        foreach ($fieldset as $fname=>$fdef) {
            if ($fdef['type']!="header" && $fdef['type']!='static') {
                $save_fields[]=$fname;
            }
        }
        return $save_fields;
    }

    function _register_fields_dynamic() {
        $options=$this->getOptions();
        if ($options['recurring_events']==true) {
            $this->fields['header_recur']= array('type'=>'header', 'label'=>'Repeating Events<BR><span class=photocaption>The next three items apply to Repeating events only:</span>', 'required'=>false, 'public'=>true,  'values'=>null, 'enabled'=>true);
            $this->fields['recurring_options']= array('type'=>'select', 'label'=>'Event Frequency', 'required'=>false, 'public'=>true, 'values'=>'Lookup(calendar_recur, name, id)', 'value'=>0, 'enabled'=>true);
            $this->fields['enddate']=array('type'=>'date', 'label'=>'Choose a date for the event to stop appearing on the calendar:', 'required'=>false, 'public'=>true,  'values'=>'today', 'enabled'=>true);
            $this->fields['recurring_description']=array('type'=>'textarea', 'label'=>'Describe the schedule for a repeating event <BR>(e.g <i>Every 2nd Tuesday of the Month</i>)', 'required'=>false, 'public'=>true,  'values'=>null, 'size'=>'3:40', 'enabled'=>true);
            $cal_fieldnames=array_keys($this->fields);
            $prefix = ($this->_field_prefix?$this->_field_prefix:'plugin_AMPCalendar').'_';
            foreach ($cal_fieldnames as $calfield) {
                $cal_fieldorder .= $prefix.$calfield.",";
            }
            $this->udm->_module_def[ 'field_order' ] = str_replace($prefix.'org,', ($prefix.'org,'.$cal_fieldorder), $this->udm->_module_def[ 'field_order']);
        }
        if ($options['reg_modin']) {
            if ($this->udm->admin) {
                $this->fields['reg_modin'] = array (
                    'label' => 'Registration',
                    'public' =>false,
                    'type' => 'select',
                    'required' => false,
                    'values'=>'Lookup(userdata_fields, name, id)',
                    'enabled' => true,
                    'size' => null,
                    );
            } else {
                $this->fields['rsvp']=array(
                    'type'=>'checkbox', 
                    'label'=>'Please setup registration/RSVPs for this event', 
                    'required'=>false, 
                    'public'=>true, 
                    'enabled'=>true);
            }
        }

    }

    function save ( $data ) {
        $options=$this->getOptions();
        if ($this->udm->uid) {
            $data['uid'] = $this->udm->uid;
        }
        if ($data['rsvp']==1) {
            $data['reg_modin']=$options['reg_modin'];
        }
        unset ($data['rsvp']);
        $sql = ($data['id']) ? $this->updateSQL( $data ) :
                                $this->insertSQL( $data );

        $rs = $this->dbcon->CacheExecute( $sql ) or
                    die( "Unable to save calendar data using SQL $sql: " . $this->dbcon->ErrorMsg() );

        if ($rs) {
            $this->udm->showForm = false;
            $this->setData(array('id'=> $this->dbcon->Insert_ID()));
            return true;
        }

        return false;
	
	}
    function updateSQL ( $data ) {

        $dbcon =& $this->dbcon;

        $sql = "UPDATE calendar SET ";

        foreach ($data as $field => $value) {
            $elements[] = $field . "=" . $dbcon->qstr( $value );
        }

        $sql .= implode( ", ", $elements );
        $sql .= " WHERE id=" . $dbcon->qstr( $data['id'] );

        return $sql;

    }

    function insertSQL ( $data ) {

        $dbcon =& $this->dbcon;

        $data['modin'] = $this->udm->instance;

        $fields = array_keys( $data );
        $values_noescape = array_values( $data );

        foreach ( $values_noescape as $value ) {
            $values[] = $dbcon->qstr( $value );
        }

        $sql  = "INSERT INTO calendar (";
        $sql .= join( ", ", $fields ) .
                ") VALUES (" .
                join( ", ", $values ) .
                ")";

        return $sql;

    }

}	



?>

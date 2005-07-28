<?php

require_once ('AMP/UserData/Plugin/Save.inc.php' );
require_once ('Modules/Schedule/Item.inc.php' );
require_once ('Modules/Schedule/Item/Form.inc.php' );
require_once ('Modules/Schedule/Item/ComponentMap.inc.php' );

class UserDataPlugin_Save_AMPSchedule extends UserDataPlugin_Save {

    var $_field_prefix = "Save_AMPSchedule";

    var $options = array(
        'schedule_id' => array(
            'type' => 'select',
            'available' => true,
            'default' => 1,
            'label' => 'Schedule Name' )
        );

    function UserDataPlugin_Save_AMPSchedule ( &$udm, $plugin_instance=null ) {
        $this->init( $udm, $plugin_instance );
    }

    function _register_fields_dynamic() {
        $options = $this->getOptions();
        $this->schedule_form = &new ScheduleItem_Form ();

        $fields = array (
        'Schedule' => array(
                'type'=>'header', 
                'label'=>'Schedule Information', 
                'public'=>true,  
                'enabled'=>true) );
        $fields = array_merge( $fields, $this->schedule_form->getFields() );

        $fields [ 'owner_id'   ][ 'type'     ] = 'hidden';
        $fields [ 'schedule_id'][ 'type'     ] = 'hidden';
        $fields [ 'schedule_id'][ 'constant' ] = true;
        $fields [ 'schedule_id'][ 'default'  ] = $options['schedule_id'];

        foreach ($fields as $fname => $fDef ) {
            $this->fields[ $fname ] = ($fDef + array('enabled'=>true));
        }

        $this->insertBeforeFieldOrder( array_keys( $this->fields ) , 'schedule_list');

    }

    function getSaveFields() {
        return $this->fields;
    }

    function save( $data ) {
        $options = $this->getOptions();

        $item = &new ScheduleItem($this->dbcon);
        $item->setData( $this->schedule_form->translate($data, 'get') );
        $item->save();
    }

}
?>

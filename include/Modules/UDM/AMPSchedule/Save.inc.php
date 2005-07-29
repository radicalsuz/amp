<?php

require_once ('AMP/UserData/Plugin/Save.inc.php' );
require_once ('Modules/Schedule/Item.inc.php' );
require_once ('Modules/Schedule/Item/Form.inc.php' );
require_once ('Modules/Schedule/Item/ComponentMap.inc.php' );

class UserDataPlugin_Save_AMPSchedule extends UserDataPlugin_Save {

    var $_field_prefix = "AMPSchedule";

    var $options = array(
        'schedule_id' => array(
            'type' => 'select',
            'available' => true,
            'default' => 1,
            'label' => 'Schedule Name' )
        );

	var $schedule_form;

    function UserDataPlugin_Save_AMPSchedule ( &$udm, $plugin_instance=null ) {
        $this->init( $udm, $plugin_instance );
    }

    function _register_fields_dynamic() {
        $options=$this->getOptions();
        $this->schedule_form = &new ScheduleItem_Form ();
        $schedule = &new Schedule( $this->udm->dbcon, $options['schedule_id'] );

        $fields = array (
        'Schedule' => array(
                'type'=>'header', 
                'label'=> $schedule->getData( 'name' ) . ' Schedule Information', 
                'public'=>true,  
                'enabled'=>true) );
        $fields = array_merge( $fields, $this->schedule_form->getFields() );

		unset($fields['owner_id']);
		unset($fields['schedule_id']);

        foreach ($fields as $fname => $fDef ) {
            $this->fields[ $fname ] = ($fDef + array('enabled'=>true));
        }

        $this->insertAfterFieldOrder( array_keys( $this->fields ) );

    }

    function getSaveFields() {
        return $this->fields;
    }

    function save( $data ) {
        $options = $this->getOptions();

		$data['owner_id'] = $this->udm->uid;
		$data['schedule_id'] = $options['schedule_id'];

        $item = &new ScheduleItem($this->dbcon);
        $item->setData( $this->schedule_form->translate($data, 'get') );
        $item->save();
    }

}
?>

<?php

class UserDataPlugin_SubmitButton_Output extends UserDataPlugin {
	var $options = array ('button_label'=>  array(
		'type'=>'text',
		'available'=>true,
		'label'=>'Name for Submit Button',
		'default'=>'Submit')
		);
	
	
	
	function UserDataPlugin_SubmitButton_Output(&$udm,$plugin_instance=NULL) {
		$this->init($udm,$plugin_instance);
	}

    function _register_fields_dynamic() {
        $get_options = $this->getOptions();		
        $this->_field_prefix = "";
	    $btn_field = array(
            'btnUdmSubmit' => array(
                'type'=>'submit', 
                'label'=>$get_options['button_label'], 
                'required'=>false, 
                'public'=>true,
                'enabled'=>true)
            );
        $this->fields = $btn_field;
    }
	
	function execute(){
    	#$button = &$form->getElement( 'btnUdmSubmit' );
        /*
		if (isset($this->udm->form)) {
			$build_plugin =& $this->udm->getPlugin('QuickForm','Build');
			$build_plugin->udm_quickform_addElement( $this->udm->form, 'btnUdmSubmit', $btn_fields, $this->udm->admin );
		}
        */
		
	}

}


?>

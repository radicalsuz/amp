<?php

require_once( 'AMP/UserData/Plugin.inc.php' );
require_once( 'AMP/User/Profile/List.php' );


class UserDataPlugin_Read_Related extends UserDataPlugin {

    var $_available = false;
    var $_field_prefix = 'plugin_related';


    var $options = array(
        'related_form_id' = array( 
            'type'      => 'select',
            'label'     => 'Related Form',
            'default'   => '',
            'available' => true
        ),
        'related_form_owner_field' => array( 
            'type'      => 'select',
            'label'     => 'Related Form Owner Field ( to store this Uid )',
            'default'   => '',
            'available' => true
        ),
        'included_fields' => array( 
            'type'      => 'textarea',
            'label'     => 'Fields to include',
            'default'   => '',
            'available' => true
        ),
        '_userid' => array(   
                'available' => false,
                    'value' => null) 
        );

    function UserDataPlugin_Read_Related( &$udm, $plugin_instance=null ) {
        $this->init( $udm, $plugin_instance );
    }

    function _register_fields_dynamic() {
        $this->fields = array(

            'related_list' => array(
                'type'=>'html',
                'public'=>false,
                'enabled'=>true
                )
            );
        $this->insertAfterFieldOrder( array_keys( $this->fields ) );
    }

    function execute( $options = array( )) {
        $options = array_merge ($this->getOptions(), $options);
        if (!isset( $options['_userid'] ) ) return false;
        $uid = $options['_userid'];
        

           
        $related_list = &new AMP_User_Profile_List( false, array( 'modin' => $options['related_form_id'], $options['related_form_owner_field'] => $options['_userid']) );

        $this->udm->fields[ $this->addPrefix('related_list') ]['values'] = $this->inForm($related_list->execute( ));
    }
}
?>

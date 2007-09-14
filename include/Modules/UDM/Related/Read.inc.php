<?php

require_once( 'AMP/UserData/Plugin.inc.php' );
require_once( 'AMP/User/Profile/List.php' );


class UserDataPlugin_Read_Related extends UserDataPlugin {

    var $available = true;
    var $_field_prefix = 'plugin_related';


    var $options = array(
        'related_form_id' => array( 
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

    #TODO: defaults to save plugin values if registered?
    function _register_options_dynamic( ) {
        if ( !$this->udm->admin ) return;
        $form_set = AMP_lookup( 'forms');
        if ( $form_set ) unset( $form_set[ $this->udm->instance ]);
        $this->options['related_form_id']['values']    = array( '' => AMP_TEXT_OPTION_DEFAULT ) + $form_set;

        $options = $this->getOptions( );
        if ( !$options['related_form_id']) return;
        require_once( 'AMP/UserData/Lookups.inc.php');

        $field_set = AMPSystem_Lookup::instance('formFields', $options['related_form_id']);

        if ( !$field_set ) $field_set = array( );
        $this->options['related_form_owner_field']['values']    = array( '' => AMP_TEXT_OPTION_DEFAULT ) + $field_set;
    }

    function execute( $options = array( )) {
        $options = array_merge ($this->getOptions(), $options);
        if (!isset( $options['_userid'] ) ) return false;
        $uid = $options['_userid'];
        
        $save_plugin = $this->udm->getPlugin('Related', 'Save');
        $save_plugin_options = $save_plugin->getOptions();
        if(!isset($options['related_form_owner_field'])) {
            $options['related_form_owner_field'] = $save_plugin_options['related_form_owner_field'];
        }

        $related_udm = &new UserData( $this->dbcon, $options['related_form_id'], $this->udm->admin );

        $related_list = &new AMP_User_Profile_List( false, array( 'modin' => $options['related_form_id'], $options['related_form_owner_field'] => $options['_userid']) );

        $related_list->suppress('form');
        $related_list->suppress('messages');
        $related_list->suppress('create');

        $related_list->columns = array('controls');
        foreach($related_udm->fields as $name => $attrs) {
            if(!$attrs['enabled']) continue;
            if(!$attrs['public'] && !$this->udm->admin) continue;
            if( isset( $options['included_fields']) 
                && $options['included_fields']
                && !preg_match( '/\b'.$name.'\b/', $options['included_fields'])) {
                continue;
            }

            $related_list->columns[] = $name;
            $related_list->column_headers[$name] = $attrs['label'];
        }

        $this->udm->fields[ $this->addPrefix('related_list') ]['values'] = $this->inForm($related_list->execute( ));
    }
}
?>

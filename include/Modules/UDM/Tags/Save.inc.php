<?php

require_once( 'AMP/UserData/Plugin/Save.inc.php');

class UserDataPlugin_Save_Tags extends UserDataPlugin_Save {

    var $options = array( 
        'public' => array( 
            'type' => 'checkbox',
            'label' => 'Allow Front-End Tagging' ,
            'default' => '',
            'available' => true
        )
    );

    var $available = false;
    var $_active;
    var $_field_prefix = 'Tags';

    function UserDataPlugin_Save_Tags( &$udm, $plugin_instance = null ) {
        $this->init( $udm, $plugin_instance );
    }

    function _register_fields_dynamic() {
        $available_tags = AMPSystem_Lookup::instance( 'tags');

        if ( !$available_tags ) return false;
        $options = $this->getOptions( );

        $public_setting = ( isset( $options['public']) && $options['public']);
        $this->_active = ( $public_setting || $this->udm->admin );

        $fields = array( 
            'tag_add' => array( 
                'type'      => 'multiselect',
                'size'      => 12,
                'label'     => 'Select ' . ucfirst( AMP_pluralize( AMP_TEXT_TAG )),
                'enabled'   => true,
                'public'    => $public_setting,
                'values'    => $available_tags
            ),
            'tag_add_text' => array( 
                'type'      => 'text',
                'size'      => 30,
                'label'     => 'Add ' . ucfirst( AMP_pluralize( AMP_TEXT_TAG )) . ' ( comma-separated )',
                'enabled'   => true,
                'public'    => $public_setting
            )
        );

        $this->fields = &$fields;
        $this->insertAfterFieldOrder( array( 'tag_list', 'tag_add', 'tag_add_text') );

    }

    function getSaveFields() {
        return $this->getAllDataFields( );
    }

    function save( $data ) {
        $tag_names = false;
        $selected_tags = false;

        if ( isset( $data['tag_add_text']) && $data['tag_add_text']) {
            $tag_names = $data['tag_add_text'];
        }
        if ( isset( $data['tag_add']) && ( $data['tag_add'])) {
            $selected_tags = split( ", ", $data['tag_add'] );
        }
        
        AMP_update_tags( $selected_tags, $tag_names, $this->udm->uid , AMP_SYSTEM_ITEM_TYPE_FORM);
        return true;
        /* older method 
        $item_data = array( );
        if ( defined( 'AMP_SYSTEM_USER_ID')) {
            $item_data['user_id'] = AMP_SYSTEM_USER_ID;
        }
        $item_data['item_type'] = AMP_SYSTEM_ITEM_TYPE_FORM;
        $item_data['item_id'] = $this->udm->uid;
        if ( $this->_active ) $this->_clear_saved_tags( );

        if ( !isset( $data['tag_add']) || !( $data['tag_add'])) {
            return true;
        }
        $selected_tags = split( ", ", $data['tag_add'] );
        require_once( 'AMP/System/Data/Set.inc.php');
        $save_table = & new AMPSystem_Data_Set( $this->dbcon );
        $save_table->setSource( 'tags_items' );

        foreach( $selected_tags as $tag_id ) {
            $tag_save_set = array( 'tag_id' => $tag_id ) + $item_data;
            $save_table->insertData( $tag_save_set );
        }
        return true;
        */
    }

    function _clear_saved_tags( ) {

        require_once( 'AMP/System/Data/Set.inc.php');
        $save_table = & new AMPSystem_Data_Set( $this->dbcon );
        $save_table->setSource( 'tags_items' );

        $delete_crit = 'item_type = "form" and item_id = ' . $this->udm->uid; 
        return $save_table->deleteData( $delete_crit );
    }
}

?>

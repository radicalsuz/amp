<?php

require_once( 'AMP/UserData/Plugin.inc.php' );

class UserDataPlugin_Read_Tags extends UserDataPlugin {
    var $options = array( 
        '_userid' => array(   
                'available' => false,
                    'value' => null) 
    );

    function UserDataPlugin_Read_Tags( &$udm, $plugin_instance = null ) {
        $this->init( $udm, $plugin_instance );
    }

    function _register_fields_dynamic( ) {
        $this->fields = array( 
            'tag_list' => array(
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

        require_once( 'AMP/Content/Tag/Item/List.php');
        $tag_list = &new AMP_Content_Tag_Item_List ( $this->dbcon, array( 'uid' => $uid ) );

        $this->udm->fields[ $this->addPrefix('tag_list') ]['values'] = $this->inForm($tag_list->output());
    }
        
}

?>

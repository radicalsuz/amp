<?php

require_once( 'AMP/UserData/Plugin.inc.php' );

class UserDataPlugin_Read_Tags extends UserDataPlugin {
    var $options = array( 
        '_userid' => array(   
                'available' => false,
                    'value' => null) 
    );

    var $_field_prefix = 'Tags';

    function UserDataPlugin_Read_Tags( &$udm, $plugin_instance = null ) {
        $this->init( $udm, $plugin_instance );
    }

    function _register_fields_dynamic( ) {
        $this->fields = array( 
            'tag_list_public' => array(
                'type'=>'html',
                'public'=>true,
                'enabled'=>true
                )
        );
    }

    function execute( $options = array( )) {
        $options = array_merge ($this->getOptions(), $options);
        if (!isset( $options['_userid'] ) ) return false;
        $uid = $options['_userid'];

        //require_once( 'AMP/Content/Tag/Item/List/Tags.php');
        //$tag_list = &new AMP_Content_Tag_Item_List_Tags ( $this->dbcon, array( 'uid' => $uid, 'live' => 1 ) );
        require_once( 'AMP/Content/Item/Tag/Public/List.php');
        $tag_list = &new AMP_Content_Item_Tag_Public_List ( false, array( 'uid' => $uid ) );

        $this->udm->fields[ $this->addPrefix( 'tag_list_public' ) ]['values'] = $this->inForm($tag_list->execute());
        $tag_values = AMPSystem_Lookup::instance( 'tagsByForm', $uid );
        if ( $tag_values ) {
            $this->udm->fields[ $this->addPrefix('tag_add') ]['value'] = array_keys( $tag_values ); 

        }
    }
        
}

?>

<?php
require_once( 'AMP/UserData/Plugin.inc.php' );

class UserDataPlugin_Read_Redirect extends UserDataPlugin {

    var $available = true;
    var $options = array( 
        '_userid' => array(   
                'available' => false,
                    'value' => null) 
    );

    var $_field_prefix = 'Redirect';

    function UserDataPlugin_Read_Redirect( &$udm, $plugin_instance = null ) {
        $this->init( $udm, $plugin_instance );
    }

    function _register_fields_dynamic( ) {
        $this->fields = array( 
            'redirect_list_public' => array(
                'type'=>'html',
                'public'=> false,
                'enabled'=>true
                )
        );
    }

    function execute( $options = array( )) {
        $options = array_merge ($this->getOptions(), $options);
        if (!isset( $options['_userid'] ) ) return false;
        $uid = $options['_userid'];

        $alias_url[] = AMP_url_add_vars ( AMP_CONTENT_URL_FORM_DISPLAY, array( 'modin=' . $this->udm->instance, 'uid=' . $uid ) );
        $alias_url[] = AMP_url_add_vars ( 'userdata_display.php', array( 'modin=' . $this->udm->instance, 'uid=' . $uid ) );

        require_once( 'AMP/Content/Redirect/List.inc.php');
        $tag_list = &new AMP_Content_Redirect_List( AMP_Registry::getDbcon( ), array( 'target' => $alias_url ) );

        $tag_list->drop_column( 'select');
        $tag_list->suppress( 'form');
        $tag_list->suppress( 'footer');
        $tag_list->suppress( 'messages');

        /*
        $tag_list->suppressToolbar( );
        $tag_list->suppressAddlink( );
        $tag_list->suppressMessages( );
        */

        $this->udm->fields[ $this->addPrefix( 'redirect_list_public' ) ]['values'] = $this->inForm($tag_list->execute());
    }
        
}


?>

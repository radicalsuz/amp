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

        require_once( 'AMP/Content/Redirect/List.inc.php');
        $alias_url = AMP_url_add_vars ( AMP_CONTENT_URL_FORM_DISPLAY, array( 'modin=' . $this->udm->instance, 'uid=' . $uid ) );
        $tag_list = &new AMP_Content_Redirect_List( AMP_Registry::getDbcon( ), array( 'target' => $alias_url ) );
        $tag_list->suppressToolbar( );
        $tag_list->suppressAddlink( );

        $this->udm->fields[ $this->addPrefix( 'redirect_list_public' ) ]['values'] = $this->inForm($tag_list->execute());
    }
        
}


?>

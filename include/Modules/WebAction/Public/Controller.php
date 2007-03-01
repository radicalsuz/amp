<?php

require_once( 'AMP/System/Component/Controller/Public.php');
require_once( 'AMP/Content/Page.inc.php');
require_once( 'AMP/Content/Manager.inc.php');

class WebAction_Public_Component_Controller extends AMP_System_Component_Controller_Public {

    function AMP_System_Component_Controller_Public( ) {
        $this->__construct( );
    }

    function _init_request( ) {
        $url_vars = AMP_URL_Read( );
        $this->_request_vars = $_POST;
        if ( $url_vars ) {
            $this->_request_vars = array_merge( $_POST, $url_vars );
        }
        //pull useful info from request values
        if  (  $action_id = $this->assert_var( 'action')) {
            $this->_action_id = $action_id;
        }
    }

    function _init_map( ) {
        $this->add_observer( $this->_map );
        $this->_action_default = $this->_map->getDefaultDisplay( );

        //set methods based on map values

        //getCachedComponent call was here
        if ( $form  = &$this->_map->getComponent( 'form', $this->_action_id )){
            $this->_init_form_request ( $form ) ;
            $this->_form = &$form;
        }
        //getCachedComponent call was here
        //if ( $model = &$this->_map->getCachedComponent( 'source', $this->_model_id )) $this->_init_model( $model) ;
        if ( $model = &$this->_map->getComponent( 'source' )) $this->_init_model( $model) ;

        if ( !$this->allow( $this->get_action( ))) $this->clear_actions( );
        $this->set_banner( $this->get_action( ));
        if ( method_exists( $this->_display, 'add_nav')){
            $this->_display->add_nav( $this->_map->getNavName( ));
        }
    }

}

?>

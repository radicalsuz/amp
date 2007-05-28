<?php

require_once( 'AMP/System/Component/Controller/Public.php');

class Housing_Public_Controller extends AMP_System_Component_Controller_Public {

    function Housing_Public_Controller( ) {
        $this->init( );
    }

    function _init_request( ) {
        parent::_init_request( );
        if ( ( $model_id = $this->assert_var( 'post_id')) ||( $model_id = $this->assert_var( 'id')) )  {
            $this->_model_id = $model_id;
            if ( !$this->assert_var( 'action')) {
                $this->request( 'view');
            }
        }
    }

    /*
    function commit_list( ) {
        $list_result = parent::commit_list( );

        $repeat_list = $this->_map->getComponent( 'list_repeat' );
        $this->_display->add( $repeat_list, 'repeat_list');

        return $list_result;
    }
    */

}


?>

<?php

require_once( 'AMP/Content/Article/Controller.php' );

class Article_Version_Component_Controller extends Article_Component_Controller {

    function Article_Component_Controller( ){
        $this->init( );
    }

    function commit_save( ){

    }

    function commit_restore( ){
        if ( !$this->_model->restore( )){

        }

    }

}
?>

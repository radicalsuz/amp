<?php

require_once( 'AMP/System/Component/Controller/Public.php');

class Share_Public_Controller extends AMP_System_Component_Controller_Public {

    function Share_Public_Controller( ) {
        $this->__construct( );
    }

    function display_default( ){
        //do nothing
    }

    function commit_add( ){
        $intro = &$this->_map->getPublicPage( 'input' );
        if ( $intro )  {
            $this->_set_public_page( $intro );
        }
        return parent::commit_add( ); 
    }

    function commit_cancel( ) {
        $url = $this->assert_var( 'source_url');
        if ( !$url ) $url = AMP_CONTENT_URL_INDEX;
        ampredirect( $url );
        return true;
    }

}

?>

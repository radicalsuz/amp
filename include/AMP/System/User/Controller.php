<?php
require_once( 'AMP/System/Component/Controller.php');

class AMP_System_User_Controller extends AMP_System_Component_Controller_Standard {

    function AMP_System_User_Controller( ) {
        $this->__construct( );
    }

    function commit_reset( ) {
        $flash = &AMP_System_Flash::instance( );
        $flash->add_message( 'Please choose a new password');
        return $this->commit_edit( );
    }
}

?>

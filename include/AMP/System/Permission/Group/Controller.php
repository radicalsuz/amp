<?php
require_once( 'AMP/System/Component/Controller.php');

class AMP_System_Permission_Group_Controller extends AMP_System_Component_Controller_Standard {

    function AMP_System_Permission_Group_Controller ( ){
        $this->__construct( );
    }

    function commit_install( ){
        require_once( 'phpgacl/setup.php');
        exit;
    }
}


?>

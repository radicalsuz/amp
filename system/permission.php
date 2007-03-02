<?php

    define( 'AMP_SYSTEM_PERMISSIONS_LOADING', true );
    require_once( 'AMP/System/Permission/ACL/Controller.php');
    $controller = &new AMP_System_Permission_ACL_Controller( );
    $controller->execute( false );
    $flash = &AMP_System_Flash::instance( );
    print( $flash->execute( ));

?>

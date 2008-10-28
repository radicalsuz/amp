<?php
require_once( 'AMP/Content/Page.inc.php' );
class AMP_Dispatcher {
    var $page;
    var $content;

    function AMP_Dispatcher() {
    }

    function __construct() {

    }

    function respond( $request ) {
        $route = AMP_dispatch_for( $request );

        if( !( $request && $route )) {
          return false;
        }

        $d = new AMP_Dispatcher( );
        return $d->init_controller_by_route( $route );
    }

    function init_controller_by_route( $route ) {
        $resource_class = ucfirst( $route['target_type'] );
        $controller_class = "AMP_Controller_" . AMP_pluralize( $resource_class );
        $controller_path = str_replace( '_', '/', $controller_class ).".php";
        require_once( $controller_path );
        $controller = new $controller_class( AMP_dbcon(), $route['target_id'] );
        $controller->set_action( 'show' );
        $controller->set_params( array( 'id' => $route['target_id']));

        return $controller;
    }

}
?>

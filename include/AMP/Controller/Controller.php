<?php
class AMP_Controller {
    var $params = array( );
    var $current_object;
    var $current_objects;
    var $_page;
    var $_action;

    function execute( ) {
        $action_name = $this->_action;
        return $this->$action_name( );
    }

    function set_params( $values ) {
        $this->params = array_merge( $this->params, $values );
    }

    function set_action( $action ) {
        $this->_action = $action;
    }

    function render( $display ) {
        $this->_page = & AMPContent_Page::instance( );
        $this->_page->contentManager->add( $display );
    }

}
?>

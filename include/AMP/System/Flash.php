<?php

class AMP_System_Flash extends AMP_Content_Buffer {

    var $_store = array( );
    var $_errors = array( );
    var $_messages = array( );
    var $_keep = array( );

    function AMP_System_Flash( ){
        /* session storage functions not yet implemented cuz php sessions r scary
        if ( isset( $_SESSION['__flash'])) $this->_store = $_SESSION['__flash'];
        if ( isset( $_SESSION['__flash']['messages'])) $this->_messages = $_SESSION['__flash']['messages'];
        if ( isset( $_SESSION['__flash']['errors']))    $this->_errors = $_SESSION['__flash']['errors'];
        */
    }

    function &instance( ){
        static $flash = false;
        if ( !$flash ) $flash = new AMP_System_Flash;
        return $flash;
    }
    
/*
    function _init_session( ){
        $_SESSION['__flash'] = array( );
    }

    function store( $value, $key ){
        $this->_store[$key] = $value;
        $_SESSION['__flash'][$key] = $value;
    }

    function keep( $key ){
        $this->_keep[$key] = true;
        $_SESSION['__flash'][$key] = $this->_store[$key];
    }

    function get( $key ){
        //not yet implemented
        if ( !isset( $this->_store[ $key ])) return false;
        return $this->_store[ $key ];
    }
*/
    function add_error( $message, $key = null ){
        $this->_errors[$key] = $message;
 //       $_SESSION['__flash']['errors'][$key] = $message;  
    }

    function add_message( $message, $key = null ){
        $this->_messages[$key] = $message;
  //      $_SESSION['__flash']['messages'][$key] = $message;  
    }

    function get_messages( ){
        return $this->_messages;
    }

    function get_errors( ){
        return $this->_errors;
    }

    function execute( ){
        $output = $this->display( );
    }

    function display( ){
        $output = "";
        foreach ($this->get_errors() as $error ) {
            $output .=  "<span class=\"page_error\">$error</span>\n";
        }
        foreach ($this->get_messages() as $message) {
            $output .=  "<span class=\"page_result\">$message</span>\n";
        }
        return $output;
    }

}

?>

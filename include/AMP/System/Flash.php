<?php
require_once( 'AMP/Content/Buffer.php');

class AMP_System_Flash extends AMP_Content_Buffer {

    var $_store = array( );
    var $_errors = array( );
    var $_messages = array( );
    var $_keep = array( );

    function AMP_System_Flash( ){
        /* session storage functions not yet implemented cuz php sessions r scary 
        $this->_init_session( );
        */
    }

    function &instance( ){
        static $flash = false;
        if ( !$flash ) $flash = new AMP_System_Flash;
        return $flash;
    }
    
/*
    function _init_session( ){
        if ( !isset( $_SESSION['__flash'])) $_SESSION['__flash'] = array( );
        if ( !isset( $_SESSION['__flash']['messages'])) $_SESSION['flash']['messages'] = array( );
        if ( !isset( $_SESSION['__flash']['errors']))   $_SESSION['flash']['errors'] = array( );   
        #if ( !isset( $_SESSION['__flash']['store']))    $_SESSION['flash']['store'] = array( );

        #$this->_store    = & $_SESSION['__flash']['store'];
        $this->_messages = & $_SESSION['__flash']['messages'];
        $this->_errors   = & $_SESSION['__flash']['errors'];
    }
    

    
    function store( $value, $key ){
        $this->_store[$key] = $value;
    }

    function keep( $key ){
        $this->_keep[] = $key;
    }

    function get( $key ){
        if ( !isset( $this->_store[ $key ])) return false;
        return $this->_store[ $key ];
    }
    */

    function add_error( $message, $key = null ){
        if ( !isset( $key )) return $this->_errors[] = $message;
        $this->_errors[$key] = $message;
    }

    function add_message( $message, $key = null ){
        if ( !isset( $key )) return $this->_messages[] = $message;
        $this->_messages[$key] = $message;
    }

    function get_messages( ){
        return $this->_messages;
    }

    function get_errors( ){
        return $this->_errors;
    }

    function execute( ){
        $value = $this->display( );
        #$this->clear_session( );
        return $value; 
    }

    /*
    function clear_session( ) {
        $_SESSION['__flash']['errors'] = array( );
        $_SESSION['__flash']['messages'] = array( );

        $keep_values = array( );
        foreach( $this->_keep as $key ) {
            if ( isset( $this->_store[$key])) {
                $keep_values[$key] = &$this->_store[ $key ];
            }
        }

        $_SESSION['__flash']['store'] = $keep_values;

    }
    */

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

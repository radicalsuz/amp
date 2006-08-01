<?php
require_once( 'AMP/Content/Buffer.php');

class AMP_System_Flash extends AMP_Content_Buffer {

    var $_store = array( );
    var $_errors = array( );
    var $_messages = array( );
    var $_keep = array( );

    var $_cache;
    var $_cache_key_messages = '__flash__messages';
    var $_cache_key_errors   = '__flash__errors';

    function AMP_System_Flash( ){
        /* session storage functions not yet implemented cuz php sessions r scary 
        $this->_init_session( );
        */
        $this->__construct( );
    }

    function &instance( ){
        static $flash = false;
        if ( !$flash ) $flash = new AMP_System_Flash;
        return $flash;
    }

    function __construct( ){
        $this->_init_cache( );
    }

    function _init_cache( ){
        $this->_cache = &AMP_get_cache( );
        if ( !$this->_cache ) return;

        $this->_cache_key_messages = $this->_cache->identify( $this->_cache_key_messages, AMP_SYSTEM_UNIQUE_VISITOR_ID );
        $this->_cache_key_errors   = $this->_cache->identify( $this->_cache_key_errors,   AMP_SYSTEM_UNIQUE_VISITOR_ID );

        if ( $messages = $this->_cache->retrieve( $this->_cache_key_messages )) {
            $this->_messages = $messages;
        }

        if ( $errors = $this->_cache->retrieve( $this->_cache_key_errors )) {
            $this->_errors = $errors;
        }
    }

    function keep( $key ) {
        $this->_keep[] = $key ;
    }
    
    function add_error( $message, $key = null ){
        if ( !isset( $key ))  {
            $this->_errors[] = $message;
        } else {
            $this->_errors[$key] = $message;
        }
        if ( $this->_cache ) $this->_cache->add( $this->_errors, $this->_cache_key_errors );
    }

    function add_message( $message, $key = null ){
        if ( !isset( $key )) {
            $this->_messages[] = $message;
        } else {
            $this->_messages[$key] = $message;
        }
        if ( $this->_cache ) $this->_cache->add( $this->_messages, $this->_cache_key_messages );
    }

    function get_messages( ){
        return $this->_messages;
    }

    function get_errors( ){
        return $this->_errors;
    }

    function execute( ){
        $value = $this->display( );
        if ( $value ) define( 'AMP_SYSTEM_FLASH_OUTPUT', true );
        if ( !defined( 'AMP_CONTENT_PAGE_REDIRECT' ) )$this->clear( );
        return $value; 
    }

    function active( ){
        if ( !empty( $this->_messages )) return true;
        if ( !empty( $this->_errors )) return true;
        return false;
        
    }

    function clear( ){
        if ( !empty( $this->_keep )) {
            $keep_messages = array_combine_key( $this->_keep, $this->_messages );
            $keep_errors = array_combine_key( $this->_keep, $this->_errors );
        }

        $this->_messages = array( );
        $this->_errors = array( );
        
        if ( !$this->_cache ) return;

        $this->_cache->delete( $this->_cache_key_messages );
        $this->_cache->delete( $this->_cache_key_errors );

        if ( !empty( $this->_keep )) {
            $this->_keep = array( );
            $this->_messages = $keep_messages;
            $this->_errors   = $keep_messages;
            $this->_cache->add( $this->_messages, $this->_cache_key_messages );
            $this->_cache->add( $this->_errors,   $this->_cache_key_errors   );
        }
    }

    function display( ){
        $output = "";
        foreach ($this->get_errors() as $error ) {
            $output .=  "\n <span class=\"page_error\">$error</span> \n\n";
        }
        foreach ($this->get_messages() as $message) {
            $output .=  "\n <span class=\"page_result\">$message</span> \n\n";
        }
        return $output;
    }

}

?>

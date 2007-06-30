<?php
require_once( 'AMP/Content/Buffer.php');

class AMP_System_Flash extends AMP_Content_Buffer {

    var $_store = array( );
    var $_errors = array( );
    var $_messages = array( );
    var $_message_urls = array( );

    var $_keep = array( );

    var $_cache = false;
    var $_cache_key_messages = '__flash__messages';
    var $_cache_key_errors   = '__flash__errors';
    var $_cache_key_message_urls = '__flash__message_urls';

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

        $this->_cache_key_message_urls = $this->_cache->identify( $this->_cache_key_message_urls, AMP_SYSTEM_UNIQUE_VISITOR_ID );
        $this->_cache_key_messages = $this->_cache->identify( $this->_cache_key_messages, AMP_SYSTEM_UNIQUE_VISITOR_ID );
        $this->_cache_key_errors   = $this->_cache->identify( $this->_cache_key_errors,   AMP_SYSTEM_UNIQUE_VISITOR_ID );

        if ( $messages = $this->_cache->retrieve( $this->_cache_key_messages )) {
            $this->_messages = $messages;
        }

        if ( $message_urls = $this->_cache->retrieve( $this->_cache_key_message_urls )) {
            $this->_message_urls = $message_urls;
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

    function add_message( $message, $key = null, $edit_url = false ){
        if ( !isset( $key )) {
            $this->_messages[] = $message;
        } else {
            $this->_messages[$key] = $message;
            if ( $edit_url ) {
                $this->_message_urls[$key] = $edit_url;
                if ( $this->_cache ) $this->_cache->add( $this->_message_urls, $this->_cache_key_message_urls );
            }
        }
        if ( $this->_cache ) {
            $this->_cache->add( $this->_messages, $this->_cache_key_messages );

        }
    }

    function get_messages( ){
        return $this->_messages;
    }

    function get_message_urls( ){
        return $this->_message_urls;
    }

    function get_errors( ){
        return $this->_errors;
    }

    function execute( ){
        $value = $this->display( );
        if ( $value ) {
			define( 'AMP_SYSTEM_FLASH_OUTPUT', true );
			if ( !defined( 'AMP_CONTENT_PAGE_REDIRECT' ) ) $this->clear( );
		}
        return $value ; 
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
            $keep_message_urls = array_combine_key( $this->_keep, $this->_message_urls );
        }

        $this->_messages = array( );
        $this->_errors = array( );
        $this->_message_urls = array( );
        
        if ( !$this->_cache ) return;

        $this->_cache->delete( $this->_cache_key_messages );
        $this->_cache->delete( $this->_cache_key_errors );
        $this->_cache->delete( $this->_cache_key_message_urls );

        if ( !empty( $this->_keep )) {
            $this->_keep = array( );
            $this->_messages = $keep_messages;
            $this->_errors   = $keep_messages;
            $this->_message_urls = $keep_message_urls;

            $this->_cache->add( $this->_messages, $this->_cache_key_messages );
            $this->_cache->add( $this->_errors,   $this->_cache_key_errors   );
            $this->_cache->add( $this->_message_urls, $this->_cache_key_message_urls );
        }
    }

    function display( ){
        $output = "";
        foreach ($this->get_errors() as $error ) {
            $output .=  "\n<span class=\"page_error\">$error</span> \n\n";
        }
        $message_urls = $this->get_message_urls( );
        foreach ($this->get_messages() as $key => $message) {
            if ( isset( $message_urls[$key] )) {
                $link_url = $message_urls[ $key ];
                $output .=  "\n<a href=\"$link_url\" class=\"page_result\">$message</a> \n\n";
            } else {
                $output .=  "\n<span class=\"page_result\">$message</span> \n\n";
            }
        }
        return $output;
    }

    function restore_cache( ) {
        foreach( $this->_messages as $message_key  => $message ) {
            $message_url = isset( $this->_message_urls[ $message_key ] ) ? $this->_message_urls[$message_key] : false;
            $this->add_message( $message, $message_key, $message_url );
        }
        foreach( $this->_errors as $error_key => $message ) {
            $this->add_error( $message, $error_key );
        }
        
    }

}

?>

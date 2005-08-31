<?php
if (!defined( 'MEMCACHE_KEY_PAGEHTML' ))  define ('MEMCACHE_KEY_PAGEHTML', 'HTMLPage' );

class AMPContent_Page_Cached {

    function AMPContent_Page_Cached() {
    }
/*
    function init() {
        $this->setURL( $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'] );
    }

    function setURL ( $url ) {
        $this->url = $url;
    }
    */

    function execute() {
        if ($output = &AMP_getCachedPageItem( MEMCACHE_KEY_PAGEHTML )) {
            print $output;
            return true;
        }
        return false;
    }

    function save( $html_value ) {
        AMP_cachePageItem( MEMCACHE_KEY_PAGEHTML, $html_value );
    }
}
?>

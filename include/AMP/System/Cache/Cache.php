<?php

class AMP_System_Cache {

    function AMP_System_Cache( ){
        $this->__construct( );
    }

    function __construct( ){
        //interface
    }

    function &instance( ){
        static $cache = false;
        if ( !$cache) $cache = new AMP_System_Cache;
        return $cache;
    }

    function add( $item, $key ){

    }

    function contains( $key ){

    }

    function retrieve( $key ){

    }

    function delete( $key ){

    }
    
    function clear( ){

    }

}

?>

<?php

class AMP_System_Cache_File {

    var $_path_cache = AMP_SYSTEM_CACHE_PATH;

    function AMP_System_Cache_File( ){
        $this->__construct( );
    }

    function __construct( ){
        if ( !file_exists( $this->_path_cache )){
            if ( !mkdir( $this->_path_cache )){
                trigger_error( sprintf( AMP_TEXT_ERROR_CACHE_PATH_NOT_FOUND, $this->_path_cache ));
            }
        }

    }

    function &instance( ){
        static $cache = false;
        if ( !$cache) $cache = new AMP_System_Cache_File;
        return $cache;
    }

    function authorize( $key ){
        if ( strpos( $key, '%s' ) === FALSE ) return $key ;
        return sprintf( $key, AMP_SYSTEM_USER_ID );
    }

    function add( $item, $key ){
        $authorized_key = $this->authorize( $key );
        $file_Ref = & fopen( $this->path( $authorized_key ), 'wb' );
        if ( !$file_Ref ) return false;
        $result = fwrite( $file_Ref, $item ) ;

        fclose( $file_Ref );
        $this->add_index_key( $authorized_key );
        //if ( AMP_SYSTEM_FILE_OWNER ) chown( $this->path( $authorized_key ), AMP_SYSTEM_FILE_OWNER );
        
        return $result;

    }

    function contains( $key ){
        $authorized_key = $this->authorize( $key );
        if ( isset( $this->_index[$authorized_key]) 
             && file_exists( $this->path( $authorized_key ))){
            return $authorized_key;
        }
        return false;

    }

    function retrieve( $key ){
        $authorized_key = $this->authorize( $key );
        if ( !$this->contains( $authorized_key )) return false;
        return file_get_contents( $this->path( $authorized_key ));
    }

    function path( $authorized_key ){
        $result = $this->_path_cache . DIRECTORY_SEPARATOR. $authorized_key ;
        return $result;
    }

    function delete( $key ){
        $authorized_key = $this->authorize( $key );
        $this->remove_index_key( $authorized_key );
        return unlink( $this->path( $authorized_key ));
    }
    
    function clear( ){
        foreach( $this->_index as $authorized_key => $time_stored ){
            $this->delete( $authorized_key );
        }

    }

    function load_index( ){
        require_once( 'AMP/System/XMLEngine.inc.php');
        $index_reader = &new AMPSystem_XMLEngine( 'AMP_cache_index');
        $result = $index_reader->readData( );
        if ( !$result ) return;

        $this->_index = $result;
        foreach( $result as $authorized_key => $time_stored ){
            if ( $time_stored + AMP_SYSTEM_CACHE_TIMEOUT < time( )){
                $this->delete( $authorized_key );
            }
        }
        $this->save_index( );

    }

    function save_index( ){
        require_once( 'AMP/System/XMLEngine.inc.php');
        $index_reader = &new AMPSystem_XMLEngine( 'AMP_cache_index');
        return $index_reader->save( $this->_index );

    }

    function add_index_key( $authorized_key ){
        $this->_index[ $authorized_key ] = time( );
        $this->save_index( );

    }

    function remove_index_key( $authorized_key ){
        unset( $this->_index[ $authorized_key ]);
        $this->save_index( );
    }

    function url( $key  ){
        #return $this->contains( $key );

        $filename = $this->contains( $key );
        if ( !$filename ) return false;
        return str_replace( AMP_LOCAL_PATH, '', $this->path( $filename)) ;
    }

    function __destroy( ){
        //wont work in php 4
        $this->save_index( );
    }

}

?>

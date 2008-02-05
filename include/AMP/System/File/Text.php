<?php

require_once( 'AMP/System/File/File.php');

class AMP_System_File_Text extends AMP_System_File {
    var $id_field = 'name';
    var $_file_contents;
    var $_base_path = '';
    var $_file_name_pattern = '*';

    function AMP_System_File_Text( $dbcon = null, $file_path = null ){
        $this->__construct( $file_path );
    }

    function __construct( $file_path = null ){
        if ( isset( $file_path )) $this->setFile( $file_path );
    }

    function getData( ){
        return array( 
                'id'   => $this->id,
                'name' => $this->id,
                'body' => $this->getBody( ));
    }

    function setFile( $path ){
        parent::setFile( $path );
        if ( file_exists( $this->getPath( ))){
            $this->getBody( );
        }
    }

    function getBody( ){
        if ( isset( $this->_file_contents)) return $this->_file_contents;
        $result = file_get_contents( $this->getPath( ) );
        $this->_file_contents = $result;
        return $result;
    }

    function save( ){
        $fRef = &fopen( $this->getPath( ), 'w');
        if ( !$fRef ) return false;
        $result = fwrite( $fRef, $this->getBody( )) ;
        fclose( $fRef );
        return $result;
    }

    function setData( $data ){
        if ( isset( $data['body'])) $this->_file_contents = $data['body'];
        if ( isset( $data['name']) && $data['name']) $this->_resetFileName( $data['name']);
    }

    function mergeData( $data ) {
        return $this->setData( $data );
    }

    function deleteData( $file_name ){
        $delete_target = &new AMP_System_File_Text( );
        if ( !$delete_target->readData( $file_name )) return false;
        return $delete_target->delete( );
    }

    function read( $file_name ) {
        return $this->readData( $file_name );
    }

    function readData( $file_name ){
        $file_path = $this->_base_path . DIRECTORY_SEPARATOR . $file_name ;
        if ( !file_exists( $file_path )) {
            AMP_flashMessage( sprintf( AMP_TEXT_ERROR_FILE_EXISTS_NOT, $file_path ), true);
            return false;
        }
        $this->setFile( $file_path );
        return true;
    }

    function _resetFileName( $name ){
        $this->setFile( $this->_base_path . DIRECTORY_SEPARATOR . $name );
    }

    function search( $folder_path = null, $filename_pattern = null ){
        if ( !isset( $folder_path )) $folder_path = $this->_base_path;
        if ( !isset( $filename_pattern )) $filename_pattern = $this->_file_name_pattern;
        $results = parent::search( $folder_path, $filename_pattern );
        foreach( $results as $key => $result_file ){
            if ( is_dir( $result_file->getPath( )) 
                  || !is_writable( $result_file->getPath( ))
                  || ( strpos( $result_file->get_mimetype( ), 'image') === 0)
                  ) {
                unset( $results[ $key ]);
            } 
        }
        return $results;

    }

    function setDefaults( ){
        //interface
    }

}

?>

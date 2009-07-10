<?php
require_once( 'AMP/System/File/File.php');
require_once( 'AMP/Content/Image/Public/Detail.php');

class AMP_System_File_Image extends AMP_System_File {

    var $_write_methods = array(
            'image/gif' => 'imagegif', 
            'image/jpeg' => 'imagejpeg', 
            'image/png' => 'imagepng' );

    var $_create_methods = array(
            'image/gif' => 'imagecreate', 
            'image/jpeg'=> 'imagecreatetruecolor', 
            'image/png' => 'imagecreatetruecolor' );

    var $_read_methods = array(
            'image/gif' => 'imagecreatefromgif', 
            'image/jpeg'=> 'imagecreatefromjpeg', 
            'image/png' => 'imagecreatefrompng' );

    var $_copy_methods = array( 
            'image/gif' => 'imagecopyresized', 
            'image/jpeg'=> 'imagecopyresampled', 
            'image/png' => 'imagecopyresampled' );
    
    
    var $height;
    var $width;
    var $_class_name = 'AMP_System_File_Image';
    var $db_metadata;
    var $display_metadata = array( );
    var $attributes = array( );

    function AMP_System_File_Image( $file_path = null ){
        if ( isset( $file_path ) && !is_object( $file_path )) $this->setFile( $file_path );
        $this->_init_display( );
    }

    function setFile( $file_path ){
        parent::setFile( $file_path );
        $this->set_mimetype( );
        $this->set_size ();
    }

    function set_size( ) {
        if ( !isset( $this->_mimetype ) || array_search( $this->_mimetype, array_keys( $this->_read_methods )) === FALSE ) return false;
        if( $this->getName( ) == 'pic') {
            print AMPbacktrace( );
            exit;
        }
        $size_data = getimagesize( $this->getPath( ) ); 
        $this->width = $size_data[0];
        $this->height = $size_data[1];
    }


    function &get_image_resource( ){
        $false = false;
        if( !($read_method = $this->_get_action_method( 'read') )) return $false;
        $value = $read_method( $this->getPath( ));
        return $value;
    }

    function &create_image_resource( $width, $height ) {
        $false = false;
        if( !( $create_method = $this->_get_action_method( 'create') )) return $false;
        $value = $create_method( $width, $height ) ;
        return $value;
    }

    function _get_action_method( $action ) {
        $method_set = '_' . $action .'_methods' ;
        $methods = $this->$method_set;
        if ( !isset( $methods[ $this->get_mimetype( )])) {
            trigger_error( sprintf( AMP_TEXT_ERROR_IMAGE_LIBRARY_NOT_FOUND, $action, $this->getName( ), $this->get_mimetype( )) );
            return false ;
        }
        return $methods[ $this->get_mimetype( )] ;
    }

    function write_image_resource( &$image_resource, $path = null, $direct = false ) {
        if ( !( isset( $path ) || $this->getName( ) || $direct )) {
            trigger_error( 'No path specified for writing image.');
            return false;
        }
        if ( !isset( $path )) $path = $this->getPath( );
        if ( !is_writeable( $path )) {
            $file_part = basename( $path );
            $path_only = $file_part ? str_replace( DIRECTORY_SEPARATOR . $file_part, '', $path ) : $path;
            if ( !is_writeable( $path_only )) {
                trigger_error( sprintf( AMP_TEXT_ERROR_FILE_WRITE_FAILED, $path_only ) );
            }
        }

        if( !($write_method = $this->_get_action_method( 'write') )) return false;

        if ( $direct ) return $write_method( $image_resource );
        $result = $write_method( $image_resource, $path );
        if ( $result ) {
            AMP_s3_save( $path );
        }
        return $result;
    }

    function &resize( $new_width, $new_height, $start_width = null, $start_height = null ) {
        $copy_method = $this->_get_action_method( 'copy');
        $start = &$this->get_image_resource( );
        if ( !( $start && $copy_method ) ) return false;

        if ( !isset( $start_height )) $start_height = $this->height;
        if ( !isset( $start_width ))  $start_width  = $this->width ;

        $result = &$this->create_image_resource( $new_width, $new_height );

        $copy_method( $result, $start, 0,0,0,0, $new_width, $new_height, $start_width, $start_height );
        return $result;
    }

    function &crop( $start_x, $start_y, $end_x, $end_y ){
        $create_method = $this->_get_action_method( 'create');
        $copy_method = $this->_get_action_method( 'copy' );
        $source = &$this->get_image_resource( );
        if ( !( $source && $create_method )) return false;

        $initial_width = $end_x - $start_x; 
        $initial_height = $end_y - $start_y; 

        $result = &$create_method( $initial_width, $initial_height );
        $copy_method($result, $source, 0, 0, $start_x, $start_y, $initial_width, $initial_height, $initial_width, $initial_height);
        return $result;
    }

    function get_height( ){
        return $this->_height;
    }

    function get_width( ){
        return $this->_width;
    }

    function gallery( $gallery_id ){
        require_once( 'Modules/Gallery/Image.inc.php' );
        $image_record = &new GalleryImage( AMP_Registry::getDbcon( ));
        $image_record->setGallery( $gallery_id );
        $image_record->setImageFileName( $this->getName( ));
        $image_record->publish( );
        $image_record->setItemDate( date( 'Y-m-d', $this->getTime( )));
        $db_metadata = $this->getData( );
        unset( $db_metadata['id']);
        $image_record->mergeData( $db_metadata );

        $result = $image_record->save( );
        $this->notify( 'update');
        $this->notify( 'gallery');
        AMP_lookup_clear_cached( 'galleries_by_image', $this->getName( ));
        return $result;
    }

    function _init_display( ) {
        $this->display = new AMP_Content_Image_Public_Detail( $this );
    }

    function getData($item=null ) {
        if ( !isset( $this->db_metadata )) {
            $this->_init_attributes( );
        }
        $values = array( );
        foreach( $this->attributes as $key => $value ) {
            if ( !$value ) continue;
            $values[$key] = $value;
        }
        if( isset( $item ) ){
            if ( isset( $values[$item]) ) return $values[ $item ];
            return false;
        }
        return $values;
        
    }

    function _init_attributes( ) {
        if ( !( $image_db_id = $this->db_id( ))) return ( $this->attributes = array_merge( $this->attributes, $this->display_metadata ));
        require_once( 'AMP/Content/Image/Image.php');
        $this->db_metadata = new AMP_Content_Image( AMP_Registry::getDbcon( ), $image_db_id );
        $this->attributes = $this->db_metadata->getData( );
        if( !empty( $this->display_metadata )) $this->attributes = array_merge( $this->attributes, $this->display_metadata );
    }

    function set_display_metadata( $data ) {
        $this->display_metadata = $data;
        $this->_init_attributes( );
        $this->_init_display( );
    }

    function get_url_edit( ){
        if( $db_id = $this->db_id( )) {
            return AMP_url_update( AMP_SYSTEM_URL_IMAGE_EDIT, array( "id" => $db_id));
        }

        $file_name = $this->id;
        if ( !$file_name || $file_name == 'downloads') return AMP_url_update( AMP_SYSTEM_URL_IMAGES, array( 'action' => 'new' ));
        return AMP_url_update( AMP_SYSTEM_URL_IMAGES, array( "file" => $file_name, 'action' => 'new' ));
    }

    function db_id( ) {
        $db_images = AMP_lookup( 'db_images');
        $name = $this->getName( );
        if ( !( $db_images && $name )) return false;
        return array_search( $name, $db_images );
    }

    function read( $id ) {
        $db_images = AMP_lookup( 'db_images');
        if ( !isset( $db_images[ $id ])) return false;
        $this->setFile( AMP_image_path( $db_images[$id]));
        return $this->getPath( );
    }

    function delete( ){
        if ( $image_db_id = $this->db_id( )) {
            require_once( 'AMP/Content/Image/Image.php');
            $image = new AMP_Content_Image( AMP_Registry::getDbcon( ), $image_db_id );
            $image->delete( );
            AMP_lookup_clear_cached( 'images' );
            AMP_lookup_clear_cached( 'db_images' );

        }
        
        $image_classes = AMP_lookup( 'image_classes');
        foreach( $image_classes as $class ) {
            $path = AMP_image_path( $this->getName( ), $class );
            if ( $path == $this->getPath( )) continue;
            if( file_exists( $path )) unlink( $path );
        }
        return parent::delete( );
    }

    function compile_image_metadata( ) {
        $metadata = array( );
        if ( !( $name = $this->getName( ))) return false;
        $gallery_ids = AMP_lookup( 'galleries_by_image', $name);
        $article_ids = AMP_lookup( 'articles_by_image', $name);
        if( !( $gallery_ids || $article_ids )) return $this->compile_file_metadata( $name );
        return array_merge( $this->compile_file_metadata( $name), 
                            $this->compile_article_metadata( $article_ids), 
                            $this->compile_gallery_metadata( $gallery_ids )
                            );

    }

    function compile_file_metadata( $name ) {
        return array( 'date' => date( 'Y-m-d', filemtime( AMP_image_path( $name, AMP_IMAGE_CLASS_ORIGINAL ))));
    }

    function compile_gallery_metadata( $gallery_image_ids ) {
        if( !$gallery_image_ids ) return array( );
        require_once( 'Modules/Gallery/Image.inc.php');
        $image_finder = new GalleryImage( AMP_Registry::getDbcon( ));
        $gallery_images = $image_finder->find( array( 'id' => array_keys( $gallery_image_ids )));
        $metadata = array( );
        foreach( $gallery_images as $image ) {
            $data = $image->getImageData( );
            foreach( $data as $key => $value ) {
                if( !$value ) continue;
                $metadata[$key] = $value;
            }
        }
        return $metadata;
        
    }

    function compile_article_metadata( $article_ids ) {
        if( !$article_ids ) return array( );
        require_once( 'AMP/Content/Article.inc.php');
        $finder = new Article( AMP_Registry::getDbcon( ));
        $articles = $finder->find( array( 'id' => array_keys( $article_ids )));
        $metadata = array( );
        foreach( $articles as $article ) {
            $data = $article->getImageData( );
            foreach( $data as $key => $value ) {
                if( !$value ) continue;
                $metadata[$key] = $value;
            }
        }
        return $metadata;

    }

}

?>

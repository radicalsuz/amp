<?php
require_once( 'AMP/System/File/Form.inc.php');

class AMP_System_File_Image_Form extends AMP_System_File_Form {
    
    function AMP_System_File_Image_Form( ){
        $name = 'AMP_System_File_Image_Form';
        $this->init( $name, 'POST', AMP_SYSTEM_URL_IMAGES );
    }

    function setDynamicValues( ){
        $this->addTranslation( 'date', '_makeDbDateTime');
        $this->addTranslation( 'name_display', '_showThumbNail', 'set');
        $this->addTranslation( 'linked_articles_display', 'render_articles', 'set');
        $this->addTranslation( 'linked_galleries_display', 'render_galleries', 'set');

        //manually register the manageUpload plugin so it run BEFORE _name_as_image
        $this->addTranslation( 'folder', '_verify_folder', 'get');
        $this->addTranslation( 'image', '_manageUpload', 'get');
        $this->addTranslation( 'image', '_name_as_image', 'get');
        $filename = ( isset( $_REQUEST['file']) && $_REQUEST['file']) ? $_REQUEST['file'] : false;
        if ( !$filename ) return;
        $this->setDefaultValue( 'name', $filename );
    }

    function _after_init( ){

    }

    function drop_uneditable_fields( ) {
        $this->dropField( 'folder');
        $this->dropField( 'new_folder');

    }

    function _name_as_image( $data, $column ) {
        $image = ( isset( $data['image']) && $data['image']) ? $data['image'] : false;
        if ( $image ) return $image;
        return ( isset( $data['name']) && $data['name'] ) ? $data['name'] : false;
    }

    function _initUploader( $data, $filefield, &$uploader ){
        if ( isset( $data['filename']) && $data['filename']){
            $uploader->setTargetFileName( $data['filename']);
        }

        $folder_name = isset( $data['folder']) && $data['folder'] ? $data['folder'] : false;
        if( !$folder_name ) {
            $folder_name = isset( $data['new_folder']) && $data['new_folder'] ? $data['new_folder'] : false;
        }
        if( $folder_name ) {
            $uploader->setFolder( 'img/original/'. $folder_name );
        }else {
            $uploader->setFolder( 'img/original');
        }

    }

    function _verify_folder( $data, $column ) {
        if( !( isset( $data['new_folder']) && $data['new_folder'])) return $data['folder'];
        if( AMP_add_image_subfolder( $data['new_folder'])) {
            return $data['new_folder'];
        }
        return $data['folder'];
        /*
        $ok = false;
        foreach( AMP_lookup( 'image_classes') as $image_class => $image_class_name ) {
            $class_folder = AMP_LOCAL_PATH . AMP_IMAGE_PATH . $image_class. DIRECTORY_SEPARATOR . $data['new_folder'];
            $ok = AMP_mkdir( $class_folder );
            if( !$ok ) {
                trigger_error( 'Unable to create folder' . $class_folder );
                break;
            }
        }

        if ( $ok ) {
            AMP_lookup_clear_cached( 'image_folders');
            return $data['new_folder'];
        }

        return $data['folder'];
         */

    }

    function setValues( $data ) {
        if ( !( isset ( $data['id']) && $data['id'])) {
            $system_data = $this->compile_image_metadata( $data );
            if( !empty( $system_data ) && is_array( $system_data )) {
                $data = array_merge( $data, $system_data );
            }
        } 
        return parent::setValues( $data );
    } 

    function compile_image_metadata( $data ) {
        if ( !( isset( $data['name']) && $data['name'])) return false;
        $image = new AMP_System_File_Image( AMP_image_path( $data['name'] ));
        return $image->compile_image_metadata( );
    }

    function _showThumbNail( $data, $column ) {
        $column = 'name';
        if( !( isset( $data[$column]) && $data[$column])) return false;
        require_once( 'AMP/System/File/Image.php');
        $image_file = new AMP_System_File_Image( AMP_image_path( $data[$column]));
        if( !( $path = $image_file->getPath( ))) return $data[$column];
        $this->dropField( 'filename' );
        $this->dropField( 'image' );

        $renderer = AMP_get_renderer( );
        return $data[$column] 
                . $image_file->display->execute( );
        /*
        $renderer = AMP_get_renderer( );
        return $renderer->link( $content_image->getURL( AMP_IMAGE_CLASS_ORIGINAL ), $renderer->image( $content_image->getURL( AMP_IMAGE_CLASS_OPTIMIZED)), array( 'target' => '_blank'))
                . $renderer->newline( )
               .  $renderer->link( $content_image->getURL( AMP_IMAGE_CLASS_ORIGINAL ), $data[$column], array( 'target' => '_blank'));
               */
    }

    function render_articles( $data, $column ) {
        if ( !isset( $data['name'])) return false;
        $article_ids = AMP_lookup( 'articles_by_image', $data['name']);
        if ( !$article_ids ) return false;

        $renderer = AMP_get_renderer( );
        $links = array( );
        asort( $article_ids );
        foreach( $article_ids as $id => $name ) {
            $links[] = $renderer->link( AMP_url_update( AMP_SYSTEM_URL_ARTICLE, array( 'id' => $id )), AMP_trimText( $name, 30, ( $tags=false) ), array( 'title' => $name ));
        }
        return 'Linked Articles: ' . $renderer->UL( $links, array( 'class' => 'linked_items'));
    }

    function render_galleries( $data, $column ) {
        if ( !isset( $data['name'])) return false;
        $gallery_ids = AMP_lookup( 'galleries_by_image', $data['name']);
        if ( !$gallery_ids ) return false;

        $renderer = AMP_get_renderer( );
        $galleries = array_elements_by_key( $gallery_ids, AMP_lookup( 'galleries'));
        $gallery_image_ids = array_flip( $gallery_ids );

        $links = array( );
        asort( $galleries );
        foreach( $galleries as $id => $name ) {
            $links[] = $renderer->link( AMP_url_update( AMP_SYSTEM_URL_GALLERY_IMAGE, array( 'id' => $gallery_image_ids[ $id ] )), AMP_trimText( $name, 30, ( $tags=false) ), array( 'title' => $name ));
        }
        return 'Linked Galleries: ' . $renderer->UL( $links, array( 'class' => 'linked_items'));
    }
}

?>

<?php
require_once( 'AMP/Content/Config.inc.php');

class Content_Image {

    var $style_def = array(
       'class'  => 'img_main' 
       );
    var $_itemdata = array();
    var $_itemdata_keys = array( 'width', 'height', 'alttag', 'caption', 'alignment', 'filename', 'image_size');
    var $_allowed_keys = array( );
    var $_files = array( );
    var $filename;

    function Content_Image( $filename=null ) {
        $this->__construct( $filename );
    }

    function __construct( $filename = null ) {
        if (isset($filename)) $this->setFile( $filename );
        $this->_allowed_keys = $this->_itemdata_keys;
    }

    function setFile( $filename ) {
        $this->filename = $filename;
        if ( !$filename ) return;
        $target_path =  $this->getPath( $this->getImageClass( ));
        if ( !file_exists( $target_path )) return;
        $this->load_db_image( $filename );

        if ( !function_exists( 'exif_imagetype' )) return ;
        $mime_filetype = image_type_to_mime_type( exif_imagetype( $target_path ));
        
        if ( strpos( $mime_filetype, 'image') === FALSE ){
            $this->setSize(  array(  0, 0 ));
            return ;
        }
        
        $this->setSize(getimagesize( $target_path ));
    }

    function load_db_image( $filename ) {
        require_once( 'AMP/System/File/Image.php');
        $image = new AMP_System_File_Image( $this->getPath( $this->getImageClass( )));
        /*
        $image = $image_finder->find( array( 'name' => $filename ));
        if ( !$images ) return false;
        $image = current( $images );

        */
        $data = $image->getData( );
        $keys = array_keys( $data );
        foreach( $keys as $key ) {
            $this->_addAllowedKey( $key );
        }
        $this->setData( $image->getData( ));
    }

	function _addAllowedKey( $key_name ) {
		if (array_search( $key_name, $this->_allowed_keys )!==FALSE) return true;
		$this->_allowed_keys[] = $key_name;
	}

    function setSize( $size_data ) {
        $this->_itemdata['width'] = $size_data[0];
        $this->_itemdata['height'] = $size_data[1];
    }

    function getData( $fieldname = null ) {
        if (!isset($fieldname)) return $this->_itemdata;
        if (!isset($this->_itemdata[ $fieldname ])) return false;
        return $this->_itemdata[ $fieldname ];
    }

    function getName() {
        return $this->filename;
    }

    function getWidth() {
        return $this->getData( 'width' );
    }

    function getHeight() {
        return $this->getData( 'height' );
    }

    function attributes( ) {
        $attr = $this->getStyleAttrs( );
        $attr['alt'] = $this->getAltTag( );
        return $attr;
    }

    function getStyleAttrs() {
        $extra = array();
        if ($align = $this->getAlignment() ) $extra = array( 'align' => $align );
        return $this->style_def + $extra;
    }

    function setStyleAttrs( $styledef ) {
        if (!is_array($styledef)) return false;
        foreach( $styledef as $styleKey => $styleItem ) {
            $this->style_def[ $styleKey ] = $styleItem;
        }
    }

    function getImageClass() {
        if(!$picSize = $this->getData( 'image_size' )) return AMP_IMAGE_CLASS_OPTIMIZED;
        return $picSize;
    }

    function display_in_body( ){
        return !( $this->getImageClass( ) == 'list_only');
    }


    function getAltTag() {
        return $this->getData( 'alttag' );
    }

    function getCaption() {
        return $this->getData( 'caption' );
    }

    function getAlignment() {
        $align = $this->getData( 'alignment' );
        return ($align? $align : AMP_IMAGE_DEFAULT_ALIGNMENT );
    }

    function getURL( $image_type = AMP_IMAGE_CLASS_OPTIMIZED ) {
        if ( array_search( $image_type, $this->getImageClasses()) === FALSE) return false;
        if ( strpos( $this->filename, '/' ) !== FALSE ) return $this->filename;

		$url_filename = AMP_IMAGE_PATH . $image_type . '/' . $this->filename;
		$url_filename = AMP_urlFlip( $url_filename );
        return $url_filename;
    }

    function get_url_size( $width = 0 , $height = 0 ) {
        $size_set = array( );
        $sizes = '';
        if ( $width ) $size_set[] = 'width=' . $width;
        if ( $height) $size_set[] = 'height=' . $height;
        if ( !empty( $size_set )) {
            $sizes = join( '&', $size_set );
        }
        return AMP_url_add_vars( AMP_CONTENT_URL_IMAGE, array( 'action=resize&class=original&filename='.$this->getName( ), $sizes ));

    }

    function getPath( $image_type = AMP_IMAGE_CLASS_OPTIMIZED ) {
        if ( array_search( $image_type, $this->getImageClasses()) === FALSE) return false;
		$file_path = AMP_LOCAL_PATH . $this->getURL($image_type );
        return AMP_pathFlip($file_path);
    }

    function getImageClasses( ){
        return array_keys( AMP_lookup( 'image_classes' ));
    }

    function setData( $data ) {
        $this->_itemdata = array_merge( $this->_itemdata, array_combine_key( $this->_allowed_keys, $data ));
        if ($filename = $this->getData( 'filename' ) and $filename != $this->filename ) $this->setFile( $filename );
    }

    function unsetData( $fieldname ) {
        if (!isset($this->_itemdata[ $fieldname ] )) return true;
        unset( $this->_itemdata[ $fieldname ] );
        return true;
    }

    function isWide( ){
        return ( $this->imageRatio( ) > 1 );
    }

    function isTall( ){
        return ( $this->imageRatio( ) < 1 );
    }

    function imageRatio( ){
        return $this->getHeight( )/$this->getWidth( );
    }

}

?>

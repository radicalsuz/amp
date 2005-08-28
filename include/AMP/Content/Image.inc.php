<?php
define( 'AMP_IMAGE_CLASS_ORIGINAL', 'original' );
define( 'AMP_IMAGE_CLASS_THUMB', 'thumb' );
define( 'AMP_IMAGE_CLASS_OPTIMIZED', 'pic' );
define( 'AMP_IMAGE_DEFAULT_ALIGNMENT', 'right' );
define( 'AMP_IMAGE_PATH', DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR ); 


class Content_Image {

    var $style_def = array(
       'hspace' => '4',
       'vspace' => '4',
       'border' => '0',
       'class'  => 'img_main' );
    var $_itemdata = array();
    var $_itemdata_keys = array( 'width', 'height', 'alttag', 'caption', 'alignment', 'filename', 'image_size');

    function Content_Image( $filename=null ) {
        if (isset($filename)) $this->setFile( $filename );
    }

    function setFile( $filename ){
        $this->filename = $filename;
        $this->setSize(getimagesize( $this->getPath( $this->getImageClass() ) )); 
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

    function getWidth() {
        return $this->getData( 'width' );
    }

    function getHeight() {
        return $this->getData( 'height' );
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
        return AMP_IMAGE_PATH . $image_type . DIRECTORY_SEPARATOR . $this->filename;
    }

    function getPath( $image_type = AMP_IMAGE_CLASS_OPTIMIZED ) {
        return AMP_LOCAL_PATH . $this->getURL( $image_type );
    }

    function setData( $data ) {
        $this->_itemdata = array_combine_key( $this->_itemdata_keys, $data );
        if ($filename = $this->getData( 'filename' )) $this->setFile( $filename );
    }
}

?>

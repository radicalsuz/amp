<?php
require_once( 'AMP/Content/Display/List.inc.php');
require_once( 'Modules/Gallery/Image/Set.inc.php');

class Gallery_Display extends AMPContent_DisplayList_HTML {
    var $_css_class_title    = "gallerytitle";
    var $_css_class_photocaption = "gallerycap";
    var $_css_class_container_listentry = "gallerycon";
    var $_css_id_container_content = "gallery";
    var $_css_class_container_pager = "gallery_pager";

    var $_gallery;
    var $_sourceItem_class = "GalleryImage";
    var $_pager_limit = 10;

    function Gallery_Display( &$gallery, $read_data=true ){
        $this->_gallery = &$gallery;
        $source = &new GalleryImageSet( $gallery->dbcon, $gallery->id );
        $this->init( $source, $read_data );
    }
    function execute() {
        if (!$this->_source->makeReady()) return $this->noResultsDisplay();
        $sourceItems = &$this->_buildItems( $this->_source->getArray() );

        return  $this->_HTML_intro( ) 
              . $this->_HTML_inDiv(  
                    ( $this->_HTML_listingStart( )
                    . $this->_HTML_listing( $sourceItems ) 
                    . $this->_HTML_listingEnd( ) ),
                    
                    array( 'id' => $this->_css_id_container_content ) 
                ); 
    }

    function _HTML_listingStart( ){
        $this->_pager_output = ( ($this->_pager_active && $this->_pager_display ) ? 
                    $this->_HTML_inDiv( $this->_pager->execute() , array( 'class' => $this->_css_class_container_pager ))
                    : false ) ;
        return $this->_pager_output;

    }

    function _HTML_listingEnd( ){
        if (! ($this->_pager_active && $this->_pager_display )) return false; 
        if ( 1 == $this->_pager->getLimit( )) return false;
        return $this->_pager_output;
    }

    function _HTML_intro( ){
        return  $this->_HTML_title( $this->_gallery->getName( ))
                . $this->_HTML_gallerySelect( )
                . $this->_HTML_blurb( $this->_gallery->getBlurb( ));

    }

    function _HTML_listingFormat( $html ) {
        return $html;
    }

    function _HTML_title( $name ){
        if ( !$name ) return false;
        return $this->_HTML_in_P( $name, array( "class"=>$this->_css_class_title ) ); 
    }

    function _HTML_blurb( $blurb ) {
        if (!$blurb) return false;
        return $this->_HTML_in_P( converttext($blurb), array('class'=>$this->_css_class_text));
    }

    function _HTML_gallerySelect( ){
        $sel_attr = array( 
                'class' => 'go', 
                'onchange' => 'AMP_openURL( "'.AMP_SITE_URL.AMP_CONTENT_URL_GALLERY.'?gal="+this.value );'
            );
        return AMP_buildSelect( 'gallery_jump', AMPContent_Lookup::instance( 'galleryMap' ), $this->_gallery->id, $this->_HTML_makeAttributes( $sel_attr) );

    }

    function _HTML_listItemLayout ( $text, $image ) {
        return  $this->_HTML_inDiv( $image . $text, array( 'class' => $this->_css_class_container_listentry ) );
    }

    function _HTML_listItemDescription( &$image ) {
        $caption = $image->getCaption( ); 
        $imagedate = $this->_HTML_listItemDate( $image->getItemDate( ));
        $source = $this->_HTML_listItemSource( $image->getSource( ) );
        if ( $complete_caption = $caption ) {
            if ( $imagedate ) $complete_caption .= "&nbsp;" . $imagedate;
        } else {
            $complete_caption = $imagedate ? $imagedate : false;
        }
        return $this->_HTML_inDiv( $complete_caption . $source, array( 'class' => $this->_css_class_photocaption ));
        

    }

    function _HTML_listItemSource( $source ){
        if ( !$source ) return false;
        return    $this->_HTML_newline( )
                . $this->_HTML_italics( $source )
                . $this->_HTML_newline( );
    }

    function noResultsDisplay( ){
        require_once( 'AMP/Content/Display/NotFound.inc.php');
        $display = &new Display_NotFound( "No photos are available in this gallery" );
        return  $this->_HTML_intro( ) .
                $display->execute( );
    }

    function _HTML_thumbnail( &$image ){
        if ( !$result = PARENT::_HTML_thumbnail( $image )) return false;
        return $this->_HTML_link( $image->getURL( AMP_IMAGE_CLASS_ORIGINAL ), $result, array( 'target' => '_blank') );
    }


}

class Gallery_DisplaySingle extends Gallery_Display {
    var $_pager_limit=1;
    var $_css_class_photocaption = "gallerycaption";
    var $_css_class_photocredit = "gallerycredit";
    var $_full_image_link_text = "Click Here for Full Size Image";
    var $_list_image_class = AMP_IMAGE_CLASS_OPTIMIZED;

    var $_layout_table_attr = array(
        'width'         => '100%',
        'border'        => '0',
        'cellspacing'   => '0',
        'cellpadding'   => '25' );
    
    function Gallery_DisplaySingle( &$gallery, $read_data=true ){
        $this->_gallery = &$gallery;
        $source = &new GalleryImageSet( $gallery->dbcon, $gallery->id );
        $this->init( $source, $read_data );
    }

    function _HTML_listItemDescription( &$image ) {
        $caption = $this->_HTML_in_P ( $image->getCaption( ), array( 'class' => $this->_css_class_photocaption)); ;
        
        $photo_desc_parts = array( );
        $source = $this->_HTML_listItemSource( $image->getSource( ) );
        if ( $source ) $photo_desc_parts[] = $source;

        $imagedate = $this->_HTML_listItemDate( $image->getItemDate( ));
        if ( $imagedate ) $photo_desc_parts[] = $imagedate;

        $photo_desc_parts[] = $this->_HTML_fullImageLink( $image->getImageRef( ) );

        $photosource = $this->_HTML_in_P ( 
                            join(  $this->_HTML_newline(), $photo_desc_parts ), 
                            array( 'class' => $this->_css_class_photocredit)
                       );
        return  $this->_HTML_newline().
                $caption.
                $photosource.
                $this->_HTML_newline( );
        

    }

    function _HTML_thumbnail( &$image ){
        if ( !$result = PARENT::_HTML_thumbnail( $image )) return false;
        return $this->_HTML_inDiv( $result, array( 'class' => 'gallerycon') );
    }
    function _HTML_fullImageLink( &$image ){
        return $this->_HTML_link( $image->getURL( AMP_IMAGE_CLASS_ORIGINAL ), $this->_full_image_link_text );

    }
    function _HTML_listItemSource( $source ){
        if ( !$source ) return false;
        return  'by :  '.  $source ;
    }

    function _HTML_listItemLayout( $text, $image ) {
       
        return  "<table" . $this->_HTML_makeAttributes( $this->_layout_table_attr ) . "><tr>" . 
                $this->_HTML_inTD( $text , array( 'class' => $this->_css_class_container_listentry ) ) . 
                $this->_HTML_inTD( $image, array( 'class' => $this->_css_class_container_listimage ) ) .
                $this->_HTML_endTable() . 
                $this->_HTML_newline();
    }


    function _afterPagerInit( ) {
        $this->_pager->setLinkText( 'First Image', 'first') ;
        $this->_pager->setLinkText( 'Last Image', 'last') ;
        $this->_pager->setLinkText( 'Show Entire Gallery', 'all') ;
    }

}
?>

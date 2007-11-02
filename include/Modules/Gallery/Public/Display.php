<?php

require_once( 'AMP/Display/List.php' );
require_once( 'Modules/Gallery/Image.inc.php' );

class Gallery_Public_Display extends AMP_Display_List {
    var $name = 'GalleryImages';
    var $_source_object = 'GalleryImage';
    var $_suppress_messages = true;
//var $_display_columns = 2;
    //var $_sort_default = 'listorder, date DESC, id DESC';
    //
    var $_css_class_container_list = AMP_CONTENT_CSS_CLASS_GALLERY_LIST_CONTAINER ;
    var $_css_class_list_title = AMP_CONTENT_CSS_CLASS_GALLERY_LIST_TITLE; 
    var $_css_class_photocaption = AMP_CONTENT_CSS_CLASS_GALLERY_IMAGE_CAPTION; 
    var $_css_class_container_caption = AMP_CONTENT_CSS_CLASS_GALLERY_CONTAINER_CREDIT; 
    var $_css_class_photocredit = "photocaption";
    var $_css_class_container_list_item = AMP_CONTENT_CSS_CLASS_GALLERY_LIST_ITEM;

    var $_source_gallery;

    var $_pager_active = true;
    var $_pager_limit = AMP_IMAGE_GALLERY_PAGE_LIMIT;

    var $_height_max = 0;
    var $_height_total = 0;
    var $_height_avg = 0;
    var $_image_count = 0;

    var $_class_pager = 'AMP_Display_Pager_Content';
    var $_path_pager = 'AMP/Display/Pager/Content.php';

    function Gallery_Public_Display( &$source, $criteria = array( )) {
        $this->__construct( $source, $criteria );
    }

    function __construct( &$source, $criteria = array( )) {
        $this->set_source_gallery( $source );
        parent::__construct( false, array( 'gallery' => $source->id ) );
    }
	function _after_init() {
    	$this->_display_columns = ceil( $this->qty()/2);
	}
    function set_source_gallery( &$gallery ) {
        $this->_source_gallery = &$gallery;
        if ( $limit = $gallery->getListItemLimit( )) {
            $this->_pager_limit = $limit; 
        }
    }

    function add_to_count( $value) {
        $this->_image_count= $this->_image_count+$value;
    }

    function _renderItem( &$source ) {
        $caption = $this->_renderer->in_P ( converttext( $source->getCaption( )), array( 'class' => $this->_css_class_photocaption)); ;

        $imageRef = &$source->getImageRef( );

        $this->_image_count++;
        $image_height = $imageRef->getHeight( );
        $this->_height_total += $image_height;
        $this->_height_avg = $this->_height_total / $this->_image_count;
        if ( $this->_height_max < $image_height && !( $image_height > ( $this->_height_avg * 2))) {
            $this->_height_max = $image_height;
        }

        $image = $this->_renderer->link( 
                                         //$imageRef->getURL( AMP_IMAGE_CLASS_ORIGINAL ),
                                         $imageRef->get_url_size( 0, $image_height > 600 ? 600 : 0),
                                         $this->_renderer->image( 
                                                    $imageRef->getURL( ), 
                                                            array(  'border' => '1', 
                                                                    )
                                                            ),
                                         array( 'alt' => AMP_TEXT_FULL_SIZE, 'border' => 0, 
                                                'rel' => 'lightbox['. $this->_source_gallery->getName( ).']', 
                                                'title' => $this->render_photo_byline( $source ),
                                                'target' => '_blank' 
                                                ));

        $image_byline = $this->render_byline( $source, $imageRef );

        return    $image 
                . $image_byline 
                . $caption ;

    }

    function _photoByline( $source ) {
        return $this->render_photo_byline( $source );
    }

    function render_photo_byline( &$source ) {
        $credit = $this->_photoCredit( $source );
        $caption = $source->getCaption( );
        if ( !$caption && !$credit ) return ' ';
        if ( !$credit ) return $source->getCaption( );
        return $credit . $this->_renderer->newline( ) . $source->getCaption( );
    }

    function render_photo_credit( $source ) {
        return $this->_photoCredit( $source );
    }

    function _photoCredit( &$source ) {
        $image_desc = '';
        if ( $image_source = $source->getSource( )) {
            $image_desc .= $image_source;
        }

        if ( $image_date = $source->getItemDate( )) {
            $nice_date = str_replace( ' ', '&nbsp;',DoDate( $image_date, AMP_CONTENT_DATE_FORMAT ));
            if ( $image_source && $nice_date ) {
                $image_desc .= ' / ' . $nice_date ;
            }
            
        }

        return $this->_renderer->div( $image_desc, array( 'class' => AMP_CONTENT_CSS_CLASS_GALLERY_IMAGE_CREDIT_TEXT  ));
    }

    function render_byline( $source, $image ) {
        return $this->_renderByline( $source, $image );
    }

    function _renderByline( &$source, $image  ) {
        $image_desc = $this->_photoCredit( $source );

        $image_enlarge_url = $image->getURL( AMP_IMAGE_CLASS_ORIGINAL );
        if ( $image_enlarge_url ) {
            $image_enlarge_link = $this->_renderer->link( 
                                        $image_enlarge_url, 
                                        $this->_renderer->image( AMP_SYSTEM_ICON_ENLARGE, array( 'width' => 15, 'height' => 11, 'class'=>'icon', 'title' => AMP_TEXT_FULL_SIZE )),
                                        array( 'target' => '_blank', 'border' => '0' ) )
                                  .  $this->_renderer->space( 2 );
        } 

        return $this->_renderer->inDiv( $image_enlarge_link .  $image_desc , array( 'class' => $this->_css_class_container_caption ));
    }

    function export_styles( ){
        $float_setting = "";
        if ( $this->_pager_active && ( $this->_pager->get_limit( ) == 1 )) {
            $float_setting = 'padding-left:33%';
        }
        $header = &AMP_get_header( );
        $header->addStylesheetDynamic( "div." . $this->_css_class_container_list_item . " {
            width: " . AMP_IMAGE_WIDTH_WIDE . "px;
            /*height: " .   ( $this->_height_max + 5 ) . "px;*/
            ". $float_setting ."
        }
        div." . $this->_css_class_container_caption . " {
            width: " . AMP_IMAGE_WIDTH_WIDE . "px;
            ". $float_setting ."
        }");
    }

    function _renderHeader( ) {
        return $this->_renderer->inDiv( $this->_source_gallery->getName( ), array( 'class' => $this->_css_class_list_title ))
                . $this->_renderer->inDiv( $this->_renderGallerySelect( ), array( 'style' => 'float:left;'))
                . $this->_renderPagerHeader( );
                //. ( $this->_pager_limit > 1 ? $this->_pager->render_top( ) . $this->_renderer->newline( ) : "" );
    }

    function _renderFooter( ) {
       // $this->export_styles( );
        return
            $this->_renderPager( )
            . $this->_renderer->newline( 2 );

    }

    function _renderGallerySelect( ){
        $sel_attr = array( 
                'class' => 'go', 
                'onchange' => 'AMP_openURL( "'.AMP_CONTENT_URL_GALLERY.'?id="+this.value );'
            );
        return AMP_buildSelect( 'gallery_jump', AMPContent_Lookup::instance( 'galleryMap' ), $this->_source_gallery->id, $this->_renderer->makeAttributes( $sel_attr) );

    }
    function get_source_gallery( ) {
        return $this->_source_gallery;
    }

    function _renderJavascript( ) {
        $header = AMP_get_header( );
        $header->addJavaScript( '/scripts/ajax/prototype.js', 'prototype');
        $header->addJavaScript( '/scripts/ajax/scriptaculous.js?load=effects', 'scriptaculous');
        $header->addJavaScript( '/scripts/lightbox/js/lightbox.js', 'lightbox');
        $header->addExtraHtml( '<link rel="stylesheet" href="http://local_pink.org/scripts/lightbox/css/lightbox.css" type="text/css" media="screen" />' );

    }

}

?>

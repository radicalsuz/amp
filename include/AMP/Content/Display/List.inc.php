<?php

require_once( 'AMP/Content/Display/HTML.inc.php' );
require_once( 'AMP/Content/Display/Pager.inc.php' );
if (!defined( 'AMP_CONTENT_MAIN_HEADER_HTML' )) define( 'AMP_CONTENT_MAIN_HEADER_HTML', false );

class AMPContent_DisplayList_HTML extends AMPDisplay_HTML {

    var $_layout_table_attr = array(
        'width'         => '100%',
        'border'        => '0',
        'cellspacing'   => '0',
        'cellpadding'   => '0' );
    var $_thumb_attr = array(
        'vspace' => 2,
        'hspace' => 4,
        'class'  => 'imgpad' );
    var $_max_blurb_length = 9000;

    var $_pager;
    var $_pager_active  = true;
    var $_pager_display = true;
    var $_pager_limit = false;

    var $_source;
    var $_sourceItem_class = 'Article';

    var $_css_class_title    = "listtitle";
    var $_css_class_subtitle = "subtitle";
    var $_css_class_morelink = "go";
    var $_css_class_text     = "text";
    var $_css_class_date     = "bodygreystrong";

    var $_css_id_container_content = "main_content";
    var $_css_class_container_listentry = "list_entry";
    var $_css_class_container_listimage = "list_image";

    var $_list_image_class = AMP_IMAGE_CLASS_THUMB;

	var $_layout_css = false;

    function AMPContent_DisplayList_HTML ( &$source, $read_data = true ) {
        $this->init( $source, $read_data );
    }

    function init( &$source, $read_data = true ) {
		if (defined( 'AMP_CONTENT_LAYOUT_CSS' )) {
			$this->setLayoutCSS(AMP_CONTENT_LAYOUT_CSS);
		}
        $this->_source = &$source;
        $this->_activatePager( );
        if ( $read_data ) $this->_source->readData();
    }

	function setLayoutCSS($css = false) {
		$this->_layout_css = $css;
		return $this->_layout_css;
	}

	function getLayoutCSS() {
		return $this->_layout_css;
	}

    function _activatePager() {
        if ( !$this->_pager_active ) return false;
        $this->_pager = &new AMPContent_Pager( $this->_source );
        if ( !$this->_pager_limit ) {
            $this->_afterPagerInit( );
            return true;
        }
        
        $this->_pager->setLimit( $this->_pager_limit ); 
        $this->_pager->init( $this->_source );
        $this->_afterPagerInit( );
    }

    function execute() {
        if (!$this->_source->makeReady()) return $this->noResultsDisplay();
        $sourceItems = &$this->_buildItems( $this->_source->getArray() );

        return  $this->_HTML_listing( $sourceItems ). 
                ( ($this->_pager_active && $this->_pager_display ) ? $this->_pager->execute() : false ) ;

    }

    function noResultsDisplay( ) {
        return false;
    }

    function addFilter( $filter_name ) {
        if ( method_exists( $this->_source, 'addFilter')) {
            return $this->_source->addFilter( $filter_name );
        }
        return false;
    }

    function getSourceArray() {
        return $this->_source->getArray();
    }

    function setPageLimit( $limit ) {
        $this->_pager_limit = $limit;
        if ( isset( $this->_source ) && isset( $this->_pager)) {
            $this->_pager->setLimit( $limit );
            $this->_pager->setPage( );
            $this->_source->readData( );
        }
    }

    function &_buildItems( $dataset ) {
        return $this->_source->instantiateItems( $dataset, $this->_sourceItem_class );
    }

    function applySearch( $search_values, $run_query = true ){
        return $this->_source->applySearch( $search_values, $run_query );
    }

    function isFirstPage() {
        if (!$this->_pager_active) return true;
        if ($this->_pager->getOffset()) return false;
        return true;
    }

    function allResultsRequested( ){
        if ( !isset( $this->_pager )) return true;
        return $this->_pager->allResultsRequested( );
    }

    function _afterPagerInit( ){
        //interface
    }

    function _HTML_listing( &$sourceItems ) {
        $output = AMP_CONTENT_MAIN_HEADER_HTML;
		$order = 0;
        foreach ($sourceItems as $contentItem ) {
			$order++;
            $output .= $this->_HTML_listItem( $contentItem, array('id' => 'list_entry_'.$order));
        }
        return $this->_HTML_listingFormat( $output );
    }

    function _HTML_listingFormat( $html ) {
        if (!$this->getLayoutCSS()) return $html;
        return $this->_HTML_inDiv( $html, array( 'id' => $this->_css_id_container_content ) );
    }

    function _HTML_listItem( &$contentItem, $attr=array() ) {
       
        $thumb = false;
        if (method_exists( $contentItem, 'getImageRef' )) {
            $thumb  = $this->_HTML_thumbnail( $contentItem->getImageRef() );
        }

        $text_description   = $this->_HTML_listItemDescription( $contentItem );

        return $this->_HTML_listItemLayout( $text_description, $thumb, $attr );

    }

    function _HTML_listItemLayout ( $text, $image, $attr=array() ) {
        if ( $this->getLayoutCSS()) {
            return  $this->_HTML_inDiv( $image . $text, array_merge($attr, array( 'class' => $this->_css_class_container_listentry ) ) );
        }

        return  "<table" . $this->_HTML_makeAttributes( $this->_layout_table_attr ) . "><tr>" . 
                $this->_HTML_inTD( $image, array( 'class' => $this->_css_class_container_listimage ) ) .
                $this->_HTML_inTD( $text , array( 'class' => $this->_css_class_container_listentry ) ) . 
                $this->_HTML_endTable() . 
                $this->_HTML_newline();
    }

    function _HTML_listItemTitle( &$source ) {
        return  $this->_HTML_link( $source->getURL(), $source->getTitle(), array( "class"=>$this->_css_class_title ) ). 
                $this->_HTML_newline();
    }

   
    function _HTML_listItemBlurb( $blurb ) {
        if (!trim( $blurb )) return false;
        return $this->_HTML_inSpan( AMP_trimText( $blurb, $this->_max_blurb_length ) , $this->_css_class_text ) ; 
    }

    function _HTML_listItemDate ( $date ) {
		if (!$date) return false;
        return $this->_HTML_inSpan( DoDate( $date, 'F jS, Y'), $this->_css_class_date ) . $this->_HTML_newline();
    }

    function _HTML_thumbnail( &$image ) {
        if (!$image) return false;
        if ( AMP_IMAGE_CLASS_THUMB == $this->_list_image_class ){
            $reg = &AMP_Registry::instance();
            if ($thumb_attr = $reg->getEntry( AMP_REGISTRY_CONTENT_IMAGE_THUMB_ATTRIBUTES )) {
                $this->_thumb_attr = array_merge( $this->_thumb_attr, $thumb_attr );
            }
        }
        return $this->_HTML_image( $image->getURL( $this->_list_image_class ), $this->_thumb_attr ) ;
    }

    function _HTML_subheader( $subheader ) {
        return $this->_HTML_in_P( $subheader, array( 'class'=>$this->_css_class_subtitle ) );
    }

    function _HTML_moreLink( $href ) {

        $text = 'More&nbsp;'.$this->_HTML_bold( '&raquo;' );
        return $this->_HTML_inSpan( $this->_HTML_link( $href, $text ), $this->_css_class_morelink );
    }

}
?>

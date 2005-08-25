<?php

require_once( 'AMP/Content/Display/HTML.inc.php' );
require_once( 'AMP/Content/Display/Pager.inc.php' );

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

    var $_source;
    var $_sourceItem_class = 'Article';

    function AMPContent_DisplayList_HTML ( &$source ) {
        $this->init( $source );
    }

    function init( &$source ) {
        $this->_source = &$source;
        if ($this->_pager_active) $this->_pager = &new AMPContent_Pager( $this->_source );
        $this->_source->readData();
    }

    function execute() {
        if (!$this->_source->makeReady()) return false;
        $sourceItems = &$this->_buildItems( $this->_source->getArray() );

        return  $this->_HTML_listing( $sourceItems ). 
                ( ($this->_pager_active && $this->_pager_display ) ? $this->_pager->execute() : false ) ;

    }

    function getSourceArray() {
        return $this->_source->getArray();
    }

    function &_buildItems( $dataset ) {
        return $this->_source->instantiateItems( $dataset, $this->_sourceItem_class );
    }

    function isFirstPage() {
        if (!$this->_pager_active) return true;
        if ($this->_pager->getOffset()) return false;
        return true;
    }

    function _HTML_listing( &$sourceItems ) {
        $output = "";
        foreach ($sourceItems as $contentItem ) {
            $output .= $this->_HTML_listItem( $contentItem );
        }
        return $output;
    }

    function _HTML_listItem( &$contentItem ) {
       
        $thumb = false;
        if (method_exists( $contentItem, 'getImageRef' )) {
            $thumb  = $this->_HTML_thumbnail( $contentItem->getImageRef() );
        }

        $text_description   = $this->_HTML_listItemDescription( $contentItem );

        return  "<table" . $this->_HTML_makeAttributes( $this->_layout_table_attr ) . "><tr>" . 
                $this->_HTML_inTD( $thumb ) . 
                $this->_HTML_inTD( $text_description ). 
                $this->_HTML_endTable() . 
                $this->_HTML_newline();
    }

    function _HTML_listItemTitle( &$source ) {
        return  $this->_HTML_link( $source->getURL(), $source->getTitle(), array( "class"=>"listtitle" ) ). 
                $this->_HTML_newline();
    }

   
    function _HTML_listItemBlurb( $blurb ) {
        if (!$blurb) return false;
        return $this->_HTML_inSpan( AMP_trimText( $blurb, $this->_max_blurb_length) , "text" ) ; 
    }

    function _HTML_listItemDate ( $date ) {
		if (!$date) return false;
        return $this->_HTML_inSpan( DoDate( $date, 'F jS, Y'), 'bodygreystrong') . $this->_HTML_newline();
    }

    function _HTML_thumbnail( &$image ) {
        if (!$image) return false;
        return $this->_HTML_image( $image->getURL( AMP_IMAGE_CLASS_THUMB ), $this->_thumb_attr ) ;
    }

    function _HTML_subheader( $subheader ) {
        return $this->_HTML_in_P( $subheader, array( 'class'=>'subtitle' ) );
    }

    function _HTML_moreLink( $href ) {

        $text = 'More&nbsp;'.$this->_HTML_bold( '&raquo;' );
        return $this->_HTML_inSpan( $this->_HTML_link( $href, $text ), "go" );
    }

}
?>
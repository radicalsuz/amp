<?php

require_once ( 'AMP/Content/Display/HTML.inc.php' );
if ( !defined( 'AMP_TEXT_PAGER_NEXT'))      define( 'AMP_TEXT_PAGER_NEXT', 'Next' );
if ( !defined( 'AMP_TEXT_PAGER_PREVIOUS'))  define( 'AMP_TEXT_PAGER_PREVIOUS', 'Prev' );

class AMPSystem_ListPager extends AMPDisplay_HTML {

    var $_default_qty = 50;
    var $_offset = 0;
    var $_qty = 50;
    var $source;

    var $source_total;
    var $page_total;

    var $_pageless_UrlVars = array( );

    var $_css_class_container = "list_pager";
    var $_css_class_container_block = "list_pager_block";
    var $_css_class_standout = 'standout';

    var $_text_next = AMP_TEXT_PAGER_NEXT;
    var $_text_previous = AMP_TEXT_PAGER_PREVIOUS;

    function AMPSystem_ListPager( &$source ) {
        $this->init ( $source );
    }

    function init( &$source ) {
        $this->source = &$source;
        $this->_readRequestPage();
        $this->setPage();
    }

    function setPage() {
        if ( is_array( $this->source )) return $this->setPageArray();
        if ( !is_object( $this->source )) return false;
        $this->source->setLimit( $this->_qty );
        $this->source->setOffset( $this->_offset );
    }

    function setPageArray( ){
        $this->source_total = count($this->source);
        $this->source = array_slice( $this->source, $this->getOffset( ), $this->getLimit( ) );
    }

    function setLimit( $limit ) {
        $this->_qty = $limit;
    }

    function setOffset( $offset ) {
        $this->_offset = $offset;
    }

    function getOffset() {
        return $this->_offset;
    }

    function getLimit() {
        return $this->_qty;
    }

    function _readRequestPage() {
        if (!($varset = AMP_URL_Read())) return false;

        if (isset($varset['offset']) && $varset['offset'] && is_numeric($varset['offset'])) {
            $this->setOffset( $varset['offset']);
        }

        if (isset($varset['qty']) && $varset['qty'] && is_numeric($varset['qty'])) {
            $this->setLimit($varset['qty']);
        }
        if ( $this->allResultsRequested() ){
            $this->setOffset( 0 );
            $this->setLimit( $this->getSourceTotal() );
        }
    }

    function allResultsRequested( ){
        if (!($varset = AMP_URL_Read())) return false;
        return (isset($varset['all']) && $varset['all'] === '1') ;
    }

    function output() {
        $this->readPosition();
        if ( !$this->getSourceTotal( )) return false;
        return  $this->_HTML_inDiv(  $this->_positionText() . $this->_HTML_newline( ). $this->_pageLinks(), array( 'class' => $this->_css_class_container_block ) ). 
                $this->_HTML_newline();
    }

    function execute( ){
        return $this->output( );
    }
    function outputTop() {
        $this->readPosition();
        return  $this->_HTML_inDiv(  $this->_positionText() . $this->_HTML_newline( ) . $this->_pageLinks( false ), array( 'class' => $this->_css_class_container ) ). 
                $this->_HTML_newline( 2);
    }

    function readPosition() {
        $this->getSourceTotal();
        $this->page_total = $this->_offset + $this->_qty;
    }

    function getSourceTotal( $reset = false ) {
        if ((!$reset) && isset($this->source_total)) return $this->source_total;

        if ( is_array( $this->source )) return count( $this->source );
        $this->source_total = $this->source->NoLimitRecordCount();
        return $this->source_total;
    }


    function _positionText() {
        if ( !$this->source_total) return false;
        if ($this->page_total > $this->source_total) $this->page_total = $this->source_total;
        if ($this->page_total) $start = 1;
        if ($this->_offset) $start = $this->_offset + 1;
        return "Displaying ".$this->_positionRange( $start )." of ".$this->source_total;
    }
    function _positionRange( $start ){
        if ( 1 == $this->_qty ) return $start;
        return $start . "-".$this->page_total;

    }

    function _pageLinks( $show_jumps = true ) {
        if ($this->source_total <= $this->_qty ) return false;
        $output = $this->_prevPageLink() . $this->_nextPageLink() . $this->_HTML_newline( );
        if ( $show_jumps ) $output .= $this->_jumpPageLinks() . $this->_HTML_newline( );
        return $output;
    }

    function _jumpPageLinks() {
        $output ="";
        for( $n=0; ($n * $this->_qty ) < $this->source_total; $n++ ) {
            $link = $this->offsetURL( ($n * $this->_qty) );
            $output .= "<a href='$link'>" . ($n+1) . '</a> ';
        }
        return $output;
    }

    function offsetURL( $new_offset ) {
        $page_url_vars = array();
        if ( $new_offset ) $page_url_vars['offset'] ='offset=' . $new_offset ;
        if ( $this->_qty != $this->_default_qty) $page_url_vars['qty'] = 'qty=' . $this->_qty;

         return AMP_Url_AddVars( $_SERVER['PHP_SELF'] , array_merge( $this->_getURLValues() , $page_url_vars )); 
    }

    function _prevPageLink() {
        if (!$this->_offset) return false;
        $href = $this->offsetURL( ($this->_offset - $this->_qty ) );
        if ($this->_offset <= $this->_qty ) $href = $this->offsetURL(0);

        return '<a class="' . $this->_css_class_standout . '" href="'. $href . '">< ' . $this->_text_previous . '</a>&nbsp;&nbsp;';
    }

    function _nextPageLink() {
        if ($this->page_total >= $this->source_total ) return false;
        $href = $this->offsetURL( $this->page_total );

        return '<a class="' . $this->_css_class_standout . '" href="'. $href . '">' . $this->_text_next . '  >></a>&nbsp;';
    }


    function _getURLValues() {
        if (!empty($this->_pageless_UrlVars) ) return $this->_pageless_UrlVars;
        
        $this->_pageless_UrlVars =  AMP_URL_Values();
        unset ($this->_pageless_UrlVars['offset']);
        unset ($this->_pageless_UrlVars['qty']);
        unset ($this->_pageless_UrlVars['id']);
        return $this->_pageless_UrlVars;
    }
}
?>

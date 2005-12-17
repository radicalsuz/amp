<?php

require_once ( 'AMP/System/List/Pager.inc.php' );

if ( !defined( 'AMP_TEXT_PAGER_LAST'))  define( 'AMP_TEXT_PAGER_LAST', 'Last Page');
if ( !defined( 'AMP_TEXT_PAGER_FIRST')) define( 'AMP_TEXT_PAGER_FIRST', 'First Page');
if ( !defined( 'AMP_TEXT_PAGER_ALL'))   define( 'AMP_TEXT_PAGER_ALL', 'Show Complete List');

class AMPContent_Pager extends AMPSystem_ListPager {

    var $_default_qty = 20;
    var $_qty = 20;

    var $_css_class_link      = "pager_link";

    var $_text_first = AMP_TEXT_PAGER_FIRST;
    var $_text_last  = AMP_TEXT_PAGER_LAST;
    var $_text_all   = AMP_TEXT_PAGER_ALL;

    function AMPContent_Pager( &$source ) {
        $this->init( $source );
    }

    function execute() {
        return $this->output();
    }

    function output() {
        $this->getSourceTotal();
        $this->page_total = $this->_offset + $this->_qty;

        $this->readPosition();
        if ( (!($this->source_total > $this->page_total)) && !$this->getOffset() ) return false;

        return  $this->_HTML_inDiv( 
                    $this->_HTML_inSpan( $this->_positionText(), $this->_css_class_link ) . 
                    str_repeat( '&nbsp;', 2 ) .  $this->_HTML_newline() . 
                    $this->_pageLinks() ,
                    array( 'class' => $this->_css_class_container ) ).
                $this->_HTML_newline();
    }

    function _HTML_topNotice( $text = null ) {
        $output = "";
        if ( !( $position = $this->_positionText( ))) return $output;
        if (isset($text)) $output = $text. '&nbsp;:&nbsp;';
        return $this->_HTML_inDiv( $this->_HTML_inSpan( $output .$position, $this->_css_class_link ), array('class'=> $this->_css_class_container ) );
    }


    function _positionText() {
        $this->readPosition();
        if ( (!($this->source_total> $this->page_total)) && !$this->getOffset() ) return false;
        return  PARENT::_positionText();
        #return $this->_HTML_inSpan( PARENT::_positionText(), 'pager_link' );
    }

    function _pageLinks() {
        if ($this->source_total <= $this->_qty ) return false;
        $output = $this->_prevPageLink() . $this->_nextPageLink();
        $output .= $this->_HTML_newline() . $this->_firstPageLink() . $this->_lastPageLink(); 
        $output .= $this->_HTML_newline() . $this->_allItemsLink();
        return $output;
    }

    function _firstPageLink() {
        if (!$this->_offset) return false;
        $href = $this->offsetURL( 0 ); 
        return '<a class="'.$this->_css_class_link .'" href="'. $href . '">&laquo; ' . $this->_text_first .'</a>&nbsp;';
    }

    function _lastPageLink() {
        if ($this->page_total >= $this->source_total ) return false;
        $href = $this->offsetURL( $this->source_total - $this->_qty );

        return '<a class="'.$this->_css_class_link.'" href="'. $href . '">' . $this->_text_last . ' &raquo;</a>&nbsp;';
    }

    function _allItemsLink() {
        if ($this->page_total >= $this->source_total && (!$this->getOffset()) ) return false;
        $href = AMP_URL_AddVars( $this->offsetURL( 0 ), "all=1" );
        return '<a class="'.$this->_css_class_link.'" href="'. $href . '">&laquo; ' . $this->_text_all . ' &raquo;</a>&nbsp;';
    }

    function getSubsetTotal( $subset_field, $subset_value ) {
        if (!($countset = $this->source->getGroupedIndex( $subset_field ))) return false;
        if (isset($countset[ $subset_value ])) return $countset[ $subset_value ];
        return 0;
    }

    function setLinkText( $text, $whichlink ){
        $linktext = '_text_'.$whichlink;
        if ( !isset( $this->$linktext)) return false;
        $this->$linktext = $text;
    }

}

?>

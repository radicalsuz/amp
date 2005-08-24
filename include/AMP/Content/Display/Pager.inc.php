<?php

require_once ( 'AMP/System/List/Pager.inc.php' );

class AMPContent_Pager extends AMPSystem_ListPager {

    var $_default_qty = 20;
    var $_qty = 20;

    function AMPContent_Pager( &$source ) {
        $this->init( $source );
    }

    function execute() {
        return $this->output();
    }

    function output() {
        $this->getSourceTotal();
        $this->page_total = $this->_offset + $this->_qty;
        return  '<div class="list_pager">' . 
                $this->_HTML_inSpan( $this->_positionText(), 'pager_link') . 
                str_repeat( '&nbsp;', 2 ) .  $this->_HTML_newline() . 
                $this->_pageLinks() .
                '</div><BR>';
    }

    function _HTML_topNotice( $text = null ) {
        $output = "";
        if (isset($text)) $output = $text. '&nbsp;:&nbsp;';
        return $this->_HTML_inDiv( $this->_HTML_inSpan( $output .$this->_positionText(), 'pager_link' ), array('class'=>'list_pager' ) );
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
        $output .= "<BR>" . $this->_firstPageLink() . $this->_lastPageLink(); 
        $output .= "<BR>" . $this->_allItemsLink();
        return $output;
    }

    function _firstPageLink() {
        if (!$this->_offset) return false;
        $href = $this->offsetURL( 0 ); 
        return '<a class="pager_link" href="'. $href . '">&laquo; First Page</a>&nbsp;';
    }

    function _lastPageLink() {
        if ($this->page_total >= $this->source_total ) return false;
        $href = $this->offsetURL( $this->source_total - $this->_qty );

        return '<a class="pager_link" href="'. $href . '">Last Page &raquo;</a>&nbsp;';
    }

    function _allItemsLink() {
        if ($this->page_total >= $this->source_total && (!$this->getOffset()) ) return false;
        $href = AMP_URL_AddVars( $this->offsetURL( 0 ), "all=1" );
        return '<a class="pager_link" href="'. $href . '">&laquo; Show Complete List &raquo;</a>&nbsp;';
    }

    function getSubsetTotal( $subset_field, $subset_value ) {
        if (!($countset = $this->source->getGroupedIndex( $subset_field ))) return false;
        if (isset($countset[ $subset_value ])) return $countset[ $subset_value ];
        return 0;
    }
}
?>

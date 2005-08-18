<?php

require_once ( 'AMP/Content/Display/HTML.inc.php' );

class AMPSystem_ListPager extends AMPDisplay_HTML {

    var $_default_qty = 50;
    var $_offset = 0;
    var $_qty = 50;
    var $source;

    var $source_total;
    var $page_total;

    var $_prepared_URL;

    function AMPSystem_ListPager( &$source ) {
        $this->init ( $source );
    }

    function init( &$source ) {
        $this->source = &$source;
        $this->_readRequestPage();
        $this->setPage();
    }

    function setPage() {
        $this->source->setLimit( $this->_qty );
        $this->source->setOffset( $this->_offset );
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
        if (isset($varset['all']) && $varset['all'] === '1') {
            $this->setOffset( 0 );
            $this->setLimit( $this->getSourceTotal() );
        }
    }

    function output() {
        $this->readPosition();
        return '<div class="list_pager">' . $this->_positionText() . $this->_pageLinks() . '</div><BR>';
    }

    function readPosition() {
        $this->getSourceTotal();
        $this->page_total = $this->_offset + $this->_qty;
    }

    function getSourceTotal( $reset = false ) {
        if ((!$reset) && isset($this->source_total)) return $this->source_total;

        $this->source_total = $this->source->NoLimitRecordCount();
        return $this->source_total;
    }


    function _positionText() {
        if ($this->page_total > $this->source_total) $this->page_total = $this->source_total;
        if ($this->page_total) $start = 1;
        if ($this->_offset) $start = $this->_offset;
        return "Displaying $start-".$this->page_total." of ".$this->source_total;
    }

    function _pageLinks() {
        if ($this->source_total <= $this->_qty ) return false;
        $output = "<BR>" . $this->_jumpPageLinks() . "<BR>";
        $output .= $this->_prevPageLink() . $this->_nextPageLink();
        #$output .= '<BR><div style="float:right;">' .$this->_prevPageLink() . $this->_nextPageLink() . '</div><BR>';
        return $output;
    }

    function _jumpPageLinks() {
        $output ="";
        for( $n=0; ($n * $this->_qty ) < $this->source_total; $n++ ) {
            $link = $this->offsetURL( ($n * $this->_qty) );
            $output .= "<a href='$link'>" . ($n+1) . '</a>&nbsp;';
        }
        return $output;
    }

    function offsetURL( $new_offset ) {
         return $_SERVER['PHP_SELF'] . '?' . $this->_prepURLValues() . ($new_offset? ('&offset=' . $new_offset ):"");
    }

    function _prevPageLink() {
        if (!$this->_offset) return false;
        $href = $this->offsetURL( ($this->_offset - $this->_qty ) );
        if ($this->_offset <= $this->_qty ) $href = $this->offsetURL(0);

        return '<a class="standout" href="'. $href . '">< Prev</a>&nbsp;&nbsp;';
    }

    function _nextPageLink() {
        if ($this->page_total >= $this->source_total ) return false;
        $href = $this->offsetURL( $this->page_total );

        return '<a class="standout" href="'. $href . '">Next >></a>&nbsp;';
    }


    function _prepURLValues() {
        if (isset($this->_prepared_URL) && $this->_prepared_URL) return $this->_prepared_URL;
        
        $values = AMP_URL_Values();
        unset ($values['offset']);
        unset ($values['qty']);
        if ($this->_qty != $this->_default_qty) $values['qty'] = 'qty=' . $this->_qty;
        $this->_prepared_URL = join( '&', $values );
        return $this->_prepared_URL;
    }
}
?>

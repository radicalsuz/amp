<?php

class AMPSystem_ListPager {

    var $default_qty = 50;
    var $offset = 0;
    var $qty = 50;
    var $source;

    var $source_total;
    var $page_total;

    var $_prepared_URL;

    function AMPSystem_ListPager( &$source ) {
        $this->init ( $source );
    }

    function init( &$source ) {
        $this->source = &$source;
        $this->_setPage();
        $this->source->setLimit( $this->qty );
        $this->source->setOffset( $this->offset );
    }

    function _setPage() {
        if (!($varset = AMP_URL_Read())) return false;

        if (isset($varset['offset']) && $varset['offset'] && is_numeric($varset['offset'])) {
            $this->offset = $varset['offset'];
        }

        if (isset($varset['qty']) && $varset['qty'] && is_numeric($varset['qty'])) {
            $this->offset = $varset['qty'];
        }
    }

    function output() {
        $this->source_total = $this->source->NoLimitRecordCount();
        $this->page_total = $this->offset + $this->qty;
        return '<div class="list_pager">' . $this->_positionText() . $this->_pageLinks() . '</div><BR>';
    }

    function _positionText() {
        if ($this->page_total > $this->source_total) $this->page_total = $this->source_total;
        if ($this->page_total) $start = 1;
        if ($this->offset) $start = $this->offset;
        return "Displaying $start-".$this->page_total." of ".$this->source_total;
    }

    function _pageLinks() {
        if ($this->source_total <= $this->qty ) return false;
        $output = "<BR>" . $this->_jumpPageLinks() . "<BR>";
        $output .= $this->_prevPageLink() . $this->_nextPageLink();
        #$output .= '<BR><div style="float:right;">' .$this->_prevPageLink() . $this->_nextPageLink() . '</div><BR>';
        return $output;
    }

    function _jumpPageLinks() {
        $output ="";
        for( $n=0; ($n * $this->qty ) < $this->source_total; $n++ ) {
            $link = $this->offsetURL( ($n * $this->qty) );
            $output .= "<a href='$link'>" . ($n+1) . '</a>&nbsp;';
        }
        return $output;
    }

    function offsetURL( $new_offset ) {
         return $_SERVER['PHP_SELF'] . '?' . $this->_prepURLValues() . ($new_offset? ('&offset=' . $new_offset ):"");
    }

    function _prevPageLink() {
        if (!$this->offset) return false;
        $href = $this->offsetURL( ($this->offset - $this->qty ) );
        if ($this->offset <= $this->qty ) $href = $this->offsetURL(0);

        return '<a class="standout" href="'. $href . '">< Prev</a>&nbsp;';
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
        if ($this->qty != $this->default_qty) $values['qty'] = 'qty=' . $this->qty;
        $this->_prepared_URL = join( '&', $values );
        return $this->_prepared_URL;
    }
}
?>

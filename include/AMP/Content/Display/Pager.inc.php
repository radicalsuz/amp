<?php

require_once ( 'AMP/System/List/Pager.inc.php' );

class AMPContent_Pager extends AMPSystem_ListPager {

    var $_default_qty = 20;

    function AMPContent_Pager( &$source ) {
        $this->init( $source );
    }

    function _pageLinks() {
        if ($this->source_total <= $this->_qty ) return false;
        $output .= $this->_prevPageLink() . $this->_nextPageLink();
        $output .= "<BR>" . $this->_firstPageLink() . $this->_lastPageLink() 
        $output .= "<BR>" . $this->_allItemsLink();
        return $output;
    }

    function _firstPageLink() {
        if (!$this->_offset) return false;
        $href = $this->offsetURL( 0 ); 
        return '<a class="go" href="'. $href . '">&laquo; First Page</a>&nbsp;';
    }

    function _lastPageLink() {
        if ($this->page_total >= $this->source_total ) return false;
        $href = $this->offsetURL( $this->source_total - $this->_qty );

        return '<a class="go" href="'. $href . '">Last Page &raquo;</a>&nbsp;';
    }

    function _allItemsLink() {
        if ($this->page_total >= $this->source_total ) return false;
        $href = AMP_URL_AddVars( $this->offsetURL( 0 ), "all=1" );
        return '<a class="go" href="'. $href . '">&laquo; Show Complete List &raquo;</a>&nbsp;';
    }

    function getSubsetTotal( $subset_field, $subset_value ) {
        if (!($countset = $this->source->getGroupedIndex( $subset_field ))) return false;
        if (isset($countset[ $subset_value ]) return $countset[ $subset_value ];
        return 0;
    }
}
?>

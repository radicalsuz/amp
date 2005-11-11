<?php

require_once( 'AMP/Content/Display/List.inc.php');

class AMPTag_Display extends AMPContent_DisplayList_HTML {

    function AMPTag_Display( &$source, $read_data = true ){
        $this->init( $source, $read_data );
    }

    function &_buildItems( $dataset ) {
        foreach( $dataset as $dataItem ){
            if ( !($item = &$this->_source->buildItem( $dataItem ) )) continue;
            $items[ $item->id ] = &$item;
        }
        return $items;
    }

}

?>

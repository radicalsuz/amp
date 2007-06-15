<?php

require_once( 'Modules/Calendar/Public/List.inc.php');

class Calendar_Public_List_Repeating extends Calendar_Public_List {
    var $name = 'CalendarEventsRepeating';
    var $_source_criteria = array( 'live' => 1, 'repeat' => 1 );
    var $_sort_sql_translations = array( 
        'default' => 'lstate, lcity'
    );
    var $_subheader_methods = array( 'getCountryName' );
    
    function Calendar_Public_List_Repeating( $source = false, $criteria = array( )) {
        $source = false;
        $this->__construct( $source, $criteria );
    }

    function _init_criteria( ) {
    //    $this->_init_search( );
        unset( $this->_source_criteria['date']) ;
        unset( $this->_source_criteria['current']) ;
        if ( isset( $_REQUEST['area']) && $_REQUEST['area'] && !isset( $this->_source_criteria['area'] )) {
            $this->_source_criteria['area'] = $_REQUEST['area'];
        }

    }

    function _renderHeader( ) {
        return '<h3>' . 'Weekly, Monthly or other Repeating Events' . '</h3>' . $this->_renderer->newline( );
    }

    function _output_empty( ) {
        $this->message( AMP_TEXT_SEARCH_NO_MATCHES );
    }

    function _renderItem( $source ) {
        $source->mergeData( array( 'date' => false ));
        $item_output = parent::_renderItem( $source );
        return $this->_renderSubheader( $source ) . $item_output;
    }

    function _renderSubheader( &$source, $depth=0 ) {
        $result = parent::_renderSubheader( $source, $depth );
        if ( $this->_current_subheaders[$depth] == 0 && $depth == 0 ) {
            return false;
        }
        return $result;

    }

}

?>

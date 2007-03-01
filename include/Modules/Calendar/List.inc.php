<?php

require_once( 'AMP/System/List/Form.inc.php');
require_once( 'Modules/Calendar/Event.php');

class Calendar_List extends AMP_System_List_Form {
    var $name = "Calendar";
    var $col_headers = array( 
        'Date' => 'date',
        'State' => '_showState',
        'Event' => 'name',
        'Status' => 'publish',
        'ID'    => 'id'
        );
    var $editlink = 'calendar.php';
    var $name_field = 'name';
    var $_source_object = 'Calendar_Event';
    var $_observers_source = array( 'AMP_System_List_Observer');
    var $_pager_active = true;
    var $_pager_limit = 250;
    var $_sort_default = array( 'date DESC' );
    var $_sort_translations_sql = array( 
        'name' => 'event',
        '_showState' => 'lstate' 
        );

    function Calendar_List( &$dbcon, $criteria = array( ) ) {
        if ( !isset( $criteria['current'])) {
            $criteria['current'] = 1;
        }
        $this->init( $this->_init_source( $dbcon, $criteria ) );
    }

    function _formattedDate( &$source, $column_name ) {
        return $this->_dateFormat( $source->getDate( ));
        //return $this->_makePrettyDate( strtotime( $source->getDate( )), $column_name, $source->getData( ));
    }

    function _showState( &$source, $column_name ) {
        $state_value = $source->getState( );
        if ( !is_numeric( $state_value )) {
            return $state_value;
        }
        $state_set = AMP_lookup( 'states');
        if ( isset( $state_set[$state_value ])) {
            return $state_set[ $state_value ];
        }
        return $state_value;
    }
}
?>

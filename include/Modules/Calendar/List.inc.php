<?php

require_once( 'AMP/System/List/Form.inc.php');
require_once( 'Modules/Calendar/Event.php');
require_once( 'Modules/Calendar/List/Request.inc.php' );

class Calendar_List extends AMP_System_List_Form {
    var $name = "Calendar";
    var $col_headers = array( 
        'Date' => 'date',
        'State' => '_showState',
        'Event' => 'name',
        'Status' => 'publish',
        'ID'    => 'id',
        );
    var $editlink = AMP_SYSTEM_URL_EVENT;
    var $_url_add = AMP_SYSTEM_URL_EVENT_ADD;
    var $editlink_uid = false;
    var $previewlink = AMP_CONTENT_URL_EVENT;

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
    var $_actions = array( 'publish', 'unpublish', 'delete', 'export');
//    var $_actions_global = array( 'export' );
    var $_request_class = 'Calendar_List_Request';

    var $_saved_edit_urls = array( );

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

    function _getUrlEdit( $row_data ) {
        if ( isset( $this->_saved_edit_urls[ $row_data['id']])) {
            return $this->_saved_edit_urls[ $row_data['id']];
        }

        $source = &$this->source[ $this->_source_keys[ $this->_source_counter ]];
        $uid =  $source ? $source->getOwner( ) : false ;

        if ( $this->editlink_uid && $uid ) {
            $editlink = isset( $this->_url_edit ) ? $this->_url_edit : $this->editlink;
            $value = AMP_url_add_vars( AMP_SYSTEM_URL_FORM_ENTRY, array( "uid=".$uid  ));
        } else {
            $value = parent::_getUrlEdit( $row_data );
        }

        $this->_saved_edit_urls[$row_data['id']] = $value;
        return $value;
        
    }

    function _HTML_header( ) {
        return $this->list_preview_link( ) . parent::_HTML_header( );
    }

}
?>

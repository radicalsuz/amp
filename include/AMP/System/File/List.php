<?php
require_once( 'AMP/Display/System/List.php');
require_once( 'AMP/System/File/File.php');

class AMP_System_File_List extends AMP_Display_System_List {
    var $_pager_active = true;
    var $_pager_limit = 100;
    var $columns = array( 'select', 'controls', 'name', 'time', );
    var $column_headers = array( 'name' => 'Filename', 'time' => 'Date Uploaded');
    var $_source_object = 'AMP_System_File';

    var $_actions = array( 'delete');
    var $_suppress_edit = true;

    function AMP_System_File_List( $source, $criteria=array( ), $limit = null ) {
        if( !is_array( $source )) $source = null;
        $this->__construct( $source, $criteria, $limit );
    }

    function _search_path( ) {
        return AMP_LOCAL_PATH . DIRECTORY_SEPARATOR . AMP_CONTENT_DOCUMENT_PATH . DIRECTORY_SEPARATOR;
    }

    function render_edit( $source ) {
        if( !$this->_suppress_edit ) return parent::render_edit( $source );
        return false;
    }

    function _generate_source( $criteria = array( ) ) {
        $this->_source_criteria = array( 'path' => $this->_search_path( ));
        $this->_source_sample = $list_source = &new $this->_source_object( );

        //initialize criteria for search
        if ( !empty( $criteria )) {
            $this->_source_criteria = array_merge (    
                $this->_source_criteria, 
                $criteria
                );
        }
        $this->_init_search_form( );
        $this->_init_criteria( );
        $this->_init_sort_glob( $list_source );

        //source created as array of values
        $list_source->assign_criteria( $this->_source_criteria );
        $this->update_pager( $list_source );
        $source = $list_source->find( $this->_source_criteria, $this->_source_object );
        $this->set_source( $source );


    }

    function _init_sort_glob( &$source )  {
        $sort_request = $this->_sort_requested( );
        if( !$sort_request ) return false;
        $this->_sort = $sort_request;

        $direction = $this->_sort_direction_requested( );
        if( $direction == AMP_SORT_DESC ) $this->_sort_direction = AMP_SORT_DESC;

        $this->update_pager_index( $sort_request );
        $source->set_sort_glob( $sort_request, $direction );
    }

    function validate_sort_link( $sort_request ) {
        $allwed_sorts=array( 'name', 'time');
        return ( array_search( $sort_request, $allwed_sorts ) !== FALSE );
    }

    function render_time( $source ) {
        return date( 'M jS, Y', $source->getTime( ));
    }

    function _renderHeader( ) {
        $output = '';
        if( $this->qty( ) >= $this->_pager_limit ) {
            $output .= $this->_renderPagerHeader( );
        }
        $output .= $this->render_search_form( );
        return $output;
    }
}
?>

<?php

class AMP_Display_List {

    var $name;

    var $_source;
    var $_source_object;
    var $_source_criteria = array( );

    var $_url_add;
    var $_url_edit;
    var $_url_detail;

    var $_target_edit = '_top';
    var $_target_detail = '_top';

    var $_item_display_method = '_renderItem';

    var $_renderer;

    var $_pager;
    var $_pager_active = false;
    var $_pager_limit;
    var $_pager_target;

    var $_class_pager = 'AMPContent_Pager';
    var $_path_pager = 'AMP/Content/Display/Pager.inc.php';

    var $_suppress_messages;
    var $_suppress_header;
    var $_suppress_footer;
    var $_suppress_pager;

    var $_display_columns = 1;
    var $_current_subheaders = array( );

    var $_translations;

    var $_css_class_container_list = 'list_block';
    var $_css_class_container_list_item = 'list_item';
    var $_css_class_container_list_column= 'list_column';
    var $_css_class_container_list_subheader = 'list_header';

    var $_sort_default = false;
    var $_sort_sql_default = false;
    var $_sort_sql_translations = array( );
    var $_sort;

    // {{{ constructors: __construct, _after_init

    function AMP_Display_List( $source = false, $criteria = array( ) ) {
        $this->__construct( $source, $criteria );
    }

    function __construct( $source = false, $criteria = array( ) ) {
        $this->_init_source( $source, $criteria  );
        $this->_init_translations( );
        $this->_renderer = AMP_get_renderer();
        $this->_after_init( );
    }

    function _after_init( ) {
        //interface
    }

    // }}}

    function set_source( &$source ) {
        $this->_source = &$source;
        $this->_init_sort( $this->_source );
        $this->_init_pager( $this->_source );
    }


    function execute() {
        //make sure there are items to render
        if ( !$this->_source || empty( $this->_source )) {
            return $this->_output_empty( );
        }

        //verify display method
        $local_method = ( $this->_item_display_method == '_renderItem' ) ;
        if ( !$local_method ){
            if ( !function_exists( $this->_item_display_method )) {
                trigger_error( sprintf(  AMP_TEXT_ERROR_NOT_DEFINED, get_class( $this ), $this->_item_display_method ));
                return $this->_output_empty( );
            }
        }
        $display_method = $this->_item_display_method;

        $items_output = '';
        $items_count = 0;

        $column_output = '';
        $column_length = round( count( $this->_source ) / $this->_display_columns );

        foreach( $this->_source as $source_item ) {
            if ( $local_method ) {
                $single_item_output = $this->_renderItem( $source_item );
            } else {
                $single_item_output = $display_method( $source_item, $this );
            }

            $items_output .= $this->_renderer->inDiv( 
                                $single_item_output,
                                array( 'class' => $this->_css_class_container_list_item )
                                );

            //column-making code
            $items_count++;
            if ( $items_count > $column_length ) {
                $column_output .= $this->_renderColumn( $items_output );
                $items_output = '';
                $items_count = 0;
            }
        }

        //put remaining output into column 
        if ( $items_output ) {
            if ( !$column_output ) {
                return $this->_renderBlock( $items_output );
            } else {
                $column_output .= $this->_renderColumn( $items_output );
            }
        }
        return $this->_renderBlock( $column_output );

    }

    function _renderBlock( $html ) {
        $list_block = $this->_renderer->inDiv( 
                            $html,
                            array( 'class' => $this->_css_class_container_list )
                        );

        $output = '';
        if ( !$this->_suppress_header ) {
            $output .= $this->_renderHeader( );
        }

        $output .= $list_block;

        if ( !$this->_suppress_footer ) {
            $output .= $this->_renderFooter( );
        }
        return $output;

    }

    function _renderColumn( $html ) {
        return $this->_renderer->inDiv( 
                    $html,
                    array( 'class' => $this->_css_class_container_list_column )
                    );

    }


    function _renderItem( &$source ) {
        //default, should be overridden
        $url = false;
        if ( method_exists( $source, 'getURL' )) {
            $url = $source->getURL( );
        }
        return $this->_renderer->link( $url, $source->getName( ), array( 'class' => 'title' ))
                . $this->_renderer->newline( );
    }

    function _renderHeader( ) {

    }

    function _renderFooter( ) {
        if ( $this->_pager_active && !$this->_suppress_pager ) {
            return $this->_pager->execute( );
        }
    }

    function _output_empty( ) {
        $this->message( AMP_TEXT_SEARCH_NO_MATCHES );
    }

    function message( $message_text ) {
        if ( $this->_suppress_messages ) return false;
        $flash = &AMP_System_Flash::instance( );
        $flash->add_message( );
    }

    // {{{ private source create methods: _init_source, _generate_source 

    function _init_source( $source, $criteria ) {
        if ( $source ) {
            //pre-loaded source
            return $this->set_source( $source );
        }

        //no source object defined
        if ( !$source && !isset( $this->_source_object )) {
            trigger_error( sprintf( AMP_TEXT_ERROR_NOT_DEFINED, get_class( $this ), '_source_object'));
            return false;
        }

        //create source from scratch
        $this->_generate_source( $criteria );
    }

    function _generate_source( $criteria = array( ) ) {
        $dbcon = AMP_Registry::getDbcon( );
        $list_source = &new $this->_source_object( $dbcon );
        if ( !empty( $criteria )) {
            $this->_source_criteria = array_merge (    
                $this->_source_criteria, 
                $list_source->makeCriteria( $criteria )
                );
        }
        $this->_init_criteria( );
        $list_sql_source = &$list_source->_getSearchSource( );
        $this->_init_sort_sql( $list_sql_source );
        $this->_init_pager( $list_sql_source );

        $source = $list_source->find( $this->_source_criteria, $this->_source_object );
        $this->set_source( $source );

    }
    // }}}

    // {{{ private _init methods: _init_criteria, _init_translations, _init_pager 

    function _init_criteria( ) {
        //interface, allows for changes to form based on existing criteria
    }

    function _init_translations( ) {
        //interface
    }

    function _init_pager( &$source ){
        if ( !$this->_pager_active || isset( $this->_pager )) {
            return false;
        }
        require_once( $this->_path_pager );
        $pager_class = $this->_class_pager;
        $this->_pager = &new $pager_class( $source );
        
        if ( $this->_pager_limit ) $this->_pager->setLimit( $this->_pager_limit ); 
        if ( $this->_pager_target ) $this->_pager->setTarget( $this->_pager_target ); 
    }
    // }}}

    // {{{ private sort helper methods: _init_sort, _init_sort_sql, _sort_requested, _translate_sort_sql_request

    /**
     * places a SQL sort clause into the source object before the find action takes place
     * this method is much more efficient for large datasets, which should be paged in SQL
     * 
     * @param & $source 
     * @access protected
     * @return void
     */
    function _init_sort_sql( &$source )  {
        $sort_request = $this->_sort_requested( );
        if ( !$sort_request ) {
            if ( !isset( $this->_sort_sql_default )) return;
            $sort_request = $this->_sort_sql_default;
        }
        
        //see if a custom SQL clause has been defined for the requested sort
        $sort_sql = $this->_translate_sort_sql_request( $sort_request, $source );
        if ( !$sort_sql ) return;

        $this->_sort = $sort_request;
        $source->addSort( $sort_sql );
    }

    function _init_sort( &$source ) {
        $sort_request = $this->_sort_requested( );
        if ( !$sort_request ) {
            if ( !isset( $this->_sort_default )) return;
            $sort_request = $this->_sort_default;
        }

        $local_sort_method = '_setSort'.ucfirst( $sort_request );
        $sort_direction = ( isset( $_REQUEST['sort_direction']) && $_REQUEST['sort_direction']) ?
                            $_REQUEST['sort_direction'] : false;

        if ( method_exists( $this, $local_sort_method)) {
            $this->$local_sort_method( $source, $sort_direction );
        }

        $itemSource = &new $this->_source_object ( AMP_Registry::getDbcon( ));
        if( $itemSource->sort( $source, $sort_request , $sort_direction )){
            $this->_sort = $sort_request;
        }
    }

    function _sort_requested( ) {
        return ( isset($_REQUEST['sort']) && $_REQUEST['sort'] ) ? $_REQUEST['sort'] : false; 
    }

    function _translate_sort_sql_request( $sort_request, &$source ){
        if ( !isset( $this->_sort_sql_translations[ $sort_request ])){
            if ( !( $source->isColumn( $sort_request ) 
                && $source->isColumn( str_replace( AMP_SORT_DESC, '', $sort_request )))) {
                return false;
            }
            if ( !( isset( $_REQUEST['sort_direction']))) return $sort_request;
            if ( $_REQUEST['sort_direction'] == AMP_SORT_DESC ) return $sort_request . AMP_SORT_DESC; 
        }
        $translated_sort_request = $this->_sort_sql_translations[ $sort_request ];
        if ( !isset( $_REQUEST['sort_direction']) || $_REQUEST['sort_direction'] != AMP_SORT_DESC ) return $translated_sort_request;

        if ( strpos( $translated_sort_request, AMP_SORT_DESC ) !== FALSE ){
            return str_replace( AMP_SORT_DESC, '', $translated_sort_request );
        }
        return $translated_sort_request . AMP_SORT_DESC; 

    }
    // }}}

}

?>
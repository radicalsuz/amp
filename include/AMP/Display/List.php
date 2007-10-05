<?php

class AMP_Display_List {

    var $list_id;

    var $_source;
    var $_source_object;
    var $_source_criteria = array( );
    var $_source_sample;

    var $_url_add;
    var $_url_edit;
    var $_url_detail;

    var $_target_edit = '_top';
    var $_target_detail = '_top';

    var $_item_display_method = '_renderItem';
    var $_header_display_method = '_renderHeader';
    var $_footer_display_method = '_renderFooter';
    var $_search_create_method = 'create_search_form';

    var $_renderer;

    var $_pager;
    var $_pager_active = false;
    var $_pager_limit;
    var $_pager_limit_first_page;
    var $_pager_target;
    var $_pager_max = AMP_CONTENT_LIST_DISPLAY_MAX;
    var $_pager_index = false;

    var $_class_pager = 'AMP_Display_Pager';
    var $_path_pager = 'AMP/Display/Pager.php';

    var $_search;

    var $_suppress_messages = false;
    var $_suppress_header = false;
    var $_suppress_footer = false;
    var $_suppress_pager  = false;
    var $_suppress_sort_links = false;
    var $_suppress_search_form = false;
    var $_suppress_form = false;

    var $_display_columns = 1;
    var $_current_columns = 0;
    var $_current_subheaders = array( );
    var $_subheader_methods = array( );

    var $_translations;

    var $_css_class_container_list = 'list_block';
    var $_css_class_container_list_item = 'list_item';
    var $_css_class_container_list_column= 'list_column';
    var $_css_class_container_list_column_last= 'list_column list_column_last';
    var $_css_class_container_list_subheader = 'list_header';
    var $_css_class_container_list_column_count = ' list_column_count_';

    var $_sort_default = false;
    var $_sort_sql_default = false;
    var $_sort_sql_translations = array( );
    var $_sort;

    var $api_version = 2;

    // {{{ constructors: __construct, _after_init

    function AMP_Display_List( $source = false, $criteria = array( ), $limit = null ) {
        $this->__construct( $source, $criteria, $limit );
    }

    function __construct( $source = false, $criteria = array( ), $limit = null ) {
        $this->_init_pager( $limit );
        $this->_init_source( $source, $criteria  );
        $this->_init_translations( );
        $this->_init_identity( );
        $this->_init_tools( );

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
        $this->update_pager( $this->_source );
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
        $this->_current_columns = 0;

        $column_output = '';
        $column_length = round( count( $this->_source ) / $this->_display_columns );

        foreach( $this->_source as $source_item ) {
            if ( $local_method ) {
                $single_item_output = $this->_renderItem( $source_item );
            } else {
                $single_item_output = $display_method( $source_item, $this );
            }

            $items_output .= $this->_renderItemContainer( $single_item_output, $source_item );

            //column-making code
            $items_count++;

            if ( $items_count >= $column_length 
                 &&  (( $this->_current_columns + 1 ) < $this->_display_columns )) {
                $column_output .= $this->_renderColumn( $items_output );
                $items_output = '';
                $items_count = 0;
            }
        }

        if ( $items_output || $column_output ) {
            $this->_renderJavascript( );
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

    function _renderItemContainer( $output, $source ) {
        return $this->_renderer->div( 
                                $output,
                                array( 'class' => $this->_css_class_container_list_item )
                                );
    }

    function _renderBlock( $html ) {
        $list_block = $this->_renderer->div( 
                            $html,
                            array( 'class' => $this->_css_class_container_list )
                        );

        $output = '';
        if ( !$this->_suppress_header ) {
            $header_method = $this->_header_display_method;
            $local_method = ( $this->_header_display_method == '_renderHeader' ) ;
            if ( $local_method ) {
                $output .= $this->$header_method( );
            } else {
                $output .= $header_method( $this->_source, $this );
            }
        }

        $output .= $list_block;

        if ( !$this->_suppress_footer ) {
            $footer_method = $this->_footer_display_method;
            $local_method = ( $this->_footer_display_method == '_renderFooter' ) ;
            if ( $local_method ) {
                $output .= $this->$footer_method( );
            } else {
                $output .= $footer_method( $this->_source, $this );
            }
        }
        return $output;

    }

    function _renderColumn( $html ) {
        $this->_current_columns++;
        $css_class_list_column = 
                ( $this->is_last_column(  ) ) ?
                 $this->_css_class_container_list_column_last
                : $this->_css_class_container_list_column;
        $css_class_list_column .= $this->_css_class_container_list_column_count . $this->_display_columns;

        return $this->_renderer->div( 
                      $this->_render_column_header(  )
                    . $html
                    . $this->_render_column_footer(  ),
                    array( 'class' => $css_class_list_column )
                    );
    }

    function is_first_column(  ) {
        return $this->_current_columns == 1;
    }

    function is_last_column(  ) {
        return $this->_current_columns == $this->_display_columns;
    }

    function _render_column_header(  ) {
        //stub
    }

    function _render_column_footer(  ) {
        //stub
    }

    function _renderItem( &$source ) {
        //default, should be overridden
        $url = false;
        if ( method_exists( $source, 'getURL' )) {
            $url = $source->getURL( );
        }
        return      $this->_renderer->link( $url, $source->getName( ), array( 'class' => 'title' ))
                  . $this->_renderer->newline( );
    }

    function _renderHeader( ) {
        //stub
        return $this->render_search_form( ) ;
    }

    function qty( ) {
        if ( $this->_source == false ) return 0;
        return count( $this->_source );
    }

    function _renderFooter( ) {
        return $this->_renderPager( );
    }

    function _renderPager( ) {
        if ( $this->_pager_active && !$this->_suppress_pager ) {
            return  $this->_renderer->newline( 1, array( 'clear' => 'all'))
                    . $this->_pager->execute( );
        }
    }

    function _renderPagerHeader( ) {
        if ( $this->_pager_active && !$this->_suppress_pager ) {
            if( $pager_output = $this->_pager->render_top( )) {
                return  
                    $this->_renderer->newline( 1, array( 'clear' => 'all'))
                    . $pager_output
                    . $this->_renderer->newline( 1, array( 'clear' => 'all'));
            }
        }
    }

    function _renderSubheader( &$source, $depth=0 ) {
        if ( !isset( $this->_subheader_methods[$depth])) return false;
        $header_method = $this->_subheader_methods[$depth];

        $item_header = $source->$header_method( );
        if ( !$item_header || 
            ( isset( $this->_current_subheaders[$depth ])
            && ( $item_header == $this->_current_subheaders[$depth] ))) {
            return false; 
        }
        $this->_current_subheaders[$depth] = $item_header;
        return $this->render_subheader_format( $item_header, $depth );

    }

    //accessor render methods
    function render_item( &$source ) {
        return $this->_renderItem( $source );
    }

    function render_subheader( &$source ) {
        return $this->_renderSubheader( $source );
    }

    function render_subheader_format( $item_header, $depth=0 ) {
        return $this->_renderer->div( 
                    $item_header,
                    array( 'class' => $this->_css_class_container_list_subheader )
                );
    }

    function render_search_form ( ) {
        if ( !isset( $this->_search_form )) return false;

        if ( $this->_suppress_search_form || $this->_suppress_form ) return false;
        return $this->_search_form->execute( );
    }

    function format_date( $value ) {
        if ( !AMP_verifyDateValue( $value )) return false;
        $date_value = strtotime( $value );
        return date( 'M j, Y', $date_value );
    }

    function render_header( &$source ) {
        return $this->_renderHeader( $source );
    }

    function render_footer( &$source ) {
        return $this->_renderFooter( $source );
    }

    function _renderJavascript( ) {
        //do nothing
    }


    function _output_empty( ) {
        $this->message( AMP_TEXT_SEARCH_NO_MATCHES );
    }

    function message( $message_text ) {
        if ( $this->_suppress_messages ) return false;
        $flash = & AMP_System_Flash::instance( );
        $flash->add_message( $message_text );
    }

    function list_item_id( $source ) {
        return $this->list_id . '_item_' . $source->id;
    }

    function suppress( $item ) {
        $suppress_var = '_suppress_'. $item;
        $this->$suppress_var = true;
    }

    function set_display_method( $function_name ) {
        if ( !function_exists( $function_name )) {
            trigger_error( sprintf(  AMP_TEXT_ERROR_NOT_DEFINED, 'AMP', $function_name ));
            return false;
        }
        $this->_item_display_method = $function_name;
    }

    function set_display_header_method( $function_name ) {
        if ( !function_exists( $function_name )) {
            trigger_error( sprintf(  AMP_TEXT_ERROR_NOT_DEFINED, 'AMP', $function_name ));
            return false;
        }
        $this->_header_display_method = $function_name;
    }

    function set_display_footer_method( $function_name ) {
        if ( !function_exists( $function_name )) {
            trigger_error( sprintf(  AMP_TEXT_ERROR_NOT_DEFINED, 'AMP', $function_name ));
            return false;
        }
        $this->_footer_display_method = $function_name;
    }

    // {{{ private source create methods: _init_source, _generate_source 

    function _init_source( $source, $criteria ) {
        $source_valid = ( $source && substr( strtoupper( get_class( $source )), 0, 5 ) != 'ADODB'); 
        if ( $source_valid ) {
            //pre-loaded source
            return $this->set_source( $source );
        }

        //no source object defined
        if ( !$source_valid && !isset( $this->_source_object )) {
            trigger_error( sprintf( AMP_TEXT_ERROR_NOT_DEFINED, get_class( $this ), '_source_object'));
            return false;
        }

        //create source from scratch
        $this->_generate_source( $criteria );
    }

    function _generate_source( $criteria = array( ) ) {
        //create DB access object
        $dbcon = AMP_Registry::getDbcon( );
        $this->_source_sample = $list_source = &new $this->_source_object( $dbcon );

        //initialize criteria for search
        if ( !empty( $criteria )) {
            $this->_source_criteria = array_merge (    
                $this->_source_criteria, 
                $criteria
                );
        }
        $this->_init_search_form( );
        $this->_init_criteria( );

        //do any required sql-based sorting and paging
        $list_sql_source = &$list_source->getSearchSource( $list_source->makeCriteria( $this->_source_criteria ) );
        $this->_init_sort_sql( $list_sql_source );
        $this->update_pager( $list_sql_source );

        //source created as array of values
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

    function _init_tools( ) {
        //interface
    }

    function _init_pager( $limit = null ){
        if ( !$this->_pager_active || isset( $this->_pager )) {
            return false;
        }

        require_once( $this->_path_pager );
        $pager_class = $this->_class_pager;
        $this->_pager = &new $pager_class( );

        if ( isset( $limit ) && $limit )  {
            $this->_pager_limit = $limit;
            $this->_pager_max = $limit;

        }

        if ( $this->_pager->view_all( ) ) {
            if ( !$this->_pager_max ) {
                $this->_pager_active = false;
                unset( $this->_pager );
                return false;
            }
            $this->_pager_limit = $this->_pager_max;
            return false;
        }

        if ( $this->_pager_limit_first_page && $this->_pager->get_offset( ) == 0) {
            $internal_limit = $this->_pager_limit; #|| $this->_pager->get_limit( );
            $this->_pager->set_limit_internal( $internal_limit );
            $this->_pager_limit = $this->_pager_limit_first_page;
        }

        if ( $request_limit = $this->_pager->get_limit( )) {
            if ( ( $this->_pager_limit && ( $request_limit < $this->_pager_limit ))
                || !$this->_pager_limit ) {
                $this->_pager_limit = $request_limit;
            }
        } 

        return true;

        //$this->_pager = &new $pager_class( $source );
        //if ( $this->_pager_limit ) $this->_pager->setLimit( $this->_pager_limit ); 
        //if ( $this->_pager_target ) $this->_pager->setTarget( $this->_pager_target ); 
        
    }

    function update_pager( &$source ) {
        if ( !$this->_pager_active || !isset( $this->_pager )) {
            return false;
        }

        if ( $this->_pager_limit ) $this->_pager->set_limit( $this->_pager_limit ); 
        if ( $this->_pager_target ) $this->_pager->set_target( $this->_pager_target ); 

        if ( $this->_pager_index ) {
            $this->_pager->pull_jumps( $source, $this->_pager_index );
        }

        $total = $this->_pager->total( $source );
        if ( $total > $this->_pager_limit ) {
            $this->_pager->set_total( $total );
            $this->_pager->trim( $source );
        } 

    }

    function _init_identity( ) {
        $this->_init_display_methods( );
    }

    function _init_display_methods( ) {
        $display_id = $this->display_id( );
        if ( defined( 'AMP_RENDER_' . $display_id )) {
            $this->_item_display_method = constant( 'AMP_RENDER_' .$display_id );
        }
    }

    function display_id( ) {
        if ( !isset( $this->list_id )) $this->list_id = strtolower( get_class( $this ));
        $display_id = strtoupper( $this->list_id );

        if ( $display_id == 'AMP_DISPLAY_LIST' ) {
            if ( isset( $this->_source_object )) {
                $display_id .= '_' . $this->_source_object ;
            } elseif ( isset( $this->list_id )) {
                $display_id .= '_' . str_replace( ' ', '_' , $this->list_id );
            } 
        }
        return strtoupper( $display_id );
    }

    function &create_search_form( ) {
        require_once( 'AMP/System/ComponentLookup.inc.php');
        $false = false;
        $map = ComponentLookup::instance( get_class( $this ));
        if ( !( $map && $map->isAllowed( 'search' ))) return $false;

        $search = $map->getComponent( 'search', false);
        if ( !$search ) return $false;

        $search->Build( true );
        return $search ;
    }

    function &_init_search_factory( ) {
        $false = false;
        $search = false;
        $display_id = $this->display_id( );

        if ( defined( 'AMP_RENDER_SEARCH_' . $display_id )) {
            $this->_search_create_method = constant( 'AMP_RENDER_SEARCH_' . $display_id );
        }

        if ( $this->_search_create_method != 'create_search_form') {
            if ( !function_exists( $this->_search_create_method )) {
                trigger_error( sprintf(  AMP_TEXT_ERROR_NOT_DEFINED, 'AMP', $this->_search_create_method ));
                return $false;
            }
            $search_method = $this->_search_create_method;
            $search = &$search_method( $this );
        } 
        if ( !$search ) {
            $search = &$this->create_search_form( );
        }
        return $search;
    }

    function _init_search_form( ) {
        $search = &$this->_init_search_factory( );
        if ( !$search ) return;

        $search_criteria = array( );

        if ( $search->submitted( )){
            $search_criteria = $search->getSearchValues( );
        } else {
            $search->applyDefaults( );
        }

        $this->_search_form = &$search;

        $this->_source_criteria = array_merge( $this->_source_criteria, $search_criteria );

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
        $this->update_pager_index( $sort_sql );
        $source->addSort( $sort_sql );
    }

    function update_pager_index( $sql ) {
        if ( !$this->_pager_index ) return;
        $this->_pager_index = $this->_source_sample->get_select_from_sort( $sql );
    }

    function _init_sort( &$source ) {
        if ( isset( $this->_sort )) return;

        $sort_request = $this->_sort_requested( );
        if ( !$sort_request ) {
            if ( ! $this->_sort_default ) return;
            $sort_request = $this->_sort_default;
        }

        $local_sort_method = '_setSort'.ucfirst( $sort_request );
        $sort_direction = ( isset( $_REQUEST['sort_direction']) && $_REQUEST['sort_direction']) ?
                            $_REQUEST['sort_direction'] : false;

        if ( method_exists( $this, $local_sort_method)) {
            $this->$local_sort_method( $source, $sort_direction );
            return ;
        }

        $itemSource = &new $this->_source_object ( AMP_Registry::getDbcon( ));
        if( $itemSource->sort( $source, $sort_request , $sort_direction )){
            $this->_sort = $sort_request;
            $this->_sort_direction = $sort_direction;
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

        $this->_sort_direction = AMP_SORT_DESC;
        return $this->_reverse_sort_direction( $translated_sort_request );
    }

    function _reverse_sort_direction( $sort_sql ) {
        $clauses = preg_split( "/\s?,\s?/", $sort_sql );
        $reversed_clauses = array( );
        foreach( $clauses as $clause ) {
            if ( strpos( $clause, AMP_SORT_DESC ) !== FALSE ){
                $reversed_clauses[] = str_replace( AMP_SORT_DESC, '', $clause );
            } else {
                $reversed_clauses[] = $clause . AMP_SORT_DESC;
            }

        }
        return join( ',', $reversed_clauses );

    }

    function render_sort_link( $text, $sort_to_request ) {
        if ( $this->_suppress_sort_links ) return $text;
        if ( !$this->validate_sort_link( $sort_to_request )) return $text;

        $url_values = $_GET;
        $url_values['sort'] = $sort_to_request;
        unset( $url_values['sort_direction'] ) ;
        if ( isset( $this->_sort ) && ( $this->_sort == $sort_to_request ) 
             && !( isset( $this->_sort_direction ) && ( $this->_sort_direction == AMP_SORT_DESC ) )) {
            $url_values['sort_direction'] = AMP_SORT_DESC;
        }

        return $this->_renderer->link( 
                AMP_url_add_vars( $_SERVER['PHP_SELF'], AMP_url_build_query( $url_values )),
                $text );

    }

    function validate_sort_link( $sort_request ) {
        $local_method = '_setSort' . AMP_to_camelcase( $sort_request );
        $valid_method = method_exists( $this, $local_method );
        if ( !$valid_method && !isset( $this->_source_sample )) return false;

        if ( !$valid_method ) {
            $valid_method = $this->_translate_sort_sql_request( $sort_request, $this->_source_sample );
        }
        if ( !$valid_method ) {
            $source_method = 'get' . AMP_to_camelcase( $sort_request );
            $valid_method = method_exists( $this->_source_sample, $source_method );
        }
        return $valid_method;
    }

    // }}}

}

?>

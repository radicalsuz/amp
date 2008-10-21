<?php

require_once( 'AMP/Display/System/List.php' );
require_once( 'AMP/Content/Section.inc.php' );

class AMP_Content_Section_List extends AMP_Display_System_List {

    var $columns = array( 'select', 'controls', 'name', 'id', 'status', 'order', 'nav_index' );
    var $column_headers = array( 'name' => 'Section', 'id' => 'ID', 'nav_index' => 'Navigation');
    var $_source_object = 'Section';

    var $_actions = array( 'publish', 'unpublish', 'trash', 'move', 'reorder');
    var $_action_args = array(
            'reorder'   => array( 'order' ), 
            'move'      => array( 'section_id' ), 
        );
    var $_actions_global = array( 'reorder');

    var $_map;


    function AMP_Content_Section_List( $source, $criteria = array( )) {
        $this->__construct( $source, $criteria );
    }

    function _after_init( ) {
        $this->_map = &AMPContent_Map::instance( );
    }

    function render_name( $source ) {
        if ( isset( $this->_sort )) return $this->render_path( $source );

        $value = $this->_renderer->link( $source->get_url_edit( ), $source->getName( ));
        $depth = $this->_map->getDepth( $source->id ) - 1;
        if ( $depth < 1 ) return $value;
        return $this->_renderer->space( $depth*8 ) . $value;
    }

    function render_path( $source ) {

        require_once( 'AMP/Content/Map/Breadcrumb.inc.php');
        $breadcrumb = &AMP_Breadcrumb_Content::instance( );
        $breadcrumb->findSection( $source->id );

        return  $this->_renderer->div( 
                    $source->getName( )
                    . $this->_renderer->newline( )
                    . $this->_renderer->span( $breadcrumb->execute( ) , array( 'class' => 'photocaption' )),
                    array( 'style' => 'padding:10px;'));
    }

    function render_nav_index( $source ){
        return AMP_navCountDisplay_Section( $source->id );
    }

    function render_toolbar_reorder( &$toolbar ) {
        $action = 'reorder';
        return    $this->_renderer->space( )
                . $this->_renderer->separator( )
                . $this->_renderer->space( )
                . $toolbar->renderDefault( $action );
    }

    function render_toolbar_move( &$toolbar ) {
        $section_options = &AMPContent_Lookup::instance( 'sectionMap' );
        if ( $section_options ) {
            $section_options = array( '' => 'Select Section', AMP_CONTENT_MAP_ROOT_SECTION => '__' . AMP_SITE_NAME  . '__') + $section_options ;
        } else {
            $section_options = array( '' => 'Select Section', AMP_CONTENT_MAP_ROOT_SECTION => AMP_SITE_NAME );
        }
        $panel_contents = $this->_renderer->select( 'section_id', null, $section_options, array( 'class' => 'searchform_element') ) ;
        return $toolbar->add_panel( 'move', $panel_contents );

    }

    function renderMove( &$toolbar ) {
        return $this->render_toolbar_move( $toolbar );
    }

    function renderReorder( &$toolbar ) {
        return $this->render_toolbar_reorder( $toolbar );
    }

    function render_content_view( $source ) {
        return $this->_renderer->link( 
            AMP_url_add_vars( AMP_SYSTEM_URL_ARTICLE, array( 'section='. $source->id )),
            $this->_renderer->image( AMP_SYSTEM_ICON_VIEW, array( 'width' => 16, 'height' => 16, 'border' => '0' )),
            array( 'title' => AMP_TEXT_CONTENT_PAGES )
        );
    }

    function render_controls( $source ) {
        return 
              $this->_renderer->div( 
                  $this->render_edit( $source )
                . $this->render_preview( $source )
                . $this->render_content_view( $source )
            , array( 'class' => 'icon list_control' ));

    }

    function _renderBlock( $output ) {
        //$sortable_script = 'Sortable.create( "'.$this->list_id.'", { tag: "tr", only: "list_row", scroll: window  });';
        return 
            $this->root_render_block( 
                $this->_renderer->form( 
                      $this->render_toolbar( )
                      . $this->_renderer->tag( 'table', 
                            $this->_renderer->tag( 'tbody', 
                                  $this->render_column_headers( ) 
                                  . $output
                                ,
                                array( 'id' => $this->list_id, 'class' => 'system' )
                            ))
                      . $this->render_toolbar( ),
                      array( 'name' => $this->list_id, 'action' => AMP_url_update( $_SERVER['REQUEST_URI']), 'method' => 'POST' )
                     )
            )
            //    . AMP_HTML_JAVASCRIPT_START
            //    . $sortable_script
            //    . AMP_HTML_JAVASCRIPT_END
                ;
    }

    function root_render_block( $html ) {
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

    /*
    function render_row( $column_output, $source ) {
        return join( $this->_renderer->newline( ), $column_output );
    }

    function _renderItemContainer( $output, $source ) {
        $row_color_class = ( $this->item_count % 2 ) ? ' list_row_odd' : ' list_row_even';
        //$hover_class = 'list_row_hover';

        return $this->_renderer->tr(
            $output, 
            array( 
                'id'            => $this->list_item_id( $source ), 
                'class'         => 'list_row' . $row_color_class,
                //'onMouseover'   => 'this.addClassName( "'.$hover_class.'" );',
                //'onMouseout'    => 'this.removeClassName( "'.$hover_class.'" );',
                //'onClick'       => '$( "select_'.$this->list_item_id( $source ).'" ).checked = !$( "select_'.$this->list_item_id( $source ) .'").checked;',


                ) 
            );
        
    }
    */

    function render_order( $source ) {
        return 
            $this->_renderer->input( 
                'order['.$source->id.']', 
                $source->getOrder( ), 
                    array(  'id' => 'order_'.$this->list_item_id( $source ), 
                            'size' => '3', 
                            'style' => 'margin-top:1em;',
                            'type'=>'text' )
                );

            // Pause -- the following code is not in use
            // Waiting to finish AJAX listener for reordering
        return
       $this->_renderer->div(  
            $this->_renderer->div( 
                     $this->_renderer->a(  
                            $this->_renderer->image( '/img/' . AMP_ICON_UP, array( 'alt' => 'up arrow', 'border' => 0 )),
                            array(  'title' => 'move higher',
                                    'onclick' => 'AMP_change_list_order( "'.$this->list_id.'", "'.$source->id.'", -1);new Effect.Pulsate( "'.$this->list_item_id( $source ) .'", { from: 0.5, duration: .5, pulses: 3 });$( "'.$this->list_item_id( $source ).'").removeClassName( "list_row_hover");'))
                     . $this->_renderer->a( 
                            $this->_renderer->image( '/img/' . AMP_ICON_DOWN, array( 'alt' => 'down arrow', 'border' => 0 )),
                            array(  'title' => 'move lower',
                                    'onclick' => 'AMP_change_list_order( "'.$this->list_id.'", "'.$source->id.'", 1);new Effect.Pulsate( "'.$this->list_item_id( $source ) .'", { from: 0.5, duration: .5, pulses: 3 });$( "'.$this->list_item_id( $source ).'").removeClassName( "list_row_hover");'))
                ,array( 'style' => 'width: 19px;float:right;')
            )
            . $this->_renderer->input( 
                'order['.$source->id.']', 
                $source->getOrder( ), 
                    array(  'id' => 'order_'.$this->list_item_id( $source ), 
                            'size' => '3', 
                            'style' => 'margin-top:1em;',
                            'type'=>'text' )
                )
            . $this->_renderer->space( )
        ,array( 'style' => 'height: 3em;width:5em;margin-right:.5em')
        ) ;
    }

    function render_toolbar_trash ( &$toolbar ){
        $tool_name = $toolbar->submitGroup . '[trash]';
        $label = AMP_TEXT_TRASH;
        $header = &AMP_get_header();
        $confirm_script = <<<CONFIRM_SCRIPT
        jq( function() {
          jq(':submit[name*=amp_content_section_list][name*=trash]').click( function() {
            var section_names = jq.makeArray( jq('#amp_content_section_list tr.selected').map(function() { 
              return jq.trim(jq('td:eq(2)', this).text()); 
            })).join('\\n\\n');
            return confirmSubmit( "%s\\n\\n" + section_names + "\\n\\n%s" );
          });
        } );
CONFIRM_SCRIPT;
        $confirm_script = sprintf( $confirm_script, AMP_TEXT_LIST_CONFIRM_DELETE_SECTIONS, AMP_TEXT_LIST_CONFIRM_DELETE );
        $header->addJavascriptDynamic( $confirm_script, 'trash_button' );
        return $this->_renderer->submit( $tool_name, $label, $attr ) . $this->_renderer->space( );
        
    }

    function _after_request( ) {
        if (( $this->_request->getPerformedAction( ) != 'trash') 
             && ( $this->_request->getPerformedAction( ) != 'move' )) {
            return;
        }
        ampredirect( $_SERVER['REQUEST_URI']);
        AMP_permission_update( );
    }

}

?>

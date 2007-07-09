<?php

require_once( 'AMP/Display/System/List.php' );
require_once( 'AMP/Content/Nav/Location/Location.php' );

class AMP_Content_Nav_Location_Values extends AMP_Display_System_List {
    var $_source_object = 'AMP_Content_Nav_Location';
    var $_blocks;
    var $_subheader_methods = array( 'getBlock');
    var $_block_end = false;

    function AMP_Content_Nav_Location_Values( $source = false, $criteria = array( )){
        $this->__construct( $source, $criteria );
    }

    function _after_init( ) {
        $this->_blocks = AMP_lookup( 'navBlocks');
        foreach( $this->_blocks as $block_name => $token ) {
            $this->_containment_blocks[$block_name] = $this->list_id . '_' . $block_name;
        }

    }

    function _renderItem( &$source ) {
        return  
                 $this->render_controls( $source )
                . $this->render_name( $source ) 
                . $this->render_position( $source ) 
                ;
    }

    function _renderItemContainer( $output, $source ) {
        return 
             $this->render_subheader( $source )
            . $this->_renderer->simple_li( 
                $output, ( array( 'id' => $this->list_item_id( $source )))
            );
    }

    function _renderBlock( $html ) {
        $sortable_script = '';
        $containment_blocks = join( '", "', $this->_containment_blocks );
        foreach( $this->_containment_blocks as $dom_block_name ) {
            $sortable_script .= 'Sortable.create( "'.$dom_block_name.'", { scroll: window, onUpdate: nav_locations_order_update, dropOnEmpty:true, constraint: false, containment: ["'.$containment_blocks.'"] });' . "\n";
        }
        return 
            $this->_renderer->div( 
                $html . $this->_block_end
                /*
                $this->_renderer->simple_ul( 
                    $html, 
                    array( 'id' => $this->list_id ) )
                */
                , array( 'class' => 'system', 'id' => $this->list_id ) 
                )
                . AMP_HTML_JAVASCRIPT_START
                . $sortable_script
                . AMP_HTML_JAVASCRIPT_END
                ; 
    }

    function _renderJavascript( ) {
        $header = AMP_get_header( );
        $header->addJavascript( '/scripts/nav_layouts.js', 'nav_layouts');
    }

    function render_controls( $source ) {
        return 
              $this->_renderer->div( 
                  $this->render_edit( $source )
                . $this->render_delete( $source )
            , array( 'class' => 'icon list_control' ));

    }

    function render_delete( $source ) {
        return
            $this->_renderer->form( 
                $this->_renderer->input( 'delete_' . $this->list_item_id( $source ), $source->id, array( 'type' => 'image', 'src' => AMP_SYSTEM_ICON_DELETE, 'class' => 'icon', 'alt' => AMP_TEXT_DELETE_ITEM, 'title' => AMP_TEXT_DELETE_ITEM ))
                . $this->_renderer->input( 'action', 'delete', array( 'type' => 'hidden' ))
                . $this->_renderer->input( 'id', $source->id, array( 'type' => 'hidden' ))
                , array( 'class' => 'delete_form', 'method' => 'POST', 'action' => AMP_SYSTEM_URL_NAV_LOCATION ));
            /*
                AMP_url_update( $source->get_url_edit( ), $this->link_vars ),
                $this->_renderer->image( AMP_SYSTEM_ICON_EDIT, array('alt' => AMP_TEXT_EDIT, 'class' => 'icon' )),
                array( 'title' => AMP_TEXT_EDIT_ITEM, 'target' => $this->link_target_edit, 'id' => 'edit_'.$this->list_item_id( $source ) )
                */
    }

    function render_position( $source ) {
        $positions = AMP_lookup( 'navPositions' );
        $position_id = $source->getPosition( );
        if ( !$position_id ) return false;

        $position_name = isset( $positions[ $position_id ]) ? $positions[ $position_id ] : $position_id;

        return $this->_renderer->newline( )
                . $this->_renderer->span( $position_name, array( 'class' => AMP_CONTENT_CSS_CLASS_SYSTEM_FINEPRINT, 'title' => 'Nav Position' ));
    }

    function render_subheader_format( $value, $depth ) {
        $header = parent::render_subheader_format( $value, $depth  );

        $ul = $this->_renderer->simple_ul( '', array( 'id' => $this->list_id . '_' . $value , 'class' => 'nav_locations' ));
        $div = $this->_renderer->div( '', array( 'class'=>'nav_block'));
        $block_start =  str_replace( '</div>', '', $div)
                        . $header
                        . str_replace( '</ul>', '', $ul );
        $output = $this->_block_end . $block_start;
        $this->_block_end = '</ul></div>';
        return $output;

    }
}


?>

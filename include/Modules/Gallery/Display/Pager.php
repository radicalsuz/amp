<?php
require_once( 'AMP/Display/Pager/Content.php');

class Gallery_Display_Pager extends AMP_Display_Pager_Content {

    var $_jump_values = array( );
    var $current_jumps = false;

    function Gallery_Display_Pager( ) {
        $this->__construct( );
    }

    function render_top( ) {
        return 
            $this->_renderer->div( 
                $this->render_controls( ),
                array( 'class' => $this->_css_class_container )
            );
    }

    function render_jump_set( ) {
        if ( $this->_qty_total < $this->_qty_page ) return false;
        if ( empty( $this->_jump_values )) return false;

        $output = '';
        foreach( $this->_jump_values as $offset_url => $image_name ) {
            $pager_thumb = new AMP_System_File_Image( AMP_image_path( $image_name, AMP_IMAGE_CLASS_THUMB ));
            $thumb_class = array( 'pager-thumb');
            if( $offset_url == $_SERVER['REQUEST_URI']) $thumb_class[] ='pager-thumb-current';
            $output[] = 
                            $this->_renderer->link( 
                               $offset_url,
                               $this->_renderer->image( $pager_thumb->display->render_url_for_scaled( $pager_thumb, AMP_IMAGE_GALLERY_PAGER_THUMB_WIDTH ), array( 'class' => join( " ", $thumb_class) ))
                            );
        }
        return $this->_renderer->div( join( "\n", $output ), array( 'class' => 'pager-thumb-set'));
    }

    function render_links( ) {
        return 
            $this->render_all( ) . $this->_renderer->newline( )
            . $this->render_jump_set( );
    }

    function pull_jumps( $source, $index_property = 'name' ) {

        if( $this->current_jumps == ($index_property.'/'.$this->get_limit( ))) return;
        $this->current_jumps = $index_property.'/'.$this->get_limit( );
        if ( is_array( $source )) {
            $source_values = array_values( $source );
            for( $index = 0; $index < count( $source_values ); $index += $this->get_limit( )) {
                $jump_item = $source_values[ $index ];
                $this->_jump_values[ $index ] = $jump_item->getName( );
            }
            return;
        } 
        
        if ( method_exists( $source, 'getLookup')){
            $jump_index = $source->getLookup( $index_property, $sort=true );
        } elseif ( method_exists( $source, 'get_index')) {
            $jump_index= $source->get_index( $index_property, $this->get_limit( ));
        } else {
            $this->_jump_values = array( );
            $this->current_jumps = false;
            return false;
        }

        $jump_values = array_values( $jump_index );
        for( $index = 0; $index < count( $jump_index ); $index = $index + $this->get_limit( )) {
            $this->_jump_values[ $this->url_offset( $index ) ] = $jump_values[ $index ];
        }
    }

}


?>

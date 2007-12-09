<?php
require_once( 'AMP/Content/Section/Public/List.php');

class Section_Public_List_Contents extends Section_Public_List {
    var $_displays_item_display_method = false;

    function Section_Public_List_Contents( $container = false, $criteria = array( ), $limit = null ) {
        $this->__construct( $container, $criteria, $limit );
    }

    function _renderItem( &$source ) {
        $text =     $this->render_subheader_format( $source->getName( ) )
                  . $this->render_blurb( $source );

        return $this->render_image_format( $this->render_image( $source ), $source )
             . $this->render_description_format( $text, $source )
             . $this->render_contents_format( $this->render_contents( $source ), $source )
                ;
    }

    function render_contents_format( $contents, $source ) {
        if( !$contents ) return false;
        return $this->_renderer->div( $contents, array( 'class' => AMP_CONTENT_CSS_CLASS_LIST_CONTENTS ));
    }

    function render_contents( $source ) {
        $display_type = $source->getDisplayClass( );
        $criteria = $source->getDisplayCriteria( );
        $placeholder = array( );
        $display = new $display_type( $placeholder, $criteria, $source->getListItemLimit( ) );

        $display->set_container( $source );
        if( isset( $this->_source_container ) && ( strtolower( get_class( $this->_source_container )) == 'section' ))  {
        } else {
            $display->suppress( 'search_form' );
        }

        $this->_config_container( $source, $display );
        $display->_class_pager=  'AMP_Display_Pager_Morelinkplus';
        $display->_path_pager=   'AMP/Display/Pager/Morelinkplus.php';
        $display->set_pager_limit( $this->_pager_limit, 'first' );
        $display->set_pager_request( array( 'type' => $source->id, 'list' => 'type'));

        return $display->execute( );
    }

    function _config_container( $source, &$display ) {
        $display->set_container( $source );
        
        //set custom display method
        if( $this->_displays_item_display_method ) {
            $display->set_display_method( $this->_displays_item_display_method );
        }
        //cut search form unless specifically defined
        if( !method_exists( $this->_source_container, 'getAllowSearchDisplay')
            || ( !$this->_source_container->getAllowSearchDisplay( )) ) {
            $display->suppress( 'search_form');
        }
        
        if( $search_method = $this->_source_container->getCustomSearch( )) {
            $display->set_display_search_method( $search_method );
        }

    }

    //TODO come up with a better idea
    //set display method affects the sub lists -- to change the main display requires a class override
    function set_display_method( $function_name ) {
        $this->_displays_item_display_method = $function_name;
    }

}
?>

<?php

require_once( 'AMP/Display/List.php');
require_once( 'AMP/Content/Section.inc.php');
require_once( 'AMP/Content/Map.inc.php');
require_once( 'AMP/Content/Article/Public/List.php');

class AMP_Content_Map_Public_List extends AMP_Display_List {
    var $_source_object = 'Section';
    var $_source_criteria = array( 'displayable' => 1 );
    var $_css_class_container_list = 'list_block map_block';

    function AMP_Content_Map_Public_List( $source = null, $criteria = array( ), $limit = null ) {
        $this->__construct( $source, $criteria, $limit );
    }

    function _after_init( ) {
        $this->content_map = AMPContent_Map::instance( );
    }

    function _renderItem( &$source ) {
        $url = $source->getURL( );
        return      $this->_renderer->link( $url, $source->getName( ), array( 'class' => 'title' ))
                  . $this->_renderer->newline( )
                  . $this->render_articles( $source );
    }

    function _renderItemContainer( $output, $source ) {
        $depth = $this->content_map->getDepth( $source->id );
        return $this->_renderer->div( 
                                $output,
                                array( 'class' => $this->_css_class_container_list_item . ' map_' . $depth)
                                );
    }

    function render_articles( $source ) {
        $list = new Article_Public_List( null, array( 'section_logic' => $source->id ), 10 );
        $list->set_display_method( 'AMP_map_display_articles');
        $list->suppress( 'pager');
        return $this->_renderer->div( $list->execute( ), array( 'class' => 'contents'));
    }

}

function AMP_map_display_articles( $source, $list ) {
    $renderer = AMP_get_renderer( );
    return $renderer->link( $source->getURL( ), $source->getName( ))
            . $renderer->newline( );
}


?>

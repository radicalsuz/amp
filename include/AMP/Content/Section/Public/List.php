<?php
require_once('AMP/Display/List.php');
require_once('AMP/Content/Section.inc.php');

class Section_Public_List extends AMP_Display_List {
    var $name = 'Section_List';
    var $_source_object = 'Section';
    var $_suppress_messages = true;

    var $_pager_active = true;
    var $_pager_limit = 100;
    var $_class_pager = 'AMP_Display_Pager_Content';
    var $_path_pager = 'AMP/Display/Pager/Content.php';

    var $_source_criteria = array( 'displayable' => 1 );

    var $_sort_sql_default = 'default';
    var $_sort_sql_translations = array( 
        'default' => 'date2 DESC, id'
    );
	var $_search;

    //section this list represents
    var $_source_container;

	function Section_Public_List ( $container = false, $criteria = array(), $limit = null ) {
        $source = $this->_init_container( $container );
		$this->__construct($source, $criteria, $limit );
	}
    
    function _init_container( $container ) {
        if ( !$container ) return false;
        if ( is_array( $container )) return $container;

        $allowed_containers = array( 'section');
        if ( array_search( strtolower( get_class( $container )), $allowed_containers) === FALSE ) {
            return $container;
        }
        $this->_source_container = &$container;
        return false;
    }
    

    function _renderItem( &$source ) {
        $text =     $this->render_title( $source )
                  . $this->render_blurb( $source );

        $image = $this->render_image( $source );
        return    $this->_renderer->div( $image, array( 'class' => AMP_CONTENT_CSS_CLASS_LIST_IMAGE ))
                . $this->_renderer->div( $text,  array( 'class' => AMP_CONTENT_CSS_CLASS_LIST_DESCRIPTION ) );
    }

    function url_for( $source ) {
        if ( !method_exists( $source, 'getURL' )) return false; 
        return $source->getURL( );
    }


    function render_title( $source ) {
        $url = $this->url_for( $source );
        return $this->_renderer->link( $url, $source->getName( ), array( 'class' => AMP_CONTENT_CSS_CLASS_LIST_ARTICLE_TITLE ))
                  . $this->_renderer->newline( );
    }

    function render_image( &$source ) {
		$image = $source->getImageRef();
		if ( !$image) return false; 
        $img_output = $this->_renderer->image($image->getURL(AMP_IMAGE_CLASS_THUMB));

        $url = $this->url_for( $source ) ;
        if ( !$url ) {
            return $img_output;
        }
        return $this->_renderer->link( $url, $img_output );
    }

    function render_blurb( $source ) {
        return $this->_renderer->div( $source->getBlurb(), array( 'class' => AMP_CONTENT_CSS_CLASS_LIST_BLURB ) );
    }
    
    function _init_sort_sql( &$source )  {
        $sort_options = AMP_lookup( 'list_sort_options_section');
        if ( $sort_options ) {
            $this->_sort_sql_translations = array_merge( $this->_sort_sql_translations, $sort_options );
        }
        return parent::_init_sort_sql( $source );
    }

    function _sort_requested( ) {
        $http_request = parent::_sort_requested( );
        if ( $http_request || !isset( $this->_source_container )) return $http_request;
        if ( !method_exists( $this->_source_container, 'getListSort')) return false;
        return $this->_source_container->getListSort( );
    }

}

?>

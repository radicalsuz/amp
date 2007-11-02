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
    
    function _init_identity( ) {
        parent::_init_identity( );
        if( isset( $this->_source_container )) {
            $this->_css_class_container_list = $this->_css_class_container_list . ' list_' . strtolower( get_class( $this->_source_container )) . '_' . $this->_source_container->id;
        }
    }


    function _renderItem( &$source ) {
        $text =     $this->render_title( $source )
                  . $this->render_blurb( $source );

        return $this->render_image_format( $this->render_image( $source ), $source )
             . $this->render_description_format( $text, $source );
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

    function render_image_format( $image, $source ) {
        $container_class = $image ? AMP_CONTENT_CSS_CLASS_LIST_IMAGE : AMP_CONTENT_CSS_CLASS_LIST_IMAGE_EMPTY ;
        return    $this->_renderer->div( $image, array( 'class' => $container_class ));
    }

    function render_description_format( $description, $source ) {
        if ( !$description ) return false;
        $image = $this->render_image( $source );
        $description_css = $image ? AMP_CONTENT_CSS_CLASS_LIST_DESCRIPTION_WITH_IMAGE : AMP_CONTENT_CSS_CLASS_LIST_DESCRIPTION;
        return $this->_renderer->div( $description,  array( 'class' => $description_css ) );
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

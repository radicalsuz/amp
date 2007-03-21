<?php
require_once('AMP/Display/List.php');
require_once('AMP/Content/Article.inc.php');

class Article_Public_List extends AMP_Display_List {
    var $name = 'Article_List';
    var $_source_object = 'Article';
    var $_suppress_messages = true;

    var $_pager_active = true;
    var $_pager_limit = 100;
    var $_class_pager = 'AMP_Display_Pager_Content';
    var $_path_pager = 'AMP/Display/Pager/Content.php';

    var $_source_criteria = array( 'live' => 1, 'allowed' => 1 );
    //var $_sort_sql_default = 'default';
    var $_sort_sql_translations = array( 
        'default' => 'date DESC, id'
    );
	var $_search;

    var $_css_class_author = "bodygreystrong";
    var $_css_class_source = "bodygreystrong";
    var $_css_class_date   = "bodygreystrong";

	function Article_Public_List ( $source = false, $criteria = array()) {
		$source = false;
		$this->__construct($source, $criteria );
	}

    function _init_criteria( ) {
        $this->_init_search( );
		if (!isset($this->_source_criteria['fulltext'])) {
			$this->_sort_sql_default = 'default';
		}
	}

    function _init_search( ) {
        require_once( 'AMP/System/ComponentLookup.inc.php');
        $map = ComponentLookup::instance( get_class( $this ));
        $search = $map->getComponent( 'search');
        if ( !$search ) return;
        $search->Build( true );
        $search_criteria = array( );

        if ( $search->submitted( )){
            $search_criteria = $search->getSearchValues( );
        } else {
            $search->applyDefaults( );
        }

        $this->_search = &$search;

        $this->_source_criteria = array_merge( $this->_source_criteria, $search_criteria );
    }

    function _output_empty( ) {
        $this->message( AMP_TEXT_SEARCH_NO_MATCHES );
        return $this->render_search( ) . AMP_TEXT_SEARCH_NO_MATCHES;
    }

    function _renderHeader( ) {
        return $this->render_search( ) ;

    }

    function render_search( ) {
        if ( !isset( $this->_search )) return false;
        return $this->_search->execute( );
    }

    function _renderItem( &$source ) {
        $url = false;
        if ( method_exists( $source, 'getURL' )) {
            $url = $source->getURL( );
        }
		$image = $source->getImageRef();
		$rendered_image = false;
		if ($image) {
			$rendered_image = $this->_renderer->image($image->getURL(AMP_IMAGE_CLASS_THUMB), array('align'=>'right'));
		}
        return      $this->_renderer->link( $url, $source->getName( ), array( 'class' => 'title' ))
                  . $this->_renderer->newline( )
				  . $this->render_date($source)
				  . $this->render_source($source)
				  . $rendered_image
				  . $this->_renderer->in_P( $source->getBlurb() );
    }

    function render_source( &$article ) {
		$author = $article->getAuthor();
		$source_name = $article->getSource();
		$source_url = $article->getSourceUrl();

        if (!(trim($author) || $source_name || $source_url)) return false;
        $output_author = FALSE;
        $output_source = FALSE;

        if (trim($author)) {
            $output_author =  $this->_renderer->inSpan( 'by&nbsp;' . converttext($author), $this->_css_class_author );
            if (!$source) return $output_author . $this->_renderer->newline();
        }

        if ($source) $output_source = $this->_renderer->inSpan( $this->_renderer->link( $source_url, $source_name  ), $this->_css_class_source );

        if ($output_author && $output_source) return $output_author . ',' . $this->_renderer->space() . $output_source . $this->_renderer->newline();

        return $output_source . $this->_renderer->newline();
    }

	function render_date( &$source ) {
		$date = $source->getItemDate();
		if (!$date) return false;

        return $this->_renderer->inSpan( DoDate( $date, 'F jS, Y'), $this->_css_class_date ) . $this->_renderer->newline();
	}
}

?>

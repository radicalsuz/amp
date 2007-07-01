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

    var $_source_criteria = array( 'displayable' => 1 );

    var $_sort_sql_default = 'default';
    var $_sort_sql_translations = array( 
        'default' => 'date DESC, id'
    );
	var $_search;

	function Article_Public_List ( $source = false, $criteria = array(), $limit = null ) {
		$source = false;
		$this->__construct($source, $criteria, $limit );
	}
    

    function _renderItem( &$source ) {
        $text =     $this->render_title( $source )
				  . $this->render_source($source )
				  . $this->render_date(  $source )
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

    function render_source( &$article ) {
		$author = $article->getAuthor();
		$source_name = $article->getSource();
		$source_url = $article->getSourceUrl();

        if (!(trim($author) || $source_name || $source_url)) return false;

        $output_author = FALSE;
        $output_source = FALSE;

        if (trim($author)) {
            $output_author =  $this->_renderer->span( sprintf( AMP_TEXT_BYLINE_SLUG, converttext($author)), array( 'class' => AMP_CONTENT_CSS_CLASS_LIST_ARTICLE_AUTHOR ));
        }

        if ($source_name || $source_url)  {
            if ( !$source_name ) {
                $source_name = $source_url;
            }
            $output_source = $this->_renderer->span( $this->_renderer->link( $source_url, $source_name  ), array( 'class' => AMP_CONTENT_CSS_CLASS_LIST_ARTICLE_SOURCE ));
        }

        if (!$output_author){
            return $output_source . $this->_renderer->newline();
        }
        if ( !$output_source ) {
            return $output_author . $this->_renderer->newline();
        }

        return    $output_author . ',' . $this->_renderer->space() 
                . $output_source . $this->_renderer->newline();
    }

	function render_date( &$source ) {
		$date = $source->getItemDate();
		if (!$date) return false;

        return $this->_renderer->span( DoDate( $date, 'F jS, Y'), array( 'class' => AMP_CONTENT_CSS_CLASS_LIST_ARTICLE_DATE )) 
                . $this->_renderer->newline();
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
    
}

?>

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

    //section, class, or tag this list represents
    var $_source_container;

	function Article_Public_List ( $container = false, $criteria = array(), $limit = null ) {
		$this->__construct($container, $criteria, $limit );
	}

    function __construct( $container= false, $criteria = array( ), $limit = null ) {
        $source = $this->_init_container( $container );
        parent::__construct( $source, $criteria, $limit );
        /*
        $this->_init_pager( $limit );
        $this->_init_source( $source, $criteria  );
        $this->_init_translations( );
        $this->_init_identity( );
        $this->_init_tools( );

        $this->_renderer = AMP_get_renderer();
        $this->_after_init( );
        */
    }

    function _init_container( $container ) {
        if ( !$container ) return false;
        if ( is_array( $container )) return $container;

        $allowed_containers = array( 'section', 'contentclass', 'amp_content_tag');
        if ( array_search( strtolower( get_class( $container )), $allowed_containers) === FALSE ) {
            return $container;
        }
        $this->_source_container = &$container;
        return false;
    }
    

    function _renderItem( &$source ) {
        $text =     $this->render_title( $source )
				  . $this->render_source($source )
				  . $this->render_date(  $source )
                  . $this->render_blurb( $source );

        $image = $this->render_image( $source );
        $image_css = $image ? AMP_CONTENT_CSS_CLASS_LIST_IMAGE : AMP_CONTENT_CSS_CLASS_LIST_IMAGE_EMPTY;
        return    $this->_renderer->div( $image, array( 'class' => $image_css ))
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

        return    $output_author . ', '
                . $output_source . $this->_renderer->newline();
    }

	function render_date( &$source ) {
		$date = $source->getItemDate();
		if (!$date) return false;

        return $this->_renderer->span( DoDate( $date, AMP_CONTENT_DATE_FORMAT), array( 'class' => AMP_CONTENT_CSS_CLASS_LIST_ARTICLE_DATE )) 
                . $this->_renderer->newline();
	}

    function render_image( &$source ) {
		$image = $source->getImageRef();
		if ( !$image) return $this->render_image_media_thumbnail( $source ); 

        $img_output = $this->_renderer->image($image->getURL(AMP_IMAGE_CLASS_THUMB));

        $url = $this->url_for( $source ) ;
        if ( !$url ) {
            return $img_output;
        }
        return $this->_renderer->link( $url, $img_output );
    }

    function render_image_media_thumbnail( $source ) {
        $image_url = $source->getMediaThumbnailUrl( );
        if ( !$image_url ) return false;
        return $this->_renderer->link(  
                    $this->url_for( $source ), 
                    $this->_renderer->image( $image_url )
                    );

    }

    function render_blurb( $source ) {
        return $this->_renderer->div( $source->getBlurb(), array( 'class' => AMP_CONTENT_CSS_CLASS_LIST_BLURB ) );
    }
    
    function _init_sort_sql( &$source )  {
        $sort_options = AMP_lookup( 'list_sort_options_article');
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

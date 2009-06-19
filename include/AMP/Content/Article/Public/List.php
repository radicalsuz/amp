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
        'default' => 'date DESC, id DESC'
    );

    var $_css_class_author = AMP_CONTENT_CSS_CLASS_LIST_ARTICLE_AUTHOR ;
    var $_css_class_source  = AMP_CONTENT_CSS_CLASS_LIST_ARTICLE_SOURCE;
    var $_css_class_container_list = "article_public_list list_block";

	var $_search;
	var $_search_fields_xml = 'AMP/Content/Article/Public/Search/Fields.xml';

    //section, class, or tag this list represents
    var $_source_container;

	function Article_Public_List ( $container = false, $criteria = array(), $limit = null ) {
		$this->__construct($container, $criteria, $limit );
	}

    function __construct( $container= false, $criteria = array( ), $limit = null ) {
        $source = $this->_init_container( $container );
        parent::__construct( $source, $criteria, $limit );
    }

    function _init_identity( ) {
        parent::_init_identity( );
        if( isset( $this->_source_container )) {
            $this->_css_class_container_list = $this->_css_class_container_list . ' list_' . strtolower( get_class( $this->_source_container )) . '_' . $this->_source_container->id;
        }
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

    function _init_pager( $limit = null ) {
        $has_pager = parent::_init_pager( $limit );
        if( $has_pager && isset( $this->_source_container )) {
            $this->_pager->describe( $this->_source_container->getName( ));
        }
    }

    function set_container( &$container ) {
        $this->_source_container = &$container;
    }
    
    function _init_display_methods( ) {
        if ( $this->_item_display_method == '_renderItem' ) {
            return parent::_init_display_methods( );
        }
    }

    function _renderItem( &$source ) {
        $text =     $this->render_title( $source )
				  . $this->render_byline($source )
				  . $this->render_date(  $source )
                  . $this->render_blurb( $source );

        $image = $this->render_image( $source );
        $image_css = $image ? AMP_CONTENT_CSS_CLASS_LIST_IMAGE : AMP_CONTENT_CSS_CLASS_LIST_IMAGE_EMPTY;
        $description_css = $image ? AMP_CONTENT_CSS_CLASS_LIST_DESCRIPTION_WITH_IMAGE : AMP_CONTENT_CSS_CLASS_LIST_DESCRIPTION;
        return    $this->_renderer->div( $image, array( 'class' => $image_css ))
                . $this->_renderer->div( $text,  array( 'class' => $description_css ));
    }

    function _renderHeader( ) {
        //stub
        return $this->render_search_form( ) 
                . $this->_renderPagerHeader( );
    }

    function &_init_search_factory( ) {
        $false = false;
        //check to see if a custom search method has been defined, the parent method will call that
        if( ( $this->_search_create_method == 'create_search_form') && isset( $this->_source_container ) && method_exists( $this->_source_container, 'getAllowSearchDisplay')) {
            if ( method_exists( $this->_source_container, 'getCustomSearch') && ( $search_method = $this->_source_container->getCustomSearch( ))) {
                $this->set_display_search_method( $search_method );
            }
        }
        $base_search = &parent::_init_search_factory( );
        return $base_search;
    }

    function &create_search_form( ) {
        $false = false;
        //if this is not a section based list, use the parent search create method
        if( !( isset( $this->_source_container ) && ( strtolower( get_class( $this->_source_container )) == 'section' ))) {
            $result = &parent::create_search_form( );
            return $result;
        }
        //if the section does not allow search display, return false
        if ( !$this->_source_container->getAllowSearchDisplay( ))  return $false;

        //have a search created based on xml
        $search = $this->create_search_form_from_xml( );
        return $search;

    }

    function &create_search_form_from_xml( ) {
        $search = parent::create_search_form_from_xml( );
        if( !$search ) return $search;
        
        //set defaults for the current section
        $search->add_field( 'section_logic', array( 'type' => 'hidden', 'value' => $this->_source_container->id ));
        $search->add_field( 'template_section', array( 'type' => 'hidden', 'value' => $this->_source_container->id ));
        return $search;

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

    /*
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
    */

    function render_byline( $source ) {

        $output_author = $this->render_author( $source );
        $output_source = $this->render_source( $source );

        if ( !( $output_source || $output_author )) return false;

        if (!$output_author){
            return $output_source . $this->_renderer->newline();
        }
        if ( !$output_source ) {
            return $output_author . $this->_renderer->newline();
        }

        return    $output_author . ',' . $this->_renderer->space() 
                . $output_source . $this->_renderer->newline();
    }

    function render_author( $source ) {
		$author = $source->getAuthor();

        if (!trim($author)) return false;
        return $this->_renderer->span( sprintf( AMP_TEXT_BYLINE_SLUG, converttext($author)), array( 'class' => $this->_css_class_author ));
    }

    function render_source( $source ) {
		$source_name = $source->getSource();
		$source_url = $source->getSourceUrl();
        if (!( $source_name || $source_url )) return false;
        if ( !$source_name ) {
            $source_name = $source_url;
        }
        return $this->_renderer->span( $this->_renderer->link( $source_url, $source_name  ), array( 'class' => $this->_css_class_source ));
    }

	function render_date( &$source ) {
		$date = $source->getItemDate();
		if (!$date) return false;

        return $this->_renderer->span( DoDate( $date, AMP_CONTENT_DATE_FORMAT), array( 'class' => AMP_CONTENT_CSS_CLASS_LIST_ARTICLE_DATE )) 
                . $this->_renderer->newline();
	}

    function render_image( &$source ) {
		$image = $source->getImageFile();
		if ( !$image) return $this->render_image_media_thumbnail( $source ); 

        $img_output = $this->_renderer->image( AMP_image_url( $image->getName(), AMP_IMAGE_CLASS_THUMB));

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
        if( !( $blurb = $source->getBlurb( ))) return false;
        return $this->_renderer->div( $source->getBlurb(), array( 'class' => AMP_CONTENT_CSS_CLASS_LIST_BLURB ) );
    }
    
    function _output_empty( ) {
        return $this->render_search_form( );
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

    function addFilter( $filter_name, $filter_var=null ) {
        $filter_def = isset( $filter_var ) ? array( 'name' => $filter_name, 'var' => $filter_var ) : $filter_name;
        $this->_source_criteria['filter'] = $filter_def;
        $this->__construct( false, $this->_source_criteria );
    }

}

?>

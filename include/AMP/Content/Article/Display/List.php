<?php

require_once( 'AMP/Display/List.php');
require_once( 'AMP/Content/Article.inc.php');

class AMP_Content_Article_Display_List extends AMP_Display_List {

    var $_css_class_container_list = 'main_content';

    var $_css_class_title    = "listtitle";
    var $_css_class_subtitle = "subtitle";
    var $_css_class_morelink = "go";
    var $_css_class_text     = "text";
    var $_css_class_date     = "bodygreystrong";
    var $_css_class_author = "bodygreystrong";
    var $_css_class_source = "bodygreystrong";
    var $_css_class_image  = "list_image";
    var $_css_class_description_block = "list_description";

    var $_list_image_class = AMP_IMAGE_CLASS_THUMB;
    var $_thumb_attr = array(
        'vspace' => 2,
        'hspace' => 4,
        'class'  => 'imgpad' );

	var $_base_object;
    var $_source_object = 'Article';
    var $_pager_active = true;

    var $_sort_sql_default = 'default';
    var $_sort_sql_translations = array( 
        'default' => "if(isnull(pageorder) or pageorder='', 9999999999, pageorder) ASC, date DESC, id DESC"
    );

    var $_class_pager = 'AMP_Display_Pager_Content';
    var $_path_pager = 'AMP/Display/Pager/Content.php';

    function AMP_Content_Article_Display_List( $source = false, $criteria = array( )) {
        $this->__construct( $source, $criteria );
    }

	function __construct( $source, $criteria = array( )) {
		if (is_object($source)){
			$this->_base_object = $source;
			if ( method_exists( $source, 'getListItemLimit' )) {
				$limit = $source->getListItemLimit();
				if ($limit) $this->_pager_limit = $limit;
			}
		} 
		return parent::__construct( false , $criteria);
	}

    function _after_init( ) {
        $this->_init_thumbnail_attributes( );
    }

    function _renderItem( &$source ) {
        $text =  
            $this->render_title( $source )
            . $this->render_source( $source )
            . $this->render_date( $source )
            . $this->render_blurb( $source );
        $media = 
            $this->render_media( $source );
        if ( !$media ) {
            $media = $this->render_image( $source );
        }

        $output = $this->render_item_layout( $source, $text, $media );
        return $output;
    }

    function render_item_layout( &$source, $text, $media ) {
        return    $this->_renderer->div( $media , array( 'class' => $this->_css_class_image ))
                . $this->_renderer->div( $text, array( 'class' => $this->_css_class_description_block ));
    }

    function render_title( &$source ) {
        //return $this->_renderer->link( $source->getURL( ), $source->getName( ), array( 'class' => $this->_css_class_title ))
        return $this->_renderer->div( 
                        $this->_renderer->link( $source->getURL( ), $source->getName( )),
                        array( 'class' => $this->_css_class_title  ));
    }

    function render_date( &$source ) {
        $date = $source->getItemDate( );
		if (!$date) return false;
        return $this->_renderer->span( DoDate( $date, AMP_CONTENT_DATE_FORMAT), $this->_css_class_date ) . $this->_renderer->newline();
    }

    function render_source( &$item ) {
        $author = $item->getAuthor( );
        $source = $item->getSource( );
        $url = $item->getSourceURL( );
        if (!(trim($author) || $source || $url)) return false;

        if (trim($author)) {
            $output_author =  $this->_renderer->span( 'by&nbsp;' . converttext($author), $this->_css_class_author );
            if (!$source) return $output_author . $this->_renderer->newline();
        }

        if ($source) $output_source = $this->_renderer->span( $this->link( $url, $source  ), $this->_css_class_source );

        if ($output_author && $output_source) return $output_author . ',&nbsp;' . $output_source . $this->_renderer->newline();

        return $output_source . $this->_renderer->newline();
    }

    function render_blurb( &$source ) {
        $blurb = $source->getBlurb( );
        if (!trim( $blurb )) return false;
        return $this->_renderer->span( AMP_trimText( $blurb, AMP_CONTENT_ARTICLE_BLURB_LENGTH_MAX ) , $this->_css_class_text ) ; 
    }

    function render_image( &$source ) {
        if ( !method_exists( $source, 'getImageRef')) return false;
        $image = $source->getImageRef( );
        if ( !$image ) return false;
        return $this->_renderer->image( $image->getURL( $this->_list_image_class ), $this->_thumb_attr ) ;
    }

    function render_media( &$source ) {
        //eventually this should return a youtube thumbnail
        return false;
    }


    function _init_thumbnail_attributes( ) {
        if ( AMP_IMAGE_CLASS_THUMB == $this->_list_image_class ){
            $reg = &AMP_Registry::instance();
            if ($thumb_attr = $reg->getEntry( AMP_REGISTRY_CONTENT_IMAGE_THUMB_ATTRIBUTES )) {
                $this->_thumb_attr = array_merge( $this->_thumb_attr, $thumb_attr );
            }
        }

    }

    function addFilter( $filter_name, $filter_var = null  ) {
        trigger_error( AMP_TEXT_ERROR_NOT_DEFINED , get_class( $this), 'addFilter' );
        return;
    }

    function setPageLimit( $limit ) {
        $this->_pager_limit = $limit;
    }

    function allResultsRequested(  ) {
        if ( !$this->_pager_active ) return false;
        if ( isset( $this->_pager ) ) return $this->_pager->view_all(  );
    }

    function isFirstPage(  ) {
        return true;
    }

    function _render_column_header(  ) {
        return "<a name=column_top></a>";
    }

    function _render_column_footer(  ) {
        static $arrow_link = '';
        if ( $this->is_last_column(  ) ) return;

        if ( $arrow_link ) return $arrow_link;

        if ( AMP_ICON_COLUMN_FOOTER ) {
            require_once( "AMP/Content/Image.inc.php");
            $image = new Content_Image( AMP_ICON_COLUMN_FOOTER );
            $icon_value = $this->_renderer->image(
                        $image->getURL( AMP_IMAGE_CLASS_ORIGINAL ),
                        array( 'align' => 'right', 'border' => '0'));

        } else {
            $icon_value = '&uarr;';
        }

        $arrow_link =   $this->_renderer->newline( )
                      . $this->_renderer->link( AMP_Url_AddAnchor( $_SERVER['REQUEST_URI'], 'column_top' ) ,
                                         $icon_value,
                                         array( 'alt' => 'Next Column', 'border' => 0, 'style' => 'float:right;' ));
        return $arrow_link;
    }

}

?>

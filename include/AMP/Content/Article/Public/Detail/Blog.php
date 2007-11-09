<?php
require_once( 'AMP/Content/Article/Public/Detail.php');

class Article_Public_Detail_Blog extends Article_Public_Detail {

    var $_css_class_date = 'date';
    function Article_Public_Detail_Blog( $source ) {
        $this->__construct( $source );
    }

    function renderItem( $source ) {
        return  $this->render_title( $source )
                . $this->render_byline( $source )
                . $this->render_image( $source )
                . $this->render_body( $source )
                . $this->render_sections_format( $source )
                ;
    }

    function render_byline( $source ) {
        $author = $source->getAuthor( );
        $date = $source->getItemDate( );
        return $this->_renderer->span( sprintf( AMP_TEXT_POSTED_BY, $author, DoDate( $date, AMP_CONTENT_DATE_FORMAT)), $this->_css_class_date );
    }

    function render_sections( $source ) {
         $section_list = $this->load_live_sections( $source );
         $sections = array( );
         foreach( $section_list as $section_id => $section_name ) {
            $sections[] = $this->_renderer->link( AMP_url_update( AMP_CONTENT_URL_LIST_CLASS, array( 'type' =>  $section_id, 'class' => AMP_CONTENT_CLASS_BLOG)), $section_name ) ;
         }
         $result = join(", ", $sections);
         if ( !$result ) return false;
         return AMP_TEXT_POSTED_IN . $this->_renderer->space( ) . $result;
    }

    function render_sections_format( $source ) {
         return $this->_renderer->div( $this->render_sections( $source ), array( 'class' => 'blog_postmark'));
    }

     function load_live_sections( $source ) {
         return $this->load_sections( $source, true );
     }

     function load_sections( $source, $live_only = 0 ) {
        $allowed_sections = AMP_lookup( 'sectionMap' );
        $lookup_name =  ( $live_only ) ? 'sectionsLive' : 'sections';
        $section_names = array_combine_key( array_keys( $allowed_sections ), AMP_lookup( $lookup_name )) ;

        $article_locations = $source->getAllSections();
        $section_list = array_combine_key( $article_locations, $section_names );
        asort( $section_list );
        if ( defined( 'AMP_CUSTOM_ITEM_BLOG_SECTION')) {
            unset( $section_list[AMP_CUSTOM_ITEM_BLOG_SECTION]);
        }
        return $section_list;

     }

}

?>

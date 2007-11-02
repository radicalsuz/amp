<?php

require_once ('AMP/Content/Article/Display.inc.php' );

class ArticleDisplay_Blog extends Article_Display {

    var $use_short_comments = FALSE;
    var $use_title_links = FALSE;

    function ArticleDisplay_Blog ( &$article ) {
        $this->init( $article );
    }

    function _HTML_Header() {
        $article = &$this->_article;
        
        return  $this->_HTML_start() .
                $this->render_title( ).
                $this->_HTML_authorDate( $article->getAuthor(), $article->getItemDate() ) .
                $this->_HTML_source( $article ) .
                $this->_HTML_endHeading();
    }

    function render_title( ) {
        $renderer =  AMP_get_renderer( );
        if ( $this->use_title_links ) {
            return $renderer->in_P( $renderer->link( $this->_article->getURL( ), converttext( $this->_article->getName( ))),
                                    array( 'class' => $this->_css_class_title ));
        }
        return $this->_HTML_title( $this->_article->getTitle( ));
    }

        
    function _HTML_Footer() {
        $output = "";
        if ($comments = &$this->_article->getComments()) {
            $comments->readPublished( );
      
            if ($this->use_short_comments) {
               $output .=  $this->_HTML_commentLink($comments);       
            } else {

               $output .= $this->newline( 3 );

               if ( $sections_output = $this->render_sections( )) {
                   $output .= AMP_TEXT_POSTED_IN . $this->space( ) 
                                . $sections_output . $this->newline( 2 );
               }

               $output .= $comments->display(); 
            }
        
        }
        return $output . $this->_HTML_end();
    }

    function _HTML_authorDate ( $author, $date ) {
   
        return $this->_HTML_inSpan(sprintf( AMP_TEXT_POSTED_BY, $author, DoDate( $date, AMP_CONTENT_DATE_FORMAT )), $this->_css_class_date );
    }

    function _HTML_endHeading() {
        return "</td></tr><tr>" .'<td  class="'.$this->_css_class_text.'">' ;
    }

    
    function _HTML_source( &$article ) {
        $source = $article->getSource( );
        $url = $article->getSourceURL( );
        if (!$source ) return false;
        return    $this->newline( ) 
                . $this->inSpan( $this->link( $url, $source  ), array( 'class' => $this->_css_class_date ) );
    }


     function load_sections( $live_only = 0 ) {
        $allowed_sections = AMP_lookup( 'sectionMap' );
        $lookup_name =  ( $live_only ) ? 'sectionsLive' : 'sections';
        $section_names = array_combine_key( array_keys( $allowed_sections ), AMP_lookup( $lookup_name )) ;

        $article_locations = $this->_article->getAllSections();
        $section_list = array_combine_key( $article_locations, $section_names );
        asort( $section_list );
        if ( defined( 'AMP_CUSTOM_ITEM_BLOG_SECTION')) {
            unset( $section_list[AMP_CUSTOM_ITEM_BLOG_SECTION]);
        }
        return $section_list;

     }

     function load_live_sections( ) {
         return $this->load_sections( true );
     }

     function _HTML_commentLink( &$commentSet) {
         $commentSet->execute( );
         $renderer = AMP_get_renderer( );
             
        $text= ($commentSet->RecordCount()? $commentSet->RecordCount() . ' ' . AMP_pluralize( AMP_TEXT_COMMENT ): AMP_TEXT_NO_COMMENTS);
        $comments =  $renderer->link(AMP_Url_AddAnchor($this->_article->getURL(), 'comments'),$text);

        $sections_output = '';
        $sections = $this->render_sections( );
        if ( $sections ) {
            $sections_output = 
                  AMP_TEXT_POSTED_IN 
                  . $renderer->space( ) 
                  . $sections ;
        }

        return 
              $renderer->div( 
                  $sections_output
                  . $renderer->separator( ) 
                  . $comments,
                  array( 'align' => 'right'))
            . $renderer->newline( ) 
            . $renderer->hr( ) 
            . $renderer->newline( );

     
     }  

     function render_sections( ) {
         $section_list = $this->load_live_sections( );
         $sections = array( );
         foreach( $section_list as $section_id => $section_name ) {
            $sections[] = $this->_HTML_link('article.php?list=class&type='. $section_id.'&class='.AMP_CONTENT_CLASS_BLOG,$section_name) ;
         }
         return join(", ", $sections);

     }


    function useShortComments() {
        $this->use_short_comments = TRUE;    
    }  
    function useTitleLinks() {
        $this->use_title_links = TRUE;    
    }  
        
}
?>

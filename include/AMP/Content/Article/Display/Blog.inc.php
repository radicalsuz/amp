<?php

require_once ('AMP/Content/Article/Display.inc.php' );

class ArticleDisplay_Blog extends Article_Display {

    var $use_short_comments = FALSE;

    function ArticleDisplay_Blog ( &$article ) {
        $this->init( $article );
    }

    function _HTML_Header() {
        $article = &$this->_article;
        
        return  $this->_HTML_start() .
                $this->_HTML_title( $article->getTitle() ) .
              $this->_HTML_authorDate( $article->getAuthor(), $article->getItemDate() ) .
                $this->_HTML_endHeading();
    }

        
    function _HTML_Footer() {
        $output = "";
        if ($comments = &$this->_article->getComments()) {
    
      
            if ($this->use_short_comments) {
               $output .=  $this->_HTML_commentLink($comments);       
            } else {
               $output .= $comments->display(); 
            }
        
        }
        return $output . $this->_HTML_end();
    }

    function _HTML_authorDate ( $author, $date ) {
		
        return $this->_HTML_inSpan('Posted by '.$author. 'on '.  DoDate( $date, 'F jS, Y'), $this->_css_class_date) . $this->_HTML_newline();
    } 
    

 function _HTML_commentLink( &$commentSet) {
         
    $text= ($commentSet->RecordCount()? $commentSet->RecordCount():'no') .' comments';
    $comments =  $this->_HTML_link(AMP_Url_AddVars($this->_article->getURL(),'#comment'),$text);

    $article_locations = $this->_article->getAllSections();
    $section_list = array_combine_key( $article_locations, AMPContent_Lookup::instance('sections'));
   
    foreach( $section_list as $section_id => $section_name ) {
        $sections[] = $this->_HTML_link('article.php?list=class&type='. $section_id.'&class='.AMP_CONTENT_CLASS_BLOG,$section_name) ;
    }
    $sections = implode(", ", $sections);
    return 
        '<div align = "right">'.
        'Posted in '. $sections . ' | '. $comments.    
        '</div><br><hr><br>';
 
 }  
    function useShortComments() {
        $this->use_short_comments = TRUE;    
    }  
        
}
?>

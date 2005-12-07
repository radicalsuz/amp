<?php

require_once ('AMP/Content/Article/Display.inc.php' );

class ArticleDisplay_Blog extends Article_Display {



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
            $output .= $comments->display();
        }
     
        return $output . $this->_HTML_end();
    }

    function _HTML_authorDate ( $author, $date ) {
		
        return $this->_HTML_inSpan('Posted by '.$author. 'on '.  DoDate( $date, 'F jS, Y'), $this->_css_class_date) . $this->_HTML_newline();
    } 
        
}
?>

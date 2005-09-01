<?php

require_once ('AMP/Content/Article/Display/News.inc.php' );

class ArticleDisplay_PressRelease extends ArticleDisplay_News {

    function ArticleDisplay_PressRelease (&$article ) {
        $this->init( $article );
    }

    function _HTML_Header() {
        $article = &$this->_article;

        return  $this->_HTML_start().
                $this->_HTML_date    ( $article->getItemDate() ) .
                $this->_HTML_contact ( $article->getContact() ).
                $this->_HTML_endHeadingPR() .
                $this->_HTML_title   ( $article->getTitle() ).
                $this->_HTML_subTitle( $article->getSubTitle() );
    }

    function _HTML_date( $date ) {
        if (!$date) return false;
        return $this->_HTML_inSpan( "For Immediate Release:&nbsp;", $this->_css_class_label ) .
                $this->_HTML_inSpan( DoDate( $date, 'F jS, Y'), $this->_css_class_subheading ) . $this->_HTML_newline();
    }

    function _HTML_contact( $contact ) {
        if (!$contact) return false;
        return  '<table border="0" cellpadding="0" cellspacing="0"><tr>'.
                $this->_HTML_inTD( $this->_HTML_inSpan( 'Contact:&nbsp;', $this->_css_class_label ), array('valign'=>'top')).
                $this->_HTML_inTD( '&nbsp;', array('valign'=>'top')).
                $this->_HTML_inTD( $this->_HTML_inSpan( converttext( $contact ), $this->_css_class_subheading ) .$this->_HTML_newline() , array('valign'=>'top')).
                $this->_HTML_endTable();
    }

    function _HTML_endHeadingPR() {
        return "</td></tr>\n<td>". $this->_HTML_spacer( $width=8, $height=5 ). 
                "</td><tr>" .'<td  class="'.$this->_css_class_text.'">' . $this->_HTML_newline();
    }

    function _HTML_end() {
        return "</td></tr></table>";
    }
}
?>

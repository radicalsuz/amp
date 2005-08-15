<?php

require_once ('AMP/Content/Display/HTML.inc.php' );

class Article_Display extends AMPDisplay_HTML {

    var $_article;
    var $css_class = array(
        'title' => 'title',
        'subtitle' => 'subtitle' );

    function Article_Display( &$article ) {
        $this->init( $article );
    }

    function init( &$article ) {
        $this->_article = &$article; 
    }

    function execute() {
        return  $this->_HTML_Header().
                $this->_HTML_Content().
                $this->_HTML_Footer();
    }

    function _HTML_Header() {
        $article = &$this->_article;
        
        return  $this->_HTML_start() .
                $this->_HTML_title( $article->getTitle() ) .
                $this->_HTML_subTitle( $article->getSubTitle() ) .
                $this->_HTML_authorSource( $article->getAuthor(), $article->getSource(), $article->getSourceURL() ) .
                $this->_HTML_contact( $article->getContact() ).
                $this->_HTML_date( $article->getArticleDate() ).
                $this->_HTML_endHeading();
    }

    function _HTML_Content() {
        $body = $this->_article->getBody();

        if (!$this->_article->isHtml()) $body = converttext( $body );
        if ($hw = &AMPContent_Lookup::instance('hotwords')) {
            $body = str_replace( array_keys($hw), array_values($hw), $body );
        }
        $body = $this->_HTML_bodyText( $body );

        $image = &$this->_article->getImageRef();
        if ($image) return $this->_HTML_imageBlock( $image ) . $body;

        return $body;
    }

    function _HTML_Footer() {
        $output = "";
        if ($comments = &$this->_article->getComments()) {
            $output .= $comments->display();
        }
        if ($docbox = &$this->_article->getDocLinkRef()) {
            $output .= $docbox->display();
        }
        return $output . $this->_HTML_end();
    }



    function _HTML_start() {
        #table frame
        return '<table width="100%" cellpadding="0" cellspacing="0" border="0" class="text"><tr><td>';
    }

    function _HTML_title( $title ) {
        if (!$title) return false;
        return  '<p class="title">'  .  converttext( $title )  .  '</p>';
    }
    function _HTML_subTitle( $subtitle ) {
        if (!$subtitle) return false;
        return $this->_HTML_inSpan( converttext( $subtitle ) , 'subtitle' ). $this->_HTML_newline();
    } 

    function _HTML_authorSource( $author, $source, $url ) {
        if (!(trim($author) || $source || $url)) return false;
        $output_author = "";
        $output_source = "";

        if (trim($author)) {
            $output_author =  $this->_HTML_inSpan( 'by&nbsp;' . converttext($author), 'author');
            if (!$source) return $output_author . $this->_HTML_newline();
        }

        if ($source) $output_source = $this->_HTML_inSpan( $this->_HTML_link( $url, $source  ), 'author' );

        if ($output_author && $output_source) return $output_author . ',&nbsp;' . $output_source . $this->_HTML_newline();

        return $output_source . $this->_HTML_newline();
    }

    function _HTML_contact( $contact ) {
        if (!$contact) return false;
        return $this->_HTML_inSpan( 'Contact:&nbsp;' . converttext($contact), 'author') . $this->_HTML_newline();
    }

    function _HTML_date ( $date ) {
		if (!$date) return false;
        return $this->_HTML_inSpan( DoDate( $date, 'F jS, Y'), 'date') . $this->_HTML_newline();
    }

    function _HTML_endHeading() {
        return "</td></tr>\n<td></td><tr>" .'<td  class="text">' . $this->_HTML_newline();
    }

    function _HTML_imageBlock( &$image ) {
	    $output  = '<table width="' . $image->getWidth(). '" border="0" align="'.$image->getAlignment().'"';
	    $output .= '" cellpadding="0" cellspacing="0"><tr><td>' . "\n";
	    $output .=  '<a href="'. $image->getURL( AMP_IMAGE_CLASS_ORIGINAL ).'" target="_blank"> <img src="' . $image->getURL() . 
                    '" alt="' . $image->getAltTag(). '"'. $this->_HTML_makeAttributes( $image->getStyleAttrs() ) . '</a>';

	    $output .= '</td></tr>' . $this->_HTML_photoCaption( $image->getCaption(), $image->getWidth() );
        return $output . '</table>';
    }
        
        
    function _HTML_photoCaption( $caption, $width ) { 
        if (!$caption) return false;
        return '<tr align="center"><td width="' .  $width  . '" class="photocaption">' . $caption . '</td></tr>';
    }

    function _HTML_bodyText( $html ) {
        return '<p class="text">'.$html;
    }

    function _HTML_end() {
        return "</td></tr></table>";
    }

}
?>

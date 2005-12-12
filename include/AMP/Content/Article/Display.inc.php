<?php

require_once ('AMP/Content/Display/HTML.inc.php' );
if (!defined( 'AMP_CONTENT_LAYOUT_CSS' )) define( 'AMP_CONTENT_LAYOUT_CSS', false );

class Article_Display extends AMPDisplay_HTML {

    var $_article;
    var $css_class = array(
        'title' => 'title',
        'subtitle' => 'subtitle' );
    var $_css_class_author = "author";
    var $_css_class_author = "author";
    var $_css_class_title = "title";
    var $_css_class_subtitle = "subtitle";
    var $_css_class_photocaption = "photocaption";
    var $_css_class_text = "text";
    var $_css_class_date = "date";
    var $_css_class_label = "bodygreystrong";
    var $_css_class_subheading = "bodygrey";

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
        $body = $this->_processBody( $body );

        return $this->_addImage( $body );
    }

    function _processBody( $body ) {
        $htmlbody = $body;
        if (!$this->_article->isHtml()) $htmlbody = converttext( $body );
        // link hot words
		if ($hw = &AMPContent_Lookup::instance('hotwords')) {
            $htmlbody = str_replace( array_keys($hw), array_values($hw), $htmlbody );
        }
		//insert sidebar
		if ($sb = &$this->_article->getSidebar() ) {
			$find = '[-sidebar-]';
			if (!$sb_class = &$this->_article->getSidebarClass() ) {
				  $sb_class = AMP_CONTENT_SIDEBAR_CLASS_DEFAULT;
			} 
			$replace = '<div class="'.$sb_class.'">'.nl2br($sb).'</div>';
			$htmlbody = str_replace( $find, $replace, $htmlbody );
        }
        return $this->_HTML_bodyText( $htmlbody );
    }

    function _addImage( $body ) {
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
        if ( AMP_CONTENT_LAYOUT_CSS ) return false;

        return '<table width="100%" cellpadding="0" cellspacing="0" border="0" class="'.$this->_css_class_text.'"><tr><td>';
    }

    function _HTML_title( $title ) {
        if (!$title) return false;
        return  $this->_HTML_in_P(  converttext( $title ),  array( 'class' => $this->_css_class_title ) ); 

    }
    function _HTML_subTitle( $subtitle ) {
        if (!$subtitle) return false;
        return $this->_HTML_inSpan( converttext( $subtitle ) , $this->_css_class_subtitle ). $this->_HTML_newline();
    } 

    function _HTML_authorSource( $author, $source, $url ) {
        if (!(trim($author) || $source || $url)) return false;
        $output_author = "";
        $output_source = "";

        if (trim($author)) {
            $output_author =  $this->_HTML_inSpan( 'by&nbsp;' . converttext($author), $this->_css_class_author );
            if (!$source) return $output_author . $this->_HTML_newline();
        }

        if ($source) $output_source = $this->_HTML_inSpan( $this->_HTML_link( $url, $source  ), $this->_css_class_author );

        if ($output_author && $output_source) return $output_author . ',&nbsp;' . $output_source . $this->_HTML_newline();

        return $output_source . $this->_HTML_newline();
    }

    function _HTML_contact( $contact ) {
        if (!$contact) return false;
        return $this->_HTML_inSpan( 'Contact:&nbsp;' . converttext($contact), $this->_css_class_author ) . $this->_HTML_newline();
    }

    function _HTML_date ( $date ) {
		if (!$date) return false;
        return $this->_HTML_inSpan( DoDate( $date, 'F jS, Y'), $this->_css_class_date) . $this->_HTML_newline();
    }

    function _HTML_endHeading() {
        if ( AMP_CONTENT_LAYOUT_CSS ) return $this->_HTML_newline();
        return "</td></tr>\n<td></td><tr>" .'<td  class="'.$this->_css_class_text.'">' . $this->_HTML_newline();
    }

    function _HTML_imageBlock( &$image ) {
	    $output  = '<table width="' . $image->getWidth(). '" border="0" align="'.$image->getAlignment().'"';
	    $output .= ' cellpadding="0" cellspacing="0"><tr><td>' . "\n";
	    $output .=  '<a href="'. $image->getURL( AMP_IMAGE_CLASS_ORIGINAL ).'" target="_blank"> <img src="' . $image->getURL( $image->getImageClass() ) . 
                    '" alt="' . $image->getAltTag(). '"'. $this->_HTML_makeAttributes( $image->getStyleAttrs() ) . '></a>';

	    $output .= '</td></tr>' . $this->_HTML_photoCaption( $image->getCaption(), $image->getWidth() );
        return $output . '</table>';
    }
        
        
    function _HTML_photoCaption( $caption, $width ) { 
        #if (!$caption) return false;
        return '<tr align="center"><td width="' .  $width  . '" class="'.$this->_css_class_photocaption.'">' . $caption . '</td></tr>';
    }

    function _HTML_bodyText( $html ) {
        return '<p class="'.$this->_css_class_text.'">'.$html;
    }

    function _HTML_end() {
        if ( AMP_CONTENT_LAYOUT_CSS ) return false;
        return "</td></tr></table>";
    }

}

require_once ('AMP/Content/Article/Display/PressRelease.inc.php' );
require_once ('AMP/Content/Article/Display/News.inc.php' );
require_once ('AMP/Content/Article/Display/FrontPage.inc.php' );
?>

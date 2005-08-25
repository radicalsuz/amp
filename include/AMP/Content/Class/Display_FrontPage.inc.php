<?php

require_once( 'AMP/Content/Class/Display.inc.php' );

class ContentClass_Display_FrontPage extends ContentClass_Display {

    var $_more_link_text = "Read More&nbsp;&#187;&nbsp;&nbsp;";

    var $_pager_active = false;

    function ContentClass_Display_FrontPage( &$dbcon ) {
        $this->_class = &new ContentClass ( $dbcon, AMP_CONTENT_CLASS_FRONTPAGE );
        $fp_articles =  &$this->_class->getContents() ;
        $fp_articles->addSort( 'pageorder' );
        $this->init( $fp_articles );
    }

    function &getIntroDisplay() {
        return false;
    }

    function _HTML_listing( &$sourceItems ) {
        $output = "";
        foreach ($sourceItems as $contentItem ) {
            $output .= $this->_HTML_listItem( $contentItem );
        }
        return $this->_HTML_inDiv( $this->_HTML_listTable($output), array('class' => 'home' ) );
    }

    function _HTML_listTable( $content ) {
        if (!$content ) return false;
        return  '<table width="100%" class="text">' . $content . '</table>';
    }


    function _HTML_listItem( &$contentItem ) {
       
        $imgblock = false;
        if (method_exists( $contentItem, 'getImageRef' )) {
            $imgblock = $this->_HTML_imageBlock( $contentItem->getImageRef() );
        }

        $text_description   = $this->_HTML_listItemDescription( $contentItem ) . $this->_HTML_newline();

        return  "<tr>".
                $this->_HTML_inTD( $imgblock . $text_description ). 
                "</tr>";
    }

    function _HTML_imageBlock( &$image ) {
        if (!$image) return false;
        $image->setStyleAttrs( array( 'border' => '1', 'style' => 'border-color:#000000' ));

	    $output  = '<table width="' . $image->getWidth(). '" border="0" align="'.$image->getAlignment().'"';
	    $output .= ' cellpadding="0" cellspacing="0"><tr><td>' . "\n";
	    $output .=  '<a href="'. $image->getURL( AMP_IMAGE_CLASS_ORIGINAL ).'" target="_blank"> <img src="' . $image->getURL( $image->getImageClass() ) . 
                    '" alt="' . $image->getAltTag(). '"'. $this->_HTML_makeAttributes( $image->getStyleAttrs() ) . '></a>';

	    $output .= '</td></tr>' . $this->_HTML_photoCaption( $image->getCaption(), $image->getWidth() );
        return $output . '</table>';
    }
        

    function _HTML_photoCaption( $caption, $width ) { 
        #if (!$caption) return false;
        return '<tr align="center"><td width="' .  $width  . '" class="photocaption">' . $caption . '</td></tr>';
    }

    function _HTML_listItemTitle( &$source ) {
        if ($href = $source->getMoreLinkURL() ) {
            $title = $this->_HTML_link( $href, $source->getTitle(), array( "class"=>"hometitle" ) ); 
        } else {
            $title = $this->_HTML_inSpan( $source->getTitle(), 'hometitle' );
        }
        return  $this->_HTML_in_P( $title, array("class" => "hometitle" ) );
    }

    function _HTML_listItemSubtitle( $subtitle ) {
        if (!$subtitle) return false;
        return $this->_HTML_inSpan( $subtitle, 'subtitle' );
    }

    function _HTML_listItemDescription( $article ) {
        return
            $this->_HTML_listItemTitle( $article ) . 
            $this->_HTML_listItemSubTitle( $article->getSubTitle() ) . 
            $this->_HTML_listItemContent( $article ) .
            $this->_HTML_moreLink( $article->getMoreLinkURL() );
    }

    function _HTML_listItemContent( $article ) {
        
        if (!( $body = $article->getBody() )) return false;
        

        if (!$article->isHtml()) $body = converttext( $body );
        if ($hw = &AMPContent_Lookup::instance('hotwords')) {
            $body = str_replace( array_keys($hw), array_values($hw), $body );
        }
        return $this->_HTML_listItemBodyText( $body );

    }

    function _HTML_listItemBodyText ( $text ) {
        return $this->_HTML_inSpan( $this->_HTML_in_P( $text, array('class' => 'homebody') ), array('class' => 'homebody' ) );
    }

    function _HTML_moreLink( $href ) {
        if (!$href) return false;
        return  $this->_HTML_inSpan( 
                    $this->_HTML_link(  $href, 
                                        $this->_getMoreLinkText(), 
                                        array( 'class' => 'morelink' )
                                      ), 
                    array('class' => 'morelink' ) 
                ).
                $this->_HTML_newline();
    }

    function _getMoreLinkText() {
        return $this->_more_link_text;
    }

    function _HTML_listIntro( &$intro ) {
        return false;
    }

}

?>

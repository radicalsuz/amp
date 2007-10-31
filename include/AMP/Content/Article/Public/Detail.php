<?php

require_once( 'AMP/Display/Detail.php');

class Article_Public_Detail extends AMP_Display_Detail {

    var $_css_class_subtitle = "subtitle";
    var $_css_class_author  = AMP_CONTENT_CSS_CLASS_ARTICLE_AUTHOR;
    var $_css_class_date    = AMP_CONTENT_CSS_CLASS_ARTICLE_DATE;
    var $_css_class_source  = AMP_CONTENT_CSS_CLASS_ARTICLE_SOURCE;
    var $_css_class_contact = AMP_CONTENT_CSS_CLASS_ARTICLE_AUTHOR;
    var $_css_class_photocaption = AMP_CONTENT_CSS_CLASS_ARTICLE_IMAGE_CAPTION;
    var $_css_class_body    = AMP_CONTENT_CSS_CLASS_ARTICLE_BODY;
    var $_css_class_media   = AMP_CONTENT_CSS_CLASS_ARTICLE_MEDIA;
    var $_css_class_container_item = 'article_public_detail';

    function Article_Public_Detail( &$source ) {
        $this->__construct( $source );
    }

    function renderItem( $source ) {
        return
              $this->render_title( $source )
            . $this->render_subtitle( $source ) 
            . $this->render_byline( $source )
            . $this->render_contact( $source )
            . $this->render_date( $source )
            . $this->render_image( $source )
            . $this->render_body( $source );
    }

    function _renderFooter( ) {
        if ( defined( 'AMP_RENDER_ARTICLE_PUBLIC_DETAIL_FOOTER' )) {
            $footer_function = AMP_RENDER_ARTICLE_PUBLIC_DETAIL_FOOTER;
            if ( method_exists( $this, $footer_function )) {
                return $this->$footer_function( $this->_source );
            }
            if ( function_exists( $footer_function )) {
                return $footer_function( $this->_source, $this );
            }
        }
        return $this->render_comments( $this->_source );
    }

    function render_title( $source ) {
        if ( !( $title = $source->getName( ))) return false;
        return $this->_renderer->p( converttext( $title ), array( 'class' => $this->_css_class_title ));
    }

    function render_subtitle( $source ) {
        if ( !( $subtitle = $source->getSubTitle( ))) return false;
        return $this->_renderer->span( converttext( $subtitle ), array( 'class' => $this->_css_class_subtitle))
                . $this->_renderer->newline( );
    }

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

    function render_contact( $source ) {
        if ( !( $contact = $source->getContact( ))) return false;
        return $this->_renderer->span( 
                AMP_TEXT_CONTACT . ':' . $this->_renderer->space( )
                . converttext( $contact ),
                array( 'class' => $this->_css_class_contact ) )
                . $this->_renderer->newline( );

    }

	function render_date( &$source ) {
		$date = $source->getItemDate();
		if (!AMP_verifyDateValue( $date )) return false;

        return $this->_renderer->span( DoDate( $date, AMP_CONTENT_DATE_FORMAT), array( 'class' => $this->_css_class_date )) 
                . $this->_renderer->newline();
	}

    function render_image( &$source ) {
        $image = $source->getImageRef();
        if (!$image || !$image->display_in_body( )) return false;
        return $this->_renderer->div( 
            $this->_renderer->link( 
                $image->getURL( AMP_IMAGE_CLASS_ORIGINAL ),
                $this->_renderer->image( $image->getURL( $image->getImageClass( )), $image->attributes( )),
                array( 'target' => '_blank'))
            . $this->render_caption( $image ),
            array( 'class' => AMP_CONTENT_CSS_CLASS_ARTICLE_IMAGE )
        );
        
    }

    function render_caption( $image ) {
        return $this->_renderer->div( 
            $image->getCaption( ),
            array( 'class' => $this->_css_class_photocaption, 
                    'width' => $image->getWidth( ) )
        );

    }

    function render_body( $source ) {
        if ( !( $body = $source->getBody( ))) return false;
        $body = ( $source->isHtml( )) ? $body : converttext( $body );

        //hot words
		if ($hw = AMP_lookup('hotwords')) {
            $body = str_replace( array_keys($hw), array_values($hw), $body );
        }

        //sidebar
        /*
        if ( $sidebar = $this->render_sidebar( $source )) {
            $body = str_replace( array( "[-sidebar-]", "%sidebar%"), $sidebar, $body );
        }

        //blocks
        if ( $media = $this->render_media( $source ) ) {
            if ( strpos( $body, '%media%') === FALSE ) {
                $body .= $media;
            }
            $body = str_replace( '%media%', $media, $body );
        }

        if ( $document = $this->render_document( $source )) {
            if ( strpos( $body, '%doc%') === FALSE ) {
                $body .= $document;
            }
            $body = str_replace( '%doc%', $document, $body );
        }
        */

        $blocks = AMP_lookup( 'article_includes' );

        foreach( $blocks as $type => $render_method ) {
            $block_content = false;
            if ( method_exists( $this, $render_method )) {
                $block_content = $this->$render_method( $source );
            } elseif ( function_exists( $render_method ) ){ 
                $block_content = $render_method( $source, $this );
            } else {
                trigger_error( sprintf( AMP_TEXT_ERROR_NOT_DEFINED, 'AMP', $render_method ));
            }
            if ( !$block_content ) {
                continue;
            }

            $block_token =  '%' . strtolower( $type ) . '%';
            if ( strpos( $body, $block_token ) === FALSE ) {
                $body .= $block_content;
            }
            $body = str_replace( $block_token, $block_content, $body );
        }
        
        return $this->_renderer->p( $body, array( 'class' => $this->_css_class_body ));

    }

    function render_sidebar( $source ) {
		if (!( $sb = $source->getSidebar() )) return false ;
        if (!( $sb_class = $source->getSidebarClass() )) {
            $sb_class = AMP_CONTENT_SIDEBAR_CLASS_DEFAULT;
        } 

        return $this->_renderer->div( nl2br( $sb ), array( 'class' => $sb_class ));
    }

    function render_document( $source ) {
        if ( !( $docbox = $source->getDocLinkRef())) return false;
        return $docbox->display( 'div' );
    }

    function render_media( $source ) {

        $media_url  = $source->getMediaUrl();
        $media_html = $source->getMediaHtml();
        if ( !( $media_html || $media_url )) return false;

        $output = '';
        if ( $media_url ) {
            $output .= $this->_renderer->embed_flash_video( $media_url );
        }
        if ( $media_html ) {
            $output .= $media_html;
        }
        return $this->_renderer->div( $output, array( 'class' => $this->_css_class_media )) ;

    }

    function render_comments( $source ) {
        if (!( $comments = &$source->getComments())) return false;
        $comments->readPublished( );
        return $comments->display();
    }

}

?>

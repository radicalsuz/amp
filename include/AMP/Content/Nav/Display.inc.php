<?php

require_once ('AMP/Content/Nav.inc.php');
define( 'AMP_NAVTYPE_DEFAULT_LINK_FORMAT', '_HTML_link' );

class NavigationDisplay {
    var $nav;
    var $position;
    var $css_class;
    var $_morelink_css_class = "go";
    var $_morelink_text = AMP_TEXT_NAV_MORELINK;
    var $_template;
    var $_legacy_positions = array( 'l' => 1, 'r' => 1, 'L' => 1, 'R'=> 1 );
    var $_renderer;
    var $order;

    function NavigationDisplay( &$nav ) {
        $this->init( $nav );
    }

    function init( &$nav ) {
        $this->nav = &$nav;
        $this->position = $this->nav->position;
        $this->order = $this->nav->order ;
        $this->_template = &$nav->template;
        $this->setCssClass( $nav->getCssClass() );
        $this->_renderer = AMP_get_renderer( );
    }

    function execute() {
        if (!($body=$this->_HTML_body())) return false;
        return $this->_templateElement( $this->_HTML_title( ) . $body );
    }

    ########################
    ### public accessors ###
    ########################

    function setCssClass( $classname ) {
        $this->css_class = $classname;
    }

    function getCssClass() {
        return $this->css_class;
    }


    #######################################
    ### private HTML generation methods ###
    #######################################

    function _HTML_title() {
        $title_html = "";
        if ($text = $this->nav->getTitle()) {
            $title_html = converttext( $text );
        }
        if ($image_name = $this->nav->getTitleImage()) {
            $imgpath = $this->_template->getNavImagePath();
            if (strpos( $image_name, $imgpath ) === FALSE) $image_name = $imgpath .$image_name;
            $title_html = "<img src=\"". $image_name ."\">";
        }
        return $this->_templateTitle( $title_html );
    }

    function _HTML_body() {
        if (!($result = $this->nav->get_contents())) return false;
        if (!is_array( $result )) return $this->_templateBody( $result );

        $output = "";
        $html_format = $this->_getLinkFormat();

        foreach( $result as $item ) {
            if (!($html = $this->$html_format( $item ))) continue;
            $output .= $this->_templateBodyItem( $html );
        }

        if ( $this->nav->exceedsLimit()) {
            $output .= $this->_HTML_moreLink();
        }

        if ( isset( $this->_legacy_positions[$this->position])) {
            return $output . $this->_templateBodyClose();
        }
        return $this->_templateBody( $output );
    }

    function _HTML_link( $link ) {
        $link_template = "<a href=\"%s\" class=\"%s\">%s</a>";
        if (empty($link) || !array_key_exists( 'label', $link )) return false;
        
        if (!isset($link['css'])) $link['css'] = $this->getCssClass();

        return sprintf( $link_template, $link['href'], $link['css'], $link['label'] ) ; 
    }

    function _HTML_datedLink( $link ) {
        $output = "";
        if (isset( $link['date'] )) {
            $output = '<span class="sidelist_date">'.
                       date("F j, Y", strtotime($link['date'])).
                      "</span><BR>\n";
        }

        return $output . $this->_HTML_link( $link );
    }

    function _HTML_moreLink() {
        if (!($href = $this->nav->getMoreLink())) return false;
        $item = array( 'href' => $href, 'css' => $this->_morelink_css_class, 'label'=> $this->_morelink_text );
        $link = $this->_HTML_link( $item );

        return $this->_templateBodyItem( $link );
    }


    ##################################
    ### private templating methods ###
    ##################################

    function _templateBodyItem( $html ) {
        if ( isset( $this->_legacy_positions[$this->position])) {
            return  $this->_template->getNavHtml( $this->position, 'start_content' ).
                    $html .
                    $this->_template->getNavHtml( $this->position, 'close_content' );

        }
        return $this->_renderer->span( $html, array( 'class' => AMP_CONTENT_CSS_CLASS_NAV_LINE_ITEM )) 
                . $this->_renderer->newline( );
    }

    function _templateTitle( $html ) {
        if ( isset( $this->_legacy_positions[$this->position])) {
            return  $this->_template->getNavHtml( $this->position, 'start_heading' ) .
                    $html .
                    $this->_template->getNavHtml( $this->position, 'close_heading' );
        }
        return $this->_renderer->span( $html, array( 'class' => AMP_CONTENT_CSS_CLASS_NAV_HEADING )) 
                . $this->_renderer->newline( );
    }

    function _templateBody( $html ) {
        if ( isset( $this->_legacy_positions[$this->position])) {
            return  $this->_templateBodyItem( $html ) .
                    $this->_templateBodyClose();
        }
        return $this->_renderer->div( $html, array( 'class' => AMP_CONTENT_CSS_CLASS_NAV_CONTENT )) ;
    }

    function _templateElement( $content ) {
        
        if ( AMP_CONTENT_NAV_LEGACY_ELEMENT_TEMPLATE && isset( $this->_legacy_positions[$this->position])) {
            return $content;
        }

        $nav_blocks = AMP_lookup( 'navBlocks' );
        $nav_key = array_search( strtolower( $this->order ), $nav_blocks );
        return $this->_renderer->div( $content, 
                                    array(  'class' => sprintf( AMP_CONTENT_CSS_CLASS_NAV_ELEMENT_POSITIONED, $nav_key ),
                                            'id'    => sprintf( AMP_CONTENT_CSS_ID_NAV_ELEMENT, $this->nav->id ))
                                    );
    }

    function _templateBodyClose(){
        return $this->_template->getNavHtml( $this->position, 'content_spacer' );
    }


    ##############################
    ### private helper methods ###
    ##############################

    function _getLinkFormat() {
        $link_descriptor = strtoupper( 'AMP_NAVTYPE_' . $this->nav->getEngineType() . '_LINK_FORMAT' );
        if (!defined( $link_descriptor )) return AMP_NAVTYPE_DEFAULT_LINK_FORMAT;
        return constant( $link_descriptor );
    }
}
?>

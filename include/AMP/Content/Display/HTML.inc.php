<?php

define ( 'AMP_ICON_SPACER', 'spacer.gif' );

class AMPDisplay_HTML {

    var $_html_value;

    function setContent( $html ) {
        $this->_html_value = $html;
    }

    function addtoContent( $html ){
        $this->_html_value .= $html;
    }

    function execute() {
        if ( !isset( $this->_html_value )) return false;
        return $this->_html_value;
    }

    function _HTML_inSpan( $text, $class=array() ) {
        $html_attr =$class;
        if (is_string($class)) $html_attr = array('class'=>$class);
        return '<span'.$this->_HTML_makeAttributes( $html_attr ).'>' . $text .'</span>';
    }

    function _HTML_inTD( $text, $attr_set = array() ) {
        $start_tag_ends = $this->_HTML_makeAttributes( $attr_set );
        return "<td$start_tag_ends>$text</td>";
    }

    function _HTML_in_P ( $text, $attr_set = array() ) {
        $p_attr= $this->_HTML_makeAttributes( $attr_set );
        return "<p$p_attr>$text</p>\n";
    }

    function _HTML_inDiv( $text, $attr_set = array() ) {
        $div_attr = $this->_HTML_makeAttributes( $attr_set );
        return "<div$div_attr>$text</div>\n";
    }

    function _HTML_spacer( $width, $height ) {
        return '<img src ="'. AMP_CONTENT_URL_IMAGES . AMP_ICON_SPACER.'"'.
                $this->_HTML_makeAttributes( array( 'width' =>$width, 'height' => $height ) ) . '>';
                 
    }

    function _HTML_image( $url, $attr_set = array() ) {
        $img_attr = array_merge( array( 'src'=>$url ), $attr_set );
        return "<img" . $this->_HTML_makeAttributes( $img_attr ) . ">";
    }

    function _HTML_endTable() {

        return "</tr></table>\n";
    }

    function _HTML_link( $href, $text, $attr = array() ) {
        if (!$href) return $this->_HTML_inSpan( $text, $attr);
        $link_attr = $this->_HTML_makeAttributes( $attr );
        return "<a href=\"".$href."\"$link_attr>". $text . "</a>";
    }

    function _HTML_newline($qty=null) {
        if (!isset($qty)) return "<BR>\n";
        return str_repeat( "<BR>\n", $qty );
    }

    function _HTML_safeQuote( $item ) {
        if (strpos( $item, '"') === FALSE ) return '"'.$item.'"';
        if (strpos( $item, "'") === FALSE ) return "'".$item."'";
        return '"'. str_replace( '"', '&quot;', $item). '"';
    }


    function _HTML_makeAttributes( $attr_set ) {
        if (empty($attr_set)) return false;
        $output = "";
        if (!is_array( $attr_set)) {
            trigger_error( 'Non array passed to makeAttributes' );
            return false;
            #print AMPbacktrace();
        }

        foreach($attr_set as $attr => $value ) {
            $output .= " " . $attr . "=" . $this->_HTML_safeQuote( $value );
        }
        return $output;
    }

    function _HTML_p_commaJoin( $text_array ) {
        return '<P>' . join( ',  ' , $text_array ) . "</P>\n";
    }

    function _HTML_bold ( $text ) {
        if (!$text) return false;
        return '<b>' . $text . '</b>';
    }
    function _HTML_italics ( $text ) {
        if (!$text) return false;
        return '<i>' . $text . '</i>';
    }
}

?>

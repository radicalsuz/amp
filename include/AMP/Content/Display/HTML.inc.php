<?php

class AMPDisplay_HTML {

    function _HTML_inSpan( $text, $class=null ) {
        $html_class_attr ="";
        if (isset($class)) $html_class_attr = ' class="'.$class.'"';
        return '<span'.$html_class_attr.'>' . $text .'</span>';
    }

    function _HTML_link( $href, $text ) {
        if (!$href) return $text;
        return "<a href=\"".$href."\">". $text . "</a>";
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

        foreach($attr_set as $attr => $value ) {
            $output .= $attr . "=" . $this->_HTML_safeQuote( $value );
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

<?php

define( 'AMP_ICON_SPACER', 'spacer.gif' );
define( 'AMP_INCLUDE_START_TAG', '{{' );
define( 'AMP_INCLUDE_END_TAG', '}}' );

class AMPDisplay_HTML {

    var $_html_value;
    var $_buffer;

    function setContent( $html ) {
        $buffer = &$this->getBuffer( );
        $buffer->clear( );
        $buffer->add( $html );
    }

    function &getBuffer( ){
        if ( isset( $this->_buffer )) return $this->_buffer;

        require_once( 'AMP/Content/Buffer.php');
        $this->_buffer = &new AMP_Content_Buffer;
        return $this->_buffer;
    }

    function addtoContent( $html ){
        $buffer = &$this->getBuffer( );
        $buffer->add( $html );
    }

    function execute() {
        $buffer = &$this->getBuffer( );
        return $buffer->execute( );
    }

    function inSpan( $text, $class=array() ) {
        return $this->_HTML_inSpan( $text, $class );
    }
    function _HTML_inSpan( $text, $class=array() ) {
        $html_attr =$class;
        if (is_string($class)) $html_attr = array('class'=>$class);
        return '<span'.$this->_HTML_makeAttributes( $html_attr ).'>' . $text .'</span>';
    }

    function inTD( $text, $attr_set = array() ) {
        return $this->_HTML_inTD( $text, $attr_set ) ;
    }
    function _HTML_inTD( $text, $attr_set = array() ) {
        $start_tag_ends = $this->_HTML_makeAttributes( $attr_set );
        return "<td$start_tag_ends>$text</td>";
    }

    function in_P ( $text, $attr_set = array() ) {
        return $this->_HTML_in_P ( $text, $attr_set ) ;
    }
    function _HTML_in_P ( $text, $attr_set = array() ) {
        $p_attr= $this->_HTML_makeAttributes( $attr_set );
        return "<p$p_attr>$text</p>\n";
    }

    function inDiv( $text, $attr_set = array() ) {
        return $this->_HTML_inDiv( $text, $attr_set = array() ) ;
    }
    function _HTML_inDiv( $text, $attr_set = array() ) {
        $div_attr = $this->_HTML_makeAttributes( $attr_set );
        return "<div$div_attr>$text</div>\n";
    }

    function spacer( $width, $height ) {
        return $this->_HTML_spacer( $width, $height ) ;
    }
    function _HTML_spacer( $width, $height ) {
        return '<img src ="'. AMP_CONTENT_URL_IMAGES . AMP_ICON_SPACER.'"'.
                $this->_HTML_makeAttributes( array( 'width' =>$width, 'height' => $height ) ) . '>';
                 
    }

    function image( $url, $attr_set = array() ) {
        return $this->_HTML_image( $url, $attr_set ) ;
    }
    function _HTML_image( $url, $attr_set = array() ) {
        $img_attr = array_merge( array( 'src'=>$url ), $attr_set );
        return "<img" . $this->_HTML_makeAttributes( $img_attr ) . ">";
    }

    function endTable() {
        return $this->_HTML_endTable() ;
    }
    function _HTML_endTable() {

        return "</tr></table>\n";
    }

    function link( $href, $text, $attr = array() ) {
        return $this->_HTML_link( $href, $text ) ;
    }
    function _HTML_link( $href, $text, $attr = array() ) {
        if (!$href) return $this->_HTML_inSpan( $text, $attr);
        $link_attr = $this->_HTML_makeAttributes( $attr );
        return "<a href=\"".$href."\"$link_attr>". $text . "</a>";
    }

    function newline($qty=null) {
        return $this->_HTML_newline($qty) ;
    }
    function _HTML_newline($qty=null) {
        if (!isset($qty)) return "<BR>\n";
        return str_repeat( "<BR>\n", $qty );
    }

    function safeQuote( $item ) {
        return $this->_HTML_safeQuote( $item ) ;
    }
    function _HTML_safeQuote( $item ) {
        if (strpos( $item, '"') === FALSE ) return '"'.$item.'"';
        if (strpos( $item, "'") === FALSE ) return "'".$item."'";
        return '"'. str_replace( '"', '&quot;', $item). '"';
    }

    function UL( $items, $attr = array( ), $item_attr = array( )){
        return $this->_HTML_UL( $items, $attr, $item_attr );
    }
    function _HTML_UL( $items, $attr = array( ), $item_attr = array( )){
        return  '<ul'.$this->_HTML_makeAttributes( $attr ).">\n" . $this->_HTML_LI( $items, $item_attr ) . "</ul>\n";
    }

    function LI( $items, $attr = array( )){
        return $this->_HTML_LI( $items, $attr = array( ));
    }
    function _HTML_LI( $items, $attr = array( )){

        $output = "";
        foreach( $items as $item ){
            if ( is_array( $item )) {
                $output = $this->_HTML_UL( $item );
            } else {
                $output .= '<li' . $this->_HTML_makeAttributes( $attr ) . '>';
                $output .= $item . "</li>\n";
            }
        }
        return $output ;
     
    }


    function makeAttributes( $attr_set ) {
        return $this->_HTML_makeAttributes( $attr_set ) ;
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

    function p_commaJoin( $text_array ) {
        return $this->_HTML_p_commaJoin( $text_array ) ;
    }
    function _HTML_p_commaJoin( $text_array ) {
        return '<P>' . join( ',  ' , $text_array ) . "</P>\n";
    }

    function bold ( $text ) {
        return $this->_HTML_bold ( $text ) ;
    }
    function _HTML_bold ( $text ) {
        if (!$text) return false;
        return '<b>' . $text . '</b>';
    }
    function italics ( $text ) {
        return $this->_HTML_italics ( $text ) ;
    }
    function _HTML_italics ( $text ) {
        if (!$text) return false;
        return '<i>' . $text . '</i>';
    }

    function _activateIncludes( $html ) {
        $start = AMPDisplay_HTML::_findIncludeStartTag( $html );
        if ($start === FALSE) return $html;
        $start = $start + strlen( AMP_INCLUDE_START_TAG );

        $end = AMPDisplay_HTML::_findIncludeEndTag( $html, $start );
        if ($end === FALSE) return $html;

        $result = AMPDisplay_HTML::_processInclude( substr( $html, $start, $end-$start) );

        $block_end = $end + strlen( AMP_INCLUDE_END_TAG );
        $block_start = $start - strlen( AMP_INCLUDE_START_TAG );
        $current_html = AMPDisplay_HTML::_replaceInclude( $html, $result, $block_start, $block_end );

        return AMPDisplay_HTML::_activateIncludes( $current_html );
    }

    #########################################
    ###  Private include parsing methods  ###
    #########################################

    function _processInclude( $code ) {
        if (!($filename = AMPDisplay_HTML::_getIncludeFilename( $code ))) return false;

        ob_start();
        include( $filename );
        $include_value = ob_get_contents();
        ob_end_clean();

        return $include_value;
    }

    function _getIncludeFilename( $code ) {
        $filename = trim ( $code );
        if (file_exists_incpath( $filename )) return $filename;
        
        return false;
    }

    function _findIncludeStartTag( $html, $offset = 0 ) {
        return strpos( $html, AMP_INCLUDE_START_TAG );
    }

    function _findIncludeEndTag( $html, $offset = 0 ) {
        return strpos( $html, AMP_INCLUDE_END_TAG );
    }

    function _replaceInclude( $original, $insert, $start, $end ) {
        return substr( $original, 0, $start) . $insert . substr($original, $end);
    }
}

?>

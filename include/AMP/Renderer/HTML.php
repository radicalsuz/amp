<?php

require_once( 'AMP/Content/Display/HTML.inc.php');

class AMP_Renderer_HTML extends AMPDisplay_HTML {

    function space($count=1 ){
        return str_repeat( '&nbsp;', $count );
    }

    function space_break( $count = 1 ) {
        return str_repeat( " \n", $count );
    }

    function inSpan( $text, $class=array() ) {
        $html_attr =$class;
        if (is_string($class)) $html_attr = array('class'=>$class);
        return '<span'.$this->makeAttributes( $html_attr ).'>' . $text .'</span>';
    }

    function span( $text, $attr_set = array( )) {
        return $this->inSpan( $text, $attr_set );
    }

    function p( $text, $attr_set = array( )) {
        return $this->in_P( $text, $attr_set );
    }

    function inTD( $text, $attr_set = array() ) {
        return $this->td( $text, $attr_set );
    }

    function td( $content, $attr_set = array( )) {
        if ( $content ) {
            $content = "\n\t" . str_replace( "\n", "\n\t", $content ) . "\n";
        }
        return "\n" . $this->tag( 'td', $content, $attr_set ) ;
        
        //$start_tag_ends = $this->makeAttributes( $attr_set );
        //return "<td$start_tag_ends>$content</td>";
    }

    function tr( $content, $attr_set = array( )) {
        $content = str_replace( "\n", "\n\t", $content ) . "\n";
        return $this->tag( 'tr', $content, $attr_set ) . "\n";
    }

    function table( $content, $attr_set = array( )) {
        $content = "\n\t" . str_replace( "\n", "\n\t", $content ) ;
        return $this->tag( 'table', $content, $attr_set ) . "\n";
    }

    function tag( $tag, $content, $attr_set = array( ) ) {
        $start_tag_ends = $this->makeAttributes( $attr_set );
        return "<$tag$start_tag_ends>$content</$tag>";
    }

    function tag_single( $tag, $attr_set = array( ) ) {
        $start_tag_ends = $this->makeAttributes( $attr_set );
        return "<$tag$start_tag_ends />";
    }

    function in_P ( $text, $attr_set = array() ) {
        $p_attr= $this->makeAttributes( $attr_set );
        return "<p$p_attr>$text</p>\n";
    }

    function inDiv( $text, $attr_set = array() ) {
        return $this->div( $text, $attr_set ) ;
    }

    function div( $text, $attr_set = array( )) {
        $div_attr = $this->makeAttributes( $attr_set );
        return "<div$div_attr>$text</div>\n";
    }

    function spacer( $width, $height ) {
        return '<img src ="'. AMP_CONTENT_URL_IMAGES . AMP_ICON_SPACER.'"'.
                $this->makeAttributes( array( 'width' =>$width, 'height' => $height ) ) . '>';
    }

    function image( $url, $attr_set = array() ) {
        $img_attr = array_merge( array( 'src'=>$url ), $attr_set );
        return "<img" . $this->makeAttributes( $img_attr ) . ">";
    }

    function endTable() {
        return "</tr></table>\n";
    }

    function link( $href, $text, $attr = array() ) {
        if (!$href) return $this->inSpan( $text, $attr);
        $link_attr = $this->makeAttributes( $attr );
        return "<a href=\"".$href."\"$link_attr>". $text . "</a>";
    }

    function simple_li( $contents, $attr = array( )) {
        $li_attr = $this->makeAttributes( $attr );
        return '     <li'.$li_attr.'>'.$contents.'</li>'."\n" ;
    }

    function simple_ul( $contents, $attr = array( )) {
        $ul_attr = $this->makeAttributes( $attr );
        return '<ul'.$ul_attr.'>'.$contents.'</ul>'."\n\n";
    }

    function newline($qty=1, $attr = array( )) {
        $nl_attr = $this->makeAttributes( $attr );
        $nl_str = "<BR$nl_attr />\n";
        return str_repeat( $nl_str, $qty );
    }

    function safeQuote( $item ) {
        if (strpos( $item, '"') === FALSE ) return '"'.$item.'"';
        if (strpos( $item, "'") === FALSE ) return "'".$item."'";
        return '"'. str_replace( '"', '&quot;', $item). '"';
    }

    function UL( $items, $attr = array( ), $item_attr = array( )){
        return  '<ul'.$this->makeAttributes( $attr ).">\n" . $this->LI( $items, $item_attr ) . "</ul>\n";
    }

    function LI( $items, $attr = array( )){
        $output = "";
        if ( !$items ) return false;
        foreach( $items as $item ){
            if ( is_array( $item )) {
                $output = $this->UL( $item );
            } else {
                $output .= '<li' . $this->makeAttributes( $attr ) . '>';
                $output .= $item . "</li>\n";
            }
        }
        return $output ;
    }


    function makeAttributes( $attr_set ) {
        if (empty($attr_set)) return false;
        $output = "";
        if (!is_array( $attr_set)) {
            trigger_error( 'Non array passed to makeAttributes' );
            return false;
            #print AMPbacktrace();
        }

        foreach($attr_set as $attr => $value ) {
            $output .= " " . $attr . "=" . $this->safeQuote( $value );
        }
        return $output;
    }

    function attr( $attr_set ) {
        return $this->makeAttributes( $attr_set );
    }

    function p_commaJoin( $text_array, $attr=array( )) {
        $p_attr = $this->makeAttributes( $attr );
        return '<P'.$p_attr.'>' . join( ',  ' , $text_array ) . "</P>\n";
    }

    function bold ( $text ) {
        if (!$text) return false;
        return '<b>' . $text . '</b>';
    }

    function strong( $text ) {
        return $this->tag( 'strong', $text );
    }

    function italics ( $text ) {
        if (!$text) return false;
        return '<i>' . $text . '</i>';
    }

    function _activateIncludes( $html ) {
        $start = $this->_findIncludeStartTag( $html );
        if ($start === FALSE) return $html;
        $start = $start + strlen( AMP_INCLUDE_START_TAG );

        $end = $this->_findIncludeEndTag( $html, $start );
        if ($end === FALSE) return $html;

        $result = $this->_processInclude( substr( $html, $start, $end-$start) );

        $block_end = $end + strlen( AMP_INCLUDE_END_TAG );
        $block_start = $start - strlen( AMP_INCLUDE_START_TAG );
        $current_html = $this->_replaceInclude( $html, $result, $block_start, $block_end );

        return $this->_activateIncludes( $current_html );
    }

    #########################################
    ###  Private include parsing methods  ###
    #########################################

    function _processInclude( $code ) {
        if (!($filename = $this->_getIncludeFilename( $code ))) return false;

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

    /**
     * separator 
     * 
     * @access public
     * @return void
     */
    function separator( ){
        return '&nbsp;&#124;&nbsp;';
    }

    function arrow_left( $count=1 ) {
        return str_repeat( '&lt;', $count );
    }

    function arrow_right( $count=1 ) {
        return str_repeat( '&gt;', $count );
    }

    function double_arrow_left( $count = 1 ) {
        return str_repeat( '&laquo;', $count );
    }

    function double_arrow_right( $count = 1 ) {
        return str_repeat( '&raquo;', $count );
    }

    function embed_flash_video( $url, $attr=array( ) ) {
        if ( !$url ) return '';
        $default_attr = 
            array(  'height' => '160', 
                    'width'=>'200', 
                    'wmode'=>'transparent', 
                    'type' => 'application/x-shockwave-flash');
        $attr_set = array_merge( $default_attr, $attr );
        $params = array( 'movie' => $url, 'wmode' => $attr_set['wmode']);

        $output = $this->params( $params );
        $output .= $this->embed( $url, $attr ) ;

        $object_attrs = array( 'height' => $attr_set['height'], 'width' => $attr_set['width'] );
        return $this->object( $output, $object_attrs );

    }

    function embed( $url, $attr = array( )) {
        $attr['src'] = $url;
        return '<embed'.$this->attr( $attr).'></embed>';
    }

    function object( $content, $attr = array( ) ) {
        return '<object' . $this->attr( $attr ) . '></object>';

    }

    function params( $params = array( )) {
        $output = '';
        foreach( $params as $name => $value ) {
            $param_value = array( 'name' => $name, 'value' => $value );
            $output .= '<param'.$this->attr( $param_value ).'></param>';
        }
        return $output;
    }

    function hr( ) {
        return '<hr />';
    }

    function indent( $content, $indent_size = 10 ) {
        return $this->div( $content, array( 'style' => ( 'padding-left:' . $indent_size . 'px' )));
    }

    function mailto( $address, $text = '', $attr_set = array( ) ) {
        return AMP_protect_email( $address, $text );
        //return $this->link( 'mailto:' . $address, ( $text?$text:$address), $attr_set );
    }

    function a( $content, $attr_set = array( )) {
        return $this->tag( 'a', $content, $attr_set );
    }

    function anchor_named( $name ) {
        return $this->a( '', array( 'name' => $name ));
    }

    function input( $name, $value = null, $attr_set = array( )) {
        $attr_set['name'] = $name;
        $attr_set['value'] = $value;
        return $this->tag_single( 'input', $attr_set );
    }

    function select( $name, $value = null, $options = array( ), $attr_set = array( )) {
        return AMP_buildSelect( $name, $options, $value, $this->makeAttributes( $attr_set ));
    }

    function form( $content, $attr_set = array( ) ) {
        return $this->tag( 'form', $content, $attr_set );
    }

    function textarea( $name, $content=null, $attr_set=array( )) {
        $attr_set['name'] = $name;
        if ( isset( $attr_set['size'])) {
            if ( !isset( $attr_set['cols'])) {
                $attr_set['cols'] = $attr_set['size'];
            }
            if ( strpos( $attr_set['size'], ':') != 0 ) {
                $sizes = split( ':', $attr_set['size']);
                $attr_set['rows'] = $sizes[0];
                $attr_set['cols'] = $sizes[1];
            }
        }
        if ( !isset( $attr_set['rows'])) {
            $attr_set['rows'] = 4;
        }
        return $this->tag( 'textarea', $content, $attr_set );
    }

    function label( $element_name, $value, $attr_set=array( )) {
        $attr_set['for'] = $element_name;
        return $this->tag( 'label', $value, $attr_set );
    }

    function wysiwyg( $name, $value=null, $attr_set= array( )) {
        $attr_set['id'] = $name;
        $textarea = $this->textarea( $name, $value, $attr_set );

        if ( !AMP_USER_CONFIG_USE_WYSIWYG 
            || ( array_search( getBrowser( ), array( 'win/ie', 'mozilla' )) === FALSE))  {
            return $textarea;
        }

        require_once( 'AMP/Form/Element/Wysiwyg.php');
        $editor_script = new AMP_Form_Element_Wysiwyg( $name );
        $editor_script->execute( );
        return $textarea;

    }

    function submit( $name, $label, $attr_set = array( )) {
        $attr_set['type'] = 'submit';
        return $this->input( $name, $label, $attr_set );
    }

}

?>

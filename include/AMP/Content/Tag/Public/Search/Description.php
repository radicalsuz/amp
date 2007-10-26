<?php
require_once( 'AMP/Display/Detail.php');
require_once( 'AMP/Content/Tag/Tag.php');

class AMP_Content_Tag_Public_Search_Description extends AMP_Display_Detail {
    var $_css_class_container_item = 'list_intro';

    function AMP_Content_Tag_Public_Search_Description( $source ) {
        $this->__construct( $source );
    }

    function __construct( $source ) {
        parent::__construct( $this->_init_source_array( $source ));
    }

    function renderItem( $source ) {
        $text = ucwords(  AMP_pluralize( AMP_TEXT_TAG )) . ':' . $this->_renderer->space( )
                . join( ', ', array_map( array( $this, 'render_name'), $source ));
        return $this->_renderer->div( $text, array( 'class' => AMP_CONTENT_CSS_CLASS_LIST_SUBHEADER));
    }

    function render_name( $item ) {
        return $item->getName( );
    }

    function _init_source_array( $source ) {
        if ( is_object( $source )) {
           return array( $source );
        }
        if( is_string( $source )) {
            return $this->_validate_source( preg_split( '/\s?,\s?/', $source ));
        }
        if( is_array( $source )) {
            return $this->_validate_source( $source );
        }
    }

    function _validate_source( $source ) {
        $source_array = array( );
        foreach( $source as $item ) {
            if( is_object( $item )) {
                $source_array[] = $item;
                continue;
            }
            if( is_numeric( $item )) {
                $tag = new AMP_Content_Tag( AMP_Registry::getDbcon( ), $item );
                if( $tag->hasData( )) {
                    $source_array[] = $tag;
                    continue;
                }
            }

        }
        return $source_array; 

    }
}


?>

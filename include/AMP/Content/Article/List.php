<?php
require_once( 'AMP/Display/System/List.php');

class Article_List extends AMP_Display_System_List {

    var $columns= array( 'controls', 'id', 'name', 'section', 'date', 'listorder', 'class', 'status');

    function Article_List( $source = null, $criteria = array( ), $limit = null ) {
        $this->__construct( $source, $criteria, $limit );
    }

    function render_section_name( $source ) {
        $section_id = $source->getParent( );
        $section_names = AMP_lookup( 'sections');
        if ( !$section_id || !isset( $section_names[$section_id]) ) {
            return false;
        }
        return $section_names[$section_id];
    }

    function render_class_name( $source ) {
        $class_id = $source->getClass( );
        $class_names = AMP_lookup( 'classes');
        if ( !$class_id || !isset( $class_names[$class_id]) ) {
            return false;
        }
        return $class_names[$class_id];
    }

    function render_editor_name( $source ) {
        $editor_id = $source->getLastEditorId( );
        $user_names = AMP_lookup( 'users');
        if ( !( $editor_id && isset( $user_names[$editor_id]))) {
            return false;
        }
        return $user_names[$editor_id];
    }

    function render_preview( $source ) {
        return $this->_renderer->link( 
            AMP_route_for( 'article', $source->id, array( 'preview' => true ) ),
            $this->_renderer->image( AMP_SYSTEM_ICON_PREVIEW, array( 'class' => 'icon')),
            array( 'target' => '_blank', 'title' => AMP_TEXT_PREVIEW_ITEM )
        );
    }
}


?>

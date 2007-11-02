<?php

class Section_Public_List_Contents extends Section_Public_List {

    function Section_Public_List_Contents( $container = false, $criteria = array( ), $limit = null ) {
        $source = $this->_init_container( $container );
        $this->__construct( $source, $criteria, $limit );
    }

    function _renderItem( &$source ) {
        $text =     $this->render_subheader_format( $source->getName( ) )
                  . $this->render_blurb( $source );

        return $this->render_image_format( $this->render_image( $source ), $source )
             . $this->render_description_format( $text, $source )
             . $this->render_contents_format( $this->render_contents( $source ), $source )
                ;
    }

    function render_contents_format( $contents, $source ) {
        if( !$contents ) return false;
        return $this->_renderer->div( $contents, array( 'class' => AMP_CONTENT_CSS_CLASS_LIST_CONTENTS ));
    }

    function render_contents( $source ) {
        $display_type = $source->getDisplayClass( );
        $criteria = $source->getDisplayCriteria( );
        $placeholder = array( );
        $display = new $display_type( $placeholder, $criteria, $source->getListItemLimit( ) );

        $display->set_container( $source );
        $display->_class_pager=  'AMP_Display_Pager_Morelinkplus';
        $display->_path_pager=   'AMP/Display/Pager/Morelinkplus.php';
        $display->set_pager_limit( $this->_pager_limit, 'first' );
        $display->set_pager_request( array( 'type' => $source->id, 'list' => 'type'));

        return $display->execute( );
    }

}
?>

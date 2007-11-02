<?php

require_once( 'Modules/Gallery/SetDisplay.inc.php');
require_once( 'AMP/System/Data/Tree.php');

class Gallery_Display_Tree extends AMP_Content_Buffer {

    var $_source_array;
    var $_renderer;
    var $_tree;

    function Gallery_Display_Tree( &$source, $criteria = array( )) {
        $this->__construct( $source, $criteria );
    }

    function __construct( &$source, $criteria = array( )) {
        $criteria = $source->makeCriteria( $criteria );
        $this->_source_array = array_combine_key( array_keys( AMPContent_Lookup::instance( 'galleryMap' )), $source->search( $criteria ));
        $this->_init_renderer( $source );
        $this->_tree = &new AMP_System_Data_Tree( $source );

    }

    function _init_renderer( &$source ){
        $this->_renderer = &new GallerySet_Display( $source->_getSearchSource( ));

    }

    function execute( ){
        foreach( $this->_source_array as $sourceItem ){
            $this->add( 
                $this->_renderer->indent( 
                    $this->_renderer->_HTML_listItem( $sourceItem ), ( 50*$this->_tree->get_depth( $sourceItem->id ))
                    ));
        }
        return parent::execute( );
    }

}

?>

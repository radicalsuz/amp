<?php

require_once( 'AMP/System/List/Form.inc.php');
require_once( 'Modules/Gallery/Image/Set.inc.php');

class GalleryImage_List extends AMP_System_List_Form {
    var $name = 'Photo Gallery Images';
    var $col_headers = array( 
        'Image'         => '_makeThumb',
        'ID'            => 'id', 
        'File Name'     => 'name',
        'Gallery'       => 'GalleryName', 
        'Section'       => '_getSectionName', 
        'Order'         => 'order',
        'Status'        => 'publish'
        );
   // var $extra_columns = array( 'Thumb' => 'thumb');
    var $_pager_active = true;
    var $editlink = 'gallery_image.php';
    var $_url_add = 'gallery_image.php?action=add';
    var $_source_object = 'GalleryImage';
    var $name_field = 'name';
    
    var $_actions = array( 'publish', 'unpublish', 'delete', 'move', 'reorder');
    var $_action_args = array( 
                'move'      => array( 'gallery_id' ), 
                'reorder'   => array( 'order' )
                );
    var $_actions_global= array( 'reorder' );
    var $_thumb_attr = array( 'border' => 0 );

    function GalleryImage_List( &$dbcon, $criteria = null ){
        $this->init( $this->_init_source( $dbcon, $criteria ));
        //$source = &new GalleryImageSet( $dbcon, $gallery_id );
        //$this->init( $source );
        //$source->addSelectExpression( 'img AS thumb');
        //$source->readData( );
        
        //$this->addTranslation( 'thumb', '_getThumbDisplay');
        //$this->addLookup( 'galleryid', AMPContent_Lookup::instance( 'galleries'));
        //$this->addTranslation( 'section', '_getSectionName');
    }

    function _after_init( ){
        $this->_initThumbAttrs( );
        $this->addTranslation( 'order', '_makeInput');
    }

    function _makeThumb( &$source, $column_name ) {
        require_once( 'AMP/Content/Image.inc.php');
        $img = &new Content_Image( $source->getName( ));
        return $this->_HTML_link( $img->getURL( AMP_IMAGE_CLASS_ORIGINAL ), $this->_HTML_image($img->getURL( AMP_IMAGE_CLASS_THUMB ), $this->_thumb_attr ), array( 'target' => 'blank' ));
    }

    function _getSectionName( &$source, $column_name ){
        static $names_lookup = false;
        if ( !$names_lookup ) $names_lookup = &AMPContent_Lookup::instance( 'sections');
        if (! ( ( $section_id = $source->getSection( )) && isset( $names_lookup[$section_id]) )) return false;
        return $names_lookup[$section_id];
        
    }

    function _initThumbAttrs( ) {
        $reg = &AMP_Registry::instance();
        if ($thumb_attr = $reg->getEntry( AMP_REGISTRY_CONTENT_IMAGE_THUMB_ATTRIBUTES )) {
            $this->_thumb_attr = array_merge( $this->_thumb_attr, $thumb_attr );
        } else {
            $this->_thumb_attr['width'] = AMP_IMAGE_WIDTH_THUMB;
        }
    }

    function renderReorder( &$toolbar ){
        $action = 'reorder';
        return '&nbsp;&nbsp;&#124;&nbsp;&nbsp;' . $toolbar->renderDefault( $action );
    }

    function renderMove( &$toolbar ){
        $renderer = &$this->_getRenderer( );
        $type_options = &AMPContent_Lookup::instance( 'galleryMap' );
        if ( $type_options ){
            $type_options = array( '' => 'Select Gallery') + $type_options;
        } else {
            $type_options = array( '' => 'Select Gallery');
        }
                
        $toolbar->addEndContent( 
                $renderer->inDiv( 
                        '<a name="move_targeting"></a>'
                        . AMP_buildSelect( 'gallery_id', $type_options, null, $renderer->makeAttributes( array( 'class' => 'searchform_element')))
                        . '&nbsp;'
                        . $toolbar->renderDefault( 'move')
                        . '&nbsp;'
                        . "<input type='button' name='hideMove' value='Cancel' onclick='window.change_any( \"move_targeting\");'>&nbsp;",
                        array( 
                            'class' => 'AMPComponent_hidden', 
                            'id' => 'move_targeting')
                    ), 'move_targeting');

        return "<input type='button' name='showMove' value='Move' onclick='window.change_any( \"move_targeting\");'>&nbsp;";
    }

}

?>

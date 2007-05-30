<?php

require_once( 'AMP/System/List/Form.inc.php');
require_once( 'Modules/Gallery/Set.inc.php');
require_once( 'Modules/Gallery/Gallery.php');
require_once( 'AMP/System/Data/Tree.php');

class Gallery_List extends AMP_System_List_Form {
    var $name = 'Photo Galleries';
    var $col_headers = array( 
        'Gallery' => 'name', 
        'ID'=>'id', 
        'Order' => 'order',
        'Status' => 'publish',
        'Publish' => 'publishButton'
        );
	var $editlink = 'gallery_type.php';    
    var $_source_object = 'Gallery';
    var $_url_add = 'gallery_type.php?action=add';
    var $name_field = 'name';

    var $_actions = array( 'publish', 'unpublish', 'delete', 'move', 'reorder');
    var $_action_args = array( 
                'move'      => array( 'gallery_id' ), 
                'reorder'   => array( 'order' )
                );
    var $_actions_global= array( 'reorder' );
    var $previewlink = AMP_CONTENT_URL_GALLERY;

    function Gallery_List ( &$dbcon ){
        //$source = &new GallerySet( $dbcon );
        //$this->init( $source );
        $this->init( $this->_init_source( $dbcon ));
    }

    function _after_init( ){
        $this->addTranslation( 'order', '_makeInput');
        $this->addTranslation( 'name', '_formattedName');
        $this->_tree = &new AMP_System_Data_Tree( new Gallery( AMP_Registry::getDbcon( )));
    }

    function publishButton( &$source, $fieldname ){
        return AMP_publicPagePublishButton( $source->id, 'gallery_id'); 
    }

    function renderReorder( &$toolbar ){
        $action = 'reorder';
        return '&nbsp;&nbsp;&#124;&nbsp;&nbsp;' . $toolbar->renderDefault( $action );

    }
    function _formattedName( $value, $column_name, $data ) {
        if ( !isset( $this->_sort )) {
            return str_replace( strip_tags( $data[$this->name_field]), $data[$this->name_field], $this->_tree->render_option( $data['id'] ));
        }
        return $value;
    }

    function _setSortTree( &$source, $sort_direction = false ) {
        $lookup = &new AMPContentLookup_GalleryMap( );
        $lookup_data = $lookup->dataset;
        $order = array_keys( $lookup_data );
        $source = array_combine_key( $order, $source );
    }

    function _after_sort( &$source ) {
        $this->_setSortTree( $source );
    }

    function renderMove( &$toolbar ){
        $renderer = &$this->_getRenderer( );
        $type_options = &AMPContent_Lookup::instance( 'galleryMap' );
        if ( $type_options ) {
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

    function _HTML_header( ) {
        return $this->list_preview_link( ) . parent::_HTML_header( );
    }

}
?>

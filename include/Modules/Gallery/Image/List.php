<?php
require_once( 'AMP/Display/System/List.php');
require_once( 'Modules/Gallery/Image.inc.php');

class Gallery_Image_List extends AMP_Display_System_List {
    var $columns = array( 'select', 'controls', 'thumb', 'id', 'name', 'gallery_name', 'section', 'order', 'status');
    var $column_names = array(  'gallery_name' => 'Gallery', 'name' => 'File Name' );

    var $_source_object='GalleryImage';
    var $_actions = array( 'publish', 'unpublish', 'delete', 'move', 'reorder');
    var $_action_args = array( 
                'move'      => array( 'gallery_id' ), 
                'reorder'   => array( 'order' )
                );
    var $_actions_global= array( 'reorder' );

    var $_sort_sql_default = 'ordered';
    var $_sort_sql_translations = array( 
        'ordered' => 'galleryid, if( listorder, listorder, 999999999), img'
    );
    var $_pager_active = true;

    function Gallery_Image_List( $source = null, $criteria = array( ), $limit = null ) {
        $this->__construct( $source, $criteria, $limit );
    }

    function render_section( $source ) {
        if ( !( $section_id = $source->getSection( ))) return false;
        $sections = AMP_lookup( 'sections');
        if( !( $sections && isset( $sections[$section_id]))) return false;
        return $sections[$section_id];
    }
    function render_thumb( $source ) {
        if(!$img = &$source->getImageFile( )) return false;
        return $this->_renderer->link( $source->get_url_edit( ),
                                        $img->display->render_thumb( )
                                        );
    }

    function render_order( $source ) {
        return 
            $this->_renderer->input( 
                'order['.$source->id.']', 
                $source->getOrder( ), 
                    array(  'id' => 'order_'.$this->list_item_id( $source ), 
                            'size' => '3', 
                            'style' => 'margin-top:1em;',
                            'type'=>'text' )
                );
    }

    function render_toolbar_reorder( &$toolbar ) {
        $action = 'reorder';
        return    $this->_renderer->space( )
                . $this->_renderer->separator( )
                . $this->_renderer->space( )
                . $toolbar->renderDefault( $action );
    }

    function render_toolbar_move( &$toolbar ) {
        $gallery_options = &AMPContent_Lookup::instance( 'galleryMap' );
        if ( $gallery_options ) {
            $gallery_options = array( '' => 'Select Gallery') + $gallery_options;
        } else {
            $gallery_options = array( '' => AMP_TEXT_NONE_AVAILABLE );
        }
        $panel_contents = $this->_renderer->select( 'gallery_id', null, $gallery_options, array( 'class' => 'searchform_element') ) ;
        return $toolbar->add_panel( 'move', $panel_contents );

    }

    function render_status( $source ) {
        $status_options = AMP_lookup( 'status');
        return $status_options[ $source->getData( 'publish') ];
    }
    function validate_sort_link( $sort_request ) {
        if( $sort_request =='gallery_name') return false;
        return parent::validate_sort_link( $sort_request );
    }
}

?>

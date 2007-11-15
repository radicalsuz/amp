<?php
require_once( 'AMP/Display/System/List.php');
require_once( 'AMP/System/File/List.php');
require_once( 'AMP/System/File/Image.php');
require_once( 'AMP/Content/Image/Observer.inc.php');
require_once( 'AMP/Content/Image/List/Request.inc.php' );

class AMP_System_File_Image_List extends AMP_System_File_List {

    var $_pager_active = true;
    var $_pager_index = 'name';
    var $_pager_limit = 100;
    var $columns = array( 'select', 'controls', 'thumb', 'name', 'time', 'galleries', 'id' );
    var $column_headers = array( 'name' => 'Filename', 'time' => 'Date Uploaded');
    var $_source_object = 'AMP_System_File_Image';

    var $_actions = array( 'delete', 'gallery', 'recalculate');
    var $_action_args = array( 
        'gallery' => array( 'gallery_id' ),
        'recalculate' => array( 'image_width_thumb', 'image_width_tall', 'image_width_wide' )
        );
    var $_actions_global = array( 'recalculate' );
    var $_request_class = 'AMP_Content_Image_List_Request';
    var $_suppress_edit = false;
    
    function AMP_System_File_Image_List( $source, $criteria=array( ), $limit = null ) {
        if( !is_array( $source )) $source = null;
        $this->__construct( $source, $criteria, $limit );
    }

    function _search_path( ) {
        return AMP_LOCAL_PATH . DIRECTORY_SEPARATOR . AMP_IMAGE_PATH . DIRECTORY_SEPARATOR . AMP_IMAGE_CLASS_ORIGINAL . DIRECTORY_SEPARATOR;
    }


    function render_thumb( $source ) {
        return $this->_renderer->link( $source->get_url_edit( ),
                                        $source->display->render_thumb( )
                                        );
    }
    
    function render_galleries( $source ) {
        $galleries = AMP_lookup( 'galleries_by_image', $source->getName( ));
        if( empty( $galleries )) return false;
        $names = array_combine_key( $galleries, AMP_lookup( 'galleries'));
        $links = array_map( array( $this, 'render_gallery_link'), array_keys( $galleries), $names);
        return join( $this->_renderer->newline( ), $links );
    }

    function render_gallery_link( $gallery_image_id, $name ){
        return $this->_renderer->link( AMP_url_update( AMP_SYSTEM_URL_GALLERY_IMAGE, array( 'id' => $gallery_image_id )), AMP_trimText( $name,30 ), array( 'title' => $name ));
    }

    function render_toolbar_gallery( &$toolbar ) {
        $gallery_options = &AMPContent_Lookup::instance( 'galleries' );
        $gallery_options = array( '' => sprintf( AMP_TEXT_SELECT, AMP_TEXT_GALLERY )) + $gallery_options;
        $panel_contents = $this->_renderer->select( 'gallery_id', null, $gallery_options, array( 'class' => 'searchform_element') ) ;
        return $toolbar->add_panel( 'gallery', $panel_contents );

    }

    function render_toolbar_recalculate( &$toolbar ) {
        $panel_contents =
          $this->_renderer->bold( AMP_TEXT_SELECT_NEW_WIDTHS_FOR . ':' )
        . $this->_renderer->newline( 2 )
        . ucwords( AMP_pluralize( AMP_TEXT_IMAGE_CLASS_THUMB )) . ': ' 
            . $this->_renderer->input( 'image_width_thumb', AMP_IMAGE_WIDTH_THUMB, array( 'class' => 'searchform_element', 'size' => '4'))
            #. '<input name="image_width_thumb" value='.AMP_IMAGE_WIDTH_THUMB.' class="searchform_element" size="4">' ;
            . $this->_renderer->space( 2 )
        . ucwords( AMP_pluralize( AMP_TEXT_IMAGE_CLASS_OPTIMIZED_TALL )) . ': ' 
            . $this->_renderer->input( "image_width_tall", AMP_IMAGE_WIDTH_TALL, array( 'class' => 'searchform_element', 'size' => '4' )) . $this->_renderer->space( 2 ) 
        . ucwords( AMP_pluralize( AMP_TEXT_IMAGE_CLASS_OPTIMIZED_WIDE )) . ': ' 
            . $this->_renderer->input( "image_width_wide", AMP_IMAGE_WIDTH_WIDE, array( 'class' => 'searchform_element', 'size' => '4' )) . $this->_renderer->space( 2 ) 
            . $this->_renderer->newline( 2 );
        return    $this->_renderer->space( )
                . $this->_renderer->separator( )
                . $this->_renderer->space( )
                . $toolbar->add_panel( 'recalculate', $panel_contents );

    }
    function render_controls( $source ) {
        return 
              $this->_renderer->div( 
                  $this->render_edit( $source )
                . $this->render_crop( $source )
            , array( 'class' => 'icon list_control' ));

    }

    function render_crop( $source ) {
        return 
            $this->_renderer->link( 
                AMP_url_update( AMP_SYSTEM_URL_IMAGES, array_merge( $this->link_vars, array( 'action' => 'crop', 'id' => $source->getName( )) )),
                $this->_renderer->image( AMP_SYSTEM_ICON_CROP, array('alt' => AMP_TEXT_CROP, 'class' => 'icon' )),
                array( 'title' => AMP_TEXT_CROP, 'target' => $this->link_target_edit, 'id' => 'crop_'.$this->list_item_id( $source ) )
            );
    }

}
?>

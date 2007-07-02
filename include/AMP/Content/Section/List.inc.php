<?php

require_once( 'AMP/System/List/Form.inc.php');
require_once( 'AMP/Content/Section/Set.inc.php' );
require_once( 'AMP/Content/Section.inc.php' );
require_once( 'AMP/System/List/Observer.inc.php');

class Section_List extends AMP_System_List_Form {
    var $name = "Section";
    var $col_headers = array( 
        'Section' => 'name',
        'ID'    => 'id',
        'Status'=> 'publish',
        'Order' => 'order',
        'Navigation' => 'navIndex');

    var $editlink = 'section.php';
    var $_url_add = 'section.php?action=add';
    var $previewlink = '/section.php';

    var $_source_object = 'Section';

    var $_map;
    var $_renderer;
    var $_observers_source = array( 'AMP_System_List_Observer');
    var $_actions = array( 'publish', 'unpublish', 'trash', 'move', 'reorder');
    var $_action_args = array(
            'reorder'   => array( 'order' ), 
            'move'      => array( 'section_id' ), 
        );

    var $_actions_global = array( 'reorder');
    var $name_field = 'name';

    function Section_List( &$dbcon, $criteria = array( ) ) {
        $criteria['allowed'] = 1; 
        $this->init( $this->_init_source( $dbcon, $criteria  ) );
        $this->_map = &AMPContent_Map::instance( );
    }

    function _after_init( ){
        $this->addTranslation( 'name', '_formattedName');
        $this->addTranslation( 'order', '_makeInput');
    }

    function _formattedName( $value, $column_name, $data ) {
        if ( !isset( $this->_sort )) {
            $depth = $this->_map->getDepth( $data['id'] ) - 1;
            if ( $depth < 1 ) return $value;
            return '&nbsp;' . str_repeat( '&nbsp;', ( $depth*8)) . $value;
        }
        require_once( 'AMP/Content/Map/Breadcrumb.inc.php');
        $breadcrumb = &AMP_Breadcrumb_Content::instance( );
        $breadcrumb->findSection( $data['id']);
        $renderer = &$this->_getRenderer( );

        return  $renderer->inDiv( 
                    $value  
                    . $renderer->newline( )
                    . $renderer->inSpan( $breadcrumb->execute( ) , array( 'class' => 'photocaption' )),
                    array( 'style' => 'padding:10px;'));

    }

    function navIndex( &$source, $fieldname ){

        $renderer = &$this->_getRenderer( );
        return  $renderer->inDiv( 
                AMP_navCountDisplay_Section( $source->id ),
                    array( 'style' => 'margin:3px;'));

    }

    function renderReorder( &$toolbar ){
        $action = 'reorder';
        return '&nbsp;&nbsp;&#124;&nbsp;&nbsp;' . $toolbar->renderDefault( $action );

    }

    function renderMove( &$toolbar ){
        $renderer = &$this->_getRenderer( );
        $section_options = &AMPContent_Lookup::instance( 'sectionMap' );
        if ( $section_options ) {
            $section_options = array( '' => 'Select Section', AMP_CONTENT_MAP_ROOT_SECTION => '__' . AMP_SITE_NAME  . '__') + $section_options ;
        } else {
            $section_options = array( '' => 'Select Section', AMP_CONTENT_MAP_ROOT_SECTION => AMP_SITE_NAME );
        }
                
        $toolbar->addEndContent( 
                $renderer->inDiv( 
                        '<a name="move_targeting"></a>'
                        . AMP_buildSelect( 'section_id', $section_options, null, $renderer->makeAttributes( array( 'class' => 'searchform_element')))
                        . '&nbsp;'
                        . $toolbar->renderDefault( 'move')
                        . '&nbsp;'
                        . "<input type='button' name='hideMove' value='Cancel' onclick='window.change_any( \"move_targeting\");'>&nbsp;",
                        array( 
                            'class' => 'AMPComponent_hidden', 
                            'id' => 'move_targeting')
                    ), 'move_targeting');

        //return "<input type='button' name='showMove' value='Move' onclick='window.change_any( \"move_targeting\");window.scrollTo( 0, document.anchors[\"move_targeting\"].y );'>&nbsp;";
        return "<input type='button' name='showMove' value='Move' onclick='window.change_any( \"move_targeting\");$( \"move_targeting\").scrollTo();'>&nbsp;";

    }

    function _HTML_previewLink( $id ) {
        if ( !isset( $this->previewlink )) return false;
        $renderer = &AMP_get_renderer( );
        return  '<a href="' . AMP_URL_AddVars( $this->previewlink , 'id='.$id) .'" target="_blank" title="'.AMP_TEXT_PREVIEW_ITEM.'">' 
                . '<img src="' . AMP_SYSTEM_ICON_PREVIEW . '" width="16" height="16" border=0></a>'
                . $renderer->space( )
                . $renderer->link( AMP_URL_AddVars( AMP_SYSTEM_URL_ARTICLE, 'section='.$id),
                                    $renderer->image( AMP_SYSTEM_ICON_VIEW, array( 'width' => 16, 'height' => 16, 'border' => 0 ) ),
                                    array( 'title' => AMP_TEXT_CONTENT_PAGES ));
    }

    function renderTrash( &$toolbar ){
        $renderer = &AMP_get_renderer( );
        $tool_name = $toolbar->submitGroup . '[trash]';
        $label = AMP_TEXT_TRASH;
        $attr['onclick'] = 'return confirmSubmit( "'.AMP_TEXT_LIST_CONFIRM_DELETE_SECTIONS.AMP_TEXT_LIST_CONFIRM_DELETE.'");';
        return $renderer->submit( $tool_name, $label, $attr ) . $renderer->space( );
        
    }

    function _after_request( ) {
        if ( $this->_request->getPerformedAction( ) != 'trash') {
            return;
        }
        ampredirect( $_SERVER['REQUEST_URI']);
        AMP_permission_update( );
    }

}
?>

<?php

require_once( 'AMP/System/List/Form.inc.php');
require_once( 'AMP/Content/Section/Set.inc.php' );
require_once( 'AMP/Content/Section.inc.php' );
require_once( 'AMP/Content/Display/Observer.inc.php');

class Section_List extends AMP_System_List_Form {
    var $name = "Section";
    var $col_headers = array( 
        'Section' => 'name',
        'ID'    => 'id',
        'Status'=> 'publish',
        'Order' => 'order',
        'Navigation' => 'navIndex');
    var $editlink = 'section.php';
    var $previewlink = '/section.php';
    var $_source_object = 'Section';

    var $_map;
    var $_renderer;
    var $_observers_source = array( 'AMP_Content_Display_Observer');
    var $_actions = array( 'publish', 'unpublish', 'delete', 'reorder');
    var $_action_args = array( 'reorder' => array( 'order'));
    var $_actions_global = array( 'reorder');

    function Section_List( &$dbcon ) {
        $this->init( $this->_init_source( $dbcon  ) );
        $this->_map = &AMPContent_Map::instance( );
    }
    function _after_init( ){
        $this->addTranslation( 'name', '_formattedName');
        $this->addTranslation( 'order', '_makeInput');
    }

    function _formattedName( $value, $column_name, $data ) {
        if ( !isset( $this->_sort )) {
            $depth = $this->_map->getDepth( $data['id'] ) - 1;
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
}
?>

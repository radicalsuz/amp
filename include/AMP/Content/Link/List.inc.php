<?php

require_once( 'AMP/System/List/Form.inc.php');
require_once( 'AMP/Content/Link/Link.php');
require_once( 'AMP/System/List/Observer.inc.php');

class AMP_Content_Link_List extends AMP_System_List_Form {
    var $name = "Link";
    var $col_headers = array( 
        'Link' => 'name',
        'URL' => 'url',
        'Order' => 'order',
        'Type' => 'LinkTypeName',
        'Status' => 'publish',
        'ID'    => 'id');
    var $editlink = 'links.php';
    var $_url_add = 'links.php?action=add';

    var $name_field = 'name';
    var $_source_object = 'AMP_Content_Link';
    var $_observers_source = array( 'AMP_System_List_Observer' );
    var $_actions = array( 'publish', 'unpublish', 'delete', 'move', 'reorder');
    var $_action_args = array( 
                'move'      => array( 'link_type_id' ), 
                'reorder'   => array( 'order' )
                );
    var $_actions_global = array( 'reorder');
    var $previewlink = AMP_CONTENT_URL_LINKS;

    function AMP_Content_Link_List( &$dbcon ) {
        $this->init( $this->_init_source( $dbcon ) );
    }

    function _after_init( ){
        $this->addTranslation( 'order', '_makeInput');
    }

    function renderReorder( &$toolbar ){
        $action = 'reorder';
        return '&nbsp;&nbsp;&#124;&nbsp;&nbsp;' . $toolbar->renderDefault( $action );

    }

    function renderMove( &$toolbar ){
        $renderer = &$this->_getRenderer( );
        $type_options = &AMPContent_Lookup::instance( 'linkTypeMap' );
        if ( $type_options ) {
            $type_options = array( '' => 'Select Link Type') + $type_options;
        } else {
            $type_options = array( '' => 'Select Link Type');
        }
                
        $toolbar->addEndContent( 
                $renderer->inDiv( 
                        '<a name="move_targeting"></a>'
                        . AMP_buildSelect( 'link_type_id', $type_options, null, $renderer->makeAttributes( array( 'class' => 'searchform_element')))
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

    function _HTML_header() {
        $this->previewlink = AMP_CONTENT_URL_LINKS;
        //$result = parent::_HTML_header( ) . $this->list_preview_link( );
        $result = $this->list_preview_link( ). parent::_HTML_header( );
        unset( $this->previewlink );
        return $result;
    }
}
?>

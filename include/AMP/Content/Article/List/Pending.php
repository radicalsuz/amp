<?php

require_once( 'AMP/Content/Article/List.php');
require_once( 'AMP/Content/Article/List/Request.php');

class Article_List_Pending extends Article_List {
    var $columns= array( 'select', 'controls', 'name', 'date', 'section_name', 'class_name', 'editor_name', 'id' );
    var $_source_criteria = array( 'status' => AMP_CONTENT_STATUS_PENDING, 'allowed' => 1 );
    var $_source_object = 'Article';
    var $_actions = array( 'publish', 'request_revision' );
    var $_request_class = 'Article_List_Request';
    var $_action_args = array(
            'request_revision'   => array( 'revision_comments' )
            );

    function Article_List_Pending( $source = null, $criteria = array( ), $limit = null ) {
        $this->__construct( $source, $criteria, $limit );
    }

    function _output_empty( ) {
        //do nothing
    }

    function execute( ) {
        $map = new ComponentMap_Article( );
        if ( !$map->isAllowed( 'publish' )) {
            return false;
        }
        return parent::execute( );
    }

    function render_toolbar_request_revision( &$toolbar ) {
        $panel_contents =     $this->_renderer->span( "Comments for Revision:", array( 'class' => 'searchform_label')) 
                            . $this->_renderer->newline( )
                            . $this->_renderer->textarea( 'revision_comments', null, array( 'class' => 'searchform_element', 'rows' => 15, 'cols' => 65  )) ;
        return $toolbar->add_panel( 'request_revision', $panel_contents );
    }

    function _renderHeader( ) {
        $text = AMP_TEXT_CONTENT_STATUS_DISPLAY_HEADING;
        return $this->_renderer->div( $text, array( 'class' => 'system_heading'));
    }

}

?>

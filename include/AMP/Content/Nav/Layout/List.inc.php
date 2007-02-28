<?php

require_once( 'AMP/System/List/Form.inc.php');
require_once( 'AMP/Content/Nav/Layout/Layout.php');

class AMP_Content_Nav_Layout_List extends AMP_System_List_Form{
    var $name = "Nav_Layout";
    var $col_headers = array( 
        'Name' => 'name',
        'Anchor' => '_describeLayoutAnchor',
        'ID'    => 'id');
    var $editlink = AMP_SYSTEM_URL_NAV_LAYOUT;
    var $name_field = 'name';
    var $_source_object = 'AMP_Content_Nav_Layout';
    var $_observers_source = array( 'AMP_System_List_Observer');
    var $_url_add = AMP_SYSTEM_URL_NAV_LAYOUT_ADD;
    var $_actions = array( 'delete', 'copy');
    var $_action_args = array( 
                'copy'  => array( 'section_id_list', 'section_id_content', 'class_id', 'publicpage_id' )
                );

    function AMP_Content_Nav_Layout_List( &$dbcon, $criteria = array( ) ) {
        $criteria['allowed'] = 1;//AMP_Content_Nav_Layout::makeCriteriaAllowed( );
        $this->init( $this->_init_source( $dbcon, $criteria  ) );
    }

    function _describeLayoutAnchor( &$source, $fieldname ){
        if ( !( $layout_anchor = $source->getLayoutAnchor( ))) return false;
        return ucwords( $layout_anchor['description'] ) . ': ' . $layout_anchor['name'];
    }

    function renderCopy( &$toolbar ) {
        $renderer = AMP_get_renderer( );
        $class_options = AMP_base_select_options( AMP_lookup( 'classes'), 'Select Class' );
        $publicpage_options = AMP_base_select_options( AMP_lookup( 'introtexts'), 'Select Public Page');
        $section_options_content = AMP_base_select_options( AMP_lookup( 'sectionMap'), 'Select Section ( '. AMP_TEXT_CONTENT_PAGES .' )');
        $section_options_list    = AMP_base_select_options( AMP_lookup( 'sectionMap'), 'Select Section ( '. AMP_TEXT_LIST_PAGES .' )');

        $copy_selects = array( 
            AMP_TEXT_LIST_NAV_LAYOUT_TARGET_COPY,
            $renderer->newline( ),
            AMP_buildSelect( 'section_id_content',  $section_options_content,   null, $renderer->makeAttributes( array( 'class' => 'searchform_element'))),
            AMP_buildSelect( 'section_id_list',     $section_options_list,   null, $renderer->makeAttributes( array( 'class' => 'searchform_element'))),
            $renderer->newline( ),
            AMP_buildSelect( 'class_id',            $class_options,     null, $renderer->makeAttributes( array( 'class' => 'searchform_element'))),
            AMP_buildSelect( 'publicpage_id',       $publicpage_options,null, $renderer->makeAttributes( array( 'class' => 'searchform_element'))),
            $renderer->newline( )

        ) ;
        return $toolbar->addTab( 'copy', $copy_selects);
    }
}
?>

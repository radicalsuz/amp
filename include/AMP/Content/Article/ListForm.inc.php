<?php

//require_once ('AMP/Content/Article/List.inc.php' );
//require_once ('AMP/Content/Article/Actions.inc.php' );
//require_once ('AMP/System/List/Pager.inc.php' );
require_once ('AMP/System/List/Form.inc.php' );
require_once( 'AMP/Content/Article.inc.php');

//class Article_ListForm extends Article_List {
class Article_ListForm extends AMP_System_List_Form {

    var $col_headers = array(
        'ID'        =>  'id',
        'Title'     =>  'title',
        'Section'   =>  'section',
        'Date'      =>  'assignedDate',
        'Order'     =>  'order',
        'Class'     =>  'class',
        'Status'    =>  'publish' );
    var $editlink = AMP_SYSTEM_URL_ARTICLE;
    var $previewlink = '/article.php?preview=1&cache=0';
    var $name_field = 'title';
    var $_source_object = 'Article';

    var $_observers_source = array( 'AMP_System_List_Observer');
    var $_actions = array( 'publish', 'unpublish', 'delete', 'move', 'regionize', 'tag','reorder' );
    var $_action_args = array( 
            'reorder'   => array( 'order' ), 
            'move'      => array( 'section_id', 'class_id', 'related_section_id' ), 
            'regionize' => array( 'region_id' ),
            'tag'       => array( 'tag_id', 'tags_text' )
        );
    var $_actions_global = array( 'reorder');

    var $formname = "Article_List";
    var $_pager_active = true;
    var $_source_criteria = array( 'type' => 'type not in ( 2 )' );
    var $_sort_default = array( 'pageorder', 'date DESC', 'id');
    var $_sort_translations_sql = array( 
        'assignedDate'  => 'date DESC', 
        'section'       => 'type', 
        'order'         => 'pageorder'
        );

    function Article_ListForm ( &$dbcon, $criteria = null ) {
        if ( isset( $criteria['type']) || isset( $criteria['section'])) {
            $this->_source_criteria = array( );
        } else {
            $criteria['allowed'] = 1;
        }
        $this->_init_default_sort( );
        $this->init( $this->_init_source( $dbcon, $criteria ));
    }

    function _init_default_sort( ){
        $this->_sort_default = array( 
            'type' => 'type',
            'publish' => 'publish DESC',
            'pageorder' => 'if ( !isnull( pageorder ) AND pageorder != "" AND pageorder !=0, pageorder, '.AMP_CONTENT_LISTORDER_MAX.')',
            'date' => 'if ( !isnull( date ), date, "0000-00-00" ) DESC',
            'id' => 'id DESC'
        );
        $this->_sort_translations_sql['order'] = 'if ( !isnull( pageorder ) AND pageorder != "" AND pageorder !=0 , pageorder, '. AMP_CONTENT_LISTORDER_MAX .')';
    }


    function _after_init( ){
        $this->addTranslation( 'order', '_makeInput' );
        $this->addLookup( 'class', AMPContent_Lookup::instance( 'classes' ));
        $this->addLookup( 'section', AMPContent_Lookup::instance( 'sections' ));
        if ( !AMP_Authorized( AMP_PERMISSION_CONTENT_EDIT )) {
            $this->suppress['editcolumn'] = true;
            $this->suppress['selectcolumn'] = true;
        }

    }

    function _after_init_search( $criteria = null ){
        $this->_url_add = AMP_Url_AddVars( AMP_SYSTEM_URL_ARTICLE, array( 'action=add' ));
        if ( !isset( $criteria )) return false;
        $section_id = ( isset( $criteria['section']) ? 
                            $criteria['section'] 
                            : ( isset( $criteria['type']) ? 
                                $criteria['type'] : false )
                            );
        $class_id = ( isset( $criteria['class']) ? $criteria['class'] : false );
        if ( $section_id ){
            unset( $this->_sort_default['type']);
            $this->_url_add = AMP_Url_AddVars( $this->_url_add, array( 'section=' . $section_id ));
        }
        if ( $class_id ){
            $this->_url_add = AMP_Url_AddVars( $this->_url_add, array( 'class=' . $class_id ));
        }

    }

    function _after_request( ){
        $list_location_cookie = get_class( $this ) . '_ListLocation';
        if ( isset( $_COOKIE[ $list_location_cookie ]) && $_COOKIE[ $list_location_cookie ]) {
            ampredirect( $_COOKIE[ $list_location_cookie ]);
        }

    }

    function renderReorder( &$toolbar ){
        $action = 'reorder';
        return '&nbsp;&nbsp;&#124;&nbsp;&nbsp;' . $toolbar->renderDefault( $action );

    }

    function renderMove( &$toolbar ){
        $renderer = AMP_get_renderer( );

        //section options
        $base_section_options = &AMPContent_Lookup::instance( 'sectionMap' );
        if ( $base_section_options ) {
            $section_options = array( '' => 'Select Section') + $base_section_options;
        } else {
            $section_options = array( '' => 'Select Section');
        }

        //create related sections
        if ( $base_section_options ){
            $related_section_options = array( '' => 'Select Related Section' ) + $base_section_options;
        } else {
            $related_section_options = array( '' => 'Select Related Section' );
        }

        //class options
        $class_options = &AMPContent_Lookup::instance( 'classes' );
        if ( $class_options ){
            $class_options = array( '' => 'Select Class') + $class_options;
        } else {
            $class_options = array( '' => 'Select Class'); 
        }

        $move_selects = array( 
            AMP_buildSelect( 'section_id',  $section_options,   null, $renderer->makeAttributes( array( 'class' => 'searchform_element'))),
            AMP_buildSelect( 'class_id',    $class_options,     null, $renderer->makeAttributes( array( 'class' => 'searchform_element'))),
            AMP_buildSelect( 'related_section_id', $related_section_options, null, $renderer->makeAttributes( array( 'class' => 'searchform_element')))
            );

        return $toolbar->addTab( 'move', $move_selects );
        /*        
        $toolbar->addEndContent( 
                $renderer->inDiv( 
                        '<a name="move_targeting"></a>'
                        . AMP_buildSelect( 'section_id', $section_options, null, $renderer->makeAttributes( array( 'class' => 'searchform_element')))
                        . '&nbsp;'
                        . AMP_buildSelect( 'class_id', $class_options, null, $renderer->makeAttributes( array( 'class' => 'searchform_element')))
                        . '&nbsp;'
                        . $toolbar->renderDefault( 'move')
                        . '&nbsp;'
                        . "<input type='button' name='hideMove' value='Cancel' onclick='window.change_any( \"move_targeting\");'>&nbsp;",
                        array( 
                            'class' => 'AMPComponent_hidden', 
                            'id' => 'move_targeting')
                    ), 'move_targeting');

        //return "<input type='button' name='showMove' value='Move' onclick='window.change_any( \"move_targeting\");if ( $(\"region_targeting\").style.display==\"block\") window.change_any( \"region_targeting\" );window.scrollTo( 0, document.anchors[\"move_targeting\"].y );'>&nbsp;";
        return "<input type='button' name='showMove' value='Move' "
                . "onclick='window.change_any( \"move_targeting\" );"
                . "if ( $(\"region_targeting\").style.display==\"block\") window.change_any( \"region_targeting\");"
                . "if ( $(\"relate_targeting\").style.display==\"block\") window.change_any( \"relate_targeting\");"
                . "window.scrollTo( 0, document.anchors[\"move_targeting\"].y );'>&nbsp;";
                */

    }

    function renderRegionize( &$toolbar ){
        $renderer = &$this->_getRenderer( );
        $region_options = &AMPSystem_Lookup::instance( 'regions' );
        if ( $region_options ){
            $region_options = array( '' => 'Select Region') + $region_options;
        } else {
            $region_options = array( '' => 'Select Region');
        }
        $region_select = array( AMP_buildSelect( 'region_id', $region_options, null, $renderer->makeAttributes( array( 'class' => 'searchform_element'))) );
        return $toolbar->addTab( 'regionize', $region_select);
                
        /*
        $toolbar->addEndContent( 
                $renderer->inDiv( 
                        '<a name="region_targeting"></a>'
                        . AMP_buildSelect( 'region_id', $region_options, null, $renderer->makeAttributes( array( 'class' => 'searchform_element')))
                        . '&nbsp;'
                        . $toolbar->renderDefault( 'regionize')
                        . '&nbsp;'
                        . "<input type='button' name='hideRegions' value='Cancel' onclick='window.change_any( \"region_targeting\");'>&nbsp;",
                        array( 
                            'class' => 'AMPComponent_hidden', 
                            'id' => 'region_targeting')
                    ), 'region_targeting');

        //return "<input type='button' name='showRegion' value='Regionize' onclick='window.change_any( \"region_targeting\" );if ( $(\"move_targeting\").style.display==\"block\") window.change_any( \"move_targeting\");window.scrollTo( 0, document.anchors[\"region_targeting\"].y );'>&nbsp;";
        return "<input type='button' name='showRegion' value='Regionize' "
                . "onclick='window.change_any( \"region_targeting\" );"
                . "if ( $(\"move_targeting\").style.display==\"block\") window.change_any( \"move_targeting\");"
                . "if ( $(\"relate_targeting\").style.display==\"block\") window.change_any( \"relate_targeting\");"
                . "window.scrollTo( 0, document.anchors[\"region_targeting\"].y );'>&nbsp;";
                */

    }

    function renderTag( &$toolbar ) {
        $renderer = AMP_get_renderer( );
        $tags = AMPSystem_Lookup::instance( 'tags');
        if ( !$tags ) {
            $tags = array( );
        }
        $tag_add =  $renderer->inSpan( AMP_TEXT_ADD . $renderer->space( ) . AMP_pluralize( AMP_TEXT_TAG ) . ': ', array( 'class' => 'searchform_element'))
                    . '<input name = "tags_text" type="text" class="searchform_element">';
        $tag_options = array( '' => 'Select ' . AMP_pluralize( ucfirst( AMP_TEXT_TAG ))) + $tags;
        $tag_selects = array(   AMP_buildSelect( 'tag_id', $tag_options, null, $renderer->makeAttributes( array( 'class' => 'searchform_element'))),
                                $tag_add );

        return $toolbar->addTab( 'tag', $tag_selects );

    }

    //deprecated
    function renderRelate( &$toolbar ){
        $renderer = &$this->_getRenderer( );
        if ( $base_section_options ){
            $related_section_options = array( '' => 'Select Related Section' ) + $base_section_options;
        } else {
            $related_section_options = array( '' => 'Select Related Section' );
        }
        $relate_select =    AMP_buildSelect( 'related_section_id', $section_options, null, $renderer->makeAttributes( array( 'class' => 'searchform_element')));
        return $toolbar->addTab( 'relate', $relate_select );
                
        /*
        $toolbar->addEndContent( 
                $renderer->inDiv( 
                        '<a name="relate_targeting"></a>'
                        . AMP_buildSelect( 'related_section_id', $section_options, null, $renderer->makeAttributes( array( 'class' => 'searchform_element')))
                        . '&nbsp;'
                        . $toolbar->renderDefault( 'relate')
                        . '&nbsp;'
                        . "<input type='button' name='hideRelate' value='Cancel' onclick='window.change_any( \"relate_targeting\");'>&nbsp;",
                        array( 
                            'class' => 'AMPComponent_hidden', 
                            'id' => 'relate_targeting')
                    ), 'relate_targeting');

        return "<input type='button' name='showRelate' value='Relate' "
                . "onclick='window.change_any( \"relate_targeting\" );"
                . "if ( $(\"move_targeting\").style.display==\"block\") window.change_any( \"move_targeting\");"
                . "if ( $(\"region_targeting\").style.display==\"block\") window.change_any( \"region_targeting\");"
                . "window.scrollTo( 0, document.anchors[\"relate_targeting\"].y );'>&nbsp;";
                */

    }

    /*
    function _noRecordsOutput( ){
        //$this->_searchFailureNotice( );
        return parent::_noRecordsOutput( );
    }
    */

    function _HTML_startForm() {
        if ( isset( $this->suppress['form_tag']) && $this->suppress['form_tag']) return false;
        $url_value = PHP_SELF_QUERY( ); 
        //does not auto_add 'action' value to URL
        return '<form name="' . $this->formname .'" method="POST" action="' . $url_value ."\">\n";
    }
}
?>

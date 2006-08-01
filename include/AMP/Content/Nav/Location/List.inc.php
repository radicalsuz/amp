<?php
require_once( 'AMP/System/List/Form.inc.php' );
require_once( 'AMP/Content/Nav/Location/Location.php' );
require_once( 'AMP/Content/Nav/Location/Toolbar.php');
require_once( 'AMP/Content/Nav/Location/ComponentMap.inc.php');

class AMP_Content_Nav_Location_List extends AMP_System_List_Form {
    var $name = "Nav_Location";
    var $copier_name = 'nav_locations_copier';
    var $col_headers = array( 
        'Nav'        => 'navid',
        'Position'   =>  'position',
        'Remove'   =>  '_listControls',
        );
    var $editlink = AMP_SYSTEM_URL_NAV_LAYOUT;
    var $_source_object = 'AMP_Content_Nav_Location';
    var $suppress = array( 
            'header' => true, 
            'editcolumn' => true, 
            'sortlinks' => true, 
            'selectcolumn' => true, 
            'addlink'=>true, 
            'form_tag' => true 
            );
    var $_actions = array( 'add' );
    var $_css_class_columnheader = 'list_column_header';
    var $_css_id_container_table = 'nav_location_listing';
    var $_toolbar_class = 'AMP_Content_Nav_Location_Toolbar';

    var $copier_name = 'nav_locations_copier';
    var $_copier;
    var $formname = 'nav_layouts';
    var $_url_add = AMP_SYSTEM_URL_NAV_LAYOUT_ADD;

    function AMP_Content_Nav_Location_List( &$dbcon ){
        $this->init( $this->_init_source( $dbcon ));
        $this->_initCopier( );
        //$this->addLookup( 'position', AMPContent_Lookup::instance( 'navPositions'));
    }

    function renderAdd( &$toolbar ){
        $field_def = $this->_copier->getAddButton( $this->copier_name );
		return "<input name='add_nav_location' type='button' value='Add Navigation Element'  onclick='DuplicateElementSet( window.nav_locations_copier, parentRow( this ).rowIndex );' class='searchform_element'>";
    }

    function _navSelect( &$source, $fieldname ){
        return $this->_makeSelect( $source->getNavId( ), 'navid', $source->getData( ), AMPContent_Lookup::instance( 'navs' ));
    }
    function _positionSelect( &$source, $fieldname ){
        return $this->_makeSelect( $source->getPosition( ), 'position', $source->getData( ), AMPContent_Lookup::instance( 'navPositions' ));
    }

    function _listControls( &$source, $fieldname ){
        $renderer = $this->_getRenderer( );
		 return       "<input name='remove_nav_location[".$source->id."]' type='button' value='Remove'  onclick='RemoveItem(".$source->id.");' class='searchform_element'>";
    }

    function _getSourceRow( ) {
        if ( $posted_values = $this->_copier->returnSets( $this->copier_name )) return false;
        $all_data = array( );
        $n = 0;
        while( $all_data[++$n] = parent::_getSourceRow( )){
            //collect data
        }
        $result_data = $this->_translateDataForCopier( $all_data );
        $this->_copier->addSets( $this->copier_name, $result_data );

        return false;
    }

    function _translateDataForCopier( $data ){
        $result_data = array( );
        foreach( $data as $row_count => $current_row ) {
            foreach( $current_row as $key => $value ){
                $result_data[ $this->copier_name . '_' . $key ][ $row_count ] = $value;

            }
        }
        return $result_data;
    }

    function addLocations( $data ){
        $copier_data = $this->_translateDataForCopier( $data );
        $this->_copier->addSets( $this->copier_name, $copier_data );
    }

    function _initCopier( ){
        require_once( 'AMP/Form/ElementCopierScript.inc.php');
        require_once( 'AMP/Content/Nav/Location/Form.inc.php');

        $form = &new AMP_Content_Nav_Location_Form( );
        $form->_getValueSet( 'navid');
        $form->_getValueSet( 'position');

        $copier_fields = array( 
            'navid'     => $form->getField( 'navid'),
            'position'  => $form->getField( 'position'),
            'id'        => array( 'type' => 'hidden'));
        foreach( $copier_fields as $key => $field_def ){
            unset( $copier_fields[$key]['label']);
        }

        $this->_copier = &ElementCopierScript::instance( );
        $this->_copier->addCopier( $this->copier_name, $copier_fields, $this->formname );
        $this->_copier->setSingleRow( true, $this->copier_name );
        $this->_copier->setLabelColumn( false, $this->copier_name );
        $this->_copier->setRowOffset( "0", $this->copier_name );
        $this->_copier->setFormTable( $this->_css_id_container_table, $this->copier_name );
        $this->_copier->setElementClass( 'system_list_input', $this->copier_name );

        if ( !empty( $_POST )){
            $this->_copier->addSets( $this->copier_name, $_POST );
        }

        $header = &AMP_getHeader( );
        $header->addJavascriptDynamic( $this->_copier->output( ), 'copier' );

    }

    function _searchFailureNotice( ){
        //do nothing
    }

    function execute( ){
        $content = $this->output( );
        $this->_copier->output( );
        return $content;
    }

    function getPositions( ){
        return $this->_copier->returnSets( $this->copier_name );
    }

    function _noRecordsOutput( ){

        return $this->_HTML_header() 
               . $this->_HTML_footer();
    }
}

?>

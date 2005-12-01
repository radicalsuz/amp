<?php

class AMPScaffold_Factory {

    var $_field_template;
    var $_translations = array( );
    var $_errors = array( );

    var $_target_files;
    var $_base_components = array( 'Item', 'Set', 'List', 'Form', 'ComponentMap');

    var $_path_scaffold;
    var $_path_target;

    var $form;
    var $_item_paths = array( 
        'system'  => 'AMP/System/',
        'content' => 'AMP/Content/',
        'module'  => 'Modules/');
    var $_special_fieldtypes = array( 
        'datetime'  =>  'date',
        'date'      =>  'date',
        'text'      =>  'textarea');
    var $_dbcon;
    var $_scaffold_item;
    var $_datatable;
    var $_field_name = 'name';
    var $_scaffold_item_type = 'module';

    function AMPScaffold_Factory( &$dbcon, $scaffold_item = null ) {
        $this->_dbcon = &$dbcon;
        if ( isset( $scaffold_item )) $this->setScaffoldItem( $scaffold_item );
    }

    function setScaffoldItem( $scaffold_item ) {
        if ( !$scaffold_item ) return false;
        $this->_scaffold_item = $scaffold_item;
        $this->setDataTable( strtolower( AMP_Pluralize( $scaffold_item )) );
    }

    function setScaffoldItemType( $scaffold_item_type ){
        if ( !$scaffold_item_type ) return false;
        $this->_scaffold_item_type = $scaffold_item_type;
    }

    function setDataTable( $tablename ){
        if ( !$tablename) return false;
        $this->_datatable = $tablename;
    }

    function setNameField( $namefield ){
        if ( !$namefield) return false;
        $this->_field_name = $namefield ;
    }

    function _verifyTable( ){
        $DBtables = $this->_dbcon->MetaTables( );
        if ( !isset( $this->_datatable)) return false;
        if ( array_search( $this->_datatable, $DBtables ) === FALSE ) {
            $this->addError( 'Table '.$this->_datatable.' not found');
            return false;
        }
        return true;

    }

    function _getFieldDefs( $tablename ){
        return $this->_dbcon->MetaColumns( $tablename );
    }

    function getScaffoldPath( ) {
        return AMP_BASE_INCLUDE_PATH . 'AMP/System/Scaffold/';
    }

    function getTargetPath( ) {
        return AMP_LOCAL_PATH . '/lib/'. $this->_item_paths[ $this->_scaffold_item_type ] . str_replace( '_', DIRECTORY_SEPARATOR, $this->_scaffold_item);
    }

    function execute( ) {
        if ( !$this->_verifyTable( )) return false;
        if ( !is_dir( $this->getTargetPath( ))) AMP_mkdir( $this->getTargetPath( ) );

        $this->generateXML( $this->_datatable, $this->getTargetPath( ).'/Fields.xml' );
        $this->generateComponents( );
    }

    function generateControllerPage( $merge_values ) {
        $target_folder = AMP_LOCAL_PATH . '/custom/system/';
        if ( !is_dir( $target_folder)) AMP_mkdir( $target_folder);
        $target_path = $target_folder . $this->getControllerPage( );

        #$result_file = vsprintf( file_get_contents( $this->getScaffoldPath( ). 'Controller.inc.php' ), $merge_values );
        $result_file = $this->stupidSingleQuoteVsprintfWorkaround( file_get_contents( $this->getScaffoldPath( ). 'Controller.inc.php' ), $merge_values );
        $this->_writeFile( $target_path, $result_file );
    }

    function generateXML( $tablename, $target_file ) {
        $field_defs = $this->_getFieldDefs( $tablename );
        $field_template = file_get_contents( $this->getScaffoldPath( ).'SingleField.xml');
        $fields_xml = "";

        foreach( $field_defs as $single_field ){
            $current_type = 'text';
            if ( isset( $this->_special_fieldtypes[ $single_field->type ])) $current_type = $this->_special_fieldtypes[ $single_field->type ];
            if ( 'id' == $single_field->name ) $current_type = 'hidden';
            $merge_values = array( $single_field->name );
            $merge_values = array( $single_field->name, $current_type, ucwords( $single_field->name ) ) ;
            $merge_values[] = ( 'date' == $current_type ) ? "-->\n\n        <default>today</default>\n      <!-- " : " ";
            #$fields_xml .= vsprintf( $field_template, $merge_values);
            $fields_xml .= $this->stupidSingleQuoteVsprintfWorkaround( $field_template, $merge_values);

            
            if ( 'date' !== $current_type ) continue;
            $this->_translations[] = $this->_dateTranslation( $single_field->name );
        }

        $final_file = $this->stupidSingleQuoteVsprintfWorkaround( file_get_contents( $this->getScaffoldPath( ). 'Fields.xml'), array( $fields_xml) );
        return $this->_writeFile( $target_file, $final_file );

    }

    function stupidSingleQuoteVsprintfWorkaround( $format, $merge_values ){
        $merge_start = 1;
        $current_output = $format;
        foreach( $merge_values as $value ){
            $sprintf_trigger = '%'.$merge_start.'\$s';
            $current_output = str_replace( $sprintf_trigger, $value, $current_output );
            $merge_start++;
        }
        return $current_output;

    }

    function _dateTranslation( $fieldname ) {
        return '\n       $this->setTranslation( "'.$fieldname.'", "_makeDbDateTime", "get");\n';
    }

    function _writeFile( $target_file, $value ) {
        if ( !file_exists( $target_file ) ) {
            $fRef = &fopen( $target_file, 'w' );
            fwrite( $fRef, $value );
            fclose( $fRef );
            return true;
        }
        $this->addError( $target_file . ' already exists' );
        return false;
    }

    function getControllerPage( ) {
        return $this->_datatable . '.php';
    }

    function generateComponents( ) {
        $merge_values = array(  
                $this->_scaffold_item, 
                $this->_datatable,
                $this->_field_name,
                $this->_item_paths[ $this->_scaffold_item_type ],
                $this->getControllerPage( ), 
                $this->_getTranslations( ) 
        );
        foreach( $this->_base_components as $item ) {
            #$result_file = vsprintf( file_get_contents( $this->getScaffoldPath( ). $item . '.inc.php' ), $merge_values );
            $result_file = $this->stupidSingleQuoteVsprintfWorkaround( file_get_contents( $this->getScaffoldPath( ). $item . '.inc.php' ), $merge_values );
            if( !$this->_writeFile( $this->_getComponentPath( $item ), $result_file )) continue;
            print $this->_getComponentPath( $item ) . ' : '. $item . ' created<BR>';
        }
        $this->generateControllerPage( $merge_values );

    }

    function _getTranslations() {
         if ( !count( $this->_translations )) return false;
         return "*/\n\n".join( "\n     ", $this->_translations ) . "\n/* ";

    }

    function _getComponentPath( $component_name ){
        if ( "Item" == $component_name ) return $this->getTargetPath( ) . '/'. $this->_scaffold_item. '.php';
        return $this->getTargetPath( ) . '/' . $component_name . '.inc.php' ;
    }

    function addError( $error ){
        $this->_errors[] = $error;
    }

    function &buildForm( ){
        require_once( 'AMP/System/Form.inc.php');
        $form = &new AMPSystem_Form( 'newScaffold') ;
        $fields = array( 
            'new_scaffold'      =>  array( 'label'  =>  'Object Name',            'type'  =>  'text',     'required'  => true),
            'new_namefield' =>  array( 'label'  =>  'Name Field',           'type'  =>  'text'),
            'sourcetable' =>  array( 'label'  =>  'Source Table ( if not plural of object )',           'type'  =>  'text'),
            'new_itype'    =>  array( 'label'  =>  'Type of Object',     'type'  =>  'select', 'values' => array( 'module' => 'New Module', 'system' => 'Core System', 'content' => 'Content Item'))
            );
        $form->addFields( $fields );
        $form->setDefaultValue( 'new_itype', 'module');
        $form->enforceRequiredFields( );
        $form->removeSubmit( 'copy');
        $form->removeSubmit( 'delete');
        $form->Build( );
        return $form;

    }

    function getErrors( ){
        if ( !count( $this->_errors )) return false;
        return join( "<BR>\n", $this->_errors );
    }
}
?>

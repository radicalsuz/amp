<?php

/*****
 *
 * AMP UserDataModule Export
 *
 * Creates an Export File based on the contents of
 * an UDM object.
 *
 *****/


require_once( 'AMP/UserData/Plugin.inc.php' );

class UserDataPlugin_ExportFile_Output extends UserDataPlugin {

    // A little bit of friendly information about the plugin.
    var $short_name  = 'Export';
    var $long_name   = 'Export List in CSV Format';
    var $description = 'Use this to set options for the CSV export plugin';
    var $available   = true;
    //List Vars
    //create default options array
    var $options=array(
        'display_fields'=>array(    'default'=>'',
                                    'available'=>true,
                                    'type'=>'textarea',
                                    'label'=>'Fields to include'),
        'show_headers'=> array( 'default'=> true,
                                'available'=> true,
                                'type'=>'checkbox',
                                'label'=>'Include Column Headers' ),
        'export_raw_values' => array(   'default'   =>  '',
                                        'available' =>  true,
                                        'type'      =>  'textarea',
                                        'label'     =>  'Export Raw Values for These Fields'),
        'export_mapping1' => array(
            'type' => 'textarea',
            'default' => '',
            'size' => '3:15',
            'label' => 'Custom Column Headers<br /><span class="photocaption">  ex: custom1=<br/>PlaceName&custom2<br/>=LastSighting...</span>',
            'available' => true),
                                
        'delimiter_char'=> array ( 
            'label'=>'Format Type' ,
            'default'=> 'csv',
            'available'=> true,
            'type'=>'select',
            'values'=>array( 'csv'=>'Comma Separated (CSV)',
                             'tab'=>'Tab Separated (TXT)' ),
            ),
        'multirow_fields'=>array(    'default'=>'',
                                    'available'=>true,
                                    'type'=>'textarea',
                                    'label'=>'Fields to multiply output rows'),
        'summation'=> array( 
            'label'=>'Include Summation Fields',
            'default'=> '',
            'available'=>true,
            'type'=>'checkbox'), 
    );

    var $format_values = array( 'csv'=> array('delimiter'=>',', 'extension'=>'csv'), 
                                'tab'=> array('delimiter'=>"\t", 'extension'=>'txt'));
    var $delimiter;
    var $file_extension;

    var $display_fieldset;
    var $current_row;
    var $dataset;
    var $Lookups;
    var $_raw_values = array( );

    var $_export_keys = array( );

    var $_multirow_fields = array( );


    function UserDataPlugin_ExportFile_Output ( &$udm, $plugin_instance=null ) {
        $this->init( $udm, $plugin_instance );

    }

    function execute ( $options = array( )) {

        $options = array_merge($this->getOptions(), $options);

        if (!isset($this->udm->dataset)) {
            $this->udm->doAction("Search");
        }
        if (!($this->dataset=$this->udm->getData_Recordset())) {
            $this->udm->errorMessage("No Dataset Specified");
            return false;
        }

        $this->_init_multirow_fields( $options );

        $this->setupColumns($this->definedColumns($options));
        
        $dataset = $this->translateValues();

        $this->_init_export_keys(  $options );

        $column_headers = ($options['show_headers'])?$this->getHeaders():'';

        $sum = $this->summation( );
	           
        if ( !AMP_DISPLAYMODE_DEBUG) { 
            header("Content-type: application/".$this->file_extension);
            header("Content-Disposition: attachment; filename=".$this->setFileName());
        }
        
        
        
        
        return $column_headers . $this->formatFileOutput($dataset) . $sum;
    }

    function definedColumns($options) {
        if(isset($options['display_fields']) && $options['display_fields']) {

            return preg_split('/\s{0,2},\s{0,2}/', $options['display_fields']);
        } else {

            $datarow = $this->dataset->FetchRow();
            return array_keys($datarow);
        }
    }


    function getHeaders() {
        foreach ($this->display_fieldset as $column) {
            if ($header = $this->getLabel($column)) {
                $result_headers[$column] = $header;
            } else {
                $result_headers[$column] = $column;
            }
        }
        $header_rows[] = $result_headers;
        return $this->formatFileOutput($header_rows);
    }

    function validateColumn( $column ) {
        if ($column == "id" && $this->udm->admin) return "text";
        if ($column == "timestamp") return "date";
        if ($column == "created_timestamp") return "date";
        if (!isset($this->udm->fields[$column])) return false;
        if (!($this->udm->fields[$column]['enabled'])) return false;
        if ((!$this->udm->admin) && !($this->udm->fields[$column]['public'])) return false;
        
        return $this->udm->fields[$column]['type'];
    }


    function setupColumns( $fieldset_def ) {
        foreach ($fieldset_def as $column) {
            if (!($type = $this->validateColumn( $column ))) continue;
            if ( array_search( $column, $this->_raw_values ) !==FALSE){
                $result_fields[]=$column;
                continue;
            }
            switch ($type) {
                case 'text':
                case 'textarea':
                case 'wysiwyg':
                case 'checkbox':
                case 'radiogroup':
                case 'date':
                    $result_fields[]=$column;
                    break;
                    
                case 'select':
                case 'multiselect':
                    $result_fields[]=$column;
                    $defaults = $this->getValueSet($this->udm->fields[$column]);
                    $this->setTranslation($column, $defaults);
                    break;
                case 'checkgroup':
                    $result_fields[]=$column . '_' . $this->getLabel($column);
                    $defaults = $this->getValueSet($this->udm->fields[$column]);
                    $this->setTranslation( $column, $defaults, 'readExpandedCheckGroup' );
                    foreach ($defaults as $ex_column) {
                        $newfield = $column.'_'.$ex_column;
                        $result_fields[] = $newfield;
                        $this->setParent( $newfield, $column);
                        $this->setTranslation( $newfield, $defaults, 'readExpandedCheckGroup' );
                    }
                    break;
               default:
                    break;
                    
            }
        }

        return ($this->display_fieldset = $result_fields);
    }

    function currentRowValue( $fieldname ) {
        if (!isset($this->current_row[$fieldname])) return false;
        return $this->current_row[ $fieldname ];
    }


    function translateValues() {

        $this->dataset->MoveFirst();
        while ($this->current_row = $this->dataset->FetchRow()) {

            $result_row = array();

            foreach ($this->display_fieldset as $readyfield) {
                if ($this->currentRowValue($readyfield) || isset($this->translations[$readyfield])) {
                    $result_row[$readyfield] = $this->translate ( $readyfield, $this->currentRowValue($readyfield) );
                } else {
                    $result_row[$readyfield]='';
                }
            }

            //hack for HARM form
            if ( empty( $this->_multirow_fields )) {
                $result_set[] = $result_row;
            } else {
                $result_row_set = $this->multiply( $result_row );
                foreach( $result_row_set as $current_result_row ) {
                    $result_set[] = $current_result_row;
                }
            }
        }
        $this->dataset->MoveFirst();

        return $result_set;
    }
        



    function setTranslation($field, $translation_set=null, $translation_method="lookup") {
        $this->translations[$field]['method']=$translation_method;
        if (isset($translation_set)) $this->setLookup($field, $translation_set);
    }

    function setLookup($field, $lookup_set) {
        $this->Lookups[$field]['Set']=$lookup_set;
    }

    function parentGroup($field) {
        if (isset($this->parentgroups[$field])) {
            return $this->parentgroups[$field];
        }
        return false;
    }

    function setParent($child, $parent) {
        $this->parentgroups[$child]=$parent;
    }

    function getLabel($field) {
        if ( isset( $this->_export_keys[ $field ])) {
            return $this->_export_keys[ $field ];
        }
        if (!isset($this->udm->fields[$field])) return false;
        return strip_tags($this->udm->fields[$field]['label']);
    }
    
    function readExpandedCheckGroup($field, $value) {
        if ($groupname = $this->parentGroup($field)) {
            $group_set = $this->expandCheckgroup( $this->currentRowValue($groupname) );
            $sought_value = substr($field, strlen($groupname)+1);
            if (isset($group_set[$sought_value])) return 1;
        }
        return '0';
    }

    function lookup($field, $value) {
        if (isset($this->Lookups[$field])) {
            if (isset($this->Lookups[$field]['Set'][$value])) {
                return $this->Lookups[$field]['Set'][$value];
            }
        }
        return $value;
    }


    function translate($field, $value) {
        if (!isset($this->translations[$field])) return $value;
        $translate_method = isset($this->translations[$field]['method']) ?
                            $this->translations[$field]['method'] :
                            false;
        if ($translate_method && method_exists($this, $translate_method)) {
            return $this->$translate_method($field, $value);
        }
        return $value;
    }

    function formatFileOutput($dataset=null, $quot='"') { 
        if (!isset($dataset)) $dataset = $this->dataset->GetArray();

        $str='';
        
        if (!is_array($dataset)) {
            $this->udm->errorMessage("No data found for export");
            return false;
        }
        
        $escape_function = "escapeforcsv";
        foreach ($dataset as $row) {
            array_walk( $row, array($this, $escape_function, $this->delimiter) );
            $str .= implode($this->delimiter, $row) . "\n";
        }
        return $str;
    } 

    function escapeforcsv( &$value, $key, $field_delimiter=',', $quot='"')  {
        $has_fd =(strchr($value, $field_delimiter)!==FALSE); 
        $has_quot = (strchr($value, $quot)!==FALSE);
        $has_leading_space = (substr($value, 0,1)==" ");
        $has_cr = ((strchr($value, "\n")!==FALSE)||(strchr($value,"\r")!==FALSE));
        if ($has_quot) $value = str_replace($quot, $quot.$quot, $value);
        if  (($has_fd || $has_quot) || ($has_cr || $has_leading_space) ) { 
            $value = $quot . $value . $quot;
        }
    }


    function setFileName(){
        $file = $this->udm->name;

        //remove illegal characters
        $file = ereg_replace ("'", "" ,$file);
        $file = ereg_replace (",", "" ,$file);
        $file = ereg_replace (" ", "_" ,$file);

        //add current date
        $file .= date('_Y_m_d');
        return $file.'.'.$this->file_extension;
        
    }

    function _register_options_dynamic() {
        $options = $this->getOptions();
        $this->file_extension = $this->format_values[$options['delimiter_char']]['extension'];
        $this->delimiter = $this->format_values[$options['delimiter_char']]['delimiter'];
        $this->setLookup('publish', array("0"=>"draft" , "1"=>"live"));
        if ( isset( $options['export_raw_values'] ) && $options['export_raw_values']) {
            $this->_raw_values = preg_split( "/\s{0,2},\s{0,2}/", $options['export_raw_values']);
        }

        
     }

     function _init_export_keys( $options = array( ) ) {
         if ( !(isset( $options['export_mapping1']) && $options['export_mapping1'] )) {
             return;
         }
         $this->_export_keys = $this->extractMapping( $options['export_mapping1']);

     }

    function extractMapping($string) {
        $mappings = preg_split("/\s{0,2}&\s{0,2}/",$string);
        $return = array( );
        foreach($mappings as $map) {
            $mapped_pair = explode('=',$map);
            if ( count( $mapped_pair ) != 2 ) {
                trigger_error( sprintf( AMP_TEXT_ERROR_OPTION_FORMAT_INCORRECT, $map, '='));
            }
            list($key, $value) = explode('=',$map);
            $return[$key] = $value;
        }
        return $return;
    }

    //wild multirow HARM hack starts here
    function _init_multirow_fields( $options = array( ) ) {
        $multirow_fields = ( isset( $options['multirow_fields']) && $options['multirow_fields'] ) ? $options['multirow_fields'] : false;
        $multirow_fields_token = 'AMP_FORM_' . $this->udm->instance . '_EXPORT_MULTIROW_FIELDS';
        if ( defined( $multirow_fields_token )) {
            $multirow_fields = constant( $multirow_fields_token );
        }
        if ( ! $multirow_fields ) return;

        $this->_multirow_fields = preg_split( "/\s{0,2},\s{0,2}/", $multirow_fields );
        $this->_raw_values = array_merge( $this->_raw_values, $this->_multirow_fields );
    }

    function multiply( $data_row, $key_field_set = false ) {
        //define the default fields to multiply rows by
        if ( !$key_field_set ) {
            $key_field_set = $this->_multirow_fields;
        }

        $new_rows = array( );

        //grab the first item from the array of "key fields"
        $key_field = array_shift( $key_field_set );

        //if there is no value in the "key field" just return the row as is
        if ( !( isset( $data_row[$key_field]) && $data_row[$key_field] )) {
            $new_rows[] = $data_row;
        } else {
            //blow apart the "key field" by comma
            $key_values = preg_split("/\s?,\s?/",$data_row[$key_field]);


            //loop through the results adding to the new_rows list as you go
            foreach( $key_values as $value ) {
                $new_data_row = $data_row;
                $new_data_row[$key_field] = $value;
                $new_rows[] = $new_data_row;
            }
        }

        //check to see if you have more "key fields" to multiply by
        if ( !empty( $key_field_set )) {
            $final_rows = array( );

            //recursively call multiply on the new rows
            foreach( $new_rows as $new_row ) {
                $final_rows[] = $this->multiply( $new_row, $key_field_set );
            }
        } else {
            $final_rows = $new_rows;
        }

        return $final_rows;
    }
    
    function summation( ){
        if ($options['summation']) {
            $summation = & $this->udm->getPlugin( 'Output','Summation');
            $summation->execute( );
            $i = 0;
            $sum_row = array( );
            $sum_dataset = array( );
            foreach( $summation->fields_to_sum as $current_sum ) {
                trigger_error( 'sum values:'.$summation->sum_values[$current_sum.'_total']);
                $sum_row[]= $summation->titles_to_sum[$i];
                $sum_row[]= $summation->sum_values[$current_sum.'_total'];
                $sum_dataset[]=$sum_row;
                $i++;
                $sum_row = array( );
        }
        $sum =  $this->formatFileOutput($sum_dataset);

       } else {
            $sum = '';
        }
        return $sum;
    } 

}


?>

<?php
require_once( 'AMP/System/Introtext.inc.php');
require_once( 'AMP/System/UserData.php');
require_once( 'Modules/WebAction/WebAction.php');
require_once( 'Modules/WebAction/Target/Target.php');

class WebAction_Deprecated extends AMPSystem_Data_Item {
    var $datatable = 'action_text';
    var $name_field = 'title';

    var $_data_segments = array( 
        'base' => array( 'title', 'shortdesc', 'enddate', 'id'),
        'intro' => array( 'title', 'introtext' ), 
        'response' => array( 'thankyou_title', 'thankyou_text'), 
        'message' => array( 'subject', 'text'), 
        'tellfriend_message'  => array( 'tf_subject', 'tf_text'),
        'target'  => array( 'firstname', 'lastname', 'prefix', 'position', 'email', 'fax'),
        );
        
    
    var $_base_target;

    var $_target_classes = array( 
        'base'  => 'WebAction',
        'intro' => 'AMPSystem_IntroText',
        'response' => 'AMPSystem_IntroText',
        'tellfriend_message' => 'AMPSystem_IntroText',
        'message' => 'AMPSystem_IntroText',
        'form' => 'AMPSystem_UserData',
        'target' => 'WebAction_Target'
        );
        

    var $_target_fields = array( 
        'base' => array( 'title' => 'name', 'shortdesc' => 'blurb', 'id' => 'id', 'enddate' => 'enddate'),
        'intro' => array( 'title' => 'title', 'introtext' => 'test'),
        'response' => array( 'thankyou_title' => 'title', 'thankyou_text' => 'test'),
        'tellfriend_message' => array( 'tf_subject' => 'title', 'tf_text' => 'test'),
        'message' => array( 'subject' => 'title', 'text' => 'test'),
        'target'  => array( 'firstname' => 'First_Name', 'lastname' => 'Last_Name', 'prefix' => 'Title', 'position' => 'occupation', 'email' => 'Email', 'fax' => 'Work_Fax' ),
        );
        
    var $_result_ids;
    var $_class_name = 'WebAction_Deprecated';
    
    function WebAction_Deprecated ( &$dbcon, $id = null ){
        $this->init( $dbcon, $id );
    }

    function getDataSegment( $which ){
        if ( !isset( $this->_data_segments[$which])) return false;
        $results = array( );
        foreach( $this->_data_segments[$which] as $db_fieldname ){
            $results[$db_fieldname] = $this->getData( $db_fieldname );
        }
        return $results;

    }

    function update( ){
        $start_data = $this->getData( );
        foreach( $this->_data_segments as $type => $fields ) {
            $segment = array_combine_key( $fields, $start_data );
            $new_data = array( );
            $translations = $this->_target_fields[$type];
            foreach( $segment as $key => $value ){
                if ( !$value ) continue;
                $new_data[ $translations[$key] ] = $value;    
            }
            if ( empty( $new_data )) continue;

            $new_model_class = $this->_target_classes[$type];
            if ( !trim($new_model_class) || !class_exists( $new_model_class)) {
                trigger_error( 'class '. $new_model_class . ' for ' . $type .' not found' );
                continue;
            }
            $new_model = &new $new_model_class( AMP_Registry::getDbcon( ));
            $new_model->setData( $this->adjust( $new_data, $new_model_class, $type ) );

            $new_model->save( );
            $this->_result_ids[$type] = $new_model->id;

        }
        $base_target_class = $this->_target_classes['base'] ;
        $this->_base_target = &new $base_target_class( $this->dbcon, $this->id );
        foreach( $this->_result_ids as $key => $id_value ){
            $base_values[$key.'_id'] = $id_value;
        }
        $base_values['status'] = 1;
        $base_values['modin'] = AMP_FORM_ID_WEBACTION_DEFAULT;
        $base_values['target_method'] = 'all';
        $this->_base_target->mergeData( $base_values );
        return $this->_base_target->save( );
    }

    function adjust( $data, $new_model_class, $type ){
        $adjust_method = 'adjust_' . $new_model_class;
        if ( !method_exists( $this, $adjust_method )) return $data;
        return $this->$adjust_method( $data, $type );
    }

    function adjust_AMPSystem_IntroText( $data, $type ){
        $tools_lookup = AMPSystem_Lookup::instance( 'toolsbyForm');
        if ( !isset( $tools_lookup[ AMP_FORM_ID_WEBACTION_DEFAULT ])) return $data ;
        $data['modid'] = $tools_lookup[ AMP_FORM_ID_WEBACTION_DEFAULT ];
        $data['name'] = 'Action ' . ucfirst( $type ) . ': ' . $data['title'];
        return $data;
    }

    function getURL( )   {
        if ( !$this->id ) return AMP_CONTENT_URL_ACTION;
        return AMP_url_add_vars( AMP_CONTENT_URL_ACTION, 'action='.$this->id );
    }

}
?>

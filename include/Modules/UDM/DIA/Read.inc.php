<?php

require_once('AMP/UserData/Plugin.inc.php');
require_once('DIA/API.php');

class UserDataPlugin_Read_DIA extends UserDataPlugin {
    var $options     = array( 
        'dia_key' => array(  
            'available' => false ),
        'organization_key' => array(
            'type'=>'text',
            'size'=>'5',
            'default' => '',
            'available'=>true,
            'label'=>'DIA Organization Key'
            ),
        'user' => array(
            'type'=>'text',
            'size'=>'5',
            'default' => '',
            'available'=>true,
            'label'=>'DIA AMP User Name'
            ),
        'password' => array(
            'type'=>'text',
            'size'=>'5',
            'default' => '',
            'available'=>true,
            'label'=>'DIA AMP User Password'
            ),
        'table_mapping1' => array(
            'type' => 'text',
            'size' => '15',
            'default' => '',
            'available' => true,
            'label' => 'Extra DIA table to read from'),
        'mapping1' => array(
            'type' => 'textarea',
            'default' => '',
            'size' => '3:15',
            'label' => '<span class="photocaption">mapping.  ex: Email_Preference=<br/>custom1&Sourch_Tracking_Code<br/>=custom2...</span>',
            'available' => true),
        'result_mapping1' => array(
            'type' => 'text',
            'default' => '',
            'size' => '15',
            'label' => 'DIA key field:',
            'available' => true),
        'table_mapping2' => array(
            'type' => 'text',
            'size' => '15',
            'default' => '',
            'available' => true,
            'label' => 'Extra DIA table to read from'),
        'mapping2' => array(
            'type' => 'textarea',
            'size' => '3:15',
            'label' => '<span class="photocaption">mapping.  ex: Group_Name=<br />custom3&Description<br/>=custom4...</span>',
            'available' => true),
        'result_mapping2' => array(
            'type' => 'text',
            'size' => '15',
            'default' => '',
            'label' => 'DIA key field:',
            'available' => true),
        'table_mapping3' => array(
            'type' => 'text',
            'size' => '15',
            'default' => '',
            'available' => true,
            'label' => 'Extra DIA table to read from'),
        'mapping3' => array(
            'type' => 'textarea',
            'size' => '3:15',
            'default' => '',
            'label' => '<span class="photocaption">mapping.  ex: Event_Name=<br/>custom5&Description<br/>=custom6...</span>',
            'available' => true),
        'result_mapping3' => array(
            'type' => 'text',
            'size' => '15',
            'default' => '',
            'label' => 'DIA key field:',
            'available' => true),
            );
    var $available = true;

	var $_dia_key;
    var $_dia_api;
    var $mappings = array('mapping1', 'mapping2', 'mapping3');

    var $_supporter_mapping = 
            array(  'Region' => 'region',
                    'Occupation' => 'occupation',
                    'Organization' => 'Company');

	function UserDataPlugin_Read_DIA (&$udm, $plugin_instance=null) {
		$this->init($udm, $plugin_instance);
	}

    function &init_api( $options = array( ) ){
        if ( isset( $this->_dia_api )) return $this->_dia_api;

        $this->_dia_api = &DIA_API::create( null, $options );
        return $this->_dia_api;
    }

	function execute($options=array( )) {
		$options = array_merge($this->getOptions(), $options);
		$this->_dia_key = $this->find_dia_key( $options );
        if ( !$this->_dia_key ) return false;
        $start_data = $this->udm->getData( );

        //accepts passed API options for testing purposes
        $api =  &$this->init_api( );
        $supporter_data = $api->getSupporter( $this->_dia_key );
        $this->udm->setData( $this->translate( $supporter_data ));
        
        foreach($this->mappings as $mapping) {
            if(!( isset($options[$mapping]) && $options[$mapping])) continue;

            $table_mapping = 'table_' . $mapping;
            if(!( isset($options[$table_mapping]) && $options[$table_mapping])) continue;
            $result_mapping = 'result_' . $mapping;
            if(!( isset($options[$result_mapping]) && $options[$result_mapping] )) continue;
            if (!( isset( $start_data[ $options[ $result_mapping ]]) && $start_data[$options[$result_mapping]])) continue;
            $result_key = $start_data[ $options[ $result_mapping ]];

            $mapped_data = $this->readLinkedMapping( $result_key, $options[ $table_mapping] );
            
            if ( !$mapped_data ) {
                trigger_error( sprintf( AMP_TEXT_ERROR_DIA_READ_FAILURE, $options[$table_mapping], $result_key) );
                continue; 
            }
            $this->udm->setData( $this->translate( $mapped_data, $this->extractMapping( $options[ $mapping ]), 'static' ));
            
        }

		return true;
    }

	function translate( $dia_data, $mapping=null, $alternate = 'direct' ) {
        if ( !isset( $mapping )) $mapping = $this->_supporter_mapping;
        $return = array( );
        $dia_data = $this->translate_hacks( $dia_data );

		foreach($dia_data as $key => $value) {
			if(isset($mapping[$key])) {
				$return[$mapping[$key]] = $value;
			} elseif( $alternate == 'direct') {
				$return[$key] = $value;
			}
		}

		return $return;
	}

    function translate_hacks( $dia_data ){

        if ( isset( $dia_data['Start'] ) && $dia_data['Start'] ){
			$start = dia_datetotime($dia_data['Start']);
			if(isset($start) && $start) {
				$dia_data['Start_Date'] = date('Y-m-d', $start);
				$dia_data['Start_Time'] = date('g:i A', $start);
			}
        }

        if ( isset( $dia_data['End'] ) && $dia_data['End'] ){
			$end_time = dia_datetotime($dia_data['End']);
			if(isset($start) && $start) {
				$dia_data['End_Date'] = date('Y-m-d', $end_time);
				$dia_data['End_Time'] = date('g:i A', $end_time);
			}
        }

		if($dia_data['Status'] == 'Active') {
			$dia_data['Status'] = 1;
		} else {
			$dia_data['Status'] = 0;
		}

        return $dia_data;
    }


    function extractMapping($string) {
        $mappings = preg_split("/\s?\n?&\s?\n?/",$string);
        $return = array( );
        foreach($mappings as $map) {
            list($key, $value) = explode('=',$map);
            $return[$key] = $value;
        }
        return $return;
    }

    function readLinkedMapping( $dia_key, $table ){
        return $this->_dia_api->get( $table, $dia_key );

    }

    function find_dia_key( $options ) {
		if ((isset( $options['dia_key'] )&&$options['dia_key'])) return $options['dia_key'];
        if ( isset( $this->udm->uid )) {
            require_once( 'AMP/System/User/Profile/Profile.php');
            $user_data = new AMP_System_User_Profile( $this->udm->dbcon, $this->udm->uid );
            $dia_key = $user_data->getData( 'dia_key');
            if ( $dia_key ) return $dia_key;
        }
        return false;

    }
}

?>

<?php

require_once( 'DIA/API.php' );
require_once( 'AMP/UserData/Plugin/Save.inc.php' );

class UserDataPlugin_Save_DIA extends UserDataPlugin_Save {
    var $options = array(
        'orgKey' => array(
            'type'=>'text',
            'size'=>'5',
            'default' => '',
            'available'=>true,
            'label'=>'DIA Organization Key'
            ),
		'user' => array(
            'type'=>'text',
            'size'=>'15',
            'default' => '',
            'available'=>true,
            'label'=>'DIA AMP User Name'
			),
		'password' => array(
            'type'=>'text',
            'size'=>'15',
            'default' => '',
            'available'=>true,
            'label'=>'DIA AMP User Password'
			),
        'show_lists' => array(
            'type' => 'checkbox',
            'default' => true,
            'available' => true,
            'label' => "User can see lists<br/>(if 'Use lists' checked on settings)"
        ),
        'table_mapping1' => array(
            'type' => 'text',
            'size' => '15',
            'default' => '',
            'available' => true,
            'label' => 'Extra DIA table to save to'),
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
            'label' => 'save returned key to:',
            'available' => true),
        'table_mapping2' => array(
            'type' => 'text',
            'size' => '15',
            'default' => '',
            'available' => true,
            'label' => 'Extra DIA table to save to'),
        'mapping2' => array(
            'type' => 'textarea',
            'default' => '',
            'size' => '3:15',
            'label' => '<span class="photocaption">mapping.  ex: Group_Name=<br />custom3&Description<br/>=custom4...</span>',
            'available' => true),
        'result_mapping2' => array(
            'type' => 'text',
            'size' => '15',
            'default' => '',
            'label' => 'save returned key to:',
            'available' => true),
        'table_mapping3' => array(
            'type' => 'text',
            'size' => '15',
            'default' => '',
            'available' => true,
            'label' => 'Extra DIA table to save to'),
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
            'label' => 'Save returned key to:',
            'available' => true),
        );
    var $available = true;

    var $mappings = array('mapping1', 'mapping2', 'mapping3');

    var $_listfield_template = array(
                'public'   => true,
                'enabled'  => true,
                'type'     => 'checkbox',
                'required' => false,
                'default'  => 1 );

    var $_listfield_header = array(
            'label'     => 'Subscribe to the following lists:',
            'public'    => true,
            'enabled'   => true,
            'type'      => 'header' );

	var $_supporter_key;
    var $_dia_api;

    /*
    var $_supporter_mapping = 
            array(  'region' => 'Region',
                    'occupation' => 'Occupation',
                    'Company' => 'Organization');
            */
    var $_supporter_mapping = 
            array(  'Region' => 'region',
                    'Occupation' => 'occupation',
                    'Organization' => 'Company');

    function UserDataPlugin_Save_DIA( &$udm, $plugin_instance ) {
        $this->init($udm, $plugin_instance);
    }

	function _register_fields_dynamic() {
        if( !( $lists = $this->udm->getRegisteredLists( ))) return;

        $list_options = $this->getOptions(array('show_lists'));
        if($list_options['show_lists']) {
            $this->fields[ 'list_header' ] = $this->_listfield_header;
        } else {
            $this->_listfield_template['type'] = 'hidden';
        }

        foreach ( $lists as $list_id => $list_name ){
            $listField = array( 'label'    => $list_name );
            $this->fields[ 'list_' . $list_id] = $listField + $this->_listfield_template;
        }
    }

    function getSaveFields() {
        $db_fields   = $this->udm->dbcon->MetaColumnNames('userdata');
        $qf_fields   = array_keys( $this->udm->form->exportValues() );

        return array_intersect( $db_fields, $qf_fields );
    }

    function &_init_api( $options = array( )){
        if ( isset( $this->_dia_api )) return $this->_dia_api;

        if(!( isset($options[ 'orgKey' ]) && $options['orgKey']) && defined('DIA_API_ORGCODE')) {
            $options['orgKey'] = DIA_API_ORGCODE;
        }
		if(!( isset($options[ 'user' ]) && $options['user']) && defined('DIA_API_USERNAME')) {
            $options['user'] = DIA_API_USERNAME;
		}
		if(!( isset($options[ 'password' ]) && $options['password']) && defined('DIA_API_PASSWORD')) {
            $options['password'] = DIA_API_PASSWORD;
		}

		$this->_dia_api =& DIA_API::create();
        $this->_dia_api->init(array('user'     => $options['user'],
                         'password' => $options['password'],
                         'organization_key' => $options['orgKey']));
        
        return $this->_dia_api;
    }

    function save ( $data ) {
        $options=$this->getOptions();
        $api = &$this->_init_api( $options );

        $supporter_id = $this->addDIASupporter($api, $data);
        if ( !$supporter_id ) {
            trigger_error( AMP_TEXT_ERROR_DIA_SAVE_FAILURE );
            if ( !defined( 'AMP_DEBUG_MODE_REMOTE_SERVICES_UNAVAILABLE' )) return false;
        }
        if ( AMP_DISPLAYMODE_DEBUG_DIA ) trigger_error( sprintf( AMP_TEXT_DIA_SAVE_SUCCESS, $supporter_id ));

        /**
         * Save data to additional DIA tables as specified by mappings
         */
        $update_array = array( );

        foreach($this->mappings as $mapping) {
            if(!( isset($options[$mapping]) && $options[$mapping])) continue;

            $table_mapping = 'table_' . $mapping;
            if(!( isset($options[$table_mapping]) && $options[$table_mapping])) continue;
            $result_key = $this->addLinkedMapping( $supporter_id, 
                                    $this->translate( $data, $this->extractMapping( $options[ $mapping ]), 'static' ),
                                    $options[ $table_mapping ] );
            if ( AMP_DISPLAYMODE_DEBUG_DIA ) trigger_error( 'saved DIA '. $options[$table_mapping] . ' link id: ' . $result_key);

            $result_mapping = 'result_' . $mapping;
            if(!( isset($options[$result_mapping]) && $options[$result_mapping] && $result_key )) continue;
            $update_array[ $options[$result_mapping] ] = $result_key ;
            
        }
            
        /**
         * Save result keys to UDM table 
         */
        if ( !empty( $update_array )) {
            $save_plugin = &$this->udm->registerPlugin( 'AMP', 'Save');
            $save_plugin->save( $update_array );
        }



/*XXX: there is an api for linking with one step in the addSupporter method
  TODO: make that api clearer and use that
*/
		if(isset($options['link'])) {
			$api->linkSupporter($options['link'], $supporter_id);
		}

		$lists = $this->udm->getRegisteredLists();
        $alldata = $this->udm->getData();
		foreach( $lists as $list_id => $list_name ) {
            $subscribe = isset($alldata['list_'.$list_id]) && $alldata['list_'.$list_id];
            if($subscribe) {
                $api->linkSupporter($list_id, $supporter_id);
            }
		}

        return $supporter_id;
    }

    function addLinkedMapping( $supporter_id, $mapped_data, $table ){
        $mapped_data['supporter_KEY'] = $supporter_id;
        if('supporter' == $table) $mapped_data['key'] = $supporter_id;
        /**
         * Hacktastic exceptions for calendar date/time fields 
         */
        if ( isset( $mapped_data['Start_Date'] ) && $mapped_data['Start_Date']
                && isset( $mapped_data['Start_Time']) && $mapped_data['Start_Time']){
            $mapped_data['Start'] = $this->_makeDIAdatetime( $mapped_data['Start_Date'], $mapped_data['Start_Time']) ;
            if ( isset( $mapped_data['End_Time']) && $mapped_data['End_Time']){
                $mapped_data['End'] = $this->_makeDIAdatetime( $mapped_data['Start_Date'], $mapped_data['End_Time']) ;
            }
        }
        if ( isset( $mapped_data['End_Date'] ) && $mapped_data['End_Date']
                && isset( $mapped_data['End_Time']) && $mapped_data['End_Time']){
            $mapped_data['End'] = $this->_makeDIAdatetime( $mapped_data['End_Date'], $mapped_data['End_Time']) ;
        }
        return $this->_dia_api->process( $table, $mapped_data );
    }

    function _makeDIAdatetime( $date_value, $time_value ){

		$base_time = strtotime($date_value .' '. $time_value);
		if(!$base_time|| (-1 == $base_time)) {
			$base_time = strtotime($date_value);
		}
		if($base_time && (-1 != $base_time)) {
            return dia_formatdate( $base_time );
		}

    }

    function addDIASupporter(&$api, $data) {
        $supporter = $this->translate($data);
        $supporter['uid'] = $this->udm->uid;
        $this->_supporter_key = $api->addSupporter($supporter);
        #$this->updateReturnKey('supporter', $this->_supporter_key);
        return $this->_supporter_key;
    }

	function translate( $data, $mapping=null, $alternate = 'direct' ) {
        if ( !isset( $mapping )) $mapping = $this->_supporter_mapping;

		foreach($data as $key => $value) {
			#if(isset($mapping[$key])) {
			if($dia_key_value = array_search( $key, $mapping )) {
				$return[$dia_key_value] = $value;
			} elseif( $alternate == 'direct') {
				$return[$key] = $value;
			}
		}

        if ( $alternate == 'static' ){
            foreach( $mapping as $key=>$value ){
                if ( !isset( $return[ $key ])) $return[$key] = $value;
            }
        }

		return $return;
	}


    function extractMapping($string) {
        $mappings = preg_split("/\s?&\s?/",$string);
        $return = array( );
        foreach($mappings as $map) {
            list($key, $value) = explode('=',$map);
            $return[$key] = $value;
        }
        return $return;
    }

	function getSupporterKey() {
		return $this->_supporter_key;
	}
		
}

?>

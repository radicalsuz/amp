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
        'table1' => array(
            'type' => 'text',
            'size' => '15',
            'default' => 'supporter',
            'available' => true,
            'label' => 'DIA table to save to'),
        'mapping1' => array(
            'type' => 'textarea',
            'size' => '3:15',
            'label' => '<span class="photocaption">mapping.  ex: custom1=<br/>Email_Preference&custom2<br/>=Source_Tracking_Code...</span>',
            'available' => true),
        'returned_key1' => array(
            'type' => 'text',
            'size' => '15',
            'label' => 'save returned key to:',
            'available' => true),
        'table2' => array(
            'type' => 'text',
            'size' => '15',
            'default' => 'groups',
            'available' => true,
            'label' => 'DIA table to save to'),
        'mapping2' => array(
            'type' => 'textarea',
            'size' => '3:15',
            'label' => '<span class="photocaption">mapping.  ex: custom3=<br/>Group_Name&custom4<br/>=Description...</span>',
            'available' => true),
        'returned_key2' => array(
            'type' => 'text',
            'size' => '15',
            'label' => 'save returned key to:',
            'available' => true),
        'table3' => array(
            'type' => 'text',
            'size' => '15',
            'default' => 'event',
            'available' => true,
            'label' => 'DIA table to save to'),
        'mapping3' => array(
            'type' => 'textarea',
            'size' => '3:15',
            'label' => '<span class="photocaption">mapping.  ex: custom5=<br/>Event_Name&custom6<br/>=Description...</span>',
            'available' => true),
        'returned_key3' => array(
            'type' => 'text',
            'size' => '15',
            'label' => 'save returned key to:',
            'available' => true),
        );
    var $available = true;

    var $mappings = array('mapping1', 'mapping2', 'mapping3');

    var $_field_prefix = 'DIA';
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

    function UserDataPlugin_Save_DIA(&$udm, $plugin_instance) {
        $this->init($udm, $plugin_instance);
    }

	function _register_fields_dynamic() {
        if(defined('AMP_UDM_PLUGIN_DIA_SAVE_LISTS_REGISTERED') 
            && AMP_UDM_PLUGIN_DIA_SAVE_LISTS_REGISTERED) return;
        if( !( $lists = $this->udm->getRegisteredLists( ))) return;
        $this->fields[ 'list_header' ] = $this->_listfield_header;

        foreach ( $lists as $list_id => $list_name ){
            $listField = array( 'label'    => $list_name );
            $this->fields[ 'list_' . $list_id] = $listField + $this->_listfield_template;
        }
        define('AMP_UDM_PLUGIN_DIA_SAVE_LISTS_REGISTERED', true);
    }

    function getSaveFields() {
        $db_fields   = $this->udm->dbcon->MetaColumnNames('userdata');
        $qf_fields   = array_keys( $this->udm->form->exportValues() );
        $this->_field_prefix="";

        return array_intersect( $db_fields, $qf_fields );
    }

    function save ( $data ) {
        $options=$this->getOptions();

        if(!isset($options[ 'orgKey' ]) && defined('DIA_API_ORGCODE')) {
            $options['orgKey'] = DIA_API_ORGCODE;
        }
		if(!isset($options[ 'user' ]) && defined('DIA_API_USERNAME')) {
            $options['user'] = DIA_API_USERNAME;
		}
		if(!isset($options[ 'password' ]) && defined('DIA_API_PASSWORD')) {
            $options['password'] = DIA_API_PASSWORD;
		}

		$api =& DIA_API::create();
        $api->init(array('user'     => $options['user'],
                         'password' => $options['password'],
                         'organization_key' => $options['orgKey']));

/*

foreach table: save
must save supporter key first SO must check to see if supporter table is specified and do that

        foreach($this->mappings as $mapping) {
            if(isset($options[$mapping])) {
            }
        }

*/
        $supporter_id = $this->addDIASupporter($api, $data);

/*XXX: there is an api for linking with one step in the addSupporter method
  TODO: make that api clearer and use that
*/
		if(isset($options['link'])) {
			$api->linkSupporter($options['link'], $supporter_id);
		}

		$lists = $this->udm->getRegisteredLists();
		foreach( $lists as $list_id => $list_name ) {
			$api->linkSupporter($list_id, $supporter_id);
		}

        return $supporter_id;
    }

    function addDIASupporter(&$api, $data) {
        $supporter = $this->translate($data);
        $supporter['uid'] = $this->udm->uid;
        $this->_supporter_key = $api->addSupporter($supporter);
        $this->updateReturnKey('supporter', $this->_supporter_key);
        return $this->_supporter_key;
    }

	function translate( $data, $table='supporter' ) {
		$translation = array('region' => 'Region',
							'occupation' => 'Occupation',
							'Company' => 'Organization');

//        $translation = array_merge($translation, $this->getMapping($table));

		foreach($data as $key => $value) {
			if(isset($translation[$key])) {
				$return[$translation[$key]] = $value;
			} else {
				$return[$key] = $value;
			}
		}

		return $return;
	}

    function getMapping($table) {
        $options = $this->getOptions();
        //XXX:NOT RIGHT
        foreach($options as $option) {
            if(!in_array($option, $this->table_options)) continue;
            if(!($table == $option['value'])) continue;
        }
    }

    function extractMapping($string) {
        $mappings = explode('&',$string);
        foreach($mappings as $map) {
            list($key, $value) = explode('=',$map);
            $return[$key] = $value;
        }
    }

	function getSupporterKey() {
		return $this->_supporter_key;
	}
		
}

?>

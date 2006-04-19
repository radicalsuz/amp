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
			)
        );
    var $available = true;

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
        if( !( $lists = $this->udm->getRegisteredLists( ))) return;
        $this->fields[ 'list_header' ] = $this->_listfield_header;

        foreach ( $lists as $list_id => $list_name ){
            $listField = array( 'label'    => $list_name );
            $this->fields[ 'list_' . $list_id] = $listField + $this->_listfield_template;
        }
    }

    function getSaveFields() {
        $db_fields   = $this->udm->dbcon->MetaColumnNames('userdata');
        $qf_fields   = array_keys( $this->udm->form->exportValues() );
        $this->_field_prefix="";

        return array_intersect( $db_fields, $qf_fields );
    }


    function save ( $data ) {
        $options=$this->getOptions();

		if(!defined('DIA_API_ORGCODE') && isset($options[ 'orgKey' ])) {
			define('DIA_API_ORGCODE', $options[ 'orgKey' ]);
		}
		if(!defined('DIA_API_USERNAME') && isset($options[ 'user' ])) {
			define('DIA_API_USERNAME', $options[ 'user' ]);
		}
		if(!defined('DIA_API_PASSWORD') && isset($options[ 'password' ])) {
			define('DIA_API_PASSWORD', $options[ 'password' ]);
		}

		$data = $this->translate($data);
		$data['uid'] = $this->udm->uid;

		$api =& DIA_API::create();
		$supporter_id = $api->addSupporter( $data[ 'Email'], $data );
		$this->_supporter_key = $supporter_id;

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

	function translate( $data ) {
		$translation = array('region' => 'Region',
							'occupation' => 'Occupation',
							'Company' => 'Organization');

		foreach($data as $key => $value) {
			if(isset($translation[$key])) {
				$return[$translation[$key]] = $value;
			} else {
				$return[$key] = $value;
			}
		}

		return $return;
	}

	function getSupporterKey() {
		return $this->_supporter_key;
	}
		
}

?>

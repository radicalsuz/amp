<?php

require_once( 'DIA/API.php' );
require_once( 'AMP/UserData/Plugin/Save.inc.php' );

class UserDataPlugin_SupporterSave_DIA extends UserDataPlugin_Save {
    var $options = array(
        'orgKey' => array(
            'type'=>'text',
            'size'=>'5',
            'available'=>true,
            'label'=>'DIA Organization Key'
            )
        );

    function UserDataPlugin_SupporterSave_DIA(&$udm, $plugin_instance) {
        $this->init($udm, $plugin_instance);
    }

    function getSaveFields() {
        $db_fields   = $this->udm->dbcon->MetaColumnNames('userdata');
        $qf_fields   = array_keys( $this->udm->form->exportValues() );
        $this->_field_prefix="";

        return array_intersect( $db_fields, $qf_fields );
    }


    function save ( $data ) {
        $options=$this->getOptions();

		if(!defined('DIA_API_ORGKEY') && isset($options[ 'orgKey' ])) define('DIA_API_ORGKEY', $options[ 'orgKey' ]);

		$data = $this->translate($data);
		$data['uid'] = $this->udm->uid;

		$api =& DIA_API::create();
		$result = $api->addSupporter( $data[ 'Email'], $data );
print "supporter ID: $result.";
//        $diaRequest = new diaRequest( $options[ 'orgCode' ] );
//        $result = $diaRequest->addSupporter( $data[ 'Email' ], $data);

        return $result;

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
		
}

?>

<?php
require_once( 'AMP/UserData/Plugin.inc.php');

class UserDataPlugin_Start_Tags extends UserDataPlugin {

    var $options = array( 
        'public' => array( 
            'type' => 'checkbox',
            'label' => 'Allow Front-End Tagging' ,
            'default' => '',
            'available' => true
        )
    );

    var $available = true;

    function UserDataPlugin_Start_Tags( &$udm, $plugin_instance = null ) {
        $this->init( $udm, $plugin_instance );
    }

	function init( &$udm, $plugin_instance=null ) {
		parent::init( $udm, $plugin_instance );

		$save =& $udm->registerPlugin('Tags', 'Save', $plugin_instance );
		$save->setOptions($this->getOptions());

		$read =& $udm->registerPlugin('Tags', 'Read', $plugin_instance );
		$read->setOptions($this->getOptions());
	}

}

?>

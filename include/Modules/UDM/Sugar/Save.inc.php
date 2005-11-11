<?php

require_once( 'AMP/UserData/Plugin/Save.inc.php' );
require_once( 'Sugar/Data/Item.inc.php' );

class UserDataPlugin_Save_Sugar extends UserDataPlugin_Save {

    var $_available = true;
    var $_field_prefix = "";

    var $options = array(
        'module' => array(
            'label'     => 'Sugar Data Type',
            'available' => true,
            'default'   => 'contacts',
            'type'      => 'select' )
        );

    function UserDataPlugin_Save_Sugar( &$udm, $plugin_instance = null ) {

        $this->init ( $udm, $plugin_instance );

    }

    function getSaveFields() {
        return array_keys( $this->udm->fields );
    }

    function save( $data, $options=null ) {
        $options = array_merge( $this->getOptions(), $options );
        $sugarDaddy = &new Sugar_Data_Item( $options['module'] ); 
        $sugarDaddy->setData( $data );
        return $sugarDaddy->save();
    }
}
?>

<?php

require_once( 'AMP/System/Data/Item.inc.php');

class AMPSystem_UserData extends AMPSystem_Data_Item {

    var $datatable = "userdata_fields";
    var $_template_data;

    function AMPSystem_UserData( &$dbcon, $id = null ){
        $this->init( $dbcon, $id );
    }

    function getData($fields=null ){
        if ( isset( $fields )) return PARENT::getData( $fields );

        return array_merge( $this->_getTemplateArray( ), PARENT::getData() );
    }
    function _getTemplateArray( ) {
        if ( isset( $this->_template_data )) return $this->_template_data;
        foreach( $this->_allowed_keys as $keyname ){
            $this->_template_data[$keyname] = null;
        }
        return $this->_template_data;

    }

    function save( ){
        if ( !isset( $this->id) && !isset( $this->itemdata[$this->id_field]) ) $this->mergeData( array( $this->id_field => lowerlimitInsertID() ));
        return PARENT::save( );
    }

}

?>

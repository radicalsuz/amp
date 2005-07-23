<?php

require_once( 'AMP/UserData/Plugin/Save.inc.php' );

class UserDataPlugin_Save_AMP extends UserDataPlugin_Save {

    var $name = 'Save Submitted Data';
    var $description = 'Save the submitted data into the AMP database';

    var $available = true;

    function UserDataPlugin_Save_AMP ( &$udm, $plugin_instance=null ) {
        $this->init( $udm, $plugin_instance );
    }

    function getSaveFields () {

        // The AMP save function only saves fields for which there is a
        // corresponding column in the userdata table
        //
        // note that only valid, accessible fields will actually be
        // returned. If the module is set to allow admin access, then all
        // "enabled" fields will be returned for saving. This decision process
        // is in the UserData object itself.

        $db_fields   = $this->udm->dbcon->MetaColumnNames('userdata');
        $qf_fields   = array_keys( $this->udm->form->exportValues() );

        $save_fields = array_intersect( $db_fields, $qf_fields );
        $this->_field_prefix="";

        return $save_fields;

    }

    function save ( $data ) {

        $sql = ($this->udm->uid) ? $this->updateSQL( $data ) :
                                   $this->insertSQL( $data );

        $rs = $this->dbcon->CacheExecute( $sql ) or
                    die( "Unable to save request data using SQL $sql: " . $this->dbcon->ErrorMsg() );

        if ($rs) {
            if (!$this->udm->uid) $this->udm->uid = $this->dbcon->Insert_ID();
            return true;
        }

        return false;
    }

    function updateSQL ( $data ) {

        $dbcon =& $this->dbcon;

        $sql = "UPDATE userdata SET ";

        $save_fields = $this->getSaveFields();

        foreach ($save_fields as $field) {
            $elements[] = $field . "=" . $dbcon->qstr( $data[$field] );
        }

        $sql .= implode( ", ", $elements );
        $sql .= " WHERE id=" . $dbcon->qstr( $this->udm->uid );

        return $sql;

    }

    function insertSQL ( $data ) {

        $dbcon =& $this->dbcon;

        $data['modin'] = $this->udm->instance;

        $fields = $this->getSaveFields();
        $values_noescape = array_values( $data );

        foreach ( $fields as $field ) {
            $value = $data[$field];
            $values[] = $dbcon->qstr( $value );
        }

        $sql  = "INSERT INTO userdata (";
        $sql .= join( ", ", $fields ) .
                ") VALUES (" .
                join( ", ", $values ) .
                ")";

        return $sql;

    }

}

?>

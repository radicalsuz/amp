<?php

require_once( 'AMP/UserData/Plugin/Save.inc.php' );

class UserDataPlugin_Save_AMP extends UserDataPlugin_Save {

    var $name = 'Save Submitted Data';
    var $description = 'Save the submitted data into the AMP database';

    var $available = true;

    function UserDataPlugin_Save_AMP ( &$udm ) {
        $this->init( $udm );
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

        return $save_fields;

    }

    function save ( $data ) {

        $sql = ($this->udm->uid) ? $this->updateSQL( $data ) :
                                   $this->insertSQL( $data );

        $rs = $this->dbcon->CacheExecute( $sql ) or
                    die( "Unable to save request data using SQL $sql: " . $this->dbcon->ErrorMsg() );

        if ($rs) {
            $this->udm->showForm = false;
            $this->udm->uid = $this->dbcon->Insert_ID();
            return true;
        }

        return false;
    }

    function updateSQL ( $data ) {

        $dbcon =& $this->dbcon;

        $sql = "UPDATE userdata SET ";

        foreach ($data as $field => $value) {
            $elements[] = $field . "=" . $dbcon->qstr( $value );
        }

        $sql .= implode( ", ", $elements );
        $sql .= " WHERE id=" . $dbcon->qstr( $this->udm->uid );

        return $sql;

    }

    function insertSQL ( $data ) {

        $dbcon =& $this->dbcon;

        $data['modin'] = $this->udm->instance;

        $fields = array_keys( $data );
        $values_noescape = array_values( $data );

        foreach ( $values_noescape as $value ) {
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

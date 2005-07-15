<?php

/**************
 *  AMPSystem_IntroText 
 *  represents introductory and response texts
 *  used by the Modules system
 *
 *  AMP 3.5.0
 *  2005-27-06
 *
 *  Author: austin@radicaldesigns.org
 *
 *****/


 class AMPSystem_IntroText {

    var $dbcon;
    var $textdata;
    var $_textdata_keys;
    var $id;

    function AMPSystem_IntroText ( &$dbcon, $text_id=null ) {
        $this->dbcon = & $dbcon;
        $this->_textdata_keys = $this->dbcon->MetaColumnNames( "moduletext" );
        if (isset($text_id) && $text_id) $this->readData( $text_id );
    }

    function readData ( $text_id ) {
        $sql = "Select * from moduletext where id = ". $this->dbcon->qstr( $text_id );

        if ( $textdata = $this->dbcon->CacheGetRow( $sql )) {
            $this->setData( $textdata );
            $this->id = $text_id;
            return true;
        }

        return false;
    }

    function deleteData( $text_id ) {
        $sql = "Delete from moduletext where id = ". $this->dbcon->qstr( $text_id );
        if ( $textdata = $this->dbcon->Execute( $sql )) {
            return true;
        }

        return false ;
    }

    function save() {
        $save_fields = array_combine_key($this->_textdata_keys, $this->getData());
        
        $result = $this->dbcon->Replace( "moduletext", $save_fields, "id", $quote=true);

        if ($result == ADODB_REPLACE_INSERTED ) $this->id = $this->dbcon->Insert_ID();
        if ($result) return true;

        return false;
    }

    function setData( $data ) {
        $this->textdata = array_combine_key( $this->_textdata_keys, $data );
        $this->legacyFieldname( $data, 'test', 'body' );
        $this->legacyFieldname( $data, 'subtitile', 'subtitle' );
        if (isset($data['id']) && $data['id']) $this->id = $data['id'];
    }

    function legacyFieldname( $data, $oldname, $newname ) {
        if (isset($data[$oldname])) $this->textdata[$newname] = $data[$oldname];
        if (isset($data[$newname])) {
            $this->textdata[$newname] = $data[$newname];
            $this->textdata[$oldname] = $data[$newname];
        }
    }

    function getData( $fieldname = null ) {
        if (!isset($fieldname)) return $this->textdata;

        if (isset($this->textdata[$fieldname])) return $this->textdata[$fieldname];

        return false;
    }
 }

 ?>

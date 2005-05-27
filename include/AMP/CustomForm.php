<?php

/* * * * * * * * * * * *
 *   AMP CustomForm
 *
 *   A base class for building forms 
 *   which save, read, and list data in any table
 *
 *   Author: austin@radicaldesigns.org
 *   Date: 5/26/2005
 *
 */



require_once('HTML/QuickForm.php');



class AMP_CustomForm {
    
    var $name;
    var $fields;
    var $redirect;
    var $datatable;
    var $form;

    var $id;
    var $dbcon;
    var $admin;

    var $list;


//Initialization Functions
    function AMP_CustomForm(&$dbcon, $name, $datatable, $fields, $redirect) {
        $this->name = $name;
        $this->datatable = $datatable;
        $this->fields = $fields;
        $this->redirect = $redirect;

        $this->init($dbcon);

    }

    function init(&$dbcon, $admin=false) {
     
        $this->dbcon = &$dbcon;
        $this->admin = $admin;
        $this->form = & new HTML_QuickForm($this->name, 'post', $_SERVER['PHP_SELF']);

        $this->register_fields();

    }

    function register_fields() {
        foreach ($this->fields as $fname => $fdef) {
            if (($this->admin or $fdef['public']) && $fdef['enabled']) {
                $fRef = &$this->form->addElement($fdef['type'], $fname, $fdef['label'], $fdef['defaults']);
                if ($fdef['attr']) {
                    $fRef->updateAttributes($fdef['attr']);
                }
            }
        }
        //add Formname and submit button
        $this->form->addElement('hidden','formname');
        $this->form->setConstants( array('formname'=>$this->name) );
        $this->form->addElement('submit', 'btnCustomFormSubmit', 'Save Data');
        $fRef = &$this->form->addElement('submit', 'btnCustomFormDelete', 'Delete Record');
        $fRef->updateAttributes( array(
            'onClick'=>'return confirmSubmit("Are you sure you want to DELETE this record?");',
            'style'=>'margin-top:30px') );

    }
    

//Data Management Functions
    function getData($id = null) {
        $this->id = $id;
        $fieldset = array_keys($this->fields);
        $sql = "Select ".join(",",$fieldset)." from ".$this->datatable;
        if (isset($id) && $id) {
            $sql.=" where id=".$id;
            if ($_REQUEST['debug']) print $sql;
            $datablock = $this->dbcon->Execute($sql);
            $dataset = $datablock->FetchRow();
            $this->form->setDefaults($dataset);
        } else {
            if (isset($this->list['criteria'])) {
                $sql .= ' WHERE '.join(" AND ", $this->list['criteria']);
            }
            if (isset($this->list['sort'])) {
                $sql .= " ORDER BY ".$this->list['sort'];
            }
            if ($_REQUEST['debug']) print $sql;
            $dataset = $this->dbcon->CacheGetAll($sql);
        }
        return $dataset;
    }

    function delData($id) {
        $sql = "DELETE FROM " . $this->datatable . " where id = ".$id;
        if($this->dbcon->Execute($sql)) {
            return true;
        }

        return false;
        
    }

    function saveData() {
        $data = $_REQUEST;

        //Fix checkbox problem
        foreach ($this->fields as $fname =>$fDef) {
            if ($fDef['type']=='checkbox' &&($this->admin||$fDef['public'])) {
                if (!isset($data[$fname])) $data[$fname]='0';
                
            }
        }

        $sql = ($data['id']) ? $this->updateSQL( $data ):
                               $this->insertSQL( $data);

        $rs = $this->dbcon->CacheExecute( $sql) or
            die ( "Unable to save form data using SQL $sql: " . $this->dbcon->ErrorMsg()  );

        if ($rs) {
            $this->id = $this->dbcon->Insert_ID();
            return true;
        }

        return false;
    }


    function updateSQL ( $data ) {

        $dbcon =& $this->dbcon;

        $sql = "UPDATE ".$this->datatable. " SET ";

        $save_fields = $this->getSaveFields($data);

        foreach ($save_fields as $field) {
            $elements[] = $field . "=" . $dbcon->qstr( $data[$field] );
        }

        $sql .= implode( ", ", $elements );
        $sql .= " WHERE id=" . $dbcon->qstr( $data['id'] );

        return $sql;

    }

    function insertSQL ( $data ) {

        $dbcon =& $this->dbcon;

        $fields = $this->getSaveFields($data);
        $values_noescape = array_values( $data );

        foreach ( $fields as $field ) {
            $value = $data[$field];
            $values[] = $dbcon->qstr( $value );
        }

        $sql  = "INSERT INTO ".$this->datatable." (";
        $sql .= join( ", ", $fields ) .
                ") VALUES (" .
                join( ", ", $values ) .
                ")";

        return $sql;

    }
    
    function getSaveFields ($data) {

        // The AMP save function only saves fields for which there is a
        // corresponding column in the data table
        //
        // note that only valid, accessible fields will actually be
        // returned. If the module is set to allow admin access, then all
        // "enabled" fields will be returned for saving. This decision process
        // is in the object itself.

        $db_fields   = $this->dbcon->MetaColumnNames($this->datatable);
        $qf_fields   = array_keys( $data );

        $save_fields = array_intersect( $db_fields, $qf_fields );

        return $save_fields;

    }
    //Display Functions

    //Output displays the form
    function output() {
        return $this->message.$this->form->toHTML();
    }


    //listpage shows the set of records
    function listpage() {
        //Sort the data
        if (isset($_REQUEST['sort']) && $_REQUEST['sort']) { 
            $this->list['sort'] = $_REQUEST['sort'];
        }

        //Retrieve the dataset from the DB
        //this page should not be used for big big lists, it will die
        $data = $this->getData();

        //Starter HTML
        $start_html = "<h2>".str_replace("_", " ", $this->name)."</h2>";
        $start_html .= $this->message;
        $start_html .= "\n<div class='list_table'> \n	<table class='list_table'>\n		<tr class='intitle'> ";
        $start_html .= "\n<td>&nbsp;</td>";

        //URL Criteria
        //Fixme I'm using this function everywhere - it should go in the Base
        //class
        parse_str($_SERVER['QUERY_STRING'], $url_criteria_set );
        unset ($url_criteria_set['sort']);
        $url_criteria = "";

        foreach($url_criteria_set as $ukey=>$uvalue) {
            $url_criteria .= $ukey."=".$uvalue.'&';
        }
        $url_criteria .= "sort=";            

        //Define HTML for Column Headers
        foreach ($this->list['alias'] as $k=>$v) {
            $column_headers .= "\n        <td><b><a href='".$_SERVER['PHP_SELF']."?".$url_criteria.$v."' class='intitle'>".$k."</a></b></td>";
        }
        foreach ($this->list['extra'] as $header=>$col) {
            $column_headers .= "\n			<td>&nbsp;</td>";
        }
        $column_headers .= "\n		</tr>";


        $i= 0;
        foreach ($data as $rownum=>$currentrow) {
            $i++;
            $bgcolor =($i % 2) ? "#D5D5D5" : "#E5E5E5";
            $list_html .="\n		<tr bordercolor=\"#333333\" bgcolor=\"". $bgcolor."\" onMouseover=\"this.bgColor='#CCFFCC'\" onMouseout=\"this.bgColor='". $bgcolor ."'\"> "; 
            $list_html .="\n			<td> <div align='center'><A HREF='".$this->list['link']."&id=".$currentrow['id']."'><img src=\"images/edit.png\" alt=\"Edit\" width=\"16\" height=\"16\" border=0></A></div></td>";

            //Publish field hack
            if (isset($currentrow['publish']))
                $currentrow['publish'] = ($currentrow['publish']==1)?'live':'draft';

            //show each row
            foreach ($this->list['alias'] as $header=>$col) {
                $display_text = $currentrow[$col];
                //check for a lookup table
                if (isset($this->lookups[$col][$display_text])) {
                    $display_text = $this->lookups[$col][$display_text];
                }
                $list_html .= "<td> " . $display_text . " </td>";
            }
           
           //show extra links for each row
            if (isset($this->list['extra'])) {
                
                
                foreach ($this->list['extra'] as $header=>$col) {
                    $id="id";
                    if (isset($this->list['extramap'][$header])) {
                        $id= $this->list['extramap'][$header];
                    }
                    $list_html .= " \n			<td> <div align='right'>";
                    $list_html .= "<A HREF='".$col.$currentrow[$id]."'>$header</A>";
                    $list_html .= "</div></td>";
                }
                
            }
            $list_html .=  "\n		</tr>";
        
        }		
        
        $end_html = "\n	</table>\n</div>\n<br>&nbsp;&nbsp;<a href=\"".$this->list['link']."\">Add new record</a> ";

        return $start_html.$column_headers.$list_html.$end_html;

    }

}

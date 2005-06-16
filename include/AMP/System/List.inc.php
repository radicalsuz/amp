<?php

/* * * * * * * * * * * *
 *   AMP System List
 *
 *   A base class for system list pages
 *
 *   Author: austin@radicaldesigns.org
 *   Date: 6/16/2005
 *
 */


class AMP_SystemList {
    
    var $name;
    var $fields;

    var $id;
    var $dbcon;
    var $admin;

    var $list;
    var $sort;
    var $message;
    var $sql_criteria;

    var $extra_columns;
    var $extra_column_mapvalue;
    var $lookups;
    var $col_headers;
    var $list_counter=0;
    var $editlink;


//Initialization Functions
    function AMP_SystemList (&$dbcon, $name, $datatable, $fields) {
        $this->name = $name;
        $this->datatable = $datatable;
        $this->fields = $fields;

        $this->init($dbcon);

    }

    function init(&$dbcon, $admin=false) {
     
        $this->dbcon = &$dbcon;
        $this->admin = $admin;

    }

    function defineFieldset() {
        if (is_numeric(key($this->fields))) return $this->fields;
        return array_keys($this->fields);
    }


    //Data Management Functions

    function getData() {
        $fieldset = $this->defineFieldSet();
        $sql = "Select ".join(",",$fieldset)." from ".$this->datatable;
        if (isset($this->sql_criteria)) {
            $sql .= ' WHERE '.join(" AND ", $this->sql_criteria);
        }
        if (isset($this->sort)) {
            $sql .= " ORDER BY ".$this->sort;
        }
        if ($_REQUEST['debug']) print $sql;
        $dataset = $this->dbcon->CacheGetAll($sql);
        
        return $dataset;
    }

    function delData($id) {
        $sql = "DELETE FROM " . $this->datatable . " where id = ".$id;
        if($this->dbcon->Execute($sql)) {
            return true;
        }

        return false;
        
    }

    function setSort() {
        //Sort the data
        if (isset($_REQUEST['sort']) && $_REQUEST['sort']) { 
            $this->sort = $_REQUEST['sort'];
        }
    }

    //listpage shows the set of records
    function output() {
        $this->setSort();

        //Retrieve the dataset from the DB
        //this page should not be used for big big lists, it will die
        $data = $this->getData();

        $output = "";

        foreach ($data as $rownum=>$currentrow) {
            $output .= $this->listitemHTML( $this->translateRow($currentrow));
        
        }		
        return $this->headerHTML().$output.$this->footerHTML();

    }

    function headerHTML() {
        //Starter HTML
        $start_html = "<h2>".str_replace("_", " ", $this->name)."</h2>";
        $start_html .= $this->message;
        $start_html .= "\n<div class='list_table'> \n	<table class='list_table'>\n		<tr class='intitle'> ";
        $start_html .= "\n<td>&nbsp;</td>";

        return $start_html.$this->columnHeaders();
    }

    function columnHeaders() {
        $url_criteria = $this->getURLCriteria();
        $output = "";
        //Define HTML for Column Headers
        foreach ($this->col_headers as $k=>$v) {
            $output.= "\n        <td><b><a href='".$_SERVER['PHP_SELF']."?".$url_criteria.$v."' class='intitle'>".$k."</a></b></td>";
        }
        foreach ($this->extra_columns as $header=>$col) {
            $output.= "\n			<td>&nbsp;</td>";
        }
        $output.= "\n		</tr>";

        return $output;
    }



    function listitemHTML( $currentrow ) {
        $this->list_counter++;
        $bgcolor =($this->list_counter % 2) ? "#D5D5D5" : "#E5E5E5";
        $list_html_endrow =  "\n		</tr>";
        $list_html = "";


        $list_html .="\n		<tr bordercolor=\"#333333\" bgcolor=\"". $bgcolor."\" onMouseover=\"this.bgColor='#CCFFCC'\" onMouseout=\"this.bgColor='". $bgcolor ."'\"> "; 
        $list_html .="\n			<td> <div align='center'><A HREF='".$this->editlink."&id=".$currentrow['id']."'><img src=\"images/edit.png\" alt=\"Edit\" width=\"16\" height=\"16\" border=0></A></div></td>";
        

        //show each row
        foreach ($this->col_headers as $header=>$col) {
            $list_html .= "<td> " . $currentrow[$col] . " </td>";
        }

        return $list_html.$this->extraColumns($currentrow).$list_html_endrow;
        
    }

    function extraColumns( $currentrow ) {
        if (!isset($this->extra_columns)) return '';
        $list_html='';

        //show extra links for each row
        foreach ($this->extra_columns as $header=>$col) {
            $list_html .= " \n			<td> <div align='right'>";
            $list_html .= "<A HREF='".$col.$currentrow[$this->requestedID( $header )]."'>$header</A>";
            $list_html .= "</div></td>";
        }
        return $list_html;
    }

    function requestedID( $header ) {
        if (!isset($this->extra_column_maps[$header])) return "id"; 
        return $this->extra_column_maps[$header];
    }

    function translateRow( $currentrow ) {
        //Publish field hack
        if (isset($currentrow['publish']))
            $currentrow['publish'] = ($currentrow['publish']==1)?'live':'draft';

        if (!isset($this->lookups)) return $currentrow;

        //check for a lookup table
        foreach ($this->lookups as $lookup_name=>$lookup_set) {
            if (!isset($current_row[$lookup_name])) continue;
            if (!isset($lookup_set[$currentrow[$lookup_name]])) continue;
            $currentrow[$lookup_name] = $lookup_set[$currentrow[$lookup_name]];
        }

        return $currentrow;
    }

    function footerHTML () {
        return "\n	</table>\n</div>\n<br>&nbsp;&nbsp;<a href=\"".$this->editlink."\">Add new record</a> ";
    }


    function getURLCriteria() {
        //URL Criteria
        //Fixme I'm using this function everywhere - it should go in the Base
        //class
        parse_str($_SERVER['QUERY_STRING'], $url_criteria_set );
        unset ($url_criteria_set['sort']);
        $url_criteria = "";

        foreach($url_criteria_set as $ukey=>$uvalue) {
            $url_criteria .= $ukey."=".$uvalue.'&';
        }
        return $url_criteria . "sort=";            
    }

}

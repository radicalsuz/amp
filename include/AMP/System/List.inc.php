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

require_once ( 'AMP/System/Data/Set.inc.php' );

class AMPSystem_List {
    
    var $name;
    var $message;

    var $source;
    var $sourceclass = "AMPSystem_Data_Set";

    var $col_headers;
    var $extra_columns;
    var $extra_column_mapvalue;

    var $editlink;

    var $lookups;

    var $color = array ( 
        'background' => array( "#D5D5D5" , "#E5E5E5" ) ,
        'border'    => '#333333',
        'mouseover' => '#CCFFCC');

    var $suppress = array( 'header'=>true );

    ####################
    ### Core Methods ###
    ####################

    //Constuctor -- this object is primarily meant to be subclassed
    //but some use can be gained via this constructor

    function AMPSystem_List (&$dbcon, $name, $col_headers=null, $datatable = null ) {

        $this->name = $name;
        if (isset($col_headers)) $this->setColumns( $col_headers );

        $source = & new $sourceclass ( $dbcon );
        $source->setSource( $datatable );

        $this->init($source);

    }

    function init(&$source) {
     
        $this->source = &$source;
        $this->_setSort();
        $this->_prepareData();

    }

    function output() {

        if (!$this->_prepareData()) return false;

        $output = "";

        while ( $currentrow = $this->source->getData()) {
            $output .= $this->_HTML_listRow ( $this->_translateRow($currentrow));
        
        }		
        return $this->_HTML_header() .
               $output .
               $this->_HTML_footer();

    }

    ###########################################
    ###  Public Presentation Option Methods ###
    ###########################################

    function setColumns( $col_headers ) {
        foreach ($col_headers as $col_name => $col_exp ) {
            $this->addColumn( $col_name, $col_exp );
        }
    }

    function addColumn ( $col_name, $col_exp ) {
        $this->col_headers[$col_name] = $col_exp;
    }

    function suppressHeader() {
        $this->suppress['header'] = true;
    }

    function suppressAddlink() {
        $this->suppress['addlink'] = true;
    }

    function getColor( $color_type ) {
        if (!isset($this->color[$color_type])) return "#000000";
        return $this->color[$color_type];
    }

    function setColor( $type, $color_id ) {
        $this->color[$type] = $color_id;
    }

    function setMessage( $text ) {
        $this->message .= $text .'<BR>';
    }


    #########################################
    ### Private HTML Construction Methods ###
    #########################################

    function _HTML_listRow ( $currentrow ) {
        $bgcolor = $this->_setBgColor();
        $list_html = "";
        $list_html_endrow =  "\n</tr>";


        $list_html .="\n<tr bordercolor=\"".$this->getColor('border')."\" bgcolor=\"". $bgcolor."\""
                    ." onMouseover=\"this.bgColor='".$this->getColor('mouseover')."';\""
                    ." onMouseout=\"this.bgColor='". $bgcolor ."';\"> "; 
        $list_html .= $this->_HTML_editColumn( $currentrow['id'] );
        

        //show each row
        foreach ($this->col_headers as $header=>$col) {
            $list_html .= "<td> " . $currentrow[$col] . " </td>";
        }

        return $list_html.$this->_HTML_extraColumns($currentrow).$list_html_endrow;
        
    }

    function _HTML_editColumn( $id ) {
        if (isset($this->suppress['editcolumn'])) return "";
        return "\n<td><div align='center'><A HREF='".$this->editlink."?id=".$id."'>"
              ."<img src=\"images/edit.png\" alt=\"Edit\" width=\"16\" height=\"16\" border=0></A></div></td>";
    }

    function _HTML_extraColumns( $currentrow ) {
        if (!isset($this->extra_columns)) return '';
        $list_html='';

        //show extra links for each row
        foreach ($this->extra_columns as $header=>$col) {
            $list_html .= " \n<td> <div align='right'>";
            $list_html .= "<A HREF='".$col.$currentrow[$this->_requestedID( $header )]."'>$header</A>";
            $list_html .= "</div></td>";
        }
        return $list_html;
    }


    function _HTML_header() {
        //Starter HTML
        $start_html = $this->_HTML_listTitle() . $this->_HTML_showMessage();
        $start_html .= "\n<div class='list_table'>\n<table class='list_table'>\n<tr class='intitle'> ";

        return $start_html.$this->_HTML_columnHeaders();
    }

    function _HTML_showMessage() {
        #if (!isset($this->suppress['message'])) return '<span class="page_result">'.$this->message.'</span>';
    }

    function _HTML_listTitle() {

        if (isset($this->suppress['header'])) return false;
        return "<h2>".str_replace("_", " ", $this->name)."</h2>";
    }

    function _HTML_editColumnHeader() {
        if (isset($this->suppress['editcolumn'])) return "";
        return "\n<td>&nbsp;</td>";
    }

    function _HTML_columnHeaders() {
        $url_criteria = $this->_prepURLCriteria();
        $output = $this->_HTML_editColumnHeader();
        $endrow = "\n</tr>";

        foreach ($this->col_headers as $header=>$col_value) {
            $link = $_SERVER['PHP_SELF']."?$url_criteria&sort=$col_value";
            $output.= 
                "\n<td><b><a href='$link' class='intitle'>".$header."</a></b></td>";
        }
        return $output . $this->_HTML_extraColumnHeaders() . $endrow ;
    }

    function _HTML_extraColumnHeaders() {
        if (!isset($this->extra_columns)) return "";
        return str_repeat( "<td>&nbsp;</td>", count ($this->extra_columns) );
    }

    function _HTML_footer() {
        return "\n	</table>\n</div>\n<br>&nbsp;&nbsp;" . $this->_HTML_addLink();
    }

    function _HTML_addLink () {
        if (isset($this->suppress['addlink'])) return false;
        return "<a href=\"".$this->editlink."\">Add new record</a> ";
    }


    ################################################
    ### Private HTML Construction Helper Methods ###
    ################################################
    
    function _setBgColor() {
        static $list_counter = 0;
        $list_counter++;
        return $this->color['background'][ ($list_counter%2) ];
    }

    function _requestedID( $header ) {
        if (!isset($this->extra_column_maps[$header])) return "id"; 
        return $this->extra_column_maps[$header];
    }

    function _prepURLCriteria() {
        $url_criteria_set = AMP_URL_Values();
        if (empty( $url_criteria_set )) return "";
        unset ($url_criteria_set['sort']);
        return join("&" , $url_criteria_set );            
    }

    #########################################
    ### Private Data Manipulation Methods ###
    #########################################

    function _translateRow( $currentrow ) {
        //Publish field hack
        if (isset($currentrow['publish']))
            $currentrow['publish'] = ($currentrow['publish']==1)?'live':'draft';

        if (!isset($this->lookups)) return $currentrow;

        //check for a lookup table
        foreach ($this->lookups as $lookup_name=>$lookup_set) {
            if (!isset($currentrow[$lookup_name])) continue;
            if (!isset($lookup_set[$currentrow[$lookup_name]])) continue;
            $currentrow[$lookup_name] = $lookup_set[$currentrow[$lookup_name]];
        }

        return $currentrow;
    }

    function _prepareData() {
        if ($this->source->isReady()) return true;
        $this->source->setSelect( $this->_defineFieldSet() );
        return $this->source->readData();
        
    }

    function _setSort() {
        //Sort the data
        if (isset($_REQUEST['sort']) && $_REQUEST['sort']) { 
            $this->source->addSort($_REQUEST['sort']);
        }
    }

    function _defineFieldset( ) {
        if (!isset($this->col_headers)) return;
        $select_exps =  array_values($this->col_headers);
        $select_exps[] = "id";
        return $select_exps;
    }


}

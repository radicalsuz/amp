<?php

/*** * * * * * * * * * *
 *   AMP System List
 *
 *   A base class for system list pages
 *
 *   Author: austin@radicaldesigns.org
 *   Date: 6/16/2005
 *
 * * **/

define( 'AMP_PUBLISH_STATUS_LIVE' , 'live' );
define( 'AMP_PUBLISH_STATUS_DRAFT' , 'draft' );
define ('AMP_SYSTEM_ICON_EDIT', '/system/images/edit.png' ); 
define ('AMP_SYSTEM_ICON_PREVIEW', '/system/images/view.gif' );
define ('AMP_SYSTEM_ICON_DELETE', '/system/images/delete.png' );

require_once ( 'AMP/System/Data/Set.inc.php' );
require_once ( 'AMP/Content/Display/HTML.inc.php');

class AMPSystem_List extends AMPDisplay_HTML {
    
    var $name;
    var $message;

    var $source;
    var $sourceclass = "AMPSystem_Data_Set";

    var $col_headers;
    var $_url_criteria;
    var $extra_columns;
    var $extra_column_mapvalue;

    var $editlink;

    var $lookups;
    var $translations;

    var $color = array ( 
        'background' => array( "#D5D5D5" , "#E5E5E5" ) ,
        'border'    => '#333333',
        'mouseover' => '#CCFFCC');

    var $suppress = array( 'header'=>true );
    var $currentrow;
    var $column_callBacks;

    var $_pager;
    var $_pager_active  = false;
    var $_pager_display = true;
    var $_pager_limit = false;

    ####################
    ### Core Methods ###
    ####################

    //Constuctor -- this object is primarily meant to be subclassed
    //but some use can be gained via this constructor

    function AMPSystem_List (&$dbcon, $name, $col_headers=null, $datatable = null ) {

        $this->name = $name;
        if (isset($col_headers)) $this->setColumns( $col_headers );

        $source = & new $this->sourceclass ( $dbcon );
        $source->setSource( $datatable );

        $this->init($source);

    }

    function init(&$source) {
     
        $this->source = &$source;
        $this->_setSort();
        $this->_activatePager( );
        $this->_prepareData();
        if (array_search( 'publish', $this->col_headers ) !== FALSE ) {
            $this->addTranslation( 'publish', '_showPublishStatus' );
        }

    }

    function _activatePager() {
        if ( !$this->_pager_active ) return false;

        require_once( 'AMP/System/List/Pager.inc.php');
        $this->_pager = &new AMPSystem_ListPager( $this->source );
        
        if ( $this->_pager_limit ) $this->_pager->setLimit( $this->_pager_limit ); 
    }


    function output() {

        if (!$this->_prepareData()) return false;

        $output = "";

        while ( $this->currentrow = $this->source->getData()) {
            $output .= $this->_HTML_listRow ( $this->_translateRow($this->currentrow));
        
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

    function applySearch( $values ){
        return $this->source->applySearch( $values );
    }


    #########################################
    ### Private HTML Construction Methods ###
    #########################################

    function _HTML_listRow ( $currentrow ) {
        //show each row
        $list_html = $this->_HTML_startRow( $currentrow['id'] );
        foreach ($this->col_headers as $header=>$col) {
            $list_html .= "<td> " . $currentrow[$col] . " </td>";
        }

        return $list_html.$this->_HTML_extraColumns($currentrow).$this->_HTML_endRow( $currentrow['id'] );
        
    }

    function _HTML_startRow( $id ) {
        $bgcolor = $this->_setBgColor();
        $output ="\n<tr bordercolor=\"".$this->getColor('border')."\" bgcolor=\"". $bgcolor."\""
                    ." onMouseover=\"this.bgColor='".$this->getColor('mouseover')."';\""
                    ." onMouseout=\"this.bgColor='". $bgcolor ."';\"> "; 
        return $output . $this->_HTML_firstColumn( $id );
    }
        
    function _HTML_firstColumn( $id ) {
        return $this->_HTML_editColumn( $id );
    }

    function _HTML_endRow( $id=null ) {
        return "\n</tr>";
    }

    function _HTML_editColumn( $id ) {
        if (isset($this->suppress['editcolumn'])) return "";
        return "\n<td><div align='center'>" . $this->_HTML_editLink( $id ) . "</div></td>";
    }

    function _HTML_editLink( $id ) {
        return  "<A HREF='". AMP_URL_AddVars( $this->editlink , "id=".$id ) ."' title='Edit this Item'>" .
                "<img src=\"". AMP_SYSTEM_ICON_EDIT ."\" alt=\"Edit\" width=\"16\" height=\"16\" border=0></A>" ;
    }

    function _HTML_extraColumns( $currentrow ) {
        if (!isset($this->extra_columns)) return '';
        $list_html='';

        //show extra links for each row
        foreach ($this->extra_columns as $header=>$baselink) {
            $list_html .= " \n<td>";
            $list_html .= ( isset( $this->translations[$baselink])) ?
                                $currentrow[$baselink] :
                                $this->_HTML_extraColumnsDefault( $header, $currentrow );
            $list_html .= "</td>";
        }
        return $list_html;
    }

    function _HTML_extraColumnsDefault(  $header, $currentrow ){
        return  " <div align='right'>"
                . "<A HREF='". $this->_getColumnLink( $header, $currentrow )."'>$header</A>"
                . "</div>";

    }

    function _getColumnLink( $colname, $currentrow  ) {
        if (isset($this->extra_columns[ $colname ])) {
            if (!isset($this->column_callBacks[ $colname ] )) {
                return $this->extra_columns[ $colname ].$currentrow[$this->_requestedID( $colname )];
            } else {
                $method = $this->column_callBacks[ $colname ]['method'];
                #$args =  $this->column_callBacks[ $colname ]['args'];
                return call_user_func( $method, $currentrow );
            }
            return false;
        }
    }


    function _HTML_header() {
        //Starter HTML
        $start_html = $this->_HTML_listTitle();
        $start_html .= "\n<div class='list_table'>\n<table class='list_table'>\n<tr class='intitle'> ";

        return $start_html.$this->_HTML_columnHeaders();
    }

    function _HTML_listTitle() {

        if (isset($this->suppress['header'])) return false;
        return "<h2>".str_replace("_", " ", $this->name)."</h2>";
    }

    function _HTML_editColumnHeader() {
        if (isset($this->suppress['editcolumn'])) return "";
        return "\n<td>&nbsp;</td>";
    }

    function _HTML_sortLink( $fieldname ) {
        $url_criteria = $this->_prepURLCriteria();
        $new_sort = $fieldname;
        if ($fieldname == $this->source->getSort()) $new_sort .= " DESC";
        return $_SERVER['PHP_SELF']."?$url_criteria&sort=$new_sort";
    }

    function _HTML_columnHeaders() {
        $output = $this->_HTML_firstColumnHeader();

        foreach ($this->col_headers as $header=>$fieldname) {
            $link = $this->_HTML_sortLink( $fieldname );
            $output.= 
                "\n<td><b><a href='$link' class='intitle'>".$header."</a></b></td>";
        }
        return $output . $this->_HTML_endColumnHeadersRow();
    }

    function _HTML_firstColumnHeader() {
        return $this->_HTML_editColumnHeader();
    }

    function _HTML_endColumnHeadersRow() {
        return $this->_HTML_extraColumnHeaders() . "\n</tr>";
    }

    function _HTML_extraColumnHeaders() {
        if (!isset($this->extra_columns)) return "";
        return str_repeat( "<td>&nbsp;</td>", count ($this->extra_columns) );
    }

    function _HTML_footer() {
        return  "\n	</table>\n"
                . ( ($this->_pager_active && $this->_pager_display ) ? $this->_pager->execute() : false ) 
                . "</div>\n<br>&nbsp;&nbsp;" 
                . $this->_HTML_addLink();
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
        if (!$this->_url_criteria ) {
            $url_criteria_set = AMP_URL_Values();
            if (empty( $url_criteria_set )) return "";
            unset ($url_criteria_set['sort']);
            $this->_url_criteria = join("&" , $url_criteria_set );            
        }
        return $this->_url_criteria;
    }

    #########################################
    ### Private Data Manipulation Methods ###
    #########################################

    function _translateRow( $currentrow ) {

        if (!$this->hasTranslations()) return $currentrow;

        $translated_row = $currentrow;

        foreach ($this->translations as $fieldname=>$translate_method) {
            if (!array_key_exists($fieldname, $currentrow)) continue;
            if (!method_exists( $this, $translate_method )) {
                trigger_error ( get_class( $this ) . ": Translation method " . $translate_method . " for field " . $fieldname . " not found. ");
                continue;
            }
            $translated_row[ $fieldname ] = $this->$translate_method( $currentrow[ $fieldname ], $fieldname, $currentrow );
        }

        return $translated_row;
    }

    function lookup( $value, $lookup_name ) {
        if (!isset($this->lookups[ $lookup_name ][ $value ] )) return $value;
        return $this->lookups[ $lookup_name ][ $value ];
    }

    function _showPublishStatus( $publish_value, $field = "publish" ) {
        if ($publish_value == 1) return AMP_PUBLISH_STATUS_LIVE;
        return AMP_PUBLISH_STATUS_DRAFT;
    }

    function addLookup( $field , $dataset ) {
        $this->lookups[ $field ] = $dataset;
        $this->addTranslation( $field , 'lookup');
    }

    function addTranslation( $field, $translation_method ) {
        $this->translations[ $field ] = $translation_method;
    }

    function hasTranslations() {
        return (!empty ($this->translations));
    }

    function _prepareData() {
        if ($this->source->makeReady()) return true;
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

    function addCriteria( $sql_criteria, $change_editlink=false) {
        $this->source->addCriteria( $sql_criteria );
        $result =  $this->source->readData(); 

        if ($result && $change_editlink) $this->appendEditlinkVar( $sql_criteria );
        return $result;
    }

    function appendEditlinkVar( $var ) {
        $this->editlink = AMP_URL_AddVars( $this->editlink, $var );
    }


}
?>

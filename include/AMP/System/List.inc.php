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

require_once ( 'AMP/System/Data/Set.inc.php' );
require_once ( 'AMP/Content/Display/HTML.inc.php');
require_once ( 'AMP/Content/Config.inc.php');

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
    var $_url_add;
    var $_url_edit;

    var $lookups;
    var $translations;
    var $_observers_source = array( );

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

    var $_css_class_columnheader = 'intitle';

    var $_sort;
    var $_source_counter = 0;
    var $_source_keys;
    var $_source_object;
    var $_source_criteria;

    var $_controller;
    var $_actions;

    var $_renderer;

    ####################
    ### Core Methods ###
    ####################

    //Constuctor -- this object is primarily meant to be subclassed
    //but some use can be gained via this constructor

    function AMPSystem_List (&$dbcon, $name, $col_headers=null, $datatable = null ) {

        if ( !( isset( $this->_source_object ) && class_exists( $this->_source_object ))) {
            $this->name = $name;
            if (isset($col_headers)) $this->setColumns( $col_headers );

            $source = & new $this->sourceclass ( $dbcon );
            $source->setSource( $datatable );

        } else {
            $source = &$this->_init_source( $dbcon );
        }

        $this->init($source);

    }

    function &_init_source( &$dbcon ){
        $listSource = &new $this->_source_object( $dbcon  );
        return $listSource->search( $this->_source_criteria, $this->_source_object );
    }

    function init(&$source) {
     
        $this->setSource( $source );
        $this->_setSort();
        $this->_activatePager( );
        $this->_prepareData();
        if (array_search( 'publish', $this->col_headers ) !== FALSE ) {
            $this->addTranslation( 'publish', '_showPublishStatus' );
        }
        $this->_initObservers( );
        $this->_after_init( );

    }

    function _after_init ( ){
        //interface
    }

    function setSource( &$source ){
        $this->source = &$source;
    }

    function _activatePager() {
        if ( !$this->_pager_active ) {
            $this->_afterPagerInit( );
            return false;
        }

        require_once( 'AMP/System/List/Pager.inc.php');
        $this->_pager = &new AMPSystem_ListPager( $this->source );
        
        if ( $this->_pager_limit ) $this->_pager->setLimit( $this->_pager_limit ); 
        $this->_afterPagerInit( );
    }

    function execute( ){
        return $this->output( );
    }


    function output() {

        if (!$this->_prepareData()) return $this->_noRecordsOutput( );

        $output = "";

        while ( $this->currentrow = $this->_getSourceRow()) {
           $output .= $this->_HTML_listRow ( $this->_translateRow($this->currentrow));
        
        }		
        return $this->_HTML_header() .
               $output .
               $this->_HTML_footer();

    }

    function _noRecordsOutput( ){

        return $this->newline( ) . $this->_HTML_addLink();
    }

    function _getSourceRow( ){
        // simple behavior for recordset map
        if ( !is_array( $this->source )) return $this->source->getData( );

        // more complex for arrays of objects

        if ( !isset( $this->_source_keys[$this->_source_counter ])) return false;

        $row_data = array( );
        $row_data_source = &$this->source[ $this->_source_keys[ $this->_source_counter ]];
        foreach( $this->col_headers as $column ){
            $row_data[$column] = $this->_getSourceDataItem( $column, $row_data_source );
        }
        if ( isset( $this->name_field ) && isset( $row_data[$this->name_field ])) {
            $row_data[$this->name_field ] = 
                    "<A HREF='". AMP_URL_AddVars( $this->editlink , "id=".$row_data['id'] ) ."' title='" . AMP_TEXT_EDIT_ITEM . "'>" 
                    . $row_data[ $this->name_field ]
                    . '</a>';

        }

        ++$this->_source_counter;
        return $row_data;
    }

    //for array objects only
    function _getSourceDataItem( $column, &$row_data_source ){
        if ( $column == 'id' ) return $row_data_source->id;
        if ( method_exists( $this, $column )) 
            return $this->$column( $row_data_source, $column );

        $get_method = 'get'.ucfirst( str_replace( ' ', '', $column ));
        if ( method_exists( $row_data_source, $get_method )) 
            return $row_data_source->$get_method();
        
        return false;
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

    function suppressHeader( $value = true ) {
        $this->suppress['header'] = $value;
    }

    function suppressEditColumn( $value=true ){
        $this->suppress['editcolumn'] = $value;
    }

    function suppressAddlink( $value = true ) {
        $this->suppress['addlink'] = $value ;
    }
    function suppressSortLinks( $value = true ) {
        $this->suppress['sortlinks'] = $value;
    }


    function getColor( $color_type ) {
        if (!isset($this->color[$color_type])) return "#000000";
        return $this->color[$color_type];
    }

    function setColor( $type, $color_id ) {
        $this->color[$type] = $color_id;
    }

    function setMessage( $text ) {
        if ( isset( $this->_controller )) return $this->_controller->setMessage( $text );

        require_once( 'AMP/System/Flash.php' );
        $flash = &AMP_System_Flash::instance( );
        $flash->add_message( $text );

        $this->message .= $text .'<BR>';
    }

    function applySearch( $values ){
        if ( !is_array( $this->source )) {
            return $this->source->applySearch( $values );
        }
        $listSource = &new $this->_source_object( $dbcon  );
        $this->_source_criteria = array_merge( $this->_source_criteria, $listSource->makeCriteria( $values ) );
        $this->init( $this->_init_source( AMP_Registry::getDbcon( ) ));
    }

    function setController( &$controller ){
        $this->_controller = &$controller;
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
        return  "<A HREF='". AMP_URL_AddVars( $this->editlink , "id=".$id ) ."' title='".AMP_TEXT_EDIT_ITEM."'>" .
                "<img src=\"". AMP_SYSTEM_ICON_EDIT ."\" alt=\"".AMP_TEXT_EDIT."\" width=\"16\" height=\"16\" border=0></A>" ;
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

        if (isset($this->suppress['header']) && $this->suppress['header']) return false;
        return "<h2>".str_replace("_", " ", $this->name)."</h2>";
    }

    function _HTML_editColumnHeader() {
        if (isset($this->suppress['editcolumn']) && $this->suppress['editcolumn']) return "";
        return "\n<td>&nbsp;</td>";
    }

    function _HTML_sortLink( $fieldname ) {
        if (isset($this->suppress['sortlinks']) && $this->suppress['sortlinks']) return "";
        $url_criteria = $this->_prepURLCriteria();
        $new_sort = $fieldname;
        if ($fieldname == $this->_sort ) {
            if ( !is_array( $this->source )) {
                $new_sort .= AMP_SORT_DESC;
            } elseif ( !isset( $_REQUEST['sort_direction']) || $_REQUEST['sort_direction'] != AMP_SORT_DESC ) {
                
                $url_criteria[] = "sort_direction=" . AMP_SORT_DESC;
            }
        }
        
        $url_criteria[] = "sort=".$new_sort;
        return AMP_Url_AddVars( $_SERVER['PHP_SELF'], $url_criteria );
    }

    function _HTML_columnHeaders() {
        $output = $this->_HTML_firstColumnHeader();

        foreach ($this->col_headers as $header=>$fieldname) {
            $link = $this->_HTML_sortLink( $fieldname );
            $output.= 
                "\n<td>". 
                $this->_HTML_bold(  
                    $this->_HTML_link( $link, $header, array( 'class' => $this->_css_class_columnheader ))
                    ) . "</td>";
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
        return  $this->_HTML_endList( )
                . $this->_HTML_addLink();
    }

    function _HTML_endList( ){
        return  "\n	</table>\n"
                . ( ($this->_pager_active && $this->_pager_display ) ? $this->_pager->execute() : false ) 
                . "</div>\n<br>&nbsp;&nbsp;";

    }

    function _HTML_addLink () {
        if (isset($this->suppress['addlink']) && $this->suppress['addlink']) return false;
        $add_url = isset( $this->_url_add ) ? $this->_url_add : $this->editlink;
        return "<a href=\"". $add_url ."\">". AMP_TEXT_ADD_ITEM . "</a> ";
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
            unset ($url_criteria_set['sort']);
            unset ($url_criteria_set['sort_direction']);
            $url_criteria_set['action'] = 'action=list';
            $this->_url_criteria = $url_criteria_set; 
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

    function _makePrettyDate( $value, $fieldname,$data  ){
        if ( !isset( $data[$fieldname])) return "";
        $result = date( 'M d, Y', $data[$fieldname]);

        if ( $result != 'Dec 31, 1969') return $result;
        return date( 'M d, Y', strtotime( $data[$fieldname]));
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
        if ( !$this->source ) return false;
        if ( is_array( $this->source )) return $this->_prepareArrayData( );
        if ($this->source->makeReady()) return true;
        $this->source->setSelect( $this->_defineFieldSet() );
        return $this->source->readData();
        
    }

    function _prepareArrayData( ){
        $this->_source_keys = array_keys( $this->source );
        $this->_source_counter = 0;
        return !( empty( $this->source ));
    }

    function _setSort() {
        //Sort the data
        if (!( isset($_REQUEST['sort']) && $_REQUEST['sort'])) return false; 
            
        //for recordset mapper
        if ( !is_array( $this->source)) {
            $this->_sort = $_REQUEST['sort'];
            return $this->source->addSort($_REQUEST['sort']);
        }

        //for arrays of objects
        $local_sort_method = '_setSort'.ucfirst( $_REQUEST['sort']);
        $sort_direction = ( isset( $_REQUEST['sort_direction']) && $_REQUEST['sort_direction']) ?
                            $_REQUEST['sort_direction'] : false;

        if ( method_exists( $this, $local_sort_method)) return $this->$local_sort_method( $sort_direction );

        $itemSource = &new $this->_source_object ( AMP_Registry::getDbcon( ));
        if( $itemSource->sort( $this->source, $_REQUEST['sort'], $sort_direction )){
            $this->_sort = $_REQUEST['sort'];
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

    function _afterPagerInit( ){
        //interface
    }

    function _initObservers( ){
        foreach( $this->_observers_source as $observer_class ){
            $observer = &new $observer_class( $this );
            $observer->attach( $this->source );
        }
    }

    function removeSourceItemId( $id ){
        if ( !is_array( $this->source )) return $this->source->readData( );
        foreach( $this->source as $sourceKey => $sourceItem ){
            if ( $sourceItem->id == $id ) unset( $this->source[$sourceKey]);
        }
    }
    
    function updateSourceItemId( $id ){
        if ( !is_array( $this->source )) return $this->source->readData( );
        if ( !isset( $this->source[ $id ])) return false;
        return $this->source[$id]->readData( $id );
    }

    function &_getRenderer( ){
        if ( isset( $this->_renderer )) return $this->_renderer;
        $this->_renderer = &new AMPDisplay_HTML;
        return $this->_renderer;
    }
    

}
?>

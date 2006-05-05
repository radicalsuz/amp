<?php

require_once( 'AMP/System/List.inc.php');
require_once( 'AMP/System/List/Toolbar.inc.php');
require_once( 'AMP/System/List/Request.inc.php');
require_once( 'AMP/System/List/Observer.inc.php');

class AMP_System_List_Form extends AMPSystem_List {
    var $formname = "System_List";

    var $_pager;
    var $_pager_active;
    var $_id_field = 'id';
    var $_controller;

    var $_actions = array( 'publish', 'unpublish', 'delete');
    var $_action_args = array( );
    var $_toolbar;
    var $_toolbar_class = 'AMP_System_List_Toolbar';
    var $_request;
    var $_request_class = 'AMP_System_List_Request';
    var $_submitGroup = 'submitAction';

    var $_source_keys;

    function AMP_System_List_Form( &$source ){
       $this->init( $source ) ;
    }

    function init( &$source ) {
     
        if ( !$source ) return;
        $this->setSource( $source );
        $this->_submitGroup .= $this->formname;

        $this->_initController( );
        $this->_initObservers( );
        $this->_initRequest( );
        $this->_initToolbar( );

        $this->_setSort();
        $this->_activatePager( );
        $this->_prepareData();
        if (array_search( 'publish', $this->col_headers ) !== FALSE ) {
            $this->addTranslation( 'publish', '_showPublishStatus' );
        }
        $this->_after_init( );
    }

    function _initRequest( ){
        $this->_request = &new $this->_request_class( $this->source );
        $this->_request->setSubmitGroup( $this->_submitGroup );
        $this->_attachActions( $this->_request );
        if ( !$this->_request->execute( )) return false;
        
        if ( $affected_qty = $this->_request->getAffectedQty( )) {
            $message = sprintf( AMP_TEXT_LIST_ACTION_SUCCESS, 
                                AMP_PastParticiple( ucfirst( $this->_request->getPerformedAction( ))), 
                                $affected_qty );
        } else {
            $message = sprintf( AMP_TEXT_LIST_ACTION_FAIL, 
                                AMP_PastParticiple( $this->_request->getPerformedAction( ))); 
        }
        $this->setMessage( $message );
        
    }

    function _initToolbar( ){
        $this->_toolbar = &new $this->_toolbar_class( $this );
        $this->_attachActions( $this->_toolbar );
        $this->_toolbar->setSubmitGroup( $this->_submitGroup );
    }

    function _initController( ){
        if ( class_exists( 'AMPSystem_Page')){
            $this->_controller =  &AMPSystem_Page::instance( );
        }
    }

    function _attachActions( &$target ){
        foreach( $this->_actions as $action ){
            $args = ( isset( $this->_action_args[$action] )) ?  $this->_action_args[$action] : null;
            $target->addAction( $action, $args ) ;
        }
        foreach( $this->_actions_global as $action ){
            $target->setActionGlobal( $action ) ;
        }
    }

    function getName( ){
        return $this->formname;
    }

    function _makeInput( $value, $fieldname, $currentrow ) {
        $id = $currentrow[ $this->_id_field ];
        return "<input style=\"text-align: right;\" name=\"$fieldname"."[$id]\" value=\"$value\" class=\"system_list_input\" type=\"text\" size=\"3\">";
    }

    /*
    function submitted() {
        $this->readRequest
        if ( !(isset( $_REQUEST['submitListAction'] ) && is_array($_REQUEST['submitListAction']))) return false;

        $action = key( $_REQUEST['submitListAction'] );
        if ($this->actions->isAction( $action )) return $action;

        return false;
    }
    */

    function _HTML_header() {
        //Starter HTML
        $start_html = $this->_HTML_listTitle() .
                      ( isset( $this->_pager ) ? $this->_pager->outputTop() : false ).
                      $this->_HTML_startForm() .
                      $this->_outputToolbar( );
        $start_html .= "\n<div class='list_table'>\n<table class='list_table'>\n<tr class='intitle'> ";

        return $start_html.$this->_HTML_columnHeaders();
    }

    function _outputToolbar( ){
        if ( !isset( $this->_toolbar)) return false;
        return $this->_toolbar->execute( );
    }

    function _HTML_footer() {
        return  "\n	</table>\n</div>"
                . $this->_outputToolbar( )
                . "</form>\n<br>&nbsp;&nbsp;" 
                . ( isset( $this->_pager ) ? $this->_pager->output() : false ) 
                . $this->_HTML_addLink()  ;
    }


    function _HTML_startForm() {
        return '<form name="' . $this->formname .'" method="POST" action="' . PHP_SELF_QUERY() ."\">\n";
    }

    function _HTML_startRow( $id ) {
        $bgcolor = $this->_setBgColor();
        $output ="\n<tr id=\"listform_row_$id\" bordercolor=\"".$this->getColor('border')."\" bgcolor=\"". $bgcolor."\""
                    ." onMouseover=\"this.bgColor='".$this->getColor('mouseover')."';\""
                    ." onMouseout=\"this.bgColor='". $bgcolor ."';\"" 
                    ." onClick='select_id(this.id.substring(13), \"". $this->formname ."\");'>\n";
        return $output . $this->_HTML_firstColumn( $id ) . $this->_HTML_editColumn( $id );
    }
        

    function _HTML_endColumnHeadersRow() {
        return $this->_HTML_extraColumnHeaders() . "\n</tr>";
    }

    function _HTML_firstColumn( $id ) {
        return $this->_HTML_selectColumn( $id );
    }

    function _HTML_firstColumnHeader() {
        return $this->_HTML_selectColumnHeader()
                . $this->_HTML_editColumnHeader() ;
    }

    function _HTML_selectColumnHeader() {
        if (isset($this->suppress['selectcolumn'])) return "";
        return '<td><center><a class="intitle" onclick=\'list_selectAll("'.$this->formname.'");\'>'.AMP_TEXT_ALL.'</a></center></td>';
    }

    function _HTML_selectColumn( $id ) {
        if (isset($this->suppress['selectcolumn'])) return "";
        return '<td><center><input type="checkbox" name="id[]" value="'.$id.'" onclick="this.checked=!this.checked;"><center></td>'; ;
    }

    function _HTML_editColumn( $id ) {
        if (isset($this->suppress['editcolumn'])) return "";
        return "\n<td nowrap><div align='center'>".
                $this->_HTML_editLink( $id )    ."&nbsp;\n" .
                $this->_HTML_previewLink( $id ) ."&nbsp;\n".
                $this->_HTML_deleteLink( $id )  ."</div></td>\n";
    }

    function _HTML_previewLink( $id ) {
        if ( !isset( $this->previewlink )) return false;
        return  '<a href="' . AMP_URL_AddVars( $this->previewlink , 'id='.$id) .'" target="_blank" title="'.AMP_TEXT_PREVIEW_ITEM.'">' .
                '<img src="' . AMP_SYSTEM_ICON_PREVIEW . '" width="16" height="16" border=0></a>';
    }

    function _HTML_deleteLink( $id ) {
        return false;
        /*
        return  '<a href="javascript: void();" onclick=\'if (confirmDelete() ) document.forms["' . $this->formname . '"].submit();\' title="Delete this Item">' .
                '<img src="' . AMP_SYSTEM_ICON_DELETE . '" width="16" height="16" border=0></a>';
                */
    }

    /*
    function _HTML_sortLink( $fieldname ) {
        if (isset($this->suppress['sortlinks']) && $this->suppress['sortlinks']) return "";
        $new_sort = $fieldname;
        $url_criteria = $this->_prepURLCriteria();
        $url_criteria[] = "sort=".$new_sort;
        if ($fieldname == $this->_sort ) $url_criteria[] = "sort_direction= DESC";
        return AMP_Url_AddVars( $_SERVER['PHP_SELF'], $url_criteria );
    }
    */
    

}
?>

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
    var $_actions_global = array( );
    var $_toolbar;
    var $_toolbar_class = 'AMP_System_List_Toolbar';
    var $_request;
    var $_request_class = 'AMP_System_List_Request';
    var $_submitGroup = 'submitAction';
    var $_css_class_elements = 'system_list_input';

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

        $this->_setSort( $this->source );
        $this->_activatePager( $this->source );
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
        if ( !$this->_request->execute( )) {
            return false;
        }
        
        if ( $affected_qty = $this->_request->getAffectedQty( )) {
            $message = sprintf( AMP_TEXT_LIST_ACTION_SUCCESS, 
                                ucfirst( AMP_PastParticiple(  $this->_request->getPerformedAction( ))), 
                                $affected_qty );
        } else {
            $message = sprintf( AMP_TEXT_LIST_ACTION_FAIL, 
                                AMP_PastParticiple( $this->_request->getPerformedAction( ))); 
        }
        $this->setMessage( $message, 'AMP_LIST_REQUEST_RESULT' );
        $this->_after_request( );
        
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
        $allowed_actions = $this->get_actions_allowed( );
        foreach( $allowed_actions as $action ){
            $args = ( isset( $this->_action_args[$action] )) ?  $this->_action_args[$action] : null;
            $target->addAction( $action, $args ) ;
        }
        foreach( $this->_actions_global as $action ){
            $target->setActionGlobal( $action ) ;
        }
    }

    function get_actions_allowed( ){
        $result = array( );
        $map = &ComponentLookup::instance( get_class( $this ));
        if ( !$map ) return $this->_actions;

        return array_filter( $this->_actions, array( $map, 'isAllowed') );
    }

    function getName( ){
        return $this->formname;
    }

    function _makeInput( $value, $fieldname, $currentrow ) {
        $id = $currentrow[ $this->_id_field ];
        return "<input name=\"$fieldname"."[$id]\" style=\"text-align: right;\" value=\"$value\" class=\"".$this->_css_class_elements."\" type=\"text\" size=\"3\">";
    }

    function _makeSelect ( $value, $fieldname, $currentrow, $values ) {
        $id = $currentrow[ $this->_id_field ];
        $select_name = $fieldname . "[$id]";
        $attr = array( 'class' => $this->_css_class_elements );
        $renderer = &$this->_getRenderer( );
        return AMP_buildSelect( $select_name, $values, $value, $renderer->makeAttributes( $attr ));
    }

    function _HTML_header() {
        //Starter HTML
        $start_html = $this->_HTML_searchForm( ) 
                      . $this->_HTML_listTitle() 
                      . ( isset( $this->_pager ) ? $this->_pager->outputTop() : false )
                      . $this->_HTML_startForm() 
                      . $this->_outputToolbar( )
                      . $this->_renderContainers( );


        return $start_html.$this->_HTML_columnHeaders();
    }

    function _outputToolbar( ){
        if ( isset( $this->suppress['toolbar']) && $this->suppress['toolbar'] ) return false;
        if ( !isset( $this->_toolbar)) return false;
        return $this->_toolbar->execute( );
    }

    function renderDelete( &$toolbar ){
        $renderer = AMP_get_renderer( );
        return "<input type='submit' name='". $toolbar->submitGroup ."[delete]' value='Delete' onclick='return confirmSubmit( \"".AMP_TEXT_LIST_CONFIRM_DELETE."\");'>\n" . $renderer->space( );

    }

    function _HTML_footer() {
        $output = "\n	</table>\n</div>"
                . $this->_outputToolbar( );

        if ( !( isset( $this->suppress['form_tag']) && $this->suppress['form_tag'])){
            $output .= "</form>\n";
        }
        $output .=  "<br>&nbsp;&nbsp;" 
                    . ( isset( $this->_pager ) ? $this->_pager->output() : false ) 
                    . $this->_HTML_addLink()  ;
        return $output;
    }


    function _HTML_startForm() {
        if ( isset( $this->suppress['form_tag']) && $this->suppress['form_tag']) return false;
        $url_value = PHP_SELF_QUERY( );
        if ( !strpos( $url_value, 'action')) {
            $url_value = AMP_URL_AddVars( $url_value, array( 'action=list'));

        }
        return '<form name="' . $this->formname .'" method="POST" action="' . $url_value ."\">\n";
    }

    function _HTML_startRow( $id ) {
        $bgcolor = $this->_setBgColor();
        $output ="\n<tr id=\"listform_row_$id\" bordercolor=\"".$this->getColor('border')."\" bgcolor=\"". $bgcolor."\""
                    ." onMouseover=\"this.bgColor='".$this->getColor('mouseover')."';\""
                    ." onMouseout=\"this.bgColor='". $bgcolor ."';\"" ;
        if ( !( isset( $this->suppress['selectcolumn']) && $this->suppress['selectcolumn'] )){
            $output .= " onClick='select_id(this.id.substring(13), \"". $this->formname ."\");'";
        }
        $output .= ">\n";

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
        if ( isset( $this->suppress['toolbar']) && $this->suppress['toolbar'] ) return false;
        if (isset($this->suppress['selectcolumn'])) return "";
        return '<td><center><a class="intitle" onclick=\'list_selectAll("'.$this->formname.'");\'>'.AMP_TEXT_ALL.'</a></center></td>';
    }

    function _HTML_selectColumn( $id ) {
        if ( isset( $this->suppress['toolbar']) && $this->suppress['toolbar'] ) return false;
        if (isset($this->suppress['selectcolumn'])) return "";
        return '<td><center><input type="checkbox" name="list_action_id[]" value="'.$id.'" onclick="this.checked=!this.checked;"><center></td>'; ;
    }

    function _HTML_editColumn( $id ) {
        if (isset($this->suppress['editcolumn'])) return "";
        return "\n<td nowrap><div align='center'>".
                $this->_HTML_editLink( $id )    ."&nbsp;\n" .
                $this->_HTML_previewLink( $id ) ."&nbsp;\n".
                $this->_HTML_deleteLink( $id )  
                ."</div></td>\n";
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

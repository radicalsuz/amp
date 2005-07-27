<?php

require_once ('AMP/Content/Article/List.inc.php' );
require_once ('AMP/Content/Article/Actions.inc.php' );
require_once ('AMP/System/List/Pager.inc.php' );

class Article_ListForm extends Article_List {

    var $col_headers = array(
        'ID' => 'id',
        'Title' => 'title',
        'Section' => 'type',
        'Date' => 'date',
        'Order' => 'pageorder',
        'Class' => 'class',
        'Status' => 'publish' );
    var $editlink = 'article_edit.php';
    var $previewlink = '/article.php?preview=1&id=';

    var $formname = "Article_List";
    var $pager;
    var $actions;

    function Article_ListForm ( &$dbcon ) {
        $source = &new ArticleSearch( $dbcon );
        $this->init( $source );
        $this->addTranslation( 'pageorder', '_makeInput' );
        $this->addTranslation( 'title', '_makeLink' );
        $this->actions = &new ArticleActions( $source );
        $this->actions->setSubmitGroup( 'submitListAction' ); 
        $this->pager = &new AMPSystem_ListPager( $source );
    }

    function _makeInput( $value, $fieldname, $currentrow ) {
        $id = $currentrow['id'];
        return "<input style=\"text-align: right;\" name=\"$fieldname"."[$id]\" value=\"$value\" class=\"articlelist_input\" type=\"text\" size=\"3\">";
    }

    function _makeLink ( $value, $fieldname, $currentrow ) {
        $id = $currentrow['id'];
        return "<a href=\"". $this->editlink . "?id=$id\">$value</a>";
    }

    function submitted() {
        if ( !(isset( $_REQUEST['submitListAction'] ) && is_array($_REQUEST['submitListAction']))) return false;

        $action = key( $_REQUEST['submitListAction'] );
        if ($this->actions->isAction( $action )) return $action;

        return false;
    }


    function doAction ($action) {
        $action_method = 'commit' . ucfirst( $action );
        if (!isset( $this->actions )) return false;

        if (method_exists( $this->actions, $action_method )) {
            $args = $this->actions->getRequestedValues( $action, $_REQUEST );
            $result = $this->actions->$action_method( $args );
            if ($result) $this->source->refreshData();
                
            return $result;
        }
        trigger_error ( $action . ' is not defined.' );
        return false;
    }

    function _HTML_header() {
        //Starter HTML
        $start_html = $this->_HTML_listTitle() .
                      $this->pager->output().
                      $this->_HTML_startForm() .
                      $this->actions->output() ;
        $start_html .= "\n<div class='list_table'>\n<table class='list_table'>\n<tr class='intitle'> ";

        return $start_html.$this->_HTML_columnHeaders();
    }

    function _HTML_footer() {
        return "\n	</table>\n</div></form>\n<br>&nbsp;&nbsp;" . $this->pager->output() . $this->_HTML_addLink()  ;
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
        return $output . $this->_HTML_firstColumn( $id );
    }
        

    function _HTML_endColumnHeadersRow() {
        return   $this->_HTML_editColumnHeader() 
               . $this->_HTML_extraColumnHeaders() . "\n</tr>";
    }

    function _HTML_firstColumn( $id ) {
        return $this->_HTML_selectColumn( $id );
    }

    function _HTML_firstColumnHeader() {
        return $this->_HTML_selectColumnHeader();
    }

    function _HTML_selectColumnHeader() {
        if (isset($this->suppress['editcolumn'])) return "";
        return '<td><center><a class="intitle" onclick=\'list_selectAll();\'>All</a></center></td>';
    }

    function _HTML_selectColumn( $id ) {
       return '<td><center><input type="checkbox" name="id[]" value="'.$id.'"><center></td>'; 
    }

    function _HTML_endRow( $id ) {
        return $this->_HTML_editColumn( $id ) . PARENT::_HTML_endRow( $id );
    }

    function _HTML_editColumn( $id ) {
        if (isset($this->suppress['editcolumn'])) return "";
        return "\n<td nowrap><div align='center'>".
                $this->_HTML_editLink( $id )    ."&nbsp;\n" .
                $this->_HTML_previewLink( $id ) ."&nbsp;\n".
                $this->_HTML_deleteLink( $id )  ."</div></td>\n";
    }

    function _HTML_previewLink( $id ) {
        return  '<a href="' . $this->previewlink . $id .'" target="_blank" title="Preview this Item">' .
                '<img src="' . AMP_SYSTEM_ICON_PREVIEW . '" width="16" height="16" border=0></a>';
    }

    function _HTML_deleteLink( $id ) {
        return  '<a href="javascript: void();" onclick=\'if (confirmDelete() ) document.forms["' . $this->formname . '"].submit();\' title="Delete this Item">' .
                '<img src="' . AMP_SYSTEM_ICON_DELETE . '" width="16" height="16" border=0></a>';
    }
}
?>

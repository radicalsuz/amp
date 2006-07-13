<?php

require_once( 'AMP/System/List/Form.inc.php' );
require_once( 'AMP/Content/Article/Version.inc.php' );

class Article_Version_List extends AMPSystem_List {
    var $name = "Version History";
    var $suppress = array( 'addlink' => true, 'sortlinks' => true, 'editcolumn' => true, 'messages' => true );
    var $col_headers = array( 
        ' ' => '_actionColumn',
        '#'       => 'id',
        'Updated' => '_updateColumn',
        'Title'   => 'name',
        'Action'  => '_renderRestoreLink'
    );

    var $_css_class_columnheader = 'list_column_header';
    var $editlink = 'article_version.php';
    var $_source_object = 'Article_Version';
    var $_sort = 'updated';
    var $_sort_direction = AMP_SORT_DESC;
    var $name_field = 'name';
    var $previewlink = AMP_CONTENT_URL_ARTICLE;
    var $_pager_target = AMP_SYSTEM_URL_ARTICLE_VERSION;
    var $_observers_source = array( 'AMP_System_List_Observer' );

    function Article_Version_List( $dbcon, $criteria = null ){
        $this->previewlink = AMP_SITE_URL . AMP_CONTENT_URL_ARTICLE;
        $this->init( $this->_init_source( $dbcon, $criteria ));
    }

    function _after_init_search( $criteria = null ){
        $this->_pager_target = AMP_Url_AddVars( AMP_SYSTEM_URL_ARTICLE_COMMENT, array( 'action=search' ));
        if ( !isset( $criteria )) return false;
        $article_id = ( isset( $criteria['article']) ? 
                            $criteria['article'] : false
                            );
        if ( $article_id){
            $this->_pager_target = AMP_Url_AddVars( $this->_pager_target, array( 'article=' . $article_id ));
        }
    }
/*
    function _getUrlEdit( $row_data ){
        return AMP_Url_AddVars( $this->editlink, "vid=".$row_data['id']);
    }
    */

    function _updateColumn( &$source, $fieldname ){
        $renderer = $this->_getRenderer( );
        $user_name = false;
        if ( $editor_id = $source->getLastEditorId( )){
            require_once( 'AMP/System/User/User.php');
            $user = &new AMPSystem_User( AMP_Registry::getDbcon( ), $editor_id );
            $user_name = $user->getName( );
            $user_name = ' ' . AMP_TEXT_BY . ' ' . $user_name;
        }
        return $source->getItemDateChanged( ) 
                . $user_name;
    }

    function _actionColumn( &$source, $fieldname ){
        $renderer = &$this->_getRenderer( );
        return  "\n<div align='center'>"
                . $this->_renderEditLink( $source->id, $source ) . $renderer->space( ) . "\n"
                . $this->_renderPreviewLink( $source->id, $source ) . $renderer->space( ) . "\n"
        //        . $this->_renderRestoreLink( $source->id, $source )
                . '</div>' . "\n";
    }

    function _renderEditLink( $id, &$source ) {
        return  "<A HREF='". $this->_getUrlEdit( array( 'id' => $id )) ."' title='".AMP_TEXT_EDIT_ITEM."' target='".$this->_getEditLinkTarget( )."'>" 
                . "<img src=\"". AMP_SYSTEM_ICON_EDIT ."\" alt=\"".AMP_TEXT_EDIT."\" width=\"16\" height=\"16\" border=0></A>" ;
    }

    function _renderPreviewLink( $id, &$source ){
        if ( !isset( $this->previewlink )) return false;
        return  '<a href="' . AMP_URL_AddVars( $this->previewlink , array( 'vid='.$id, 'preview=1', 'id='.$source->getArticleId( ))) .'" target="blank" title="'.AMP_TEXT_PREVIEW_ITEM.'">'
                . '<img src="' . AMP_SYSTEM_ICON_PREVIEW . '" width="16" height="16" border=0></a>';
    }

    function _renderRestoreLink( &$source, $fieldname ){
        $id = $source->id;
        return '<form name=\'article_version_restore_'.$id.'\' method="POST" action="' .AMP_SYSTEM_URL_ARTICLE . '">' . "\n"
                .'<input name="submitAction[restore]" type="submit" value="Restore"/ class="searchform_element" onclick="return confirmSubmit( \'This will overwrite the current version of this article!  Continue?\');">'
                .'<input name="action" type="hidden" value="restore">'
                .'<input name="id" type="hidden" value="'.$source->getArticleId( ).'">'
                .'<input name="vid" type="hidden" value="'.$id.'">'
                .'</form>';
    }

    function _noRecordsOutput( ){
        return false;
    }
}

?>

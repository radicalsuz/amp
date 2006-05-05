<?php

require_once( 'AMP/System/List/Form.inc.php');
require_once( 'AMP/Content/RSS/Article/Article.php');

class RSS_Article_List extends AMP_System_List_Form {
    var $name = "RSS_Article";
    var $col_headers = array( 
        'Source' => '_sourceLink',
        'Content' => '_contentBox',
        'Feed' => 'FeedNameText',
        'ID'    => 'id');
    var $editlink = 'rss_content.php';
    var $name_field = 'title';
    var $_source_object = 'RSS_Article';
    var $suppress = array( 'header' => true, 'editcolumn' => true, 'addlink' => true );
    var $_pager_active = true;
    var $_observers_source = array( 'AMP_System_List_Observer' );
    var $_url_add = 'rss_content.php?action=update';
    var $_actions = array( 'publish', 'delete');

    function RSS_Article_List( &$dbcon ) {
        $this->init( $this->_init_source( $dbcon ) );
    }

    function _sourceLink( &$source, $column_name ){
        $renderer = &$this->_getRenderer( );
        return $renderer->link( 
                    $source->getLinkURL( ),
                    $renderer->image( AMP_SYSTEM_ICON_PREVIEW, 
                                      array('alt'    => AMP_TEXT_VIEW_SOURCE, 
                                            'width'  => 16,
                                            'height' => 16,
                                            'align'  => 'left',
                                            'border' => 0)) . ' ' . AMP_TEXT_SOURCE,
                    array( 'target' => '_blank') );
    }

    function _contentBox( &$source, $column_name ){
        $renderer = &$this->_getRenderer( );
        return $renderer->bold( $source->getName( )) . $renderer->newline(2) . AMP_trimText( $source->getBody( ), 700, false);

    }

    function _setSort_contentBox( $sort_direction ){
        $itemSource = &new $this->_source_object ( AMP_Registry::getDbcon( ));
        if( $itemSource->sort( $this->source, 'title', $sort_direction )){
            $this->_sort = 'title';
        }
    }

}
?>

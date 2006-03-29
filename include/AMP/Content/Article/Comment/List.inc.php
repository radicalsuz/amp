<?php

require_once( 'AMP/System/List/Form.inc.php');
require_once( 'AMP/Content/Article/Comments.inc.php' );
require_once( 'AMP/Content/Article/Comment/ArticleComment.php' );
require_once( 'AMP/System/List/Observer.inc.php');

class ArticleComment_List extends AMP_System_List_Form {
    var $name = "Article Comment";
    var $col_headers = array( 
        'Comment' => 'name',
        'ID'    => 'id',
        'Date'    => 'date',
        'Status' => 'publish');
    var $editlink = 'comments.php';
    var $_source_object = 'ArticleComment';
    var $_sort = 'timestamp';
    var $_observers_source = array( 'AMP_System_List_Observer');

    function ArticleComment_List( &$dbcon ) {
        #$source = & new ArticleCommentSet( $dbcon );
        $this->addTranslation( 'date', '_makePrettyDate');
        $this->addTranslation( 'name', 'shortComment');
        $this->init( $this->_init_source($dbcon ));
    }

    function addCriteriaArticle( $articleid ){
        $this->source->addCriteriaArticle( $articleid );
    }

    function shortComment( $text, $fieldname, $row_data ){
        return AMP_trimText( $text, 70, false );
    }
}
?>

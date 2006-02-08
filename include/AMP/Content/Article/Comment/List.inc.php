<?php

require_once( 'AMP/System/List.inc.php');
require_once( 'AMP/Content/Article/Comments.inc.php' );

class ArticleComment_List extends AMPSystem_List {
    var $name = "Article Comment";
    var $col_headers = array( 
        'Comment' => 'comment',
        'ID'    => 'id',
        'Status' => 'publish');
    var $editlink = 'comments.php';

    function ArticleComment_List( &$dbcon ) {
        $source = & new ArticleCommentSet( $dbcon );
        $this->init( $source );
    }

    function addCriteriaArticle( $articleid ){
        $this->source->addCriteriaArticle( $articleid );
    }

    function shortComment( $text, $fieldname, $row_data ){
        return AMP_trimText( $text, 70, false );
    }
}
?>

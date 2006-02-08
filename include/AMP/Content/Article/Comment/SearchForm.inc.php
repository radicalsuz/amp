<?php

require_once( 'AMP/Form/SearchForm.inc.php');

class ArticleCommentSearch extends AMPSearchForm {
    var $_component_header = 'Search Comments';

    function ArticleCommentSearch( ){
        $name = "CommentSearch";
        $this->init( $name );
    }
}
?>

<?php

require_once( 'AMP/Form/SearchForm.inc.php');

class ArticleCommentSearch extends AMPSearchForm {
    var $_component_header = 'Search Comments';

    function ArticleCommentSearch( ){
        $name = "CommentSearch";
        $this->init( $name );
    }

    function getSearchValues(  ) {
        $values = parent::getSearchValues(  );
        //convert spam status to search spam field
        if( isset( $values['status'] ) && ( $values['status'] == 2)) {
            $values['spam'] = 1;
            unset( $values['status'] );
        }
        return $values;

    }
}
?>

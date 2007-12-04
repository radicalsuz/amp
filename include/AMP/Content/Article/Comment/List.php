<?php
require_once( 'AMP/Content/Article/Comment/ArticleComment.php' );
require_once( 'AMP/Display/System/List.php' );

class Comment_List extends AMP_Display_System_List {
    var $columns = array( 'select', 'controls', 'name', 'date', 'author', 'status', 'id' );
    var $column_headers = array( 
        'name' => 'Comment',
        );
    var $_source_object = 'ArticleComment';
    var $_pager_active = true;

    var $_sort_sql_default = 'status_datedesc';
    var $_sort_sql_translations = array( 'status_datedesc' => 'spam, publish, date DESC' );

    var $_source_criteria = array( 'allowed' => 1 );
    var $_actions = array( 'publish', 'unpublish', 'delete', AMP_TEXT_DESIGNATE_AS_SPAM, AMP_TEXT_DESIGNATE_AS_NOT_SPAM );

    function Comment_List( $source = null, $criteria = array(  ), $limit = null ) {
        $this->__construct( $source, $criteria, $limit );
    }

    function render_date( $source ) {
       $date = $source->getItemDate(  );
       if( !AMP_verifyDateValue( $date ) ) return false;
       return date( AMP_CONTENT_DATE_FORMAT, strtotime( $date ));
    }

    function render_name( $source ) {
        $body = $source->getBody(  );
        return AMP_trimText( $body, 70, false );
    }

    function render_edit( $source ) {
        if( !$this->_access_permitted( $source )) return false;

        return parent::render_edit( $source );
    }

    function render_select( $source ) {
        if( !$this->_access_permitted( $source )) return false;

        return parent::render_select( $source );
    }

    function _access_permitted( $source ) {
        $existing_articles = AMP_lookup( 'articles_existing' );
        if( !isset( $existing_articles[$source->getArticleId(  )] ) ) return true;

        $allowed_articles = AMP_lookup( 'articles' );
        if( !isset( $allowed_articles[$source->getArticleId(  )] ) ) return false;

        return true;
    }
}

?>

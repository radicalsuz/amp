<?php
require_once( 'AMP/Display/List.php');
require_once( 'AMP/Content/Article/Comment/ArticleComment.php');

class Article_Comment_Public_List extends AMP_Display_List {
    var $_source_object = 'ArticleComment';
    var $_suppress_messages = true;
    var $_source_criteria = array( 'displayable' => 1 );
    var $_sort_sql_default = 'default';
    var $_sort_sql_translations = array( 
        'default' => 'date'
    );

    function Article_Comment_Public_List( $source, $criteria = array( ), $limit = null ) {
        $source = null;
        $this->__construct( $source, $criteria, $limit );
    }

    function _renderItem( $source ) {
        return $this->render_byline( $source )
                . $this->render_body( $source );
    }

    function render_byline( $source ) {
        $author = $this->render_author( $source );
        $date = $this->render_date( $source );
        return $this->_renderer->div( sprintf( AMP_TEXT_POSTED_BY, $author, $date ), array( 'class' => 'comment_byline'));
    }

	function render_date( &$source ) {
		$date = $source->getItemDate();
		if (!$date) return 'a whim';

        return $this->_renderer->span( DoDate( $date, AMP_CONTENT_DATE_FORMAT), array( 'class' => AMP_CONTENT_CSS_CLASS_LIST_ARTICLE_DATE )) 
                . $this->_renderer->newline();
	}

    function render_author( $source ) {
		$author = $source->getAuthor();
        $author_url = $source->getAuthorURL( );
        if( !( AMP_validate_url( $author_url ))) $author_url = false;

        if (!trim($author)) $author = 'a stranger';
        return $this->_renderer->link( $author_url, converttext($author), array( 'class' => AMP_CONTENT_CSS_CLASS_ARTICLE_AUTHOR ));
    }

    function render_body( $source ) {
        if ( !( $body = $source->getBody( ))) return false;
        return $this->_renderer->p( $body, array( 'class' => AMP_CONTENT_CSS_CLASS_LIST_DESCRIPTION ));
    }

    function _renderItemContainer( $output, $source ) {
        return $this->_renderer->simple_li( $output, array( 'class' => $this->_css_class_container_list_item ));
    }

    function _renderBlock( $html ) {
        return $this->_renderer->tag( 'ol', $html, array( 'class' => 'comments_list', 'id' => 'comments_list'));
    }
        
}

?>

<?php

require_once ( 'AMP/System/Data/Set.inc.php' );
require_once ( 'AMP/Content/Article/Display.inc.php' );


class ArticleCommentSet extends AMPSystem_Data_Set {

    var $datatable = "comments";
    var $sort = array( 'date desc' );
    
    function ArticleCommentSet ( &$dbcon, $article_id=null  ) {
        $this->init( $dbcon );
        if (isset($article_id)) $this->setArticle( $article_id ); 
    }

    function setArticle( $article_id ) {
        $this->addCriteria( "articleid=".$article_id );
        $this->article_id = $article_id;
    }

    function readPublished() {
        $this->addCriteria( "publish=1" );
    }

    function execute( $show_all ) {
        if ($show_all) $this->dropCriteria( "publish=1" );
        $this->readData();
    }

    function display( $show_drafts = false ) {
        if (!$this->makeReady()) $this->execute( $show_drafts );
        $this->display = &new ArticleCommentSet_Display( $this );
        return $this->display->execute();
    }

    function getArticleId() {
        if (!isset($this->article_id)) return false;
        return $this->article_id;
    }
}

class ArticleCommentSet_Display extends AMPDisplay_HTML {

    function ArticleCommentSet_Display( &$comment_set ) {
        $this->comment_set = &$comment_set;
    }

    function execute() {
        $output = $this->_HTML_addCommentLink( $this->comment_set->getArticleId() );
        if (!$this->comment_set->makeReady()) return $output;

        while( $data = $this->comment_set->getData() ) {
            $output .=  $this->_HTML_title( $data['title'] .
                        $this->_HTML_p_commaJoin( 
                        array( 
                            $this->_HTML_author( $data['author'], $data['email'] ),
                            $this->_HTML_date( $data['date'] )) 
                        ) .
                        $this->_HTML_commentBody( $data['comment'] ) );
        }

        return $output;
    }

    function _HTML_addCommentLink ($article_id) {
	    return '<br><p>' . $this->_HTML_link( AMP_URL_AddVars( "comment.php", 'cid=' . $article_id ), "add a comment" ) ."</p>\n";
    }
    function _HTML_title( $title ) {
		return "<hr><p>" .$this->_HTML_bold( $title ). $this->_HTML_newline();
    }

    function _HTML_author( $author, $email ) {
        if ($email) $href = 'mailto:'.$email;
        return $this->_HTML_italics( 'by ' . $this->_HTML_link( $href, $author ));
    }
    function _HTML_date( $date ) {
        if (!$date) return false;
        return $this->_HTML_italics( DoDateTime( $date, "l, M jS, Y g:ia" ) );
    }

    function _HTML_commentBody( $comment ) {
        if (!$comment) return false;
        return '<P>' . converttext( $comment ) . "</P>\n";
    }
}
?>

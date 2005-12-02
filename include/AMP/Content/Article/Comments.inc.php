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
        $output = '<hr><p class="subtitle">Comments</p>';
        if (!$this->comment_set->makeReady()) return $output;
        
       
        $output .= '<ol>';
       
        while( $data = $this->comment_set->getData() ) {
                
            $output .=  $this->_HTML_comment( 
                        $this->_HTML_p_commaJoin( 
                        array( 
                            $this->_HTML_author( $data['author'], $data['website'] ),
                            $this->_HTML_date( $data['date'] )) 
                        ) .
                        $this->_HTML_commentBody( $data['comment'] ) );
        }
        
        $output .= '</ol>';
        $output .= $this->_HTML_addCommentLink( $this->comment_set->getArticleId() ).'  |  '. $this->_HTML_trackback();
        return $output;
    }

    function _HTML_addCommentLink ($article_id) {
	    return  $this->_RDF_trackbacks( $article_id ). 
                $this->_HTML_link( AMP_URL_AddVars( "comment.php", 'cid=' . $article_id ), "Add a Comment" ) ;
    }
    function _RDF_trackbacks( $article_id ){
        require_once( 'AMP/Content/Article.inc.php');
        $article = &new Article( $this->comment_set->dbcon, $article_id );
        return
            '<!--
            <rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
                     xmlns:dc="http://purl.org/dc/elements/1.1/"
                     xmlns:trackback="http://madskills.com/public/xml/rss/module/trackback/">

            <rdf:Description
                rdf:about="'.$article->getURL( ) .'"
                dc:identifier="'.$article->getURL( ).'"
                dc:title="'.$article->getTitle( ).'"
                trackback:ping="' . AMP_Url_AddVars( AMP_CONTENT_URL_TRACKBACKS, 'id='.$article_id) . '" />
            </rdf:RDF>
            -->';
    }
    function _HTML_comment( $comment ) {
		return "<li>" .$this->_HTML_bold( $comment ).'</li>'. $this->_HTML_newline();
    }

    function _HTML_author( $author, $website ) {
        if ($website ) $href = $website;
        return $this->_HTML_italics(  'Comment by '. $this->_HTML_link( $href, $author ) );
    }
    
    function _HTML_trackback() {
         $href = 'article_trackback.php?id='.$_GET['id'];
        return 'Trackback '.$this->_HTML_link( $href, 'URI');
    }
    
    function _HTML_date( $date ) {
        if (!$date) return false;
        return $this->_HTML_italics( DoDateTime( $date, " M jS, Y g:ia" ) );
    }

    function _HTML_commentBody( $comment ) {
        if (!$comment) return false;
        return '<P class="text">' . converttext( $comment ) . "</P>\n";
    }
}
?>

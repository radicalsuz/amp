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

    function addCriteriaArticle( $article_id ){
        return $this->setArticle( $article_id );
    }

    function addCriteriaCid( $article_id ){
        //retained for legacy compatibility
        return $this->addCriteriaArticle( $article_id );
    }

    function readPublished() {
        $this->addCriteria( "publish=1" );
        $this->addCriteria( "spam!=1" );
    }

    function execute( $show_all=false ) {
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
        $output = '<hr><p class="subtitle"><a name="comments"></a>Comments</p>';
        $output .= $this->_HTML_addCommentLink( $this->comment_set->getArticleId() );
        if ( AMP_CONTENT_TRACKBACKS_ENABLED ) {
            $output .= '  |  '. $this->_HTML_trackback($this->comment_set->getArticleId());
        }

        if (!$this->comment_set->makeReady()) return $output;
        
       
        $output .= '<ol>';
       
        while( $data = $this->comment_set->getData() ) {
                
            $output .=  $this->_HTML_comment( 
                        $this->_HTML_p_commaJoin( 
                        array( 
                            $this->_HTML_author( $data['author'], $data['author_url'] ),
                            $this->_HTML_date( $data['date'] )) 
                        ) .
                        $this->_HTML_commentBody( $data['comment'] ) );
        }
        
        $output .= '</ol>';
        return $output;
    }

    function _HTML_addCommentLink ($article_id) {
	    return  $this->_RDF_trackbacks( $article_id ). 
                $this->_HTML_link( AMP_URL_AddVars( "comment.php", 'articleid=' . $article_id ), AMP_TEXT_ADD_A_COMMENT ) ;
    }
    function _RDF_trackbacks( $article_id ){
        if ( !AMP_CONTENT_TRACKBACKS_ENABLED ) return false;
        require_once( 'AMP/Content/Article.inc.php');
        AMP_config_load( 'urls' );
        $article = &new Article( $this->comment_set->dbcon, $article_id );
        return
            '<!--
            <rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
                     xmlns:dc="http://purl.org/dc/elements/1.1/"
                     xmlns:trackback="http://madskills.com/public/xml/rss/module/trackback/">

            <rdf:Description
                rdf:about="'.AMP_SITE_URL.$article->getURL( ) .'"
                dc:identifier="'.AMP_SITE_URL.$article->getURL( ).'"
                dc:title="'.$article->getTitle( ).'"
                trackback:ping="' . AMP_Url_AddVars( AMP_SITE_URL.AMP_CONTENT_URL_TRACKBACKS, 'id='.$article_id) . '" />
            </rdf:RDF>
            -->';
    }
    function _HTML_comment( $comment ) {
		return "<li>" .$this->_HTML_bold( $comment ).'</li>'. $this->_HTML_newline();
    }

    function comment( $comment ) {
        return $this->_HTML_comment( $comment );
    }

    function _HTML_author( $author, $author_url = null ) {
        $href = (isset( $author_url ) && $author_url && AMP_validate_url( $author_url )) ? $author_url : false;
        return $this->_HTML_italics(  'Comment by '. $this->link( $href, $author, array( 'target' => 'blank' ) ) );
    }

    function author( $author, $author_url = null ) {
        return $this->_HTML_author( $author, $author_url );
    }
    
    function _HTML_trackback($id) {
         $href = 'article_trackback.php?id='.$id;
         return $this->trackback( $href );
    }

    function trackback( $url ){
        return 'Trackback '.$this->_HTML_link( $url, 'URI');
        
    }
    
    function _HTML_date( $date ) {
        if (!$date) return false;
        return $this->_HTML_italics( DoDateTime( $date, " M jS, Y g:ia" ) );
    }

    function date( $date ) {
        return $this->_HTML_date( $date );
    }

    function _HTML_commentBody( $comment ) {
        if (!$comment) return false;
        return '<P class="text">' . converttext( $comment ) . "</P>\n";
    }

    function commentBody( $comment ){
        return $this->_HTML_commentBody( $comment );
    }
}
?>

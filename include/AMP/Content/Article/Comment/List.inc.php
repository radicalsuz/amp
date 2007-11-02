<?php

require_once( 'AMP/System/List/Form.inc.php');
require_once( 'AMP/Content/Article/Comments.inc.php' );
require_once( 'AMP/Content/Article/Comment/ArticleComment.php' );
require_once( 'AMP/System/List/Observer.inc.php');

class ArticleComment_List extends AMP_System_List_Form {
    var $name = "Article Comment";
    var $col_headers = array( 
        'Comment' => 'name',
        'Date'    => 'date',
        'Author'    => 'author',
        'Status' => 'publish',
        'ID'    => 'id'
        );
    var $editlink = 'comments.php';
    var $_source_object = 'ArticleComment';
    var $_sort = 'timestamp';
    var $_observers_source = array( 'AMP_System_List_Observer');
    var $_pager_active = true;

    var $_sort_default = array( 'date DESC' );
    var $_sort_translations_sql = array( );

    var $_source_criteria = array( 'allowed' => 1 );

    function ArticleComment_List( &$dbcon, $criteria = null ) {
        $criteria['allowed'] = 1;
        $this->init( $this->_init_source($dbcon, $criteria ));
    }

    function _after_init( ){
        $this->addTranslation( 'date', '_makePrettyDate');
        $this->addTranslation( 'name', 'shortComment');
    }

    function addCriteriaArticle( $articleid ){
        $this->source->addCriteriaArticle( $articleid );
    }

    function shortComment( $text, $fieldname, $row_data ){
        return AMP_trimText( $text, 70, false );
    }

    function _after_init_search( $criteria = null ){
        $this->_url_add = AMP_Url_AddVars( AMP_SYSTEM_URL_ARTICLE_COMMENT, array( 'action=add' ));
        if ( !isset( $criteria )) return false;

        $article_id = ( isset( $criteria['article_id']) ? 
                            $criteria['article_id'] : false
                            );
        $userdata_id = ( isset( $criteria['userdata_id']) ? 
                            $criteria['userdata_id'] : false
                            );

        if ( $article_id){
            $this->_url_add = AMP_Url_AddVars( $this->_url_add, array( 'article_id=' . $article_id ));
        }
        if ( $userdata_id ){
            $this->_url_add = AMP_Url_AddVars( $this->_url_add, array( 'userdata_id=' . $userdata_id));
        }
    }

    function _noRecordsOutput( ){
        $this->_searchFailureNotice( );
        return parent::_noRecordsOutput( );
    }

    function _HTML_editColumn( $id ) {
        $source = new ArticleComment( AMP_Registry::getDbcon( ), $id );
        $article_id = $source->getArticle( );
        $allowed_articles = AMP_lookup( 'articles');
        $existing_articles = AMP_lookup( 'articles_existing' );
        if ( !isset( $allowed_articles[$article_id]) && isset( $existing_articles[ $article_id ])) {
            return "\n<td nowrap><div align='center'>".
                    "</div></td>\n";
        }
        return parent::_HTML_editColumn( $id );

    }


}
?>

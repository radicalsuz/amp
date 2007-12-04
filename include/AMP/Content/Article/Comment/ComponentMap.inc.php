<?php

require_once( 'AMP/System/ComponentMap.inc.php');

class ComponentMap_Article_Comment extends AMPSystem_ComponentMap {
    var $heading = "Comment";
    var $nav_name = "comments";
    var $_action_default = 'list';
    var $_allow_search = true; 

    var $paths = array( 
        'fields' => 'AMP/Content/Article/Comment/Fields.xml',
        #'list'   => 'AMP/Content/Article/Comment/List.inc.php',
        'list'   => 'AMP/Content/Article/Comment/List.php',
        'form'   => 'AMP/Content/Article/Comment/Form.inc.php',
        'search_fields'   => 'AMP/Content/Article/Comment/SearchFields.xml',
        'search'   => 'AMP/Content/Article/Comment/SearchForm.inc.php',
        'source' => 'AMP/Content/Article/Comment/ArticleComment.php');
    
    var $components = array( 
        'form'  => 'ArticleComment_Form',
        #'list'  => 'ArticleComment_List',
        'list'  => 'Comment_List',
        'search'   => 'ArticleCommentSearch',
        'source'=> 'ArticleComment');


    function onInitForm( &$controller ){

        if (!( $article_id = $controller->assert_var( 'article_id' ))) return false;
        $form = &$controller->get_form( );
        $form->setDefaultValue( 'articleid', $article_id );
    }

    function isAllowed( $action, $model_id = false ) {
        if ( $model_id ) {
            $allowed_articles = AMP_lookup( 'AllowedArticles');
            $articles = AMP_lookup( 'articles_existing');
            $model = $this->getComponent( 'source');
            $model->readData( $model_id );
            $article_id = $model->getArticle( );

            if ( isset( $articles[ $article_id ]) && !isset( $allowed_articles[ $article_id ])) {
                return false;
            }
        }
        return parent::isAllowed( $action, $model_id );

    }
}

?>

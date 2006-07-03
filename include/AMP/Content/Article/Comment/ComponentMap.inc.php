<?php

require_once( 'AMP/System/ComponentMap.inc.php');

class ComponentMap_Article_Comment extends AMPSystem_ComponentMap {
    var $heading = "Comment";
    var $nav_name = "comments";
    var $_action_default = 'list';
    var $_allow_search = true; 

    var $paths = array( 
        'fields' => 'AMP/Content/Article/Comment/Fields.xml',
        'list'   => 'AMP/Content/Article/Comment/List.inc.php',
        'form'   => 'AMP/Content/Article/Comment/Form.inc.php',
        'search_fields'   => 'AMP/Content/Article/Comment/SearchFields.xml',
        'search'   => 'AMP/Content/Article/Comment/SearchForm.inc.php',
        'source' => 'AMP/Content/Article/Comment/ArticleComment.php');
    
    var $components = array( 
        'form'  => 'ArticleComment_Form',
        'list'  => 'ArticleComment_List',
        'search'   => 'ArticleCommentSearch',
        'source'=> 'ArticleComment');


    function onInitForm( &$controller ){

        if (!( $article_id = $controller->assert_var( 'article_id' ))) return false;
        $form = &$controller->get_form( );
        $form->setDefaultValue( 'articleid', $article_id );
    }
}

?>

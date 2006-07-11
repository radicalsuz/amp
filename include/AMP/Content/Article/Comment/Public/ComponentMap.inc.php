<?php

require_once ( 'AMP/System/ComponentMap.inc.php' );
require_once ( 'AMP/BaseDB.php' );
require_once ( 'AMP/Content/Config.inc.php' );

class ComponentMap_Article_Comment_Public extends AMPSystem_ComponentMap {

    var $heading  = "Comment";
    var $nav_name = "content";

    var $_action_default  = 'add';
    var $_path_controller = 'AMP/Content/Article/Comment/Public/Controller.php';
    var $_component_controller = 'Article_Comment_Public_Controller';

    var $_public_page_id_input = AMP_CONTENT_PUBLICPAGE_ID_COMMENT_INPUT;

    var $paths = array(
        'fields' => 'AMP/Content/Article/Comment/Public/Fields.xml',
        'form'   => 'AMP/Content/Article/Comment/Public/Form.inc.php',
        'source' => 'AMP/Content/Article/Comment/ArticleComment.php',
        );

    var $components = array (
        'form' => 'Article_Comment_Public_Form',
        'source' => 'ArticleComment' 
        );

    function onInitForm( &$controller ){

        $form = &$controller->get_form( );
        $article_id = $controller->assert_var( 'articleid' );
        if ( !$article_id ) $article_id = $controller->assert_var( 'cid' );
        if ( $article_id ) $form->setValues( array( 'articleid' => $article_id ));

        $userdata_id = $controller->assert_var( 'userdata_id' );
        if ( $userdata_id ) $form->setValues( array( 'userdata_id' => $userdata_id )); 

    }

}
?>

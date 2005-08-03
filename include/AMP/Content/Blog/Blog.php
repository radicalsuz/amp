<?php

/**************
 *  Article_Blog
 *
 *  AMP 3.5.1
 *  2005-03-08
 *
 *  Author: david@radicaldesigns.org
 *
 *****/

#require_once ( 'AMP/System/Data/Item.inc.php' );
require_once ( 'AMP/Content/Article.inc.php' );

 #class AMPSystem_Blog extends AMPSystem_Data_Item {
 class Article_Blog extends Article {

    #var $datatable = "articles";
    #var $name_field = 'title';

    #function AMPSystem_Blog ( &$dbcon, $text_id=null ) {
    function Article_Blog ( &$dbcon, $article_id=null ) {
        $this->init( $dbcon, $article_id );
    }
    
	function setData( $data ) {
		$data['class'] = AMP_CONTENT_CLASS_BLOG;
		PARENT::setData($data);
	}

 }

?>

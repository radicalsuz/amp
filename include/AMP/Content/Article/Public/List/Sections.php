<?php
require_once( 'AMP/Content/Article/Public/List.php');

class Article_Public_List_Sections extends Article_Public_List {

    function Article_Public_List_Sections( $source = null, $criteria= array( ), $limit=null ) {
        $this->__construct( $source, $criteria, $limit );
    }

    function execute( ) {
        $section_list = new Section_Public_List( $this->_source_container, $this->_source_criteria );
        return $this->_renderer->div( 
                    $section_list->execute( ) 
                    . parent::execute( ),
                    array( 'class' => 'list_articles_sections'));
    }

}
?>

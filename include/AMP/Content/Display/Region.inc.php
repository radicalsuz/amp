<?php
require_once ('AMP/Content/Article/SetDisplay.inc.php');
require_once ('AMP/Content/Article/Display.inc.php');

class AMPContentDisplay_Region extends ArticleSet_Display  {

    function AMPContentDisplay_Region( &$region ) {
        $this->_region = &$region;
        $this->init( $this->initArticleSet( $region->dbcon ) );
    }

    function &initArticleSet( &$dbcon ) {
        $articleSet = &new ArticleSet( $region->dbcon );
        $articleSet->addCriteria( "state=".$region->id );
        $criteria = new AMPContent_DisplayCriteria();
        $criteria->clean( $articleSet );
        return $articleSet;
    }

    function execute() {
        if ($this->_source->RecordCount === 1) return $this->displayArticle();

        return $this->_HTML_listTitle( $this->_region->getName() ).
                PARENT::execute();
    }

    function _HTML_listTitle( $title ) {
        return $this->_HTML_in_P( $title, array( 'class' => 'title' ) );
    }

    function displayArticle() {
        $data = $this->_source->getData();
        $article = &new Article( $this->_region->dbcon, $data['id'] );
        $display = &new Article_Display( $article );
        return $display->execute();
    }

}
?>

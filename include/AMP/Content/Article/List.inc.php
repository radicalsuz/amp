<?php

require_once( 'AMP/System/List.inc.php' );
require_once( 'AMP/Content/Article/Search.inc.php' );

class Article_List extends AMPSystem_List {

    var $col_headers = array(
        'ID' => 'id',
        'Title' => 'title',
        'Section' => 'type',
        'date' => 'date',
        'Class' => 'class',
        'Status' => 'publish' );
    var $editlink = 'article_edit.php';

    var $null_date = "0000-00-00";

    function Article_List ( &$dbcon ) {
        $source = &new ArticleSearch( $dbcon );
        $this->init( $source );
    }

    function init( &$source ) {
        $this->addLookup( 'type', AMPContent_Lookup::instance( 'sections' ));
        $this->addLookup( 'class', AMPContent_Lookup::instance( 'class' ));
        $this->addTranslation( 'date', '_clearNullDate' );
        PARENT::init( $source );
        $this->suppressAddLink();
    }

    function _clearNullDate( $value, $fieldname = "date" ) {
        if ($value != $this->null_date ) return $value;
        return null;
    }

}
?>

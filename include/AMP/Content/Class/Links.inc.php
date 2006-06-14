<?php

require_once ('AMP/Content/Article/Set.inc.php');
require_once ('AMP/Content/Lookups.inc.php');
require_once ( 'AMP/Content/Display/HTML.inc.php');

class Class_Links {
    
    var $component_header = "View by Class";
    var $_renderer;

    function Class_Links () {
        $this->init();
    }

    function init() {
        $this->_renderer = &new AMPDisplay_HTML( );
    }

    function getComponentHeader() {
        return $this->component_header;
    }

    function output() {

        if (!($class_set = & AMPContent_Lookup::instance('activeClasses'))) return false;
        $output = "";

        foreach( $class_set as $id => $name ) {
            $output .= $this->_renderer->link(
                            AMP_Url_AddVars( AMP_SYSTEM_URL_ARTICLE, array( 'class' => 'class='.$id, 'AMPSearch' => 'AMPSearch=1')),
                            $name )
                        . $this->_renderer->newline( );
            
            //$output .= '<a href = "article_list.php?class='. $id . '">'. $name . "</a><BR>\n";
            //$output .= '<a href = "article_list.php?class='. $class_id . '">'. $class_set[ $class_id ] . "</a> ( ".  $class_count ." ) <BR>\n";
        }

        return $output;
    }

    function execute( ){
        return $this->output( );
    }
        

    function _countArticles() {
        $articleset = & new ArticleSet ( AMP_Registry::getDbcon() );
        if( !($counts = $articleset->getGroupedIndex( 'class' ))) return false;
        return $counts ;
    }
}
?>

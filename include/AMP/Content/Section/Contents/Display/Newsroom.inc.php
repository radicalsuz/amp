<?php

require_once( 'AMP/Content/Article/SetDisplay.inc.php' );
require_once( 'AMP/Content/Display/Criteria.inc.php' );

class SectionContentDisplay_Newsroom extends ArticleSet_Display {

    var $_subcategories = array( 
        AMP_CONTENT_CLASS_PRESSRELEASE,
        AMP_CONTENT_CLASS_NEWS
        );

    var $_pager_display = false;

    function SectionContentDisplay_Newsroom ( &$articleSet, $read_data = true ) {
        $this->init( $articleSet, $read_data );
    }

    function execute() {
        if (!$this->_source->makeReady()) return false;
        $listBody = "";
        foreach( $this->_subcategories as $current_class ) {
            
            #if( !($article_data = &$this->_source->filter( 'class', $current_class, $this->_pager->getLimit() ))) continue;
            #$articles  = &$this->_source->instantiateItems ( $article_data, $this->_sourceItem_class );
            
            $subsource = &$this->_getSubSource($current_class);
            if (!$subsource->hasData()) continue;
            $articles = &$subsource->instantiateItems( $subsource->getArray(), $this->_sourceItem_class );

            $listBody .=    
                $this->_HTML_subheader( $this->_getSubheading( $current_class ) ) .
                $this->_HTML_listing( $articles ) .
                $this->_HTML_moreLink( $this->_getMoreLinkHref( $current_class ), $this->_getClassName($current_class) ) .
                $this->_HTML_newline();

        }


        return $listBody;

    }

    function &_getSubSource( $current_class ) {
        $subsource = $this->_source;
        $subsource->addCriteria( "class=".$current_class );
        $subsource->readData();
        return $subsource;
    }

    function _getSubheading( $class_id ) {
        if (!($name  = $this->_getClassName( $class_id ))) return false;
        return 'Recent&nbsp;' . $name;
    }

    function _getClassName( $class_id ) {
        $classNames = AMPContent_Lookup::instance('class');
        if (!isset($classNames[ $class_id ] )) return false;
        return $classNames[ $class_id ];
    }

    function _getMoreLinkHref( $class_id ) {
        if ($this->_pager->getSubsetTotal( 'class', $class_id ) < $this->_pager->getLimit()) return false;

        return  AMP_URL_AddVars( 
            AMP_CONTENT_URL_LIST_CLASS, 
                array(  "class=$class_id",
                        "offset=".$this->_pager->getLimit()
            ) );
    }
    function _HTML_moreLink( $href, $name=false ) {

        $text = 'More&nbsp;' . $name . '&nbsp;' . $this->_HTML_bold( '&raquo;' );
        return $this->_HTML_inDiv( 
                    $this->_HTML_inSpan( $this->_HTML_link( $href, $text ), "standout" ),
                    array( 'class'=>'list_pager' )
                    );
    }

}
?>

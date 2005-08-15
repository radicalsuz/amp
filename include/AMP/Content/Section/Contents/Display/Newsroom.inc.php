<?php

require_once( 'AMP/Content/Display/List.inc.php' );

class SectionContentsDisplay_Newsroom extends AMPContent_DisplayList_HTML {

    var $_subcategories = array( 
        AMP_CONTENT_CLASS_PRESSRELEASE,
        AMP_CONTENT_CLASS_NEWS
        );

    var $_pager_display = false;

    function SectionContentsDisplay_Newsroom ( &$articleSet ) {
        $this->init( &$articleSet );
    }

    function execute() {
        if (!$this->_source->makeReady()) return false;
        $listBody = "";
        foreach( $this->_subcategories as $current_class ) {
            if( !($article_data = &$this->_source->filter( 'class', $current_class, $this->_pager->getLimit() ))) continue;
            $articles  = &$this->_source->instantiateItems ( $article_data, $this->_sourceItem_class );
            $listBody .=    
                $this->_HTML_subheader( $this->getSubheading( $current_class ) ) .
                $this->_HTML_listing( $articles ) .
                $this->_HTML_moreLink( $this->_getMoreLinkHref( $current_class ) ) ;
        }


        return $listBody;

    }

    function _getSubheading( $class_id ) {
        $classNames = AMPContent_Lookup::instance('class');
        if (!isset($classNames[ $class_id ] )) return false;
        return 'Recent&nbsp;' . $classNames[ $class_id ];
    }

    function _getMoreLinkHref( $class_id ) {
        if ($this->_pager->getSubsetTotal( 'class', $class_id ) < $this->_pager->getLimit()) return false;

        return  AMP_URL_AddVars( 
            AMP_CONTENT_URL_LIST_CLASS, 
                array(  "class=$class_id",
                        "offset=".$this->_pager->getLimit()
            ) );
    }

}
?>

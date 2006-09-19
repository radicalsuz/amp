<?php

require_once ('AMP/System/Data/Item.inc.php' );
require_once ('AMP/Content/Class/Display.inc.php' );
require_once ('AMP/Content/Display/Criteria.inc.php' );

class ContentClass extends AMPSystem_Data_Item {

    var $datatable = "class";
    var $name_field = "class";
    var $_contents_criteria = array();

    function ContentClass( &$dbcon, $id = null ) {
        $this->init( $dbcon, $id );
    }

    function getSection() {
        if($section = $this->getData( 'type' )) return $section;
        return AMP_CONTENT_MAP_ROOT_SECTION;
    }

    function &getContents() {
        if (isset($this->_contents)) return $this->_contents;

        $this->_contents = &new ArticleSet( $this->dbcon );
        $this->_contents->addCriteria( 'class='.$this->id );
        $this->_contents->setSort( array( 'date DESC', 'id DESC' ) );
        $this->_contents->addSort( 'if (( isnull( pageorder ) or pageorder="" or pageorder=0 ), ' 
                                 . AMP_CONTENT_LISTORDER_MAX . ', pageorder)' );
        foreach ($this->_contents_criteria as $criteria ) {
            $this->_contents->addCriteria( $criteria );
        }


        $criteria_set = &new AMPContent_DisplayCriteria();
        $criteria_set->allowClass( $this->id );
        $criteria_set->clean( $this->_contents );

        return $this->_contents;
    }

    function addContentsCriteria( $criteria ) {
        if ( array_search( $criteria, $this->_contents_criteria ) !== FALSE ) return true;
        $this->_contents_criteria[] = $criteria;
    }

    function addContentsCriteriaSection( $section_id ) {
        $contents = &$this->getContents( );
        $contents->addCriteriaSection( $section_id );
        /*
        $base_section = "type=".$section_id ;
        if (!($related_ids = $this->_getRelatedArticles( $section_id ))) return $this->addContentsCriteria( $base_section );

        return $this->addContentsCriteria( "( ". $base_section . ' OR ' . $related_ids . ")" );
        */
    }

    /*
    function _getRelatedArticles( $section_id = null) {
        require_once( 'AMP/Content/Section/RelatedSet.inc.php' );

        $related = &new SectionRelatedSet( $this->dbcon, $section_id );
        $relatedContent = &$related->getLookup( 'typeid' );
        if (empty( $relatedContent )) return false;

        return "id in (" . join( ", ", array_keys( $relatedContent) ). ")";
    }
    */

    function &getDisplay() {
        $classes = filterConstants( 'AMP_CONTENT_CLASS' );
        $display_def_constant= 'AMP_CONTENT_CLASSLIST_DISPLAY_' . array_search( $this->id , $classes );
        include_once( 'AMP/Content/Class/Display_Blog.inc.php');
        include_once( 'AMP/Content/Class/Display_FrontPage.inc.php');

        $display_class = AMP_CONTENT_CLASSLIST_DISPLAY_DEFAULT;
        if (defined( $display_def_constant )) $display_class = constant( $display_def_constant );

        if (!class_exists( $display_class )) $display_class = AMP_CONTENT_CLASSLIST_DISPLAY_DEFAULT;
        $result = &new $display_class( $this );
        return $result;
    }

    function display() {
        $display = &$this->getDisplay();
        return $display->execute();
    }

    function &getHeaderRef() {
        $result = false;
        if ($id = $this->getHeaderTextId() ) {
            $result = &new Article( $this->dbcon, $id );
        }
        return $result;
    }

    function getHeaderTextId() {
        if (!($id =  $this->getData( 'url' ))) return false;
        if ($id == AMP_CONTENT_MAP_ROOT_SECTION ) return false;
        return $id;
    }

    function getItemDate() {
        //interface
        return false;
    }

    function getTitle () {
        return $this->getName();
    }

    function getBlurb() {
        return $this->getData( 'description' );
    }

    function get_url_edit( ) {
        if ( !( isset( $this->id ) && $this->id )) return false;
        return AMP_Url_AddVars( AMP_SYSTEM_URL_CLASS, array( 'id=' . $this->id ) );
    }
}
?>

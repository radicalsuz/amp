<?php

require_once ( 'AMP/System/Data/Item.inc.php' );
require_once ( 'AMP/Content/Image.inc.php' );
require_once ( 'AMP/Content/Section/Contents/Manager.inc.php' );

class Section extends AMPSystem_Data_Item {

    var $datatable = "articletype";
    var $name_field = "type";
    var $_contents;
    var $_class_name = 'Section';
    var $_field_status = 'usenav';

    function Section( &$dbcon, $id = null ) {
        $this->init( $dbcon, $id );
    }

    function &getContents() {
        if (isset($this->_contents)) return $this->_contents;

        $this->_contents = &new SectionContents_Manager( $this );
        return $this->_contents;
    }

    function getCriteriaForContent() {
        $contentsource = & $this->getContents();
        return $contentsource->getSectionCriteria();
    }

    function &getDisplay() {
        $contents = &$this->getContents();
        return $contents->getDisplay();
    }

    function display() {
        $display = &$this->getDisplay();
        return $display->execute();
    }

    function &getHeaderRef() {
        if (!$this->getData( 'header' )) return false;
        if ($id = $this->getHeaderTextId() ) return new Article( $this->dbcon, $id );
        if (!($headers = &AMPContent_Lookup::instance( 'sectionHeaders' ))) return false;
        if (isset( $headers[ $this->id ] )) return new Article( $this->dbcon, $headers[ $this->id ] );
        return false;
    }
    function &getFooterRef() {
        if (!AMP_CONTENT_CLASS_SECTIONFOOTER) return false;
        if (!($footers = &AMPContent_Lookup::instance( 'sectionFooters' ))) return false;
        if (isset( $footers[ $this->id ] )) return new Article( $this->dbcon, $footers[ $this->id ] );
        return false;
    }

    function getHeaderTextId() {
        if (!$this->getData( 'header' )) return false;
        if (!($id =  $this->getData( 'url' ))) return false;
        if ( $id == 1 ) return false;
        return $id;
    }

    function showContentList() {
        return !($this->getData('usetype'));
    }

    function getParent() {
        return $this->getData( 'parent' );
    }

    function getSecured() {
        return $this->getData( 'secure' );
    }

    function getBlurb() {
        return $this->getData('description' );
    }

    function getSectionDate() {
        if (!($value = $this->getData( 'date2' ))) return false;
        if ($value == AMP_NULL_DATE_VALUE) return false;
        return $value;
    }

    function getItemDate() {
        return $this->getSectionDate();
    }

    function getTemplate() {
        return $this->getData( 'templateid' );
    }

    function getTitle() {
        //convenience alias
        return $this->getName();
    }

    function getURL() {
        if ($url = $this->getRedirect() ) return $url;
        if (!$this->id ) return false;
        return AMP_Url_AddVars( AMP_CONTENT_URL_LIST_SECTION, "type=".$this->id );
    }
    

    function getStylesheet() {
        return $this->getData( 'css' );
    }

    function getListItemLimit() {
        return $this->getData( 'up' );
    }

    function getListType() {
        return $this->getData( 'listtype');
    }

    function getRedirect() {
        if (!$this->getData('uselink')) return false;
        if (!( $target = $this->getData('linkurl'))) return false;
        return $target;
    }

    function getHeaderRedirect() {
        if (!($article = &$this->getHeaderRef())) return false;
        return $article->getRedirect();
    }

    function &getImageRef() {
        if (! ($img_path = $this->getImageFileName())) return false;
        $image = &new Content_Image( $img_path );
        return $image;
    }

    function getOrder( ){
        return $this->getData( 'textorder');
    }

    function getImageFileName() {
        return $this->getData( 'image2' );
    }


    function _sort_default( &$item_set ){
        require_once( 'AMP/Content/Map.inc.php');
        $map = &AMPContent_Map::instance( );
        $order = array_keys( $map->selectOptions( ));
        $item_set = array_combine_key( $order, $item_set );
    }

    function reorder( $new_order_value ){
        if ( $new_order_value == $this->getOrder( )) return false;
        $this->mergeData( array( 'textorder' => $new_order_value ));
        if ( !( $result = $this->save( ))) return false;
        $this->notify( 'update');
        $this->notify( 'reorder');
        return $result;

    }
    
    function getNavIndex( ){
        if ( isset( $this->_nav_index )) return $this->_nav_index;
        $navs_lists =       AMPContent_Lookup::instance( 'sectionListsNavigationCount');
        $navs_content =     AMPContent_Lookup::instance( 'sectionContentNavigationCount');
        $this->_nav_index = ( isset( $navs_content[ $this->id ]) ? $navs_content[$this->id] : 0 )
                     + ( isset( $navs_lists[ $this->id ]) ? $navs_lists[ $this->id ] : 0 );
        return $this->_nav_index;
    }

    function _afterSave( ){
        if ( $this->id != $this->dbcon->Insert_ID( )) return;

        require_once( 'AMP/Content/Article.inc.php');
        $section_header = &new Article( $this->dbcon );
        $article_data = array( 
            'title' =>  $this->getName( ),
            'body' => $this->getBlurb( ),
            'publish'   => true,
            'class'     => AMP_CONTENT_CLASS_SECTIONHEADER,
            'type'      => $this->id );
        $section_header->setData( $article_data );
        return $section_header->save( );
    }

}
?>

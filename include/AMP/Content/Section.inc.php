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
        $empty_value = false;
        if (!$this->getData( 'header' )) return $empty_value;
        if ($id = $this->getHeaderTextId() ) {
            $result = &new Article( $this->dbcon, $id );
            return $result;
        }

        if (!($headers = &AMPContent_Lookup::instance( 'sectionHeaders' ))) return $empty_value;

        if (isset( $headers[ $this->id ] )) {
            $result = &new Article( $this->dbcon, $headers[ $this->id ] );
            return $result;
        }
        return $empty_value;
    }

    function &getFooterRef() {
        $empty_value = false;
        if (!AMP_CONTENT_CLASS_SECTIONFOOTER) return $empty_value;
        if (!($footers = &AMPContent_Lookup::instance( 'sectionFooters' ))) return $empty_value;

        if (isset( $footers[ $this->id ] )) {
            $result = &new Article( $this->dbcon, $footers[ $this->id ] );
            return $result;
        }
        return $empty_value;
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
        if ( !$this->isColumn( 'date_display')) return $this->getSectionDate();
        if ( $this->getData( 'date_display' )) return  $this->getSectionDate();
        return false;
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
        return $this->getURL_default( );
    }

    function getURL_default( ) {
        return AMP_Url_AddVars( AMP_CONTENT_URL_LIST_SECTION, "type=".$this->id );
    }
    
    function getExistingAliases( ){
        if ( !isset( $this->id )) return false;
        require_once( 'AMP/Content/Redirect/Redirect.php' );
        $redirect = &new AMP_Content_Redirect( $this->dbcon );
        return $redirect->search( $redirect->makeCriteria( array( 'target' => $this->getURL_default( ))));
    }

    function getStylesheet() {
        return $this->getData( 'css' );
    }

    function getListItemLimit() {
        return $this->getData( 'up' );
    }

    function getListFilter( ){
        return $this->getData( 'filter' );
    }

    function getListType() {
        return $this->getData( 'listtype');
    }

    function getRedirect() {
        //if (!$this->getData('uselink')) return false;
        if (!( $target = $this->getData('linkurl'))) return false;
        return $target;
    }

    function getHeaderRedirect() {
        if (!($article = &$this->getHeaderRef())) return false;
        return $article->getRedirect();
    }

    function &getImageRef() {
        $empty_value = false;
        if (! ($img_path = $this->getImageFileName())) return $empty_value;
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
        #require_once( 'AMP/Content/Map.inc.php');
        #$map = &AMPContent_Map::instance( );
        if ( !( $order_base = AMP_lookup( 'sectionMap'))) return false;
        $order = array_keys( $order_base );
        $item_set = array_combine_key( $order, $item_set );
    }

    function reorder( $new_order_value ){
        if ( $new_order_value == $this->getOrder( )) return false;
        $this->setOrder( $new_order_value );
        if ( !( $result = $this->save( ))) return false;
        $this->notify( 'update' );
        $this->notify( 'reorder' );
        AMP_cacheFlush( AMP_CACHE_TOKEN_LOOKUP );
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
        $this->_create_section_header( );
        $this->_create_permission_values( );
    }

    function _create_permission_values( ) {
        $groups = AMP_lookup( 'permissionGroups');
        foreach( $groups as $group_id => $name ) {
            $allowed_sections_source = new AMPSystemLookup_SectionsByGroup( $group_id );
            $allowed_sections = $allowed_sections_source->dataset; //AMP_lookup( 'sectionsByGroup', $group_id );
            if ( !$allowed_sections ) {
                //all sections are allowed this group by default
                continue;
            }
            
            //if group has restrictions
            $parent_id = $this->getParent( );
            $current_user = AMP_current_user( );
            if ( $current_user && ( $current_user->getGroup( ) == $group_id )) {
                $allow_new_section = true;
            } elseif ( $parent_id == AMP_CONTENT_MAP_ROOT_SECTION ) {
                $map = AMPContent_Map::instance( );
                $siblings = $map->getChildren( AMP_CONTENT_MAP_ROOT_SECTION );
                $allowed_siblings = array_combine_key( $siblings, $allowed_sections );
                $allow_new_section = ( count( $siblings ) == count( $allowed_sections ));
            } else {
                $allow_new_section = isset( $allowed_sections[ $parent_id ]);
            }

            if ( $allow_new_section ) {
                require_once( 'AMP/System/Permission/Item/Item.php');
                AMP_System_Permission_Item::create_for_group( $group_id, 'access', 'section', $this->id );

            }

        }

        AMP_permission_update( );
    }

    function _create_section_header( ) {
        require_once( 'AMP/Content/Article.inc.php');
        $section_header = &new Article( $this->dbcon );
        $section_header->setDefaults( );
        $article_data = array( 
            'title' =>  $this->getName( ),
            'body' => $this->getBlurb( ),
            'publish'   => true,
            'class'     => AMP_CONTENT_CLASS_SECTIONHEADER,
            'section'      => $this->id );
        $section_header->setData( $article_data );
        return $section_header->save( );

    }

    function setBlurb( $blurb ){
        return $this->mergeData( array( 'description' => $blurb ));
    }

    function setName( $name ){
        return $this->mergeData( array( 'type' => $name ));
    }

    function setParent( $parent_id = AMP_CONTENT_MAP_ROOT_SECTION ) {
        return $this->mergeData( array( 'parent' => $parent_id ));
    }

    function setListType( $listtype = AMP_SECTIONLIST_ARTICLES ) {
        return $this->mergeData( array( 'listtype' => $listtype ));
    }

    function setOrder( $order ) {
        return $this->mergeData( array( 'textorder' => $order ));
    }


    function setDefaults( ) {
        $this->setListType( );
    }

    function move( $section_id ){
        if ( !( $section_id && $section_id != $this->getParent( ))) {
            return false ;
        }

        $this->setParent( $section_id );

        if ( !( $result = $this->save( ))) return false;
        $this->notify( 'update' );
        $this->notify( 'move' );
        AMP_cacheFlush( AMP_CACHE_TOKEN_LOOKUP );

        return $result;

    }

    function get_url_edit( ) {
        if ( !isset( $this->id )) return false;
        return AMP_URL_AddVars( AMP_SYSTEM_URL_SECTION, array( 'id=' . $this->id ));
    }


}
?>

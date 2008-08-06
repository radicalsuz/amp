<?php

require_once ('AMP/System/Data/Item.inc.php' );
require_once ('AMP/Content/Class/Display.inc.php' );
require_once ('AMP/Content/Display/Criteria.inc.php' );
require_once ( 'AMP/Content/Article.inc.php');

class ContentClass extends AMPSystem_Data_Item {

    var $datatable = "class";
    var $name_field = "class";
    
    // api v1 displays store sql fragments in here
    var $_contents_criteria = array();

    // for api v2 displays, criteria are stored as an array
    var $_display_criteria = array( );

    function ContentClass( &$dbcon, $id = null ) {
        $this->init( $dbcon, $id );
    }

    function getSection() {
        if($section = $this->getData( 'type' )) return $section;
        return AMP_CONTENT_MAP_ROOT_SECTION;
    }

    function &getContents() {
        if (isset($this->_contents)) return $this->_contents;

        $finder = new Article( $this->dbcon );
        $this->_contents = &new ArticleSet( $this->dbcon );
        $this->_contents->setSort( array( 'date DESC', 'id DESC' ) );
        if ( $this->id == AMP_CONTENT_CLASS_FRONTPAGE ) {
			$this->_contents->addCriteria( join( $finder->makeCriteria( array( 'class' => $this->id, 'live' => 1 )), " AND "));
            $this->_contents->addSort( 'if (( isnull( pageorder ) or pageorder="" or pageorder=0 ), ' 
                                     . AMP_SORT_MAX. ', pageorder)' );
        } else {
			$this->_contents->addCriteria( join( $finder->makeCriteria( array( 'class' => $this->id, 'displayable' => true )), " AND "));
		}
        
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

    function getDisplayCriteria( ) {
		if ($this->id == AMP_CONTENT_CLASS_FRONTPAGE ) {
			return array_merge( array( 'class' => $this->id ), $this->_display_criteria );
		} else {
			return array_merge( array( 'class' => $this->id , 'displayable' => true ), $this->_display_criteria );
		}
    }

    function getDisplayIntro( ) {
        if( !( $intro = $this->getHeaderRef( ))) {

            $intro = new Article( AMP_Registry::getDbcon( ));
            $intro->setDefaults( );
            $intro->mergeData(  array( 
                'publish'   => 1, 
                'title'     => $this->getName( ) . $this->getListNameSuffix( ), 
                'body'      => $this->getBlurb( ), 
                'class'     => AMP_CONTENT_CLASS_SECTIONHEADER 
                ));
        }
        return $intro->getDisplay( );
    }

    function addContentsCriteriaSection( $section_id ) {
        $contents = &$this->getContents( );
        $this->_display_criteria['section'] = $section_id ;
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
        include_once( 'AMP/Content/Class/Display_Blog.inc.php');
        include_once( 'AMP/Content/Class/Display_FrontPage.inc.php');

        $displays = AMP_lookup( 'class_displays');
        $display_class = isset( $displays[$this->id] ) ? $displays[ $this->id ] : AMP_CONTENT_CLASSLIST_DISPLAY_DEFAULT;
        if ( !class_exists( $display_class )) {
            trigger_error( sprintf( AMP_TEXT_ERROR_NOT_DEFINED, 'AMP', $display_class ));
            $display_class = AMP_CONTENT_CLASSLIST_DISPLAY_DEFAULT;
        }

        $display_class_vars = get_class_vars( $display_class );

        if (!isset( $display_class_vars['api_version'] ) || ( $display_class_vars['api_version'] == 1)) {
            $result = &new $display_class( $this );
        } elseif ($display_class_vars['api_version'] == 2 ) {
			$result = new $display_class( 
                                    $this,
                                    $this->getDisplayCriteria( ),
                                    $this->getListItemLimit( )
                                    );
        }
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

    function getListItemLimit() {
        return $this->getData( 'up' );
    }

    function get_image_banner( ) {
        return $this->getData( 'flash');
    }

    function getListNameSuffix( ) {
        return AMP_format_date_from_array( AMP_date_from_url( ));


    }

}
?>

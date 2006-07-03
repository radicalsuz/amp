<?php

define( 'AMP_NAV_TITLE_SQL_FIELD_FLAG', 'zzz' );
require_once( 'AMP/Content/Nav/EngineHTML.inc.php' );

class NavEngine_SQL extends NavEngine {

    var $_engine_type = "SQL";
    var $_sql_result;

    function NavEngine_SQL( &$nav ) {
        $this->init( $nav );
    }

    function execute() {
        if (!($sql = $this->nav->getSQL())) return false;

        return $this->_linksGetArray( $this->_runSQL( $sql ) );
    }

    ############################################
    ### public subcomponent creation methods ###
    ############################################

    function processTitle( $title ) {
        if (strpos($title, AMP_NAV_TITLE_SQL_FIELD_FLAG )!==0) return $title;

        $fieldname = substr($title, strlen(AMP_NAV_TITLE_SQL_FIELD_FLAG));
        if (!($items = $this->_runSQL())) return $fieldname;
        foreach( $items as $result_row ) {
            if (isset($result_row[ $fieldname ])) return $result_row[ $fieldname ];
        }
        return false;
    }

    function processMoreLink() {
        $currentPage = &AMPContent_Page::instance();
        if (!$pagename = $this->nav->getMoreLinkPage()) return false;
        $url_vars = array();
        if ($listType = $this->nav->getData('mcall1')) {
            $url_vars[] = "list=" . $listType;
            if ($listType == "classt" && ($section_id = $currentPage->getSectionId( ))) $url_vars[] = "type=" . $section_id;
        }

        if (!($result = $this->_runSQL())) return false;
        $item = current($result);
       
        if (($result_field = $this->nav->getData('mvar2')) && ($result_field_sql = $this->nav->getSecondLinkValue() )) {
           if ( is_numeric( $result_field_sql )) $url_vars[] = $result_field . '=' . $result_field_sql;
           elseif (isset( $item[ $result_field_sql ] )) $url_vars[] = $result_field . '=' . $item[ $result_field_sql ];
        }
	   
	    if (empty($url_vars)) return $pagename;
        return $pagename . '?' . join( '&', $url_vars );
    }

    ######################################
    ### private sql evaluation methods ###
    ######################################

    function _runSQL( $db_sql=null ) {
        if (isset($this->_sql_result)) return $this->_sql_result;
        if (!isset($db_sql)) $db_sql = $this->nav->getSQL();
        if (!$db_sql) return false;

        $sql = $this->_evalSQL( $db_sql );
        
        if ($limit = $this->_getLimit()) {
            $this->_countSqlItems( $sql );
            $sql .= $limit;
        }

        if (AMP_DISPLAYMODE_DEBUG_NAVS) AMP_DebugSQL( $sql, $this->nav->getName() );
        if ($result = $this->nav->dbcon->CacheGetArray( $sql )) {
            $this->_sql_result = $result;
            return $result;
        }
        if ($error = $this->nav->dbcon->ErrorMsg() ) {
            trigger_error( $this->nav->getName() . ' nav read failed: ' . $error );
        }
        return false;
    }

    function _countSqlItems( $sql ) {
        $start_crit = strpos(strtoupper($sql), "FROM " );
        if ($start_crit === FALSE) return;

        $count_sql = "SELECT count(*) as AMPqty " . substr( $sql, $start_crit );
        if (AMP_DISPLAYMODE_DEBUG_NAVS) AMP_DebugSQL( $count_sql, ($this->nav->getName() . " count"));
        if ($result = $this->nav->dbcon->CacheExecute( $count_sql )) {
            return $this->nav->setCount( $result->Fields( "AMPqty" ));
        }
        if ($error = $this->nav->dbcon->ErrorMsg() ) {
            trigger_error( $this->nav->getName() . ' nav count failed: ' . $error );
        }
        return false;
    }

    function _evalSQL( $sql ) {
        extract( $this->_getEvalVars());
        return stripslashes( eval( "return \"" .addslashes( $sql ) ."\";" ));
    }

    function _getEvalVars() {
        if (!($page = &AMPContent_Page::instance())) return array();
        $map = &AMPContent_Map::instance( );
        return array(
            'MM_parent' =>  $map->getParent( $page->getSectionId( )),
            'MM_type'   =>  $page->getSectionId( ),
            'MM_author' =>  ($article = &$page->getArticle() ? $article->getAuthor(): "" ),
            'MM_id'     =>  $page->getArticleId( ),
            'MX_top'    =>  AMP_CONTENT_MAP_ROOT_SECTION,
            'intro_id'  =>  $page->getIntroId()
            );
    }

    function _getLimit() {
        if ($limit = $this->nav->getLimit()) {
            if ($limit == AMP_NAV_NO_LIMIT) return false;
            return " LIMIT " . $limit;
        }
        return false;
    }


    #####################################
    ### private link creation methods ###
    #####################################

    function _linksGetArray( $item_set ) {
        if (empty($item_set)) return false;
        $links = array();

        foreach( $item_set as $item ) {
            $links[] = array( 'href'=> $this->_getLink( $item ), 'label' => $this->_getLinkText( $item ) );
        }

        if (empty( $links )) return false;
        return $links;
    }

    function _getLink( $item ) {
        if (!($pagename = $this->nav->getLinkPage())) return false;
        $pagename = $this->_getDynamicVar( $pagename, $item );
        if (!($url_var = $this->nav->getLinkVarName())) return $pagename;
        return AMP_Url_AddVars( $pagename ,  $url_var . '=' . $this->_getLinkVarValue( $item ));
    }

    function _getDynamicVar( $var, $item ) {
        if ( strpos( $var, AMP_NAV_TITLE_SQL_FIELD_FLAG ) === FALSE ) return $var;
        
        $sqlfield = str_replace( AMP_NAV_TITLE_SQL_FIELD_FLAG, "", $var ); 
        if (isset( $item[ $sqlfield ] ))  return $item[ $sqlfield ];
        return $var;
    }
        

    function _getLinkVarValue( $item ) {
        if ($varname = $this->nav->getData('mvar1val')) {
            if (isset($item[ $varname ])) return $item[ $varname ];
        }
        if ( isset( $item['id'])) return $item[ 'id' ];
        return false;
    }


    function _getLinkText( $item ) {
        $fieldname =  $this->nav->getLinkTextField();
        if (isset($item[ $fieldname ])) return $item[ $fieldname ];
        return "";
    }


}
?>

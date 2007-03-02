<?php
require_once ( 'AMP/Content/Page/Urls.inc.php' );
require_once ( 'AMP/Content/Config.inc.php');
require_once ( 'AMP/Content/Map.inc.php');

class AMP_Content_Map_Complete extends AMPContent_Map {

    var $_no_permissions = true;

    function AMP_Content_Map_Complete( &$dbcon, $top = AMP_CONTENT_MAP_ROOT_SECTION ) {
        $this->init( $dbcon, $top );
    }
    function buildMap() {
        $sql = "Select " . join(", ", $this->fields ) 
                ." from articletype "
                ." where id != ".AMP_CONTENT_SECTION_ID_TOOL_PAGES
                ." order by " . $this->getParentFieldSql( ) . ", textorder, type";
        if ( AMP_DISPLAYMODE_DEBUG ) AMP_debugSQL( $sql, 'content_map');
        $this->dataset = &$this->dbcon->CacheGetAssoc( $sql );
        //$this->childset = &AMPContent_Lookup::instance( 'sectionParents' );
        $set_start = new AMPContentLookup_SectionParents( );
        $this->childset = $set_start->dataset;
        //$this->childset = AMP_lookup( 'sectionParents' );
        //AMP_varDump( $this->childset );

        //$this->top = $this->find_top( $this->top );
        if ( $this->find_top( $this->top ) != $this->top ) {
            foreach( $this->top_set as $topchild ) {
                $this->childset[ $topchild ] = $this->top ;
                $this->dataset[$topchild]['parent'] = $this->top;
            }
        }
        
        $this->buildLevel( $this->top );
    }

    function &instance( ) {

        $content_map_complete = new AMP_Content_Map_Complete( AMP_Registry::getDbcon() );
        return $content_map_complete;

    }
}
?>

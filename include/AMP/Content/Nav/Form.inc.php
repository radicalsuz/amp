<?php

require_once( 'AMP/System/Form/XML.inc.php');
require_once( 'AMP/Content/Nav/ComponentMap.inc.php');

class Nav_Form extends AMPSystem_Form_XML {

    var $name_field = 'name';
    var $_sql_basic = 'Select id, title from articles where publish = 1 AND uselink=1 AND ';
    var $_sql_sort = ' ORDER BY date DESC';

    function Nav_Form( ) {
        $name = 'NavForm';
        $this->init( $name );
    }

    function setDynamicValues( ){
        $this->addTranslation( 'sql', '_checkCriteriaSelects', 'get');
    }

    function _checkCriteriaSelects( $data, $fieldname ) {
        if ( $data[ $fieldname ]) return $data[ $fieldname ];

        $criteria_values = array( );
        if ( isset( $data['section']) && $data['section']) $criteria_values[] = $this->_getRelatedContent( $data['section'] );
        if ( isset( $data['class']) && $data['class']) $criteria_values[] = 'class='.$data['class'];
        if ( empty( $criteria_values )) return false;
        return $this->_sql_basic . join( ' AND ', $criteria_values ) . $this->_sql_sort ;
    }

    function _getRelatedContent( $section_id ){
        $this->_sql_basic = 
            "SELECT distinct a.title, a.id, a.type as typeid, a.linkover, a.link, a.linktext, t.type  "
            . "FROM articles a, articletype t Left Join articlereltype   on articleid = a.id  "
            . "WHERE a.publish=1 and a.uselink=1 and ";
        $this->_sql_sort = 'order by a.date desc';

        return "( a.type =$section_id  or typeid = $section_id )  and  t.id=$section_id and "
            . " ( a.class!=".AMP_CONTENT_CLASS_FRONTPAGE." AND a.class!=".AMP_CONTENT_CLASS_SECTIONHEADER.") ";
    }
}
?>

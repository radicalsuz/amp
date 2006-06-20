<?php

require_once( 'AMP/System/Data/Item.inc.php');

class AMP_Content_Redirect extends AMPSystem_Data_Item {

    var $datatable = "redirect";
    var $name_field = "old";
    var $_class_name = 'AMP_Content_Redirect';

    function AMP_Content_Redirect ( &$dbcon, $id = null ) {
        $this->init( $dbcon, $id );
    }

    function getAlias( ){
        return $this->getData( 'old');
    }

    function getTarget( ){
        return $this->getData( 'new');
    }

    function getPublish( ){
        return $this->getData( 'publish');
    }

    function isConditional( ){
        return $this->getData( 'conditional' );
    }

    function getSuffix( $alias_url ){
        if ( !( $prefix_chars = $this->getData( 'num' ))) return false;
        return substr( $alias_url, $prefix_chars );
    }

    function setAlias( $alias_url ){
        return $this->mergeData( array( 'old' => $alias_url ));
    }

    function setTarget( $url ){
        return $this->mergeData( array( 'new' => $url ));
    }

    function setDefaults( ){
        return $this->mergeData( array( 'publish' => 1 ));
    }

    function makeCriteriaAlias( $alias ){
        return 'old =' .$this->dbcon->qstr( $alias );
    }

    function makeCriteriaConditional_alias( $alias ){
        return $this->dbcon->qstr( $alias ) . ' LIKE CONCAT( old, "%") AND conditional = 1';
    }

    function makeCriteriaStatus( $status_value ){
        return $this->_field_status . '=' . $this->dbcon->qstr( $status_value );
    }

    function makeCriteriaTarget( $target ){
        return 'new =' .$this->dbcon->qstr( $target );
    }

    function assembleTargetUrl( $requested_url=null ){
        $target = $this->getTarget( );
        if ( !$target ) return false;
        if ( substr( $target, 0 , 4 ) != 'http'){
            $target = AMP_SITE_URL . $target;
        }
        if ( !isset( $requested_url )) return $target;

        if ( $suffix = $this->getSuffix( $requested_url )) {
            $target = AMP_Url_AddVars( $target, $suffix );
        }
        return $target;
    }
}

?>

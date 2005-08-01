<?php

require_once ( 'AMP/System/Data/Item.inc.php' );

class Article extends AMPSystem_Data_Item {

    var $datatable = "articles";
    var $name_field = "title";

    function Article( &$dbcon, $id = null ) {
        $this->init ($dbcon, $id);
    }

    function getParent() {
        return $this->getData( 'type' );
    }

    function getSection() {
        return $this->getParent();
    }

    function getClass() {
        return $this->getData( 'class' );
    }

    function getTitle() {
        return $this->getData( 'title' );
    }

    function getAuthor() {
        return $this->getData( 'author' );
    }

    function getBlurb() {
        return $this->getData( 'shortdesc' );
    }

    function getRedirect() {
        if (!$this->getData( 'linkover' )) return false;
        if (! ($target = $this->getData( 'link' ))) return false;
        return $target;
    }
}
?>

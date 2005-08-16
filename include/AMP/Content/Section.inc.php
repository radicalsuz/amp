<?php

require_once ( 'AMP/System/Data/Item.inc.php' );
require_once ( 'AMP/Content/Image.inc.php' );
require_once ( 'AMP/Content/Section/Contents/Manager.inc.php' );

class Section extends AMPSystem_Data_Item {

    var $datatable = "articletype";
    var $name_field = "type";
    var $_contents;

    function Section( &$dbcon, $id = null ) {
        $this->init( $dbcon, $id );
    }

    function &getContents() {
        if (isset($this->_contents)) return $this->_contents;

        $this->_contents = &new SectionContents_Manager( $this );
        return $this->_contents;
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
        if (isset($headers[ $this->id ])) return new Article( $this->dbcon, $headers[ $this->id ] );
        return false;
    }

    function getHeaderTextId() {
        if (!$this->getData( 'header' )) return false;
        if (!($id =  $this->getData( 'url' ))) return false;
        if ($id === 1) return false;
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

    function &getImageRef() {
        if (! ($img_path = $this->getImageFileName())) return false;
        $image = &new Content_Image( $img_path );
        return $image;
    }

    function getImageFileName() {
        return $this->getData( 'image2' );
    }


    function isLive() {
        return ($this->getData('usenav')==AMP_CONTENT_STATUS_LIVE);
    }


}
?>

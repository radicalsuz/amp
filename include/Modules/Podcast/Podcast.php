<?php

require_once ( 'AMP/System/Data/Item.inc.php' );
require_once ( 'Modules/Podcast/Item/Set.inc.php' );

class AMPSystem_Podcast extends AMPSystem_Data_Item {


    var $datatable = "podcast";
    var $name_field = 'name';

    function AMPSystem_Podcast ( &$dbcon, $id=null ) {
        $this->init( $dbcon, $id );
    }

}

//unnecessary middleman
class Podcast extends AMPSystem_Podcast {

	var $_items = null;
	var $_lastBuildDate = null;

    function Podcast( &$dbcon, $id = null ) {
        $this->init( $dbcon, $id );
    }

    function convert_time($base) {
        $base = trim($base);
        $sec = substr($base, -2 ) ;
        $min = (60 * substr($base, 0,-3 ) );
        $time = $min + $sec ;
       return $time;
    }

	function getItems() {
		if(!isset($this->_items)) {
			$this->_items =& new PodcastItemSet($this->dbcon, $this->id);
			$this->_items->readData();
		}
		return $this->_items;
	}

	function lastBuildDate() {
		if(!isset($this->_lastBuildDate)) {
			$this->_lastBuildDate = $this->dbcon->Execute(
				'SELECT MAX(`last_modified`) FROM podcast_items WHERE podcast='.$this->id);
		}
		return $this->_lastBuildDate;
	}
}

?>

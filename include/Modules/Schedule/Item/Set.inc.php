<?php

require_once ("AMP/System/Data/Set.inc.php");
require_once ("Modules/Schedule/Item.inc.php");

class ScheduleItemSet extends AMPSystem_Data_Set {

	var $datatable = 'scheduleitems';

	function ScheduleItemSet( &$dbcon, $schedule_id=null ) {
		$this->init($dbcon);
		if(isset($schedule_id)) {
			$this->addCriteria("schedule_id = $schedule_id");
		}
	}

    function getItemsByOwner( $owner_id ) {
        $this->addCriteria( "owner_id = ".$owner_id );
        if ($this->readData()) $this->_owner = $owner_id;
//		$this->buildSchedule();
    }

	function getOpenItems() {
		return $this->getItemsByStatus(AMP_SCHEDULE_STATUS_OPEN);
	}

	function getItemsByStatus($status) {
		$items = array();
		if (!$this->makeReady()) return false;

		while($item = $this->getData() ) {
			if (!$item['status'] == $status) continue;
            $id = $item[ 'id' ];
			$items[ $id ] = &new ScheduleItem($this->dbcon);
			$items[ $id ]->setData($item);
		}
		return $items;
	}

    function &getItem( $item_id ) {
        return new ScheduleItem( $this->dbcon, $item_id );
    }
}
?>

<?php

require_once ("AMP/System/Data/Item.inc.php");

class CalendarFeeds extends AMPSystem_Data_Item {

	var $datatable = "px_feeds";
	var $types;

	function CalendarFeeds( &$dbcon, $id = null) {
		$this->init( $dbcon, $id );
		$this->types = AMPSystem_Lookup::instance('EventTypes');
	}

	/*
	fetch rss from url
	save to px_feeds and px_items
	save event info to calendar table, tag it as a syndicated event
	make sure it gets updated/deleted when the px_item it's associated with does?

in px_feeds - service=Calendar
in px_items where feed_id in px_feeds where service=Calendar and link = calendar.url
	*/
	function save() {
//		global $FOF_FEED_TABLE;
		$data = $this->getData();
		$url = $data['url'];
//		fof_add_feed($data['url']);
		$rss = fetch_rss($url);

/*
foreach($rss->items as $item) {
print "<pre>";
var_dump($item['ev']);
print "</pre>";
}
*/
		$num_events = 0;
		foreach($rss->items as $item) {
			$event = $item['ev'];
			if(!$event) continue;
			$num_events++;

			$typemap = array_flip($this->types);
			$type = $typemap[$event['type']];
			if(!$type) $type = $typemap['Other'];
			$calendar = array('event'=>$item['title'],
							  'shortdesc'=>$item['description'],
							  'url'=>$item['link'],
							  'contact1'=>$event['organizer'],
							  'location'=>$event['location'],
							  'date'=>$event['startdate'],
							  'enddate'=>$event['enddate'],
							  'type'=>$type);
			$result = $this->dbcon->Replace( 'calendar', $calendar, 'url', true );
		}

		if(!$num_events) {
			$this->calendar->error="Feed did not contain event information!";
			return false;
		}

		$id = $data['id']?$data['id']:'';
		$feed = array('id'=>$id,'url'=>$url,'title'=>$rss->channel['title'],
											 'link' =>$rss->channel['link'],
											 'description'=>$rss->channel['description'],
											 'service'=>'Calendar');
		$result = $this->dbcon->Replace( 'px_feeds', $feed, 'id', true );
		if ($result == ADODB_REPLACE_INSERTED ) $this->id = $this->dbcon->Insert_ID();

		fof_update_feed($url, 0);

		return true;
	}

	function readData( $id ) {
		$feed = fof_feed_row( $id );
		if ($feed) {
			$this->setData( $feed );
			$this->items = fof_get_items( $id );
		}
	}

	function deleteData( $id ) {
		global $FOF_FEED_TABLE, $FOF_ITEM_TABLE;
		$result = $this->dbcon->Execute("DELETE from calendar WHERE url IN (SELECT * FROM $FOF_ITEM_TABLE WHERE feed_id = $id");
		$result = fof_do_query("delete from $FOF_FEED_TABLE where id = $id");
		$result = fof_do_query("delete from $FOF_ITEM_TABLE where feed_id = $id") || $result;
		return $result;
	}
}
?>

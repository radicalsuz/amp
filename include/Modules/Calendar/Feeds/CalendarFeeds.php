<?php

require_once ("AMP/System/Data/Item.inc.php");

class CalendarFeeds extends AMPSystem_Data_Item {

//	var $datatable = "px_feeds";
	var $datatable = "calendar_feeds";
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

fof_add_feed, then rss_fetch, cause it'll be cached, right?  how to test this...
	*/
	function save() {
		$data = $this->getData();
		$url = $data['url'];
		if(substr($url, 0, 7) != 'http://' && substr($url, 0, 8) != 'https://')
		{
			$url = 'http://' . $url;
		}

		$rss = fetch_rss($url);
		if(!$rss->channel && !$rss->items) {
			$this->calendar->error="URL is not RSS or is invalid";
			return false;
		}

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
							  'typeid'=>$type);

			$result = $this->dbcon->Replace( 'calendar', $calendar, 'url', true );
/* ok, so Replace doesn't do what it seems to say it does in the docs, so this
   is how i think it should work, at least for this application.  stole this
   straight outta adodb-lib.inc.php */

/*
			$first = true;
			foreach($calendar as $key => $value) {
				if (!is_numeric($value) and strncmp($value,"'",1) !== 0 and strcasecmp($value,'null')!=0) {
					$value = $this->dbcon->qstr($value);
					$calendar[$key] = $value;
            }
				if ($first) {
					$first = false;
					$uSet = "$key=$value";
				} else
					$uSet .= ",$key=$value";
			}
				
			$update = "UPDATE calendar SET $uSet WHERE url=$calendar[url]";
print "about to execute $update<br/>";
			$rs = $this->dbcon->Execute($update);
var_dump($rs);
print $this->dbcon->ErrorMsg();

			if($rs) continue;
print "update failed, let's try an insert<br/>";

			$first = true;
			foreach($calendar as $key => $value) {
				if($first) {
					$first = false;
					$iCols = "$key";
					$iVals = "$value";
				} else {
					$iCols .= ",$key";
					$iVals .= ",$value";
				}
			}
			$insert = "INSERT INTO calendar ($iCols) VALUES ($iVals)";
print "about to execute $insert<br/>";
			$rs = $this->dbcon->Execute($insert);
print $this->dbcon->ErrorMsg();
*/
		}

		if(!$num_events) {
			$this->calendar->error="Feed did not contain event information!";
			return false;
		}

		$id = $data['id']?$data['id']:'';
		$feed = array('id'=>$id,'url'=>$url,'title'=>$rss->channel['title'],
											 'link' =>$rss->channel['link'],
											 'description'=>$rss->channel['description']);
		$result = $this->dbcon->Replace( 'calendar_feeds', $feed, 'id', true );
		if ($result == ADODB_REPLACE_INSERTED ) $this->id = $this->dbcon->Insert_ID();

//		fof_update_feed($url, 0);

		return true;
	}

/*
	function readData( $id ) {
//		$feed = fof_feed_row( $id );
		if ($feed) {
			$this->setData( $feed );
			$this->items = fof_get_items( $id );
			return true;
		}
	}
*/

	function deleteData( $id ) {
		$result = $this->dbcon->Execute("DELETE from calendar WHERE feed_id=$id");
		$result = $this->dbcon->Execute("DELETE from calendar_feeds where id = $id");
		return $result;
	}
}
?>

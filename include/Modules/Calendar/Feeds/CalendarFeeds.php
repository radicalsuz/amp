<?php

require_once ("AMP/System/Data/Item.inc.php");

class CalendarFeeds extends AMPSystem_Data_Item {

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

		$id = $data['id']?$data['id']:'';

		return $this->subscribe($url, $id);
	}

	function subscribe($url, $id=null) {
		$rss = fetch_rss($url);

		if(!$rss->channel && !$rss->items) {
			$this->error="URL is not RSS or is invalid";
			return false;
		}

		$feed = array('id'=>$id,'url'=>$url,'title'=>$rss->channel['title'],
											 'link' =>$rss->channel['link'],
											 'description'=>$rss->channel['description']);
		$result = $this->dbcon->Replace( 'calendar_feeds', $feed, array('id', 'url'), true );
		if(!$result) {
			$this->error="Could not save feed";
			return false;
		}
		if ($result == ADODB_REPLACE_INSERTED ) $id = $this->dbcon->Insert_ID();

		$num_events = 0;
		foreach($rss->items as $item) {
			$event = $item['ev'];
			$vcard = $item['vcard'];

			if($contact = $event['organizer']) {

			} else {
				$contact = $vcard['organizer_fn'];
				$email = $vcard['organizer_email'];
				$phone = $vcard['organizer_tel'];
				$uid = $vcard['organizer_uid'];
			}

			$udm =& new UserDataInput($this->dbcon,AMP_CALENDAR_ENTRY_FORM_DEFAULT);
			$udm->setData(array('Last_Name' => $contact,
								'Email'		=> $email,
								'Phone'		=> $phone));
			if(!$udm->saveUser()) continue;
			$local_uid = $udm->uid;

			$geo = $item['geo'];
			if(!$event) continue;
			$num_events++;

			$typemap = array_flip($this->types);
			$type = $typemap[$event['type']];
			if(!$type) $type = $typemap['Other'];
			$calendar = array('event'=>$item['title'],
							  'shortdesc'=>$item['description'],
							  'url'=>$item['link'],
							  'contact1'=>$contact,
							  'email1'=>$email,
							  'phone1'=>$phone,
							  'date'=>$event['startdate'],
							  'location'=>$event['location'],
							  'enddate'=>$event['enddate'],
							  'typeid'=>$type,
							  'lcity'=>$vcard['adr_locality'],
							  'lstate'=>$vcard['adr_region'],
							  'lcountry'=>$vcard['adr_country'],
							  'laddress'=>$vcard['adr_street'],
							  'lzip'=>$vcard['adr_pcode'],
							  'lat'=>$geo['lat'],
							  'lon'=>$geo['long'],
							  'uid'=>$local_uid,
							  'feed_id'=>$id);

			$result = $this->dbcon->Replace( 'calendar', $calendar, array('feed_id', 'url'), true );
		}

		if(!$num_events) {
			$this->error="Feed did not contain event information!";
			return false;
		}


		return true;
	}

	function deleteData( $id ) {
		$result = $this->dbcon->Execute("DELETE from calendar WHERE feed_id=$id");
		$result = $this->dbcon->Execute("DELETE from calendar_feeds where id = $id");
		return $result;
	}

	function update() {
		$sql = "SELECT url, id FROM calendar_feeds";
		$feeds = $this->dbcon->Execute($sql);
		if(!$feeds) {
			return false;
		}
		while(!$feeds->EOF) {
			$this->subscribe($feeds->Fields('url'), $feeds->Fields('id'));
			$feeds->MoveNext();
		}

		$this->dbcon->CacheFlush();

		return true;
	}
}
?>

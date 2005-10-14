<?php
if(!defined(AMP_CALENDAR_ENTRY_FORM_DEFAULT)) define(AMP_CALENDAR_ENTRY_FORM_DEFAULT, 50);

require_once ("AMP/System/Data/Item.inc.php");
require_once ("AMP/UserData/Input.inc.php");
require_once ("Modules/Calendar/Lookups.inc.php");

class CalendarFeeds extends AMPSystem_Data_Item {

	var $datatable = "calendar_feeds";
	var $types;

	function CalendarFeeds( &$dbcon, $id = null) {
		$this->init( $dbcon, $id );
		$this->types = AMPSystem_Lookup::instance('EventTypes');
	}

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
		$rss = fetch_rss(trim($url));

		if(!$rss->channel && !$rss->items) {
			$this->addError("URL is not RSS or is invalid");
			return false;
		}

		$feed = array('url'=>$url,'title'		=> $rss->channel['title'],
								  'link' 		=> $rss->channel['link'],
								  'description'	=> $rss->channel['description']);
		if(isset($id) && $id) {
			$feed['id'] = $id;
		}
		$result = $this->dbcon->Replace( 'calendar_feeds', $feed, 'url', true );
		if(!$result) {
			$this->addError("Could not save feed");
			return false;
		}
		if ($result == ADODB_REPLACE_INSERTED ) $id = $this->dbcon->Insert_ID();

		$num_events = 0;
/*XXX: need to check here for:
get all events that already have this feed_id
if any events already here have publish=1 and are missing out of the current feed,
turn them to publish=0;
*/
		foreach($rss->items as $item) {
			$event = $item['ev'];
			if(!$event) continue;
			$num_events++;
			$vcard = $item['vcard'];

			if($contact = trim($event['organizer'])) {

			} else {
				$contact = $vcard['organizer_fn'];
				$email = $vcard['organizer_email'];
				$phone = $vcard['organizer_tel'];
				$uid = $vcard['organizer_uid'];
			}

			$geo = $item['geo'];
			//if no zip, figure it out if possible
			if(!$vcard['adr_pcode'] && include_once('AMP/Geo/Geo.php')) {
				$zip = false;
				if($geo['lat'] && $geo['long']) {
					$geo_lookup =& new Geo($this->dbcon);
					$geo_lookup->lat  = $geo['lat'];
					$geo_lookup->long = $geo['long'];
					$info = $geo_lookup->zip_radius(0);
					if($info && is_array($info)) {
						$zips = array_keys($info);
						$zip = (isset($zips[0]) && $zips[0])?$zips[0]:false;
					}
				}
				if(!$zip && $vcard['adr_locality'] && $vcard['adr_region']) {
					$geo_lookup =& new Geo($this->dbcon, $vcard['adr_street'],
														 $vcard['adr_locality'],
														 $vcard['adr_region'],
														 null,
														 array('city_fulltext', 'city_soundex'));
					$info = $geo_lookup->zip_radius(0);
					if($info && is_array($info)) {
						$zips = array_keys($info);
						$zip = (isset($zips[0]) && $zips[0])?$zips[0]:false;
					}
				}
				if($zip) {
					$vcard['adr_pcode'] = $zip;
				}
			}

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
//							  'uid'=>$local_uid,
							  'feed_id'=>$id);

			$result = $this->dbcon->Replace( 'calendar', $calendar, array('feed_id', 'url'), true );

			//test for uid in return record, update that, 
			//else add new and set it in calendar table
			if ($result == ADODB_REPLACE_INSERTED ) {
				$calendar_id = $this->dbcon->Insert_ID();
				$calendaruids = AMPSystem_Lookup::instance('CalendarUid');
				$uid = $calendaruids[$calendar_id];

				$user = array('Last_Name' => $contact,
							  'Email'		=> $email,
							  'Phone'		=> $phone
							 );

				if(isset($uid) && $uid) {
					$user['id'] = $uid;
					$this->dbcon->Replace( 'userdata', $user, null, true);
				} else {
					$udm =& new UserDataInput($this->dbcon,AMP_CALENDAR_ENTRY_FORM_DEFAULT,true);
					$udm->setData($user);
//need better error handling here
					if(!$udm->saveUser()) continue;
					$local_uid = $udm->uid;
					$uid_update = array('id' => $calendar_id,
										'uid' => $local_uid);
					$result = $this->dbcon->Replace( 'calendar', $uid_update, 'id', true );
				}
			}
		}

		if(!$num_events) {
			$this->addError("Feed did not contain event information!");
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

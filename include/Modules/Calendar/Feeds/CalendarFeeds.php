<?php
if(!defined(AMP_CALENDAR_ENTRY_FORM_DEFAULT)) define(AMP_CALENDAR_ENTRY_FORM_DEFAULT, 50);

require_once ("AMP/System/Data/Item.inc.php");
require_once ("AMP/UserData/Input.inc.php");
require_once ("Modules/Calendar/Lookups.inc.php");
require_once('magpierss/rss_fetch.inc');

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

		return $this->subscribe($url, $data['id'], $data['publish'], $data['name']);
	}

	function subscribe($url, $id=null, $publish=null, $name=null) {
		$rss = fetch_rss(trim($url));

		if(!$rss->channel && !$rss->items) {
			$this->addError("URL is not RSS or is invalid");
			return false;
		}

		$feed = array('url'=>$url,'title'		=> $rss->channel['title'],
								  'link' 		=> $rss->channel['link'],
								  'description'	=> $rss->channel['description'],
								  'last_update' => $this->dbcon->DBTimeStamp());
		if(isset($id) && $id) {
			$feed['id'] = $id;
		}
		if(isset($publish)) {
			$feed['publish'] = $publish;
		}
		if(isset($name)) {
			$feed['name'] = $name;
		}
		$result = $this->dbcon->Replace( 'calendar_feeds', $feed, 'url', true );
		if(!$result) {
			$this->addError("Could not save feed");
			return false;
		}
		if ($result == ADODB_REPLACE_INSERTED ) $id = $this->dbcon->Insert_ID();
		$this->init($this->dbcon, $id);

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
				$contact = trim($vcard['organizer_fn']);
				$email = trim($vcard['organizer_email']);
				$phone = trim($vcard['organizer_tel']);
				$uid = trim($vcard['organizer_uid']);
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
					$geo_lookup =& new Geo($this->dbcon, trim($vcard['adr_street']),
														 trim($vcard['adr_locality']),
														 trim($vcard['adr_region']),
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
							  'lcity'=>trim($vcard['adr_locality']),
							  'lstate'=>trim($vcard['adr_region']),
							  'lcountry'=>trim($vcard['adr_country']),
							  'laddress'=>trim($vcard['adr_street']),
							  'lzip'=>trim($vcard['adr_pcode']),
							  'lat'=>$geo['lat'],
							  'lon'=>$geo['long'],
//							  'uid'=>$local_uid,
							  'feed_id'=>$this->getData('id'));

			if(isset($item['content']) && isset($item['content']['encoded'])) {
				$encoded = $item['content']['encoded'];
				$data = $this->decode($encoded);
				if($data) $calendar = array_merge($data, $calendar);
			}

			$result = $this->dbcon->Replace( 'calendar', $calendar, array('feed_id', 'url'), true );
			if(2 == $result && $this->getData('publish')) {
				$this->dbcon->Execute('UPDATE calendar SET publish=1 WHERE feed_id='.$calendar['feed_id'].' AND url='.$calendar['url']);
			}

			//test for uid in return record, update that, 
			//else add new and set it in calendar table
			/* XXX: and what if we don't even have a uid?  what's the big deal?
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
			 */
		}

		if(!$num_events) {
			$this->addError("Feed did not contain event information!");
			return false;
		}

		return true;
	}

	function decode($xml) {
		if(!include_once('XML/Unserializer.php')) return false;
		$parser =& new XML_Unserializer(array('parseAttributes' => true, 'attributesArray' => '_attributes', 'returnResult' => true));
		$data = $parser->unserialize($xml);
		if(PEAR::isError($data)) {
			trigger_error('could not unserialize content: '.$data->getMessage());
			return false;
		}

		return $this->parseHcal($data, $hcal);
	}

	function parseHcal($data) {
		if(!is_array($data)) return;
		if(isset($data['_attributes']) && 'vevent' == $data['_attributes']['class']) {
			return $this->parseVevent($data, $vevent);
		} else {
			foreach($data as $key => $value) {
				if('_content' == $key || '_attributes' == $key) continue;
				if($hcal = $this->parseHcal($value)) return $hcal;
			}
		}
	}

	function &parseVevent($data, &$vevent) {
		if(!is_array($data)) return;
		if(!isset($data['_attributes'])) {
			foreach($data as $datum) {
				$this->parseVevent($datum, $vevent);
			}
		} else {
			switch($data['_attributes']['class']) {
			case 'rrule':
				$freq_ids = array('Once', 'Daily', 'Weekly', 'Monthly', 'Yearly');
				$rules = explode(';', $data['_attributes']['title']);
				if(!is_array($rules)) $rules = array($rules);
				foreach($rules as $rule) {
					list($name, $value) = explode('=',$rule);
					if('FREQ' == strtoupper(trim($name))) {
						$freq = ucfirst(strtolower(trim($value)));
						$freq_id = array_search($freq, $freq_ids);
						if(false === $freq_id) continue;
						$vevent['recurring_options']=$freq_id;
					}
				}
				if(isset($data['_content'])) $vevent['recurring_description'] = $data['_content'];
				break;
			case 'dtstart':
				$vevent['date'] = $data['_attributes']['title'];
				break;
			case 'dtend':
				$vevent['enddate'] = $data['_attributes']['title'];
				break;
			case 'summary':
				$vevent['event'] = $data['_content'];
				break;
			case 'description':
				$vevent['shortdesc'] = $data['_content'];
				break;
			case 'location':
				$vevent['location'] = $data['_content'];
				break;
			case 'locality':
				$vevent['lcity'] = $data['_content'];
				break;
			case 'region':
				$vevent['lstate'] = $data['_content'];
				break;
			case 'postal-code':
				$vevent['lzip'] = $data['_content'];
				break;
			case 'street-address':
				$vevent['laddress'] = $data['_content'];
				break;
			}
			foreach($data as $type => $value) {
				if('_attributes' == $type || '_content' == $type) continue;
				$this->parseVevent($value, $vevent);
			}
		}

		return $vevent;
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

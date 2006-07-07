<?php
require_once ('Modules/Calendar/Plugin.inc.php');
require_once ('RSSWriter/AMP_RSSWriter.php');
require_once ('AMP/System/Lookups.inc.php');

class CalendarPlugin_RSS_Output extends CalendarPlugin {

    function CalendarPlugin_RSS_Output (&$calendar, $options=null, $instance=null) {   
        $this->init($calendar, $options, $instance);
    }

    function init (&$calendar, $options=null, $instance=null) {
        $this->dbcon=&$calendar->dbcon;
        $this->calendar= &$calendar;

		$this->types = AMPSystem_Lookup::instance('EventTypes');
    }

    function execute ($options=null) {

		$rss =& new AMP_RSSWriter(AMP_SITE_URL, AMP_SITE_NAME, AMP_SITE_META_DESCRIPTION);

		$rss->useModule('ev', 'http://purl.org/rss/1.0/modules/event/');
		$rss->useModule('vCard', 'http://www.w3.org/2001/vcard-rdf/3.0#');
		$rss->useModule('geo', 'http://www.w3.org/2003/01/geo/wgs84_pos#');
		$rss->useModule('content', 'http://purl.org/rss/1.0/modules/content/');

		if (isset($options['calid']) && ($id = $options['calid'])) {
			$timestamp = $this->add_event_rss($rss, $id);
		} else {
			$this->calendar->doAction('Search');
			$timestamp = $this->add_eventlist_rss($rss, $this->calendar->events);
		}

		$rss->lastModified($timestamp);

		while(@ob_end_clean());
		$rss->execute();
		exit;
	}

	function add_event_rss(&$rss, $id) {

		$event = $this->calendar->readData($id);
		$event['id'] = $id;

		$this->add_event_item($rss, $event);

		return strtotime($event['datestamp']);
	}

	function add_eventlist_rss(&$rss, $events) {

		$timestamp = 0;
		foreach ($events as $event) {
			$datestamp = strtotime($event['datestamp']);
			if($datestamp && ($timestamp < $datestamp)) {
				$timestamp = $datestamp;
			}

			$this->add_event_item($rss, $event);
		}

		return $timestamp;
	}

	function add_event_item(&$rss, $event) {
		if(is_numeric($state_id = $event['lstate'])) {
			$states = AMPSystem_Lookup::instance('States');
			$event['lstate'] = $states[$state_id];
		}
		$item = array();
		if(isset($event['shortdesc']) && $event['shortdesc']) $item['description'] = $event['shortdesc'];
		if(isset($event['date']) && $event['date']) $item['ev:startdate'] = $event['date'];
		if(isset($event['enddate']) && $event['enddate']) $item['ev:enddate'] = $event['enddate'];
		if(isset($event['location']) && $event['location']) $item['ev:location'] = $event['location'];
		if(isset($event['typeid']) && $this->types[$event['typeid']]) $item['ev:type'] = $this->types[$event['typeid']];

		$adr = array();
		if(isset($event['laddress']) && $event['laddress'])
			$adr['vCard:Street'] = '   '.$event['laddress'].' ';
		if(isset($event['lcity']) && $event['lcity'])
			$adr['vCard:Locality'] = ' '.$event['lcity'].' ';
		if(isset($event['lstate']) && $event['lstate'] && 'Intl' != $event['lstate'])
			$adr['vCard:Region'] = '   '.$event['lstate'].' ';
		if(isset($event['lzip']) && $event['lzip'])
			$adr['vCard:Pcode'] = '    '.$event['lzip'].' ';
		if(isset($event['lcountry']) && $event['lcountry'])
			$adr['vCard:Country'] = '  '.$event['lcountry'].' ';
		if(!empty($adr))
			$item['vCard:ADR rdf:parseType="Resource"'] = $adr;


		$organizer = array();

		if($event['email1'])
			$organizer['vCard:EMAIL'] = ' '.$event['email1'].' ';
		if($event['phone1'])
			$organizer['vCard:TEL'] = '   '.$event['phone1'].' ';
		if($event['uid'])
			$organizer["vCard:UID"] = '   '.$event['uid'].' ';
		if(!empty($organizer)) {
			if($event['contact1']) $organizer["vCard:FN"] = '  '.$event['contact1'].' ';
			$item["ev:organizer"] = $organizer;
		} else {
			if($event['contact1']) $item["ev:organizer"] = $event['contact1'];
		}

		if($event['lat'] && $event['lon']) {
			$item["geo:lat"] = $event['lat'];
			$item["geo:long"] = $event['lon'];
		}

		$item['content:encoded'] = '<![CDATA['.$this->hcalendar($event).']]>';

		$rss->addItem(AMP_SITE_URL.'calendar.php?calid='.$event['id'], $event['event'], $item);
	}

	function hcalendar($e, $options=null) {
		$hcal = '<p class="vevent">';
		$hcal .= '<a href="'.AMP_SITE_URL.'calendar.php?calid='.$e['id'].'">'.$this->hcal_summary($e).'</a><br />';
		$recur = $this->hcal_rrule($e);
		if($recur) $hcal .= $recur.' ';
		$hcal .= $this->hcal_dtstart($e);
		if($recur && isset($e['enddate'])) $hcal .= '-<abbr class="dtend" title="'.$e['enddate'].'">'.DoDate($e['enddate'], 'l, F jS Y').'</abbr>';
		if ($e['time'] != '00:00 ') $hcal .=  ' '.$e['time'];
		$desc = $this->hcal_description($e);
		if($desc) $hcal .= "<br /><br />$desc";
		$location = $this->hcal_location($e);
		if($location) $hcal .= "<br /><br />Location:<br />$location";
		$hcal .= '</p>';
		return $hcal;
	}

	function hcal_summary($e) {
		return '<span class="summary">'. $this->cleanXHTML($e['event']) .'</span>';
	}

	function hcal_rrule($e) {
		$freq = false;
		if(isset($e['recurring_options'])) {
			switch($e['recurring_options']) {
			case 1:
				$freq='Daily';
				break;
			case 2:
				$freq='Weekly';
				break;
			case 3:
				$freq='Monthly';
				break;
			case 4:
				$freq='Yearly';
				break;
			default:
				$freq=false;
			}
		}
		if(!$freq) return '';

		$recur = '<abbr class="rrule" title="FREQ='.strtoupper($freq).'">';
		if(isset($e['recurring_description'])) {
			$recur .= $e['recurring_description'];
		} else {
			$recur .= $freq;
		}
		$recur .= '</abbr>';

		return $recur;
	}

	function hcal_dtstart($e) {
		if ($e['date'] != '0000-00-00' || $e['time'] != '') {
			$formatted_date = DoDate($e['date'], 'l, F jS Y');
		}
		if(!$formatted_date) return '';
		return '<abbr class="dtstart" title="'.$e['date'].'">'.$formatted_date.'</abbr>';
	}

	function hcal_description($e) {
		$desc = false;
		if ($e['shortdesc'] && !$e['fulldesc'])
			$desc = converttext(trim($e['shortdesc']));
		else
			$desc = converttext(trim($e['fulldesc']));
		if(!$desc) return false;

		return '<span class="description">'.$this->cleanXHTML($desc).'</span>';
	}

	function hcal_location($e) {
		$hcal = '<span class="location">'.$e['location'].'</span>';
		if(isset($e['laddress']) || isset($e['lcity']) || isset($e['lstate']) || isset($e['lzip'])) {
			$hcal .= '<br /><span class="location vcard"><span class="adr">';
			$addr = array();
			if(isset($e['lcity'])) $addr[] = '<span class="locality">'.$e['lcity'].'</span>';
			if(isset($e['lstate'])) $addr[] = '<span class="region">'.$e['lstate'].'</span>';
			$city_state = implode(', ', $addr);
			$addr = array();

			if($city_state) $addr[] = $city_state;
			if(isset($e['lzip'])) $addr[] = '<span class="postal-code">'.$e['lzip'].'</span>';
			$city_state_zip = implode(' ', $addr);
			$addr = array();
			if(isset($e['laddress'])) $addr[] = '<span class="street-address">'.$e['laddress'].'</span>';
			if($city_state_zip) $addr[] = $city_state;
			$location = implode('<br />', $addr);
			
			$hcal .= $location;
			$hcal .= '</span></span>';
		}
		return $hcal;
	}

	function cleanXHTML($string) {
		if(!extension_loaded('tidy')) {
			if(!dl('tidy.so')) {
				return strip_tags($string, '<br><a>');
			}
		}
		tidy_setopt('output-xhtml', true);
		tidy_setopt('doctype', 'omit');
		tidy_setopt('show-body-only', true);

		tidy_parse_string($string);
		tidy_clean_repair();
		$clean = tidy_get_output();
		if(!$clean) return strip_tags($string, '<br><a>');
		return $clean;
	}

/*
from Index.inc.php

    function execute ($options=null) {

		$index['state']['name']="Upcoming Events By State";
		$index['state']['sql'].="SELECT count(calendar.id) as qty, calendar.lstate as item_key, states.statename as item_name from calendar, states WHERE calendar.lstate=states.state and calendar.publish=1 and ((recurring_options>0 and enddate>=CURDATE()) OR (recurring_options=0 and date>=CURDATE())) GROUP BY calendar.lstate ";
		$index['caltype']['sql']="SELECT count(calendar.id) as qty, calendar.typeid as item_key, eventtype.name as item_name from calendar, eventtype WHERE calendar.typeid=eventtype.id and calendar.publish=1 and ((recurring_options>0 and enddate>=CURDATE()) OR (recurring_options=0 and date>=CURDATE())) GROUP BY calendar.typeid ORDER BY eventtype.name ";
		$index['caltype']['name']="Upcoming Events By Type";
		foreach ($index as $index_key=>$this_index) {
			$index_set=$this->dbcon->CacheGetAll($this_index['sql']);
			$output.='<P><B>'.$this_index['name'].'</B><BR>';
			foreach ($index_set as $index_item) {
				$output.='<a href="'.$_SERVER['PHP_SELF'].'?'.$index_key.'='.$index_item['item_key'].'&bydate='.date("Y-m-d").'">'.$index_item['item_name'].'</a> ('.$index_item['qty'].')<BR>';
			}
		}
		return $output;
    }
        
    
    
/*	

	function a_slow_bad_object_based_approach_to_Index() {
		$index['state']['name']="Events By State";
        $index['state']['source_sql']="Select state as id_val, statename as description_val from states";
        $index['state']['cal_field']="lstate";
		$index['caltype']['name']="Events By Type";
        $index['caltype']['source_sql']="Select id as id_val, name as description_val from eventtype";
        $index['caltype']['cal_field']="typeid";
        
        $base_criteria="((recurring_options>0 and enddate>=CURDATE()) OR (recurring_options=0 and date>=CURDATE()))"; 
        $result=new CalendarSearch ($this->dbcon, $base_criteria); 

		foreach ($index as $index_key=>$this_index) {
			$source_set=$this->dbcon->GetArray($this_index['source_sql']);
			$output.='<P><B>'.$this_index['name'].'</B><BR>';
            foreach ($source_set as $index_id=>$index_val) {
                $criteria = $this_index['cal_field']."=".$this->dbcon->qstr($index_val['id_val']);
                $result_count=$result->count_events($criteria);
                if ($result_count>0) {
                    $output.='<a href="'.$_SERVER['PHP_SELF'].'?'.$index_key.'='.$index_val['id_val'].'&bydate='.date("Y-m-d").'">'.$index_val['description_val'].'</a> ('.$result_count.')<BR>';
                }
			}
		}

		return $output;
	}
    
 */   
}    
    
?>

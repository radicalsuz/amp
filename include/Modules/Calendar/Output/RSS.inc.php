<?php
require_once ('Modules/Calendar/Plugin.inc.php');
require_once ('RSSWriter/AMP_RSSWriter.php');

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

		if ($id = $options['calid']['value']) {
			$timestamp = $this->add_event_rss($rss, $id);
		} else {
			$timestamp = $this->add_eventlist_rss($rss);
		}

		if($timestamp) {
//			$this->httpConditionalGet($timestamp);
		}

		while(@ob_end_clean());
		$rss->serialize();
		exit;
	}

	function httpConditionalGet($timestamp) {
		header('Last-Modified: '.($last_modified = gmdate('r', $timestamp)));
		header('ETag: "'.$timestamp.'"');
		$client_etag = isset($_SERVER['HTTP_IF_NONE_MATCH'])
						? $_SERVER['HTTP_IF_NONE_MATCH'] : NULL;
		$client_lm   = isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])
						? $_SERVER['HTTP_IF_MODIFIED_SINCE'] : NULL;

		if( ($client_etag and $client_etag == '"'.$timestamp.'"') #etag
			or
			($client_lm and ($client_lm == $last_modified or $timestamp == strtotime($client_lm))) #last-modified
		) header('HTTP/1.1 304 Not Modified') and exit;
}

	function add_event_rss(&$rss, $id) {

		$event = $this->calendar->readData($id);
		$event['id'] = $id;

		$this->add_event_item($rss, $event);

		return strtotime($event['datestamp']);
	}

	function add_eventlist_rss(&$rss) {

		$this->calendar->doAction('Search');

		$timestamp = 0;
		foreach ($this->calendar->events as $event) {
			$datestamp = strtotime($event['datestamp']);
			if($datestamp && ($timestamp < $datestamp)) {
				$timestamp = $datestamp;
			}

			$this->add_event_item($rss, $event);
		}

		return $timestamp;
	}

	function add_event_item(&$rss, $event) {
		$id = $event['id'];
		$item = array("description" => $event['shortdesc'],
						"ev:startdate" => $event['date'],
						"ev:enddate" => $event['enddate'],
						"ev:location" => $event['location'],
						"ev:organizer" => $event['contact1'],
						"ev:type" => $this->types[$event['typeid']],
						"vCard:Adr" => array(
							"vCard:Street" => $event['laddress'],
							"vCard:Extadd" => '',
							"vCard:Locality" => $event['lcity'],
							"vCard:Region" => ($event['lstate'] != 'Intl')?$event['lstate']:'',
							"vCard:Pcode" => $event['lzip'],
							"vCard:Country" => $event['lcountry']));
		if($event['lat'] && $event['lon']) {
			$item["geo:lat"] = $event['lat'];
			$item["geo:long"] = $event['lon'];
		}

		$rss->addItem(AMP_SITE_URL . "calendar.php?calid=$id", $event['event'], $item);
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

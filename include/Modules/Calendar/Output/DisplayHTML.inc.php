<?php
require_once ('Modules/Calendar/Plugin.inc.php');
require_once ('AMP/Region.inc.php');

if (!defined( 'AMP_CALENDAR_RSVP_FORM_DEFAULT' )) define( 'AMP_CALENDAR_RSVP_FORM_DEFAULT', 51);

class CalendarPlugin_DisplayHTML_Output extends CalendarPlugin {
    
    var $options= array( 
        'subheader'=>array('value'=>'lcity'),
        'display_format'=>array('value'=>'calendar_list_rsvp_format'),
        'detail_format'=>array('value'=>'calendar_output_detail_rsvp_mapped'),
        'rsvp_modin'=>array('value'=>AMP_CALENDAR_RSVP_FORM_DEFAULT));
    
    var $current_subheader;
    var $regionset;

    function CalendarPlugin_DisplayHTML_Output (&$calendar, $instance=null) {   
        $this->init($calendar, $instance);
        $this->regionset=new Region;
    }

    function execute ($options=null) {
        //Check to see if a single event was specified
        //if so, return detail information for that event
		if (isset($options['calid'])) {
            $detail_function=isset($this->options['detail_format']['value'])?($this->options['detail_format']['value']):"display_detail";
            $inclass=method_exists($this, $detail_function);
            if ($inclass){ $output=$this->$detail_function($this->dbcon, $options['calid']['value'] );
            } else {
                $output=$detail_function($this->dbcon, $options['calid']['value']);
            }
        } else {
        //Print the current results list
            $dataset=$this->calendar->results();
            
            $display_function=isset($this->options['display_format']['value'])?($this->options['display_format']['value']):"display_item";
            $inclass=method_exists($this, $display_function);

            //output display format
            foreach ($dataset as $dataitem) {
                if (isset($this->options['subheader'])) $output.=$this->subheader($dataitem);
                if($inclass) $output.=$this->$display_function($dataitem);
                else $output.=$display_function($dataitem, $this->options);
            }
        }

		return $output;
    }
        
    function subheader($dataitem) {
        if ($this->current_subheader != trim($dataitem[$this->options['subheader']['value']])) {
            $this->current_subheader = trim($dataitem[$this->options['subheader']['value']]);
            $output .= '<h1 style="font-size: small; background: #ccc; padding: 3px 3px;">' . $this->current_subheader;
            if ($this->options['subheader']['value']=='lcity') {
                if ($dataitem['lstate']) $output.= ', ' . $this->regionset->regions['US AND CANADA'][$dataitem['lstate']];
                if ($dataitem['lcountry']!="USA") $output.= '&nbsp;&nbsp;' . $this->regionset->regions['WORLD'][$dataitem['lcountry']];
            }
            $output.= '</h1>';
        }
        return $output;
    }
    function display_detail($dbcon, $calid) {
        print 'Warning: this detail page has not been specified.  Please contact your site administrator!';
    }

    function display_item($dataitem, $options) {
        print 'Warning: this display type has not been specified.  Please contact your site administrator!';
    }
        
}    





//end of Calendar list class
//utility output functions follow

function calendar_list_rsvp_format($e, $options=null) {
        if (isset ($e['registration_modin'])&&$e['registration_modin']) {
            $meat .= "<a href=\"modinput4.php?modin=".$e['registration_modin']."&calid=".$e['id']."\">RSVP</a>&nbsp; &bull; ";
        }
        $meat .= '<a href="'.$_SERVER['PHP_SELF'].'?calid='. $e['id'] .'" class="eventtitle">'. $e['event'] .'</a></span><br />';
        if (($e['date'] != '0000-00-00' || $e['time'] != '')&& $e['recurring_options']==0) $meat .= '<span class="eventsubtitle">'. DoDate($e['date'], 'l, F jS Y') .' '.'</span>';
        if ($e['time'] != '00:00 ') $meat .= '<span class="eventsubtitle">'. $e['time'] .'</span>';
        $meat.='<br />';
        if ( $e['recurring_options'] == 1 ) $meat .= "<i>Multi-Day Event</i><br/>";
        if ( $e['recurring_options'] == 2 ) $meat .= "<i>Weekly Event </i><br/>";
        if ( $e['recurring_options'] == 3 ) $meat .= "<i>Monthly Event </i><br/>";
        if ( $e['recurring_options'] == 4) $meat .= "<i>Yearly Event </i><br/>";
        if ( $e['recurring_description'] && isset($e['recurring_description'])) $meat .= "<i> ".$e['recurring_description']."</i><BR>";
        
        $meat .= '<span class="text">';
        if ($e['shortdesc'] != NULL) {
            $meat .= converttext(trim($e['shortdesc'])); 
            }
        $meat .= '</span><br>';
        if (isset($options['Lookups']['rsvpcount']['Set'][$e['id']])) {
            $rsvpcount=$options['Lookups']['rsvpcount']['Set'][$e['id']];
            $meat .='Current RSVPs: $rsvpcount<BR>';
        }
        $meat.='<BR><br>';
        return $meat;		
	}

	function calendar_list_organizer_format ($e, $options=null) {
		if (!isset($options['list_row_format'])) {
			return calendar_list_rsvp_format($e, $options);
		} else {
			$format=$options['list_row_format'];
			$e['time']=$e['time']=='00:00 am'?'':$e['time'];
			if ($e['modin']>0 && is_numeric($e['modin'])) $format=str_replace("<!--RSVP--", "", str_replace("--RSVP-->", "", $format) );
			$output.=sprintf($format, $e['id'], $e["event"], $e["id"], $e["nicedate"], $e["time"], $e["lcity"], $e["lstate"], $e["shortdesc"]);
			return $output;
		}
	}


	function calendar_output_detail_rsvp_mapped($dbcon, $id) {
		$q = "select calendar.* from calendar where calendar.id = $id";
		$event = $dbcon->CacheGetAll($q);
		foreach ($event as $e) {
			if ($e['registration_modin']>0) $meat .="<br><a href=\"modinput4.php?modin=" . $e['registration_modin'] . "&calid=$id\">RSVP</a>";
			$meat .= "<p><span class=title>". $e['event'] .'</span>';
			if ( $e['recurring_options'] == 1 ) $meat .= "<br/><i>Multi-Day Event</i>";
			if ( $e['recurring_options'] == 2 ) $meat .= "<br/><i>Weekly Event </i>";
			if ( $e['recurring_options'] == 3 ) $meat .= "<br/><i>Monthly Event </i>";
			if ( $e['recurring_options'] == 4 ) $meat .= "<br/><i>Annual Event</i>";
			
			if ( $e['recurring_options']>0 && $e['recurring_description']!=NULL) $meat .= ", ".$e['recurring_description'];

			if ($e['date'] != '0000-00-00' || $e['time'] != '') {
				$meat .= '<br>
				<span class="eventsubtitle">'. DoDate($e['date'], 'l, F jS Y') 
				.' ';
							if ($e['time'] != '00:00 ') $meat .=  $e['time'] ;
				
				
			}
			$meat .= '<br>'. $e['lcity'] 
				.',&nbsp;'. $e['lstate'] .'  '. $e['lcountry'] .'</span><br><br>
				<span class="text">';
			if ($e['shortdesc'] != '' && $e['fulldesc'] == '') $meat .= converttext(trim($e['shortdesc'])). '<br>';
			$meat .= converttext(trim($e['fulldesc'])). '<br>';
			if ($e['location'] != ($null)) { 
				$meat .= '<br><br>
				<b>Location:&nbsp;</b><br>'
				. $e['location'] .'&nbsp;'
				. $e['laddress'] .'&nbsp;'
				. $e['lcity'] .'&nbsp;'
				. $e['lstate'] .'&nbsp;'
				. $e['lzip'] .'&nbsp;'; 
			}
			if ( $e['contact1'] != '' or $e['phone1'] != '' or $e['email1'] != '' ) $meat .= '<br><br><b>Contact:</b>';
			if ($e['contact1'] != '') $meat .= '<br>'. $e['contact1'];
			if ($e['email1'] != '') $meat .= '<br><a href="mailto:'. $e['email1'] .'">'. $e['email1'] .'</a>';
			if ($e['phone1'] != '') $meat .= '<br>'. $e['phone1'];
			if ($e['org'] != '') $meat .= '<br><br><b>Sponsored By:</b><br>'. $e['org'];
			if ($e['url'] != '' and $e['url'] != 'http://') $meat .= ' <a href="'. $e['url'] .'">'. $e['url'] .'</a>';
			$meat .= '</span></p>';
		
		//
		//insert map
		//var_dump($event); 
        /* broken and not always valid: buh-bye
		if ($e['lat']&&$e['lon']) {
			$map_lat=$e['lat'];$map_long=$e['lon']; 
			$mapcode = "mapgen?lon=" .  $map_long . "&lat=" .  $map_lat . "&wid=0.035&ht=0.035&iht=320&iwd=320&mark=" .  $map_long . "," .  $map_lat . ",redpin";
			$meat .= str_replace('$map_long',$map_long,str_replace('$map_lat',$map_lat,file_get_contents('http://'.$_SERVER['SERVER_NAME'].'/scripts/geocode.js')));
			$meat .= str_replace('$mapcode',$mapcode,file_get_contents('includes/geocode.inc.php'));
		}
        
		$meat .= '<p class="eventsubtitle">Event followups and comments:</p>';
		// read in results for this calendar id here
		if (!$resultspresent) {$meat .= '<p class="text">No results entered yet for this event.</p>';};

		$meat .= '<p class="eventsubtitle">Event photos:</p>';
		// read in photos for this calendar id here
		if (!$photospresent) {$meat .= '<p class="text">No photos entered yet for this event.</p>';};
            */
		}
		
		return $meat;
	}
?>

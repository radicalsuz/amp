<?php

require_once("AMP/Geo/Geo.php");

Class Maps {
	var $dbcon; 
	var $map_ID;
	var $Range;
	var $points;
	var $Count;
	var $P= array(	
		'default_color'=>'bbbbbb',
		'background_color'=>'ffffff',
		'outline_color'=>'666666',
		'default_point_color'=>'ffff00',
		'default_point_size'=>'3',
		'default_point_src'=>'',
		'font_size'=>'11',
		'arc_color'=>'0000ff',
		'map_height'=>'300',
		'map_width'=>'550',
		'line_color'=>'0000ff',
		'state_info_icon'=>'',
		'label_field'=>'City',
		'hover_field'=>'',
		'point_url'=>'',
		'state_url'=>'',
		'extra_sql'=>'',
		'table'=>'userdata',
		'target'=>'',
		'title'=>'',
		'description'=>'',
		'center_lat'=>'',
		'center_long'=>'',
		'span_lat'=>'',
		'span_long'=>'',
		'geo_field'=>''
	);
	
	function Maps($dbcon,$map_ID){
        if (!isset($dbcon)) return false;
        $this->dbcon =& $dbcon;
		$this->map_ID = $map_ID;

		$this->load_data();
	}

	
	function prop($var,$R) {
		if ($R->Fields($var)) {
			$this->P[$var] = $R->Fields($var);
		}
	}

	function load_data() {
		$sql="select * from maps where id = ".$this->map_ID;
	
		$R= $this->dbcon->CacheExecute($sql)or DIE("Error loading map info  ".$sql.$this->dbcon->ErrorMsg());
		$this->prop('default_color',$R);
		$this->prop('background_color',$R);
		$this->prop('outline_color',$R);
		$this->prop('default_point_color',$R);
		$this->prop('default_point_size',$R);
		$this->prop('default_point_src',$R);
		$this->prop('defualt_point_opacity',$R);
		$this->prop('font_size',$R);
		$this->prop('arc_color',$R);
		$this->prop('map_height',$R);
		$this->prop('map_width',$R);
		$this->prop('line_color',$R);
		$this->prop('state_info_icon',$R);
		$this->prop('label_field',$R);
		$this->prop('hover_field',$R);
		$this->prop('point_url',$R);
		$this->prop('state_url',$R);
		$this->prop('extra_sql',$R);
		$this->prop('table',$R);
		$this->prop('target',$R);		
		$this->prop('title',$R);
		$this->prop('description',$R);
		$this->prop('center_lat',$R);
		$this->prop('center_long',$R);
		$this->prop('span_lat',$R);
		$this->prop('geo_field',$R);
		$this->prop('span_long',$R);
		$this->P['table'] = $R->Fields("map_table");;
		if ($this->P['table'] == 'userdata') {
			$this->P['extra_sql'] .= ' and modin = '.$R->Fields("type");
		}
		if ($this->P['table'] == 'calendar') {
			if ($this->P['extra_sql']) {
				$this->P['extra_sql'] .= ' and caltype = '.$R->Fields("type");
			}
		}
	}

	//function that create the range array
	function build_range() {
		$sql="select * from map_range where map_ID = ".$this->map_ID;
		$R= $this->dbcon->CacheExecute($sql)or DIE("Error getting range data in build_range function ".$sql.$this->dbcon->ErrorMsg());
		$x=0;
		while (!$R->EOF) {
			$this->Range[$x]['range'] = $R->Fields("range");
			$this->Range[$x]['color'] = $R->Fields("color");
			$x++;
			$R->MoveNext();
		}
	}			
	function build_points() {
		if ( ($this->P['label_field']) and $this->P['label_field'] != 'City') {
			$extra_fields = ", ".$this->P['label_field'];
		}
		if (($this->P['hover_field']) ) {
			$extra_fields .= ", ".$this->P['hover_field'];
		}	
		if (($this->P['geo_field']) ) {
			$extra_fields .= ", ".$this->P['geo_field'];
		}	

		if ($this->P['table'] == 'calendar') {
			$sql="select distinct id, lcity as City, lstate as State, lzip as Zip".$extra_fields." from calendar where  publish=1 ".$this->P['extra_sql']; // and typeid =$type 
		} else {
			$sql="select distinct id, City, State, Street, Zip".$extra_fields." from ".$this->P['table']." where publish=1 ". $this->P['extra_sql'] ;
		}
		
		$R= $this->dbcon->CacheExecute($sql)or DIE("Error getting city data in build_points function ".$sql.$this->dbcon->ErrorMsg());
		$x=0;
		while (!$R->EOF) {
			if ($this->P['geo_field']  ) {
				$location = $R->Fields($this->P['geo_field']);
			} else {
				$geo = new Geo($this->dbcon);
				$geo->City = $R->Fields("City");
				$geo->State = $R->Fields("State");
				$geo->city_lookup();
				$location = $geo->lat.",".$geo->long;
			}
			

			if ($location != ',' and isset($location)) {
				$this->points[$x]['name'] = htmlspecialchars($R->Fields($this->P['label_field']));
				$this->points[$x]['loc'] = $location;
				$this->points[$x]['Street'] = htmlspecialchars($R->Fields("Street"));
				$this->points[$x]['City'] = htmlspecialchars($R->Fields("City"));
				$this->points[$x]['State'] = $R->Fields("State");
				$this->points[$x]['Zip'] = $R->Fields("Zip");
				$this->points[$x]['id'] =$R->Fields("id");
				$this->points[$x]['hover'] =htmlspecialchars($R->Fields($this->P['hover_field']));
				$x++;
			}
			$R->MoveNext();
		}
		
		
	}
	
	function build_count(){
		$sql = "select * from states";
		$S= $this->dbcon->CacheExecute($sql)or DIE("Error getting state list in functon build_count ".$sql.$this->dbcon->ErrorMsg());
		while (!$S->EOF) {
			if ($this->P['table'] == 'calendar') {
				$sql="select count(id) from calendar where lstate ='".$S->Fields("state")."' and publish=1 $extra"; // and typeid =$type 
			} else {
				$sql="select count(id) from ".$this->P['table']." where State ='".$S->Fields("state")."' and  publish=1 ". $this->P['extra_sql'] ;
			}
			
			$C= $this->dbcon->CacheExecute($sql)or DIE("Error getting state count in functon build_count Query:".$sql.$this->dbcon->ErrorMsg());
//			if ($S->Fields("state") =="CA") { die (count(id));}//die ($C[0][0]);}
			$this->Count[$S->Fields("State")]['data'] = $C->Fields("count(id)"); 
			$this->Count[$S->Fields("State")]['label'] = $S->Fields("statename"); 
	
			$S->MoveNext();
		}
	}
	function xml_file() {
		#checks to see if xmlfile is there
		#checks age of xml file	
		#if stale rewrites xml file
		
	}
	
	
	function us_xml() {	
		if (!$this->Range) {
			//$this->build_range();
		}
		if (!$this->points) {
			$this->build_points();
		}
		if (!$this->Count) {
			$this->build_count();
		}
		
		header('Content-type: text/xml');
		$out .= '<?xml version="1.0" encoding="iso-8859-1"?>';
		$out .= '<us_states>';
		$out .= '<state id="default_color"><color>'.$this->P['default_color'].'</color></state>';
		$out .= '<state id="background_color"><color>'.$this->P['background_color'].'</color></state>';
		$out .= '<state id="outline_color"><color>'.$this->P['outline_color'].'</color></state>';
		$out .= '<state id="default_point"><color>'.$this->P['default_point_color'].'</color><size>'.$this->P['default_point_size'].'</size><src>'.$this->P['default_point_src'].'</src></state>';
		$out .= '<state id="font_size"><data>'.$this->P['font_size'].'</data></state>';
		$out .= '<state id="state_info_icon"><src>'.$this->P['state_info_icon'].'</src></state>';
		$out .= '<state id="line_color"><color>'.$this->P['line_color'].'</color></state>';
		$out .= '<state id="arc_color"><color>'.$this->P['arc_color'].'</color></state>';

				 
		if ($this->Range) {
			$x=0;
			foreach($this->Range as $r) {
				$out .= '<state id="range">';
				$out .= '<data>' . $r['range'] . '</data>';
				$out .= '<color>' . $r['color'] .'</color>';
				$out .= '</state>';
				$x++;
			}
		} 
	
		if ($this->points) {
			
			foreach($this->points as $p) {
				$out .= "\n<state id=\"point\">";
				$out .= '<name>' . $p['name'] . '</name>';
				$out .= '<loc>' . $p['loc'] .'</loc>';
				$out .= '<opacity>' . $this->P['opacity'] .'</opacity>';
				$out .= '<target>' . $this->P['target'] .'</target>';

				if ($p['id']) {
					$out .= '<url>' .$this->P['point_url']. $p['id'] .'</url>';
				}
				if ($p['hover']) {
					$out .= '<hover>' .$p['hover'] .'</hover>';
				}
				$out .= '</state>';
				
			}
		}
		foreach($this->Count as $st => $v) {
			$out .= "\n".'<state id="' . $st . '">';
			$out .= '<name>' . $v['label'] . '</name>';
			$out .= '<data>' . $v['data'] .'</data>';
			$out .= '<target>' . $this->P['target'] .'</target>';

			if ($v['hover']) {
				$out .= '<hover>' .  $v['hover'] .'</hover>';
			}
			if ($this->P['state_url'] && ($v['data'] >= 1)) {
				$out .= '<url>' .$this->P['state_url']. $st .'</url>';
			}
			$out .=  '</state>';
		}
		$out .=  '</us_states>';
		return $out;
	}
	
	function google_xml() {
		if (!$this->points) {
			$this->build_points();
		}

		header('Content-type: text/xml');
		$out .= '<?xml version="1.0" ?>';
		$out .= "<markers>";
		if ($this->points) {
			
			foreach($this->points as $p) {
				$out .= "\n<marker lat=\"".$p['lat']."\" lng=\"".$p['long']."\"";
				$out .= " html=\"";	
				$out .= '&lt;a href=&quot;'.$this->P['point_url']. $p['id'].'&quot;&gt;';	
				$out .= $p['name'].'&lt;/a&gt;';
				$out .= "&lt;br&gt;".$p['City'].", ".$p['State'];
				$out .= '" />';
			}
		}
		$out .= "</markers>";
		return $out;
	}
	 
		
	function flash_map($map='us',$file='flashxml.php?id=',$bgcolor='#FFFFFF') {
	
		$html .= '<OBJECT classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" ';
		$html .= 'codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,0,0" ';
		$html .= 'WIDTH="'.$this->P['map_width'].'" HEIGHT="'.$this->P['map_height'].'" id="zoom_map" ALIGN="top"> ';
		$html .= '<PARAM NAME=movie VALUE="flash/'.$map.'.swf?data_file='.$file.$this->map_ID.'"> ';
		$html .= '<PARAM NAME=quality VALUE=high>  ';
		$html .= '<PARAM NAME=bgcolor VALUE='.$bgcolor.'>  ';
		$html .= '<EMBED src="flash/'.$map.'.swf?data_file='.$file.$this->map_ID.'" ';
		$html .= 'quality=high bgcolor=#FFFFFF  WIDTH="'.$this->P['map_width'].'" HEIGHT="'.$this->P['map_height'].'" NAME="Clickable Map"';
		$html .= 'ALIGN="" TYPE="application/x-shockwave-flash" '; 
		$html .= 'PLUGINSPAGE="http://www.macromedia.com/go/getflashplayer"> ';
		$html .= '</EMBED> ';
		$html .= '</OBJECT> ';
		$html .= "<p><font size ='-4'> Powered by <a href='http://backspace.com/mapapp/'>DIY Map</a></font></p>";
		
		return $html;
	}
	
	function google_map($width='500', $height='400', $zoom='14') {

		$out .= '<script src="http://maps.google.com/maps?file=api&v=1&key='.GOOGLE_API_KEY.'" type="text/javascript"></script>';
		$out .= '<div id="map" style="width: '.$width.'px; height: '.$height.'px"></div>';
$out .= '
<script type="text/javascript">
    //<![CDATA[

    if (GBrowserIsCompatible()) {
      // this variable will collect the html which will eventualkly be placed in the sidebar
      var sidebar_html = "";
    
      // arrays to hold copies of the markers and html used by the sidebar
      // because the function closure trick doesnt work there
      var gmarkers = [];
      var htmls = [];
      var i = 0;


// A function to create the marker and set up the event window

      function createMarker(point,name,html) {
        // FF 1.5 fix
        html = \'<div style="white-space:nowrap;">\' + html + \'</div>\';
        var marker = new GMarker(point);
        GEvent.addListener(marker, "click", function() {
          marker.openInfoWindowHtml(html);
        });
        // save the info we need to use later for the sidebar
        gmarkers[i] = marker;
        htmls[i] = html;
        // add a line to the sidebar html
        sidebar_html += \'<a href="javascript:myclick(\' + i + \')">\' + name + \'</a><br>\';
        i++;
        return marker;
      }


      // create the map
      var map = new GMap(document.getElementById("map"));
      map.addControl(new GLargeMapControl());
      map.addControl(new GMapTypeControl());
      map.centerAndZoom(new GPoint(-95.615534, 37.043358), '.$zoom.');

      // Read the data from example.xml
      var request = GXmlHttp.create();
      request.open("GET", "googlexml.php?id='.$this->map_ID.'", true);
      request.onreadystatechange = function() {
        if (request.readyState == 4) {
          var xmlDoc = request.responseXML;
          // obtain the array of markers and loop through it
          var markers = xmlDoc.documentElement.getElementsByTagName("marker");
          
          for (var i = 0; i < markers.length; i++) {
            // obtain the attribues of each marker
            var lat = parseFloat(markers[i].getAttribute("lat"));
            var lng = parseFloat(markers[i].getAttribute("lng"));
            var point = new GPoint(lng,lat);
            var html = markers[i].getAttribute("html");
            var label = markers[i].getAttribute("label");
            // create the marker
            var marker = createMarker(point,label,html);
            map.addOverlay(marker);
          }
          // put the assembled sidebar_html contents into the sidebar div
          document.getElementById("sidebar").innerHTML = sidebar_html;
        }
      }
      request.send(null);
    }

    else {
      alert("Sorry, the Google Maps API is not compatible with this browser");
    }
    // This Javascript is based on code provided by the
    // Blackpool Community Church Javascript Team
    // http://www.commchurch.freeserve.co.uk/   
    // http://www.econym.demon.co.uk/googlemaps/

    //]]>
    </script>';
		return $out;
	}	
}



?>
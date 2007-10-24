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
		'type'=>'us',
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
		'geo_field'=>'',
        'include_credit' => 0
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
	
		$R= $this->dbcon->CacheExecute($sql)or trigger_error("Error loading map info  ".$sql.$this->dbcon->ErrorMsg());
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
		$this->prop('type',$R);
		$this->P['table'] = $R->Fields("map_table");
		if ($R->Fields("type")) {
			if ($this->P['table'] == 'userdata') {
				$this->P['extra_sql'] .= ' and modin = '.$R->Fields("type");
			}
			if ($this->P['table'] == 'calendar') {
				if ($this->P['extra_sql']) {
					$this->P['extra_sql'] .= ' and caltype = '.$R->Fields("type");
				}
			}
		}
	}

	//function that create the range array
	function build_range() {
		$sql="select * from map_range where map_ID = ".$this->map_ID;
		$R= $this->dbcon->CacheExecute($sql)or trigger_error("Error getting range data in build_range function ".$sql.$this->dbcon->ErrorMsg());
		$x=0;
		while (!$R->EOF) {
			$this->Range[$x]['range'] = $R->Fields("range");
			$this->Range[$x]['color'] = $R->Fields("color");
			$x++;
			$R->MoveNext();
		}
	}			
	function build_points() {
        $extra_fields = "";
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
			$sql="select id, lcity as City, lstate as State, lzip as Zip".$extra_fields." from calendar where  publish=1 ".$this->P['extra_sql']; // and typeid =$type 
		} else {
			$sql="select id, City, State, Street, Zip".$extra_fields." from ".$this->P['table']." where publish=1 ". $this->P['extra_sql'] ;
		}
		
		$R= $this->dbcon->CacheExecute($sql)or trigger_error("Error getting city data in build_points function ".$sql.$this->dbcon->ErrorMsg());
		$x=0;
		while (!$R->EOF) {
			if ($this->P['geo_field']  ) {
				$location = $R->Fields($this->P['geo_field']);
				list($lat, $lng) = explode(",", $location);
			} else {
				$geo = new Geo($this->dbcon);
				$geo->City = $R->Fields("City");
				$geo->State = $R->Fields("State");
				$geo->city_lookup();
				$lat =$geo->lat;
				$lng =$geo->long;
				$location = $geo->lat.",".$geo->long;
			}
			

			if ($location != ',' and isset($location)) {
				$this->points[$x]['name'] = wordwrap( htmlspecialchars($R->Fields($this->P['label_field'])),30,htmlspecialchars( '<br>'));
				$this->points[$x]['loc'] = $location;
				$this->points[$x]['lat'] = $lat;
				$this->points[$x]['long'] = $lng;
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
		$S= $this->dbcon->CacheExecute($sql)or trigger_error("Error getting state list in functon build_count ".$sql.$this->dbcon->ErrorMsg());
		while (!$S->EOF) {
			if ($this->P['table'] == 'calendar') {
				$sql="select count(id) from calendar where lstate ='".$S->Fields("state")."' and publish=1 $extra"; // and typeid =$type 
			} else {
				$sql="select count(id) from ".$this->P['table']." where State ='".$S->Fields("state")."' and  publish=1 ". $this->P['extra_sql'] ;
			}
			
			$C= $this->dbcon->CacheExecute($sql)or trigger_error("Error getting state count in functon build_count Query:".$sql.$this->dbcon->ErrorMsg());
//			if ($S->Fields("state") =="CA") { die (count(id));}//die ($C[0][0]);
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



	function world_xml() {	
		header('Content-type: text/xml');

		$cache_key = __FILE__.'-'.__FUNCTION__.'-map_ID='.$this->map_ID;
		$cached_map = AMP_cache_get( $cache_key );

		if ($cached_map ) return $cached_map;

	
		if (!$this->points) {
			$this->build_points();
		}
	
		
        $out = "";
		$out .= '<?xml version="1.0" encoding="iso-8859-1"?>';
		$out .= '<countrydata>';
		$out .= '<state id="default_color"><color>'.$this->P['default_color'].'</color></state>';
		$out .= '<state id="background_color"><color>'.$this->P['background_color'].'</color></state>';
		$out .= '<state id="outline_color"><color>'.$this->P['outline_color'].'</color></state>';
		$out .= '<state id="default_point"><color>'.$this->P['default_point_color'].'</color><size>'.$this->P['default_point_size'].'</size><src>'.$this->P['default_point_src'].'</src></state>';
		$out .= '<state id="font_size"><data>'.$this->P['font_size'].'</data></state>';
		$out .= '<state id="state_info_icon"><src>'.$this->P['state_info_icon'].'</src></state>';
		$out .= '<state id="line_color"><color>'.$this->P['line_color'].'</color></state>';
		$out .= '<state id="arc_color"><color>'.$this->P['arc_color'].'</color></state>';
		$out .= '<state id="scale_points"><data>50</data></state>';	
	
		if ($this->points) {
			
			foreach($this->points as $p) {
				$out .= "\n<state id=\"point\">";
				$out .= '<name>' . $p['name'] . '</name>';
				$out .= '<loc>' . $p['loc'] .'</loc>';
                if ( isset( $this->P['opacity'])){
                    $out .= '<opacity>' . $this->P['opacity'] .'</opacity>';
                }
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
		$out .='
<state id="AF">
	<name>Afghanistan</name>
</state>
<state id="AL">
	<name>Albania</name>
</state>
<state id="AG">
	<name>Algeria</name>
</state>
<state id="AN">
	<name>Andorra</name>
</state>
<state id="AO">
	<name>Angola</name>
</state>
<state id="AC">
	<name>Antigua and Barbuda</name>
</state>
<state id="AR">
	<name>Argentina</name>
</state>
<state id="AM">
	<name>Armenia</name>
</state>
<state id="AA">
	<name>Aruba</name>
</state>
<state id="AS">
	<name>Australia</name>
</state>
<state id="AU">
	<name>Austria</name>
</state>
<state id="AJ">
	<name>Azerbaijan</name>
</state>
<state id="BF">
	<name>The Bahamas</name>
</state>
<state id="BA">
	<name>Bahrain</name>
</state>
<state id="FQ">
	<name>Baker Island</name>
</state>
<state id="BG">
	<name>Bangladesh</name>
</state>
<state id="BB">
	<name>Barbados</name>
</state>
<state id="BO">
	<name>Belarus</name>
</state>
<state id="BE">
	<name>Belgium</name>
</state>
<state id="BH">
	<name>Belize</name>
</state>
<state id="BN">
	<name>Benin</name>
</state>
<state id="BD">
	<name>Bermuda</name>
</state>
<state id="BT">
	<name>Bhutan</name>
</state>
<state id="BL">
	<name>Bolivia</name>
	<data>-17.00000,-65.00000</data>
</state>
<state id="BK">
	<name>Bosnia and Herzegovina</name>
</state>
<state id="BC">
	<name>Botswana</name>
</state>
<state id="BV">
	<name>Bouvet Island</name>
</state>
<state id="BR">
	<name>Brazil</name>
</state>
<state id="IO">
	<name>British Indian Ocean Territory</name>
</state>
<state id="VI">
	<name>British Virgin Islands</name>
</state>
<state id="BX">
	<name>Brunei</name>
</state>
<state id="BU">
	<name>Bulgaria</name>
</state>
<state id="UV">
	<name>Burkina Faso</name>
</state>
<state id="BY">
	<name>Burundi</name>
</state>
<state id="CB">
	<name>Cambodia</name>
</state>
<state id="CM">
	<name>Cameroon</name>
</state>
<state id="CA">
	<name>Canada</name>
</state>
<state id="CV">
	<name>Cape Verde</name>
</state>
<state id="CJ">
	<name>Cayman Islands</name>
</state>
<state id="CT">
	<name>Central African Republic</name>
</state>
<state id="CD">
	<name>Chad</name>
</state>
<state id="CI">
	<name>Chile</name>
</state>
<state id="CH">
	<name>China</name>
</state>
<state id="CO">
	<name>Colombia</name>
</state>
<state id="CN">
	<name>Comoros</name>
</state>
<state id="CF">
	<name>Congo</name>
</state>
<state id="CW">
	<name>Cook Islands</name>
</state>
<state id="CS">
	<name>Costa Rica</name>
</state>
<state id="IV">
	<name>Cote dIvoire</name>
</state>
<state id="HR">
	<name>Croatia</name>
</state>
<state id="CU">
	<name>Cuba</name>
</state>
<state id="CY">
	<name>Cyprus</name>
	<data>35.00000,33.00000</data>
</state>
<state id="EZ">
	<name>Czech Republic</name>
</state>
<state id="CG">
	<name>Democratic Republic of the Congo</name>
</state>
<state id="DA">
	<name>Denmark</name>
</state>
<state id="DJ">
	<name>Djibouti</name>
</state>
<state id="DO">
	<name>Dominica</name>
</state>
<state id="DR">
	<name>Dominican Republic</name>
</state>
<state id="EC">
	<name>Ecuador</name>
</state>
<state id="EG">
	<name>Egypt</name>
</state>
<state id="ES">
	<name>El Salvador</name>
</state>
<state id="EK">
	<name>Equatorial Guinea</name>
</state>
<state id="ER">
	<name>Eritrea</name>
</state>
<state id="EN">
	<name>Estonia</name>
</state>
<state id="ET">
	<name>Ethiopia</name>
</state>
<state id="FK">
	<name>Falkland Islands (Islas Malvinas)</name>
</state>
<state id="FO">
	<name>Faroe Islands</name>
</state>
<state id="FM">
	<name>Federated States of Micronesia</name>
</state>
<state id="FJ">
	<name>Fiji</name>
</state>
<state id="FI">
	<name>Finland</name>
</state>
<state id="FR">
	<name>France</name>
</state>
<state id="FG">
	<name>French Guiana</name>
</state>
<state id="FP">
	<name>French Polynesia</name>
</state>
<state id="GB">
	<name>Gabon</name>
</state>
<state id="GA">
	<name>The Gambia</name>
</state>
<state id="GG">
	<name>Georgia</name>
</state>
<state id="GM">
	<name>Germany</name>
</state>
<state id="GH">
	<name>Ghana</name>
</state>
<state id="GI">
	<name>Gibraltar</name>
</state>
<state id="GO">
	<name>Glorioso Islands</name>
</state>
<state id="GR">
	<name>Greece</name>
</state>
<state id="GL">
	<name>Greenland</name>
</state>
<state id="GJ">
	<name>Grenada</name>
</state>
<state id="GP">
	<name>Guadeloupe</name>
</state>
<state id="GQ">
	<name>Guam</name>
</state>
<state id="GT">
	<name>Guatemala</name>
</state>
<state id="GK">
	<name>Guernsey</name>
</state>
<state id="PU">
	<name>Guinea-Bissau</name>
</state>
<state id="GV">
	<name>Guinea</name>
</state>
<state id="GY">
	<name>Guyana</name>
</state>
<state id="HA">
	<name>Haiti</name>
</state>
<state id="HM">
	<name>Heard Island & McDonald Islands</name>
</state>
<state id="HO">
	<name>Honduras</name>
</state>
<state id="HU">
	<name>Hungary</name>
</state>
<state id="IC">
	<name>Iceland</name>
</state>
<state id="IN">
	<name>India</name>
</state>
<state id="IO">
	<name>British Indian Ocean Territory</name>
</state>
<state id="ID">
	<name>Indonesia</name>
</state>
<state id="IR">
	<name>Iran</name>
</state>
<state id="IZ">
	<name>Iraq</name>
</state>
<state id="EI">
	<name>Ireland</name>
</state>
<state id="IM">
	<name>Isle of Man</name>
</state>
<state id="IS">
	<name>Israel</name>
</state>
<state id="GZ">
	<name>Palestinian Authority</name>
</state>
<state id="IT">
	<name>Italy</name>
</state>
<state id="JM">
	<name>Jamaica</name>
</state>
<state id="JN">
	<name>Jan Mayen</name>
</state>
<state id="JA">
	<name>Japan</name>
</state>
<state id="DQ">
	<name>Jarvis Island</name>
</state>
<state id="JE">
	<name>Jersey</name>
</state>
<state id="JQ">
	<name>Johnston Atoll</name>
</state>
<state id="JO">
	<name>Jordan</name>
</state>
<state id="JU">
	<name>Juan De Nova Island</name>
</state>
<state id="KZ">
	<name>Kazakhstan</name>
</state>
<state id="KE">
	<name>Kenya</name>
</state>
<state id="KR">
	<name>Kiribati</name>
</state>
<state id="KS">
	<name>South Korea</name>
</state>
<state id="KU">
	<name>Kuwait</name>
</state>
<state id="KG">
	<name>Kyrgyzstan</name>
</state>
<state id="LA">
	<name>Laos</name>
</state>
<state id="LG">
	<name>Latvia</name>
</state>
<state id="LE">
	<name>Lebanon</name>
</state>
<state id="LT">
	<name>Lesotho</name>
</state>
<state id="LI">
	<name>Liberia</name>
</state>
<state id="LY">
	<name>Libya</name>
</state>
<state id="LS">
	<name>Liechtenstein</name>
</state>
<state id="LH">
	<name>Lithuania</name>
</state>
<state id="LU">
	<name>Luxembourg</name>
</state>
<state id="MC">
	<name>Macau</name>
</state>
<state id="MK">
	<name>Macedonia</name>
</state>
<state id="MA">
	<name>Madagascar</name>
</state>
<state id="MI">
	<name>Malawi</name>
</state>
<state id="MY">
	<name>Malaysia</name>
</state>
<state id="MV">
	<name>Maldives</name>
</state>
<state id="ML">
	<name>Mali</name>
</state>
<state id="MT">
	<name>Malta</name>
</state>
<state id="RM">
	<name>Marshall Islands</name>
</state>
<state id="MB">
	<name>Martinique</name>
</state>
<state id="MR">
	<name>Mauritania</name>
</state>
<state id="MP">
	<name>Mauritius</name>
</state>
<state id="MF">
	<name>Mayotte</name>
</state>
<state id="MX">
	<name>Mexico</name>
</state>
<state id="MQ">
	<name>Midway Islands</name>
</state>
<state id="MD">
	<name>Moldova</name>
</state>
<state id="MN">
	<name>Monaco</name>
</state>
<state id="MG">
	<name>Mongolia</name>
</state>
<state id="MH">
	<name>Montserrat</name>
</state>
<state id="MO">
	<name>Morocco</name>
</state>
<state id="MZ">
	<name>Mozambique</name>
</state>
<state id="BM">
	<name>Myanmar (Burma)</name>
</state>
<state id="WA">
	<name>Namibia</name>
</state>
<state id="NR">
	<name>Nauru</name>
</state>
<state id="NP">
	<name>Nepal</name>
</state>
<state id="NT">
	<name>Netherlands Antilles</name>
</state>
<state id="NL">
	<name>Netherlands</name>
</state>
<state id="NC">
	<name>New Caledonia</name>
</state>
<state id="NZ">
	<name>New Zealand</name>
</state>
<state id="NU">
	<name>Nicaragua</name>
</state>
<state id="NG">
	<name>Niger</name>
</state>
<state id="NI">
	<name>Nigeria</name>
</state>
<state id="NE">
	<name>Niue</name>
</state>
<state id="NF">
	<name>Norfolk Island</name>
</state>
<state id="KN">
	<name>North Korea</name>
</state>
<state id="CQ">
	<name>Northern Mariana Islands</name>
</state>
<state id="NO">
	<name>Norway</name>
</state>
<state id="MU">
	<name>Oman</name>
</state>
<state id="PS">
	<name>Pacific Islands (Palau)</name>
</state>
<state id="PK">
	<name>Pakistan</name>
</state>
<state id="PM">
	<name>Panama</name>
</state>
<state id="PP">
	<name>Papua New Guinea</name>
</state>
<state id="PA">
	<name>Paraguay</name>
</state>
<state id="PE">
	<name>Peru</name>
	<data>-10.00000,-76.00000</data>
</state>
<state id="RP">
	<name>Philippines</name>
</state>
<state id="PL">
	<name>Poland</name>
</state>
<state id="PO">
	<name>Portugal</name>
</state>
<state id="RQ">
	<name>Puerto Rico</name>
</state>
<state id="QA">
	<name>Qatar</name>
</state>
<state id="RE">
	<name>Reunion</name>
</state>
<state id="RO">
	<name>Romania</name>
</state>
<state id="RS">
	<name>Russia</name>
</state>
<state id="RW">
	<name>Rwanda</name>
</state>
<state id="SM">
	<name>San Marino</name>
</state>
<state id="TP">
	<name>Sao Tome and Principe</name>
</state>
<state id="SA">
	<name>Saudi Arabia</name>
</state>
<state id="SG">
	<name>Senegal</name>
</state>
<state id="SR">
	<name>Serbia</name>
</state>
<state id="MW">
	<name>Montenegro</name>
</state>
<state id="SE">
	<name>Seychelles</name>
</state>
<state id="SL">
	<name>Sierra Leone</name>
</state>
<state id="SN">
	<name>Singapore</name>
</state>
<state id="LO">
	<name>Slovakia</name>
</state>
<state id="SI">
	<name>Slovenia</name>
</state>
<state id="BP">
	<name>Solomon Islands</name>
</state>
<state id="SO">
	<name>Somalia</name>
</state>
<state id="SF">
	<name>South Africa</name>
</state>
<state id="SX">
	<name>South Georgia and the South Sandwich Is</name>
</state>
<state id="SP">
	<name>Spain</name>
</state>
<state id="PG">
	<name>Spratly Islands</name>
</state>
<state id="CE">
	<name>Sri Lanka</name>
</state>
<state id="SC">
	<name>St. Kitts and Nevis</name>
</state>
<state id="ST">
	<name>St. Lucia</name>
</state>
<state id="SB">
	<name>St. Pierre and Miquelon</name>
</state>
<state id="VC">
	<name>St. Vincent and the Grenadines</name>
</state>
<state id="SU">
	<name>Sudan</name>
</state>
<state id="NS">
	<name>Suriname</name>
</state>
<state id="SV">
	<name>Svalbard</name>
</state>
<state id="WZ">
	<name>Swaziland</name>
</state>
<state id="SW">
	<name>Sweden</name>
</state>
<state id="SZ">
	<name>Switzerland</name>
</state>
<state id="SY">
	<name>Syria</name>
</state>
<state id="TE">
	<name>East Timor</name>
</state>
<state id="TW">
	<name>Taiwan</name>
</state>
<state id="TI">
	<name>Tajikistan</name>
</state>
<state id="TZ">
	<name>United Republic of Tanzania</name>
</state>
<state id="TH">
	<name>Thailand</name>
</state>
<state id="TO">
	<name>Togo</name>
</state>
<state id="TL">
	<name>Tokelau</name>
</state>
<state id="TN">
	<name>Tonga</name>
</state>
<state id="TD">
	<name>Trinidad and Tobago</name>
</state>
<state id="TS">
	<name>Tunisia</name>
</state>
<state id="TU">
	<name>Turkey</name>
</state>
<state id="TX">
	<name>Turkmenistan</name>
</state>
<state id="TK">
	<name>Turks and Caicos Islands</name>
</state>
<state id="TV">
	<name>Tuvalu</name>
</state>
<state id="UG">
	<name>Uganda</name>
</state>
<state id="UP">
	<name>Ukraine</name>
</state>
<state id="TC">
	<name>United Arab Emirates</name>
</state>
<state id="UK">
	<name>United Kingdom</name>
</state>
<state id="US">
	<name>United States</name>
</state>
<state id="UY">
	<name>Uruguay</name>
</state>
<state id="UZ">
	<name>Uzbekistan</name>
</state>
<state id="NH">
	<name>Vanuatu</name>
</state>
<state id="VE">
	<name>Venezuela</name>
</state>
<state id="VM">
	<name>Vietnam</name>
</state>
<state id="WQ">
	<name>Wake Island</name>
</state>
<state id="WI">
	<name>Western Sahara</name>
</state>
<state id="WS">
	<name>Western Samoa</name>
</state>
<state id="YM">
	<name>Yemen</name>
</state>
<state id="ZA">
	<name>Zambia</name>
</state>
<state id="ZI">
	<name>Zimbabwe</name>
</state>';

		}
		$out .=  '</<countrydata>';

		AMP_cache_set( $cache_key, $out );
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
				$out .= '&lt;a href=&quot;'.htmlentities( $this->P['point_url']). $p['id'].'&quot;&gt;';	
				$out .= htmlentities( $p['name']).'&lt;/a&gt;';
				$out .= "&lt;br&gt;".$p['City'].", ".$p['State'];
				$out .= '" />';
			}
		}
		$out .= "</markers>";
		return $out;
	}
	 


	
	
	function us_xml() {	
	
		
		if ($this->P['type']]== 'world'){
			return $this->world_xml();
			end
		}
		
		header('Content-type: text/xml');

		$cache_key = __FILE__.'-'.__FUNCTION__.'-map_ID='.$this->map_ID;
		$cached_map = AMP_cache_get( $cache_key );

		if ($cached_map ) return $cached_map;

		if (!$this->Range) {
			//$this->build_range();
		}
		if (!$this->points) {
			$this->build_points();
		}
		if (!$this->Count) {
			$this->build_count();
		}
		
        $out = "";
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
                if ( isset( $this->P['opacity'])){
                    $out .= '<opacity>' . $this->P['opacity'] .'</opacity>';
                }
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

			if (isset( $v['hover']) && $v['hover']) {
				$out .= '<hover>' .  $v['hover'] .'</hover>';
			}
			if ($this->P['state_url'] && isset( $v['data']) && ($v['data'] >= 1)) {
				$out .= '<url>' .$this->P['state_url']. $st .'</url>';
			}
			$out .=  '</state>';
		}
		$out .=  '</us_states>';

		AMP_cache_set( $cache_key, $out );
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
				$out .= '&lt;a href=&quot;'.htmlentities( $this->P['point_url']). $p['id'].'&quot;&gt;';	
				$out .= htmlentities( $p['name']).'&lt;/a&gt;';
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
        if ( ( $this->P['include_credit'])) {
            $html .= "<p><font size ='-4'> Powered by <a href='http://backspace.com/mapapp/'>DIY Map</a></font></p>";
        }
		
		return $html;
	}
	
	function google_map($width='500', $height='400', $zoom='14',$center_lat='37.043358', $center_lng='-95.615534') {

		$out .= '<script src="http://maps.google.com/maps?file=api&v=1&key='.GOOGLE_API_KEY.'" type="text/javascript"></script>';
		$out .= '<div id="map" style="width: '.$width.'px; height: '.$height.'px"></div>';
$start = '
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
	  // arrays to hold variants of the info window html with get direction forms open
      var to_htmls = [];
      var from_htmls = [];
';
$basic_marker = '
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
      } ';
$direction_marker = "
      // A function to create the marker and set up the event window
      function createMarker(point,name,html) {
        var marker = new GMarker(point);

        // The info window version with the \"to here\" form open
        to_htmls[i] = html + '<br>Directions: <b>To here</b> - <a href=\"javascript:fromhere(' + i + ')\">From here</a>' +
           '<br>Start address:<form action=\"http://maps.google.com/maps\" method=\"get\" target=\"_blank\">' +
           '<input type=\"text\" SIZE=40 MAXLENGTH=40 name=\"saddr\" id=\"saddr\" value=\"\" /><br>' +
           '<INPUT value=\"Get Directions\" TYPE=\"SUBMIT\">' +
           '<input type=\"hidden\" name=\"daddr\" value=\"' +
           point.y + ',' + point.x + \"(\" + name + \")\" + '\"/>';
        // The info window version with the \"to here\" form open
        from_htmls[i] = html + '<br>Directions: <a href=\"javascript:tohere(' + i + ')\">To here</a> - <b>From here</b>' +
           '<br>End address:<form action=\"http://maps.google.com/maps\" method=\"get\"\" target=\"_blank\">' +
           '<input type=\"text\" SIZE=40 MAXLENGTH=40 name=\"daddr\" id=\"daddr\" value=\"\" /><br>' +
           '<INPUT value=\"Get Directions\" TYPE=\"SUBMIT\">' +
           '<input type=\"hidden\" name=\"saddr\" value=\"' +
           point.y + ',' + point.x + \"(\" + name + \")\" + '\"/>';
        // The inactive version of the direction info
        html = html + '<br>Directions: <a href=\"javascript:tohere('+i+')\">To here</a> - <a href=\"javascript:fromhere('+i+')\">From here</a>';

        GEvent.addListener(marker, \"click\", function() {
          marker.openInfoWindowHtml('<div style=\"white-space:nowrap;\">'+html+'</div>');
        });
        // save the info we need to use later for the sidebar
        gmarkers[i] = marker;
        htmls[i] = html;
        // add a line to the sidebar html
        sidebar_html += '<a href=\"javascript:myclick(' + i + ')\">' + name + '</a><br>';
        i++;
        return marker;
      }


      // This function picks up the click and opens the corresponding info window
      function myclick(i) {
        gmarkers[i].openInfoWindowHtml('<div style=\"white-space:nowrap;\">'+htmls[i]+'</div>');
      }

      // functions that open the directions forms
      function tohere(i) {
        gmarkers[i].openInfoWindowHtml('<div style=\"white-space:nowrap;\">'+ to_htmls[i]+'</div>');
      }
      function fromhere(i) {
        gmarkers[i].openInfoWindowHtml('<div style=\"white-space:nowrap;\">'+ from_htmls[i] +'</div>');
      }
";	 
	  


 $map= '     // create the map
      var map = new GMap(document.getElementById("map"));
      map.addControl(new GLargeMapControl());
      map.addControl(new GMapTypeControl());
      map.centerAndZoom(new GPoint('.$center_lng.', '.$center_lat.'), '.$zoom.');

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
		return $out.$start.$direction_marker.$map;
	}	
}



?>

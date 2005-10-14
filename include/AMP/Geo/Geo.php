<?php

require_once "HTTP/Request.php";
require_once "XML/Unserializer.php";

class rdfDocument
{
	var $channel;
	var $geo;

	function getItems($amount)
	{
		return array_splice($this->geo,0,$amount);
	}
}


Class Geo {
	var $dbcon;
	var $Street;
	var $City;
	var $State;
	var $Zip;
	var $lat;
	var $long;
	
	var $city_fulltext = false;
	
	function Geo(&$dbcon,$Street=NULL,$City=NULL,$State=NULL,$Zip=NULL, $city_fulltext = false) {
		$this->dbcon =& $dbcon;
		$this->Street =$Street;
		$this->City =$City;
		$this->State =$State;
		$this->Zip =$Zip;
		$this->city_fulltext = $city_fulltext;

		if  ( isset($this->Street) ) {
            if  ( (isset($this->City) && $this->City && isset($this->State) && $this->State ) 
                or (isset($this->Zip) && $this->Zip)) {
                $this->geocoder_getdata();
            }
        }
		if  (  (!isset($this->lat)) && isset($this->City) && $this->City 
            && isset($this->State) && $this->State)  {
			$this->city_lookup();
		} 
        if ((!isset($this->lat)) && isset($this->Zip) && $this->Zip) {
            $this->zip_lookup();
        }
	}
	
	function google_getdata(){
		$req =& new HTTP_Request( "http://google.com" );
		$req->setMethod( HTTP_REQUEST_METHOD_GET );
		
		// assumes $address is *not* urlencoded
		$geoaddress = $this->Street.", ".$this->City.", ".$this->State.", ".$this->Zip; 			

		$req->addQueryString( 'address', $geoaddress );
		$req->addHeader( "User-Agent", "RadicalDesigns/AMP" );
		
		if ( !PEAR::isError( $req->sendRequest() ) ) {
			$result = $req->getResponseBody();
		} else {
			// failed
				$result = $req->getResponseHeader();
			//print "there was an error...";
			
		}
	
			// echo '<pre><br>geocode result<br>';var_dump($result);echo '</pre><br>';
		
	
		$result = '<?xml version="1.0" encoding="iso-8859-1"?>' . "\n" . $result . "\n";
		$xmlparser = new XML_Unserializer();
		$parse_result = $xmlparser->unserialize( $result, false );
		/*
		  if ( PEAR::isError( $parse_result )) {
			print 'yah<BR>';
		} else {
			print 'nah<BR>';
		}
		*/
	
		$data = $xmlparser->getUnserializedData();
		if (array_key_exists('geo:lat', $data['geo:Point'])) {
			//return array( $data['geo:Point']['geo:lat'], $data['geo:Point']['geo:long'],$result );
			$this->lat = $data['geo:Point']['geo:lat'];
			$this->long = $data['geo:Point']['geo:long'];
		} else {
			#print_r (($data));
			//return array( $data['geo:Point'][0]['geo:lat'], $data['geo:Point'][0]['geo:long'],$result );
		}
	
	
	}
	
	function geocoder_getdata() {
	
		$req =& new HTTP_Request( "http://rpc.geocoder.us/service/rest" );
		$req->setMethod( HTTP_REQUEST_METHOD_GET );
		
		// assumes $address is *not* urlencoded
		$geoaddress = $this->Street.", ".$this->City.", ".$this->State.", ".$this->Zip; 			

		$req->addQueryString( 'address', $geoaddress );
		$req->addHeader( "User-Agent", "RadicalDesigns/AMP" );
		
		if ( !PEAR::isError( $req->sendRequest() ) ) {
			$result = $req->getResponseBody();
		} else {
			// failed
				$result = $req->getResponseHeader();
			//print "there was an error...";
			
		}
	
			// echo '<pre><br>geocode result<br>';var_dump($result);echo '</pre><br>';
		
	
		$result = '<?xml version="1.0" encoding="iso-8859-1"?>' . "\n" . $result . "\n";
		$xmlparser = new XML_Unserializer();
		$parse_result = $xmlparser->unserialize( $result, false );
		/*
		  if ( PEAR::isError( $parse_result )) {
			print 'yah<BR>';
		} else {
			print 'nah<BR>';
		}
		*/
	
		$data = $xmlparser->getUnserializedData();
		if (array_key_exists('geo:lat', $data['geo:Point'])) {
			//return array( $data['geo:Point']['geo:lat'], $data['geo:Point']['geo:long'],$result );
			$this->lat = $data['geo:Point']['geo:lat'];
			$this->long = $data['geo:Point']['geo:long'];
		} else {
			#print_r (($data));
			//return array( $data['geo:Point'][0]['geo:lat'], $data['geo:Point'][0]['geo:long'],$result );
		}
	}
	
	function city_lookup() {
		$sql = "select latitude,longitude from zipcodes where city = ".$this->dbcon->qstr($this->City)." and  state = ".$this->dbcon->qstr($this->State); 
		$R= $this->dbcon->CacheExecute($sql)or DIE("Error getting location list in functon get_latlong ".$sql.$this->dbcon->ErrorMsg());

		if ( $this->city_fulltext && !(($R->Fields("latitude")) && ($R->Fields("longitude"))) ){
			$sql = "SELECT latitude, longitude from zipcodes WHERE MATCH (city) AGAINST (".$this->dbcon->qstr($this->City).") AND state = ".$this->dbcon->qstr($this->State); 
			$R= $this->dbcon->CacheExecute($sql)or DIE("Error getting location list in functon get_latlong ".$sql.$this->dbcon->ErrorMsg());
		}

		if ( ($R->Fields("latitude")) && ($R->Fields("longitude")) ){
			$this->lat = $R->Fields("latitude") ;
			$this->long = $R->Fields("longitude");			
		}
	}
	function zip_lookup() {
		$sql = "select latitude,longitude from zipcodes where zip = ".$this->dbcon->qstr($this->Zip);
		if ($R=$this->dbcon->CacheGetRow($sql)) {
			$this->lat = $R["latitude"] ;
			$this->long = $R["longitude"];			
		}
	}
	
//a function that retruns an array of zip codes with a radius of mise from a set zip code
	function zip_radius($radius) {	
		$zip_query='SELECT zip, zip,latitude,longitude, (ACOS((SIN(' . $this->lat . '/57.2958) * SIN(latitude/57.2958)) + (COS(' . $this->lat . '/57.2958) * COS(latitude/57.2958) * COS(longitude/57.2958 - ' . $this->long. '/57.2958)))) * 3963 AS distance FROM zipcodes WHERE (latitude >= ' . $this->lat . ' - (' . $radius . '/111)) AND (latitude <= ' . $this->lat . ' + (' . $radius . '/111)) AND (longitude >= ' . $this->long . '- (' . $radius . '/111)) AND (longitude <= ' . $this->long. '+ (' . $radius . '/111)) ORDER BY distance ASC;';
        if ( $zipset=$this->dbcon->CacheGetAssoc($zip_query) ) {
            return $zipset; 
        } else {
            return false;
        }
	}

//a function the returns links to othe maping programs
	function map_links() {
		$link['MapQuest'] = "http://www.mapquest.com/maps/map.adp?address=".$this->Street."&city=".$this->City."&state=".$this->State."&zipcode=".$this->Zip."&cid=lfmaplink";
	
		$link['Google Maps'] = "http://maps.google.com/maps?q=".$this->Street." ".$this->City." ".$this->State." ".$this->Zip;
	
	// the url is encoded so this only retuns the city, I guess we could query it get the url (or have a db field)
		$link['Yahoo Maps'] = "maps.yahoo.com/maps_result?csz=".$this->City."%2C+".$this->State."+".$this->Zip."&country=us&cat=&trf=0";
		
		return $link;
	}

	function geo_showmap() {		
		global $Web_url;
		$mapcode = "mapgen?lon=" .  $this->long . "&lat=" .  $this->lat . "&wid=0.035&ht=0.035&iht=320&iwd=320&mark=" .  $this->long . "," .  $this->lat . ",redpin";

?>  
 <script language="JavaScript">
    function getOffsets (evt) {
	var target = evt.target;
	if (typeof target.offsetLeft == 'undefined') {
	    target = target.parentNode;
	}
	var pageCoords = getPageCoords(target);
	var eventCoords = { 
	    x: window.pageXOffset + evt.clientX,
	    y: window.pageYOffset + evt.clientY
	};
	var offsets = {
	    offsetX: eventCoords.x - pageCoords.x,
	    offsetY: eventCoords.y - pageCoords.y
	}
	return offsets;
    }

    function getPageCoords (element) {
	var coords = {x : 0, y : 0};
	while (element) {
	    coords.x += element.offsetLeft;
	    coords.y += element.offsetTop;
	    element = element.offsetParent;
	}
	return coords;
    }

    var mapCenterLat = <?php echo $this->lat ; ?>, mapCenterLon = <?php echo $this->long ;?>,
	mapPixels = 320, mapWid = 0.035;

    var mapLat = mapCenterLat, mapLon = mapCenterLon, 
	mapZoom = 3, mapMaxWid = 50, mapMinWid = .001;

    function mapZoomOut (event) {
	if (mapWid * mapZoom < mapMaxWid) 
	    mapWid *= mapZoom;
	else
	    mapWid = mapMaxWid;
	mapRedraw();
	return false;
    }

    function mapZoomIn (event) {
	if (mapWid / mapZoom > mapMinWid)
	    mapWid /= mapZoom;
	else
	    mapWid = mapMinWid;
	mapRedraw();
	return false;
    }

    function mapCenter (event) {
	var perPixel = mapWid / mapPixels,
	    mapCenter = mapPixels / 2;
	var off = getOffsets(event);
	var x = off.offsetX,
            y = off.offsetY;
	mapLat += (mapCenter - y) * perPixel;
	mapLon += (x - mapCenter) * perPixel / 
		    Math.cos(mapLat * Math.PI / 180);
	mapRedraw();
	return false;
    }

    function mapRecenter (event) {
	mapLat = mapCenterLat;
	mapLon = mapCenterLon;
	mapRedraw();
	return false;
    }
	
    function mapRedraw () {
	document.mapLoading.src = "<?php echo $Web_url ;?>system/images/red-dot.png";
	document.mapImage.src = 
	    "http://tiger.census.gov/cgi-bin/mapgen?" +
		"lon=" + mapLon + "&lat=" + mapLat + 
		"&wid=" + mapWid + "&ht="  + mapWid +
		"&iht=" + mapPixels + "&iwd=" + mapPixels +
		"&mark=" + mapCenterLon + "," + mapCenterLat + ",redpin";
    }
</script> 
<table border="0" cellspacing="0" cellpadding="20" align="center">
  <tr>
    <td width =320 ><div onClick="return mapCenter(event)"><img
	name="mapImage" src="http://tiger.census.gov/cgi-bin/<?php echo $mapcode ; ?>"
	width="320" height="320" border="2"
	style="cursor: crosshair"
	onLoad="document.mapLoading.src = "<?php echo $Web_url ;?>system/images/green-dot.png";" /></div></td>
    <td valign="top"><a href="" onClick="return mapZoomIn()"><img
	    src="<?php echo $Web_url ;?>system/images/zoom-in.png" width="32" height="32" border="0" /></a>
	    <br>
	    <br >

	<a href="" onClick="return mapZoomOut()"><img
	    src="<?php echo $Web_url ;?>system/images/zoom-out.png" width="32" height="32" border="0" /></a>
	    <br><br>
	<a href="" onClick="return mapRecenter()"><img
	    src="<?php echo $Web_url ;?>system/images/recenter.png" width="32" height="32" border="0" /></a>
    <br><br>
	<img name="mapLoading"
	    src="<?php echo $Web_url ;?>system/images/red-dot.png" width="32" height="32" border="0" /></td>
  </tr>
</table> <?php
 
	}


}


?>

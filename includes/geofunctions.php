<?php

require_once "HTTP/Request.php";
require_once "XML/Unserializer.php";

geo_getdata( "3212 24th St., San Francisco, CA" );

function geo_getdata($address) {

	$req =& new HTTP_Request( "http://rpc.geocoder.us/service/rest" );
	$req->setMethod( HTTP_REQUEST_METHOD_GET );
	
	// assumes $address is *not* urlencoded
	$req->addQueryString( 'address', $address );
	$req->addHeader( "User-Agent", "RadicalDesigns/AMP" );
	
	if ( !PEAR::isError( $req->sendRequest() ) ) {
		// failed
        print "there was an error...";
		$result = $req->getResponseBody();
	} else {
		$result = $req->getResponseBody();
	}

    $xmlparse = new XML_Unserializer;
    $xmlparse->unserialize( $result );

print "<pre>";
print_r( $result ) . "\n";
    print_r( $xmlparse->getUnserializedData() );
print "</pre>";

}
		
		
function geo_showmap($map_lat,$map_long) {		
	global $Web_url;
	$mapcode = "mapgen?lon=" .  $map_long . "&lat=" .  $map_lat . "&wid=0.035&ht=0.035&iht=320&iwd=320&mark=" .  $map_long . "," .  $map_lat . ",redpin";

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

    var mapCenterLat = <?php echo $map_lat ;?>, mapCenterLon = <?php echo $map_long ;?>,
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
	onLoad="document.mapLoading.src = '<?php echo $Web_url ;?>system/images/green-dot.png';" /></div></td>
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

 ?>
  

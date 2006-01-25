<?php

# function that gets the lat/lng of an address or location

require_once("Connections/freedomrising.php");
require_once("header.php");
require_once("AMP/Geo/Geo.php");
set_time_limit(0);



function get_geo($modin,$geo_field=NULL,$update=NULL,$limit='1000',$offset='0'){
	global $dbcon;

	if ($update) {
		$up_sql = ' and '.$geo_field.' != "" ';
	}
	
	$sql = "select * from userdata where modin =".$modin.$up_sql." limit $offset, $limit";
	$R= $dbcon->Execute($sql)or DIE("Error getting udm data ".$sql.$dbcon->ErrorMsg());
			$t= 0;
			$x= 0;
	while (!$R->EOF) {	
		$t++;
		$geo = new Geo($dbcon);
		
		if ( $R->Fields("Street")  ) {
			$geo->City =  $R->Fields("City");
			$geo->State =  $R->Fields("State");
			$geo->Street = $R->Fields("Street");
			$geo->Zip = $R->Fields("Zip");
			$geo->geocoder_getdata();
		}
		if ( ($geo->lat) && ($geo->long) ){
			$sql = "update userdata set ". $geo_field ." = '".$geo->lat.",".$geo->long."' where id = " . $R->Field("id");
			$dbcon->Execute($sql)or DIE("Error updating udm ".$sql.$dbcon->ErrorMsg());

			echo $R->Fields("id").": ".$geo->lat.$geo->long."<br>";
			$x++;
		} else {
			$html .= "<a href = 'modinput4_view.php?uid=".$R->Fields("id")."&modin=".$modin."'>".$R->Fields("Street")." ".$R->Fields("City")." ".$R->Fields("State")." ".$R->Fields("Zip")." </a><br>";
		}
		$R->MoveNext();	
	}
	$out = "Found $x address of $t <br><br>Failed Addresses<br> $html";
	return $out;
}



?>
<form action="geo_batch.php" method="post">
<input type="hidden" name="go" value="1">
UDM: <input type="text" name="modin"><br>
Limit: <input type="text" name="limit"><br>
Offset: <input type="text" name="offset"><br>
Override Past Data:<input type="checkbox" name="update" value="1"><br>
Update Field:<input type="text" name="geo_field"><br>
<input type="submit">

</form>
<?php
if ($_REQUEST['go']) {
	echo get_geo($_REQUEST['modin'],$_REQUEST['geo_field'],$_REQUEST['update'],$_REQUEST['limit'],$_REQUEST['offset']);
}
require_once("header.php");

?>
<?php

# function that gets the lat/lng of an address or location

require_once("Connections/freedomrising.php");
require_once("header.php");
require_once("AMP/Geo/Geo.php");
$geo_field = 'custom40';
$sql = "select * from userdata where modin =".$_REQUEST['modin']." and ".$geo_field." != '' ";
$R= $dbcon->Execute($sql)or DIE("Error getting udm data ".$sql.$dbcon->ErrorMsg());
		
while (!$R->EOF) {	
	$t++;
	$geo = new Geo($dbcon);
	$geo->City =  $R->Fields("City");
	$geo->State =  $R->Fields("State")
	$geo->Street = $R->Fields("Street");
	$geo->Zip = $R->Fields("Zip");
	$geo->geocoder_getdata();
	
	if ( ($geo->lat) && ($geo->lng) ){
		//$sql = "update userdata set ". $geo_field ." = '".$geo->lat.",".$geo->lng."' where id = " . $R->Field("id");
		echo $R->Field("id").": ".$geo->lat.$geo->lng."<br>";
		$x++;
	} else {
		$html .= "<a href = 'modinput4_view.php?uid=".."&modin=".$_REQUEST['modin']."'>".$R->Fields("Street")." ".$R->Fields("City")." ".$R->Fields("State")." ".$R->Fields("Zip")." </a><br>";
	}
$R->MoveNext();	
}
$out = "Found $x address of $t <br><br>Failed Addresses<br> $html";
echo $out;
require_once("header.php");

?>
<?php

# function that gets the lat/lng of an address or location

require_once("Connections/freedomrising.php");
require_once("header.php");
require_once("AMP/Geo/Geo.php");
$geo_field = 'custom40';
$sql = "select * from userdata where modin =".$_REQUEST['modin']. " limit 10";
$R= $dbcon->Execute($sql)or DIE("Error getting udm data ".$sql.$dbcon->ErrorMsg());
		$t= 0;
		$x= 0;
while (!$R->EOF) {	
	$t++;
	$geo = new Geo($dbcon);
	
	if ( $R->Fields("Street")  ) {
		echo $R->Fields("Street");
		$geo->City =  $R->Fields("City");
		$geo->State =  $R->Fields("State");
		$geo->Street = $R->Fields("Street");
		$geo->Zip = $R->Fields("Zip");
		$geo->geocoder_getdata();
	}
	if ( ($geo->lat) && ($geo->long) ){
		//$sql = "update userdata set ". $geo_field ." = '".$geo->lat.",".$geo->lng."' where id = " . $R->Field("id");
		echo $R->Fields("id").": ".$geo->lat.$geo->long."<br>";
		$x++;
	} else {
		$html .= "<a href = 'modinput4_view.php?uid=".$R->Fields("id")."&modin=".$_REQUEST['modin']."'>".$R->Fields("Street")." ".$R->Fields("City")." ".$R->Fields("State")." ".$R->Fields("Zip")." </a><br>";
	}
	$R->MoveNext();	
}
$out = "Found $x address of $t <br><br>Failed Addresses<br> $html";
echo $out;
require_once("header.php");

?>
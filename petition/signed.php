<?php

//  phpetition v0.3, An easy to use PHP/MySQL Petition Script
//  Copyright (C) 2001,  Mike Gifford, http://openconcept.ca
//
//  This script is free software; you can redistribute it and/or
//  modify it under the terms of the GNU General Public License
//  as published by the Free Software Foundation; either version 2
//  of the License, or (at your option) any later version.
//
//  This program is distributed in the hope that it will be useful,
//  but WITHOUT ANY WARRANTY; without even the implied warranty of
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//  GNU General Public License http://www.gnu.org/copyleft/ for more details. 
//
//  If you distribute this code, please maintain this header.
//  Links are always appreciated!
//
// This file is used to display confirmed & public signatures

require_once("config.php");
require_once("functions.php");



progressBox();

include_once ("petitiontext.php"); 

echo "<a style=\"text-decoration:none\" name=\"namelist\"></name>";
echo "<h3>$concerned_individuals</h3>";
echo "<table border=0 width=\"100%\" cellspacing=0 cellpadding=0><tr>";
echo "<td align=left>";

$end = ($start+$many);
if(isset($start) && isset($many)){
	if ($current_sigs < $end) {
		$end=$current_sigs;
	}
	echo "<b>Most Recent: ".$start." - ". $end ."</b>&nbsp;&nbsp;";
} else {
	$start=1;
	$many=50;
	if ($current_sigs < $many) {
		$many=$current_sigs;
	}
	echo "<b>Most Recent: " . $start . " - " . $many . "</b>&nbsp;&nbsp;";
}

echo "</td>";

echo "<td align=right>";


if(!$start){
	$start=0;
} else {
	$start--;
}
if(!$many){
	$many=50;
}
if(!$orderby){
	$orderby="ID";
}
?>

	<table border=0 cellspacing=0 cellpadding=0><tr>
	<td>
			<?php 
			$beginning=($start-$many );
			if (($beginning) > 0)  { 
				echo "<a href=\"signed.php?id=$id&lang=$lang&start=" . $beginning . "&many=50&orderby=$orderby&logic=$logic#namelist\">$previous</a>"; 
			}
			?>
	</td><td>
		<?php 
		if ($beginning > 0 && $current_sigs >= ($start+$many))  { 
			echo " - ";
		}
		?>
	</td><td>
			<?php 
			if ( $current_sigs >= ($start+$many))  { 
			 	echo "<a href=\"signed.php?id=$id&lang=$lang&start=" . ($start+$many ) . "&many=50&orderby=$orderby&logic=$logic#namelist\">$next</a>"; 
			 }
			?>
	</td>
	</tr></table>
	
</td>
</tr>
</table>

<table align="center" width="100%" cellpadding="4" cellspacing=0 border=0>

<tr bgcolor="<?php echo $boxBGcolor; ?>">

<td width="1%" align="center"><b><a href="signed.php?id=<?php echo $id ?>&lang=<?php echo $lang; ?>&start=<?php echo $start; ?>&many=<?php echo $many ?>&orderby=ID&logic=<?php echo switchLogic($GLOBALS["logic"]); ?>#namelist">#</a></b></td>

<td align=left><b><a href="signed.php?id=<?php echo $id ?>&lang=<?php echo $lang; ?>&start=<?php echo $start; ?>&many=<?php echo $many ?>&orderby=LastName&logic=<?php echo switchLogic($GLOBALS["logic"]); ?>#namelist"><?php echo $name; ?></a></b></td>

<td align=left><b><a href="signed.php?id=<?php echo $id ?>&lang=<?php echo $lang; ?>&start=<?php echo $start; ?>&many=<?php echo $many ?>&orderby=Organization&logic=<?php echo switchLogic($GLOBALS["logic"]); ?>#namelist"><?php echo $group_affiliation; ?></a></b></td>

<?php echo "<td align=left><b><a href=\"signed.php?id=$id&lang=$lang&start=$start&many=$many&orderby=City&logic=" . switchLogic($GLOBALS["logic"]) . "#namelist\">$city</a> <a href=\"signed.php?id=$id&lang=$lang&start=$start&many=$many&orderby=State&logic=" . switchLogic($GLOBALS["logic"]) . "#namelist\">$state</a></b></td>";

// echo "<td align=left><b><a href=\"signed.php?lang=$lang&start=$start&many=$many&orderby=Country&logic=" . switchLogic($GLOBALS["logic"]) . "#namelist\">$country</a></b></td>";
?>
<td align=left><b><a href="signed.php?id=<?php echo $id ?>&lang=<?php echo $lang; ?>&start=<?php echo $start; ?>&many=<?php echo $many ?>&orderby=Comments&logic=<?php echo switchLogic($GLOBALS["logic"]); ?>#namelist"><?php echo $comments; ?></a></b></td>



</tr>

<?php	
$get_sites=mysql_query("SELECT phpetition.*, states.state FROM phpetition left join states on phpetition.State=states.id WHERE phpetition.Public='on' and phpetition.petid=$id AND phpetition.confirmDate IS NOT NULL ORDER BY  phpetition." . $orderby . " " . switchLogic($GLOBALS["logic"])  . " LIMIT $start,$many",$db);

while($get_rows=mysql_fetch_array($get_sites)){
	$ID=$get_rows[ID];
	$Name = $get_rows[FirstName] . " " . $get_rows[LastName];
	$Organization = $get_rows[Organization];
	$City = $get_rows[City];
	$State = $get_rows[state];
	$Country=$get_rows[Country];
	$Comments=$get_rows[Comments];
	$cc++;
	$cell_color = "white";
	$cc % 2  ? 0 : $cell_color = $lightBoxColor;

echo "\n<tr bgcolor=\"".$cell_color."\">";
echo "<td valign=\"top\" align=\"center\"><small>&nbsp;".$ID."</small></td>";
echo "<td valign=\"top\" align=\"left\"><small>".stripslashes($Name)."</small></td>";
echo "<td valign=\"top\" align=\"left\"><small>".stripslashes($Organization)."&nbsp;</small></td>";
echo "<td valign=\"top\" align=\"left\"><small>".stripslashes($City)." ".stripslashes($State)."</small></td>";
// echo "<td valign=\"top\" align=\"left\"><small>".stripslashes($Country)."</small></td>";
echo "<td valign=\"top\" align=\"left\"><small>".stripslashes($Comments)."&nbsp;</small></td>";
echo "</tr>";
}		
?>

</td></tr></table>

<table border=0 cellspacing=0 cellpadding=0  width="100%"><tr><td>
	</td><td align=right>
	<table border=0 cellspacing=0 cellpadding=0><tr>
	<td>
			<?php 
			if ($beginning > 0)  { 
				echo "<a href=\"signed.php?id=$id&lang=$lang&start=" . $beginning . "&many=50&orderby=$orderby&logic=$logic#namelist\">$previous</a>"; 
			}
			?>
	</td><td>
		<?php 
		if ($beginning > 0 && $current_sigs >= ($start+$many))  { 
			echo " - ";
		}

echo "	</td><td>"; 

			if ( $current_sigs >= ($start+$many))  { 
			 	echo "<a href=\"signed.php?id=$id&lang=$lang&start=" . ($start+$many ) . "&many=50&orderby=$orderby&logic=$logic#namelist\">$next</a>"; 
			 }
		
echo "	</td></tr></table>"; 
echo "</td></tr></table>"; 



//if (file_exists("lang/support.$lang.txt")) readfile( "lang/support.$lang.txt" );  
//else include ("petitiontext.php");  

echo "<P><div align=left><strong>If you have not already done so, please <a href=\"index.php?lang=$lang&id=$id\">sign the appeal!</a></strong></div>"; 
include('alert.php');
echo "</td></tr></table>"; 

// }
echo "<P><br>"; 
include("$base_path"."footer.php");

echo "<!-- $script_display -->";
?>
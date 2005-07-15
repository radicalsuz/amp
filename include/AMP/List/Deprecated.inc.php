<?php

function listpage($listtitle,$listsql,$fieldsarray,$filename,$orderby=null,$sort=null,$extra=null,$extramap=NULL) {
	global $dbcon;
	if ($sort) { $orderby =" order by $sort asc ";}
	$query=$dbcon->Execute($listsql.$orderby) or DIE($dbcon->ErrorMsg());
	
	echo "<h2>".$listtitle."</h2>";
	echo "\n<div class='list_table'> \n	<table class='list_table'>\n		<tr class='intitle'> ";
	echo "\n			<td>&nbsp;</td>";
	foreach ($fieldsarray as $k=>$v) {
		echo "\n			<td><b><a href='".$_SERVER['PHP_SELF']."?action=list&sort=".$v."' class='intitle'>".$k."</a></b></td>";
	}
	
	if ($extra) {
		for ($i = 1; $i <= sizeof($extra); $i++) {
			echo "\n			<td>&nbsp;</td>";
		}
	}
	echo "\n		</tr>";
	$i= 0;
	while (!$query->EOF) {
		 $i++;
		 $bgcolor =($i % 2) ? "#D5D5D5" : "#E5E5E5";
	
		echo "\n		<tr bordercolor=\"#333333\" bgcolor=\"". $bgcolor."\" onMouseover=\"this.bgColor='#CCFFCC'\" onMouseout=\"this.bgColor='". $bgcolor ."'\"> "; 
		echo "\n			<td> <div align='center'><A HREF='".$filename."?id=".$query->Fields("id")."'><img src=\"images/edit.png\" alt=\"Edit\" width=\"16\" height=\"16\" border=0></A></div></td>";
		foreach ($fieldsarray as $k=>$v) {
			if ($v =='publish' ) {
				if ($query->Fields($v) == 1) { $live= "live";}
				else { $live= "draft";}
				echo "\n			<td> $live </td>";
			}
			else {
				echo "\n			<td> ".$query->Fields($v)." </td>";
			}
		}
		
		if ($extra) {
			
			
			foreach ($extra as $k=>$v) {
				$id=NULL;
				if (isset($extramap[$k]) && $extramap[$k] !== false) {
					$id= $extramap[$k];
				}else {
					$id= "id";
				}
				echo " \n			<td> <div align='right'>";
				echo "<A HREF='".$v.$query->Fields($id)."'>$k</A>";
				echo "</div></td>";
			}
			
		}
		echo "\n		</tr>";
	
		$query->MoveNext();
	}		
	
	echo "\n	</table>\n</div>\n<br>&nbsp;&nbsp;<a href=\"$filename\">Add new record</a> ";

}

function listpage_basic($listtitle,$fieldsarray,$filename) {
	echo "<h2>".$listtitle."</h2>";
	echo "<div class='list_table'> \n	<table class='list_table'> \n		<tr class='intitle' > ";
	$r=0;
	foreach ($fieldsarray[0] as $k=>$v) {
		echo "\n			<td><b><a href='".$_SERVER['PHP_SELF']."?action=list&sort=".$k."' class='intitle'>".$k."</a></b></td>";
		$f[$r]=$k;
		$r++;
	}
	echo "\n		</tr>";
	$i=0;
	for($x=0;$x<sizeof($fieldsarray);$x++){
		$i++;
		$bgcolor =($i % 2) ? "#D5D5D5" : "#E5E5E5";
		echo "\n		<tr bordercolor=\"#333333\" bgcolor=\"". $bgcolor."\" onMouseover=\"this.bgColor='#CCFFCC'\" onMouseout=\"this.bgColor='". $bgcolor ."'\"> "; 
		foreach ($f as $k=>$v) {
			echo "\n			<td> ".$fieldsarray[$x][$v]." </td>";
		}
		echo "\n		</tr>";
	}
	echo "\n	</table> \n</div>";
}


?>

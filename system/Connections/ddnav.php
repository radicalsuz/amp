<?php

function no_wh($text) {
	$t = str_replace('','&nbsp;',$text);
	echo '<nobr>'.$t.'</nobr>';
}

function nav_item($var=NULL) {
	if (!$var) {
			$output =",\"\",\"\",,,1 ";
	} else {
		$output = ",\"".$var['name']."\",\"".$var['link']."\",,,1 ";
	}
	return $output;
}

function nav_udm_item($id,$name) {
	$output = ",\"".$name."\",\"modinput4_data.php?modin=".$id."\",,,1 ";
	return $output;
}

function nav_udms() {
	global $dbcon;
	$R=$dbcon->Execute("select id, name from userdata_fields where id >= 50  order by name") or DIE($dbcon->ErrorMsg());
	while (!$R->EOF) {
		$output .= nav_udm_item($R->Fields("id"),$R->Fields("name"));
		$R->MoveNext();
	}
	return $output;
}

function nav_mod_type($type) {
	global $dbcon;
	$sql ="select i.id, i.name from userdata_fields i, modules m where i.id = m.userdatamodid and m.module_type =$type and m.userdatamodid > 49 order by name";
	$R=$dbcon->Execute($sql) or DIE('error in nov_mod_tpe sql= '.$sql.$dbcon->ErrorMsg());
	while (!$R->EOF) {
		$output .= nav_udm_item($R->Fields("id"),$R->Fields("name"));
		$R->MoveNext();
	}
	return $output;
}

function nav_mod_type_check($type) {
	global $dbcon;
	$C=$dbcon->Execute("select id from  modules where module_type =$type and userdatamodid > 49 ") or DIE($dbcon->ErrorMsg());
	if ($C->Fields("id")) {
		$R=$dbcon->Execute("select name from module_type where id =$type ") or DIE($dbcon->ErrorMsg());
		$output .= 	',"'.$R->Fields("name").'","show-menu='.$R->Fields("name").'",,,1';
		return $output;
	}
}

?>
<script language="JavaScript">
menunum=0;
menus=new Array();
_d=document;

function addmenu() {
		menunum++;
		menus[menunum]=menu;
}

function dumpmenus() {
		mt="<script language=javascript>";
		for(a=1;a<menus.length;a++) {
				mt+=" menu"+a+"=menus["+a+"];"
		}
		// weird string concat is for syntax highlighting in php.
		mt+='</sc'+'ript>';
		_d.write(mt);
}

////////////////////////////////////
// Editable properties START here //
////////////////////////////////////

// Special effect string for IE5.5 or above please visit http://www.milonic.co.uk/menu/filters_sample.php for more filters
effect = "Fade(duration=0.2);Alpha(style=0,opacity=88);Shadow(color='#777777', Direction=135, Strength=2)"


timegap=100			// The time delay for menus to remain visible
followspeed=5		// Follow Scrolling speed
followrate=40		// Follow Scrolling Rate
suboffset_top=10;	// Sub menu offset Top position 
suboffset_left=100;	// Sub menu offset Left position
closeOnClick = true

style1=[			// style1 is an array of properties. You can have as many property arrays as you need. This means that menus can have their own style.
"#006699",				// Mouse Off Font Color
"",			// Mouse Off Background Color
"#006699",			// Mouse On Font Color
"",			// Mouse On Background Color
"#006699",			// Menu Border Color 
12,					// Font Size in pixels
"normal",			// Font Style (italic or normal)
"bold",				// Font Weight (bold or normal)
"Arial",	// Font Name
8,					// Menu Item Padding
"",		// Sub Menu Image (Leave this blank if not needed)
,					// 3D Border & Separator bar
"",			// 3D High Color
"",			// 3D Low Color
"",			// Current Page Item Font Color (leave this blank to disable)
"",				// Current Page Item Background Color (leave this blank to disable)
"",		// Top Bar image (Leave this blank to disable)
"#006666",			// Menu Header Font Color (Leave blank if headers are not needed)
"",			// Menu Header Background Color (Leave blank if headers are not needed)
"#006699",				// Menu Item Separator Color
]

style2=[			// style1 is an array of properties. You can have as many property arrays as you need. This means that menus can have their own style.
"#006699",			// Mouse Off Font Color
"#dedede",			// Mouse Off Background Color
"",			// Mouse On Font Color
"#dedede",			// Mouse On Background Color
"#006699",			// Menu Border Color 
12,					// Font Size in pixels
"normal",			// Font Style (italic or normal)
"bold",				// Font Weight (bold or normal)
"Arial",	// Font Name
5,					// Menu Item Padding
"images/arrow.gif",		// Sub Menu Image (Leave this blank if not needed)
,					// 3D Border & Separator bar
"",			// 3D High Color
"",			// 3D Low Color
"",			// Current Page Item Font Color (leave this blank to disable)
"",				// Current Page Item Background Color (leave this blank to disable)
"",		// Top Bar image (Leave this blank to disable)
"ffffff",			// Menu Header Font Color (Leave blank if headers are not needed)
"",			// Menu Header Background Color (Leave blank if headers are not needed)
"black",				// Menu Item Separator Color
]

addmenu(menu=[		// This is the array that contains your menu properties and details
"mainmenu",			// Menu Name - This is needed in order for the menu to be called
68,					// Menu Top - The Top position of the menu in pixels
0,				// Menu Left - The Left position of the menu in pixels
,					// Menu Width - Menus width in pixels
0,					// Menu Border Width 
"right",					// Screen Position - here you can use "center;left;right;middle;top;bottom" or a combination of "center:middle"
style1,				// Properties Array - this is set higher up, as above
1,					// Always Visible - allows the menu item to be visible at all time (1=on/0=off)
"center",				// Alignment - sets the menu elements text alignment, values valid here are: left, right or center
,					// Filter - Text variable for setting transitional effects on menu activation - see above for more info
,					// Follow Scrolling - Tells the menu item to follow the user down the screen (visible at all times) (1=on/0=off)
1, 					// Horizontal Menu - Tells the menu to become horizontal instead of top to bottom style (1=on/0=off)
0,					// Keep Alive - Keeps the menu visible until the user moves over another menu or clicks elsewhere on the page (1=on/0=off)
"left",					// Position of TOP sub image left:center:right
,					// Set the Overall Width of Horizontal Menu to 100% and height to the specified amount (Leave blank to disable)
0,					// Right To Left - Used in Hebrew for example. (1=on/0=off)
1,					// Open the Menus OnClick - leave blank for OnMouseover (1=on/0=off)
,					// ID of the div you want to hide on MouseOver (useful for hiding form elements)
,					// Background image for menu when BGColor set to transparent.
,					// Scrollable Menu
,					// Reserved for future use

,"<?php no_wh('Content')?>","show-menu=Content","","Content",1
,"<?php no_wh('Docs and Images')?>","show-menu=Docs","","Docs and Images",1
,"<?php no_wh('Tools')?>","show-menu=Modules","","Modules",1
,"<?php no_wh('Forms')?>","show-menu=Form","","Forms",1
,"<?php no_wh('Navigation')?>","show-menu=Nav","","Navigation",1
//,"<?php #no_wh('User Data')?>","show-menu=User Data","","User Data",1
,"<?php no_wh('Site Template')?>","show-menu=Template","","Template",1
,"<?php no_wh('Settings')?>","show-menu=Settings","","Settings",1
,"<?php no_wh('Help&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;')?>","show-menu=Help","","Help",1

])


	addmenu(menu=["Content",,,190,1,"",style2,,"top",effect,,,,,,,,,,,,
<?php
echo nav_item($sys_nav['content'][0]); # view/edit
echo nav_item($sys_nav['content'][1]); # add
echo nav_item(); #
echo nav_item($sys_nav['content'][2]); # view homepage
echo nav_item($sys_nav['content'][3]); # add homepage
echo nav_item(); #
echo nav_item($sys_nav['content'][10]); # view sections
echo nav_item($sys_nav['content'][11]); # add section
echo nav_item($sys_nav['content'][12]); # add class
echo nav_item(); #
?>
	,"RSS","show-menu=rss",,,1
	,"Content Tools","show-menu=Content Tools",,,1

		])
	
	
	addmenu(menu=["Nav",,,175,1,"",style2,,"top",effect,,,,,,,,,,,,
<?php
echo nav_item($sys_nav[30][0]); # Default Navigation Layout
echo nav_item($sys_nav[30][1]); # Homepage Navigation Layout
echo nav_item($sys_nav[30][2]); # View  Navigation Files
echo nav_item($sys_nav[30][3]); # Add Navigation File
?>

			
		])
	
	addmenu(menu=["Template",,,175,1,"",style2,,"top",effect,,,,,,,,,,,,
<?php
echo nav_item($sys_nav[31][0]); # View/Edit Design Template
echo nav_item($sys_nav[31][1]); # Add Design Template
echo nav_item($sys_nav[31][2]); # Edit Standard CSS
echo nav_item($sys_nav[31][3]); # Edit Custom CSS
?>
		])
	
	addmenu(menu=["Modules",,,175,1,"",style2,,"top",effect,,,,,,,,,,,,
<?php
#echo nav_item($sys_nav[''][]); # view/edit
?>
	,"Calendar","<?php echo $sys_nav[1][0]['link']; ?>",,,1
	,"Photo Gallery","<?php echo $sys_nav[8][1]['link']; ?>",,,1
	,"Advocacy Modules","show-menu=Advocacy",,,1
	,"Email Lists","show-menu=Email",,,1
	,"Directories","show-menu=Dir",,,1
	,"Boards","show-menu=Boards",,,1
	<?php	echo nav_mod_type_check(11); ?>
	<?php	echo nav_mod_type_check(1); ?>
	//,"User Data Modules","show-menu=UDMs",,,1
	,"Volunteer","vol_list.php",,,1
	,"Media Sign In","modinput4_data.php?modin=7",,,1
	,"FAQs","f<?php echo $sys_nav[4][0]['link']; ?>",,,1
	,"Tell A Friend","module_header_list.php?modid=22",,,1
	,"Contact Us","module_header_list.php?modid=17",,,1
	,"Quotes","<?php echo $sys_nav[41][0]['link']; ?>",,,1
	//,"Forums","",,,1

		])
		
	addmenu(menu=["Advocacy",,,175,1,"",style2,,"top",effect,,,,,,,,,,,,
<?php
#echo nav_item($sys_nav[''][]); # view/edit
?>
	,"Web Actions","sendfax_list.php",,,1
	,"Petitions","petition_list.php",,,1
	,"Endorsements","modinput4_data.php?modin=1",,,1
<?php	echo nav_mod_type(11); ?>

		])
	addmenu(menu=["Boards",,,175,1,"",style2,,"top",effect,,,,,,,,,,,,
<?php
#echo nav_item($sys_nav[''][]); # view/edit
?>
	,"Ride Board","modinput4_data.php?modin=10",,,1
	,"Housing Board","modinput4_data.php?modin=11",,,1
<?php	echo nav_mod_type(10); ?>
		])
		
	addmenu(menu=["Email",,,175,1,"",style2,,"top",effect,,,,,,,,,,,,
<?php
echo nav_item($sys_nav[9][1]); # amp
echo nav_item($sys_nav[9][0]); # phplist
?>
		])
		
	addmenu(menu=["Dir",,,175,1,"",style2,,"top",effect,,,,,,,,,,,,
<?php
#echo nav_item($sys_nav[''][]); # view/edit
?>
	,"Links","link_list.php?action=list",,,1
	,"Local Groups","modinput4_data.php?modin=2",,,1
	,"Speakers","modinput4_data.php?modin=6",,,1
	,"Trainers","modinput4_data.php?modin=5",,,1
	<?php	echo nav_mod_type(9); ?>
		])

	addmenu(menu=["Other",,,175,1,"",style2,,"top",effect,,,,,,,,,,,,
<?php
#echo nav_item($sys_nav[''][]); # view/edit
?>

	<?php	echo nav_mod_type(12); ?>
		])
	addmenu(menu=["Custom",,,175,1,"",style2,,"top",effect,,,,,,,,,,,,
<?php
#echo nav_item($sys_nav[''][]); # view/edit
?>

	<?php	echo nav_mod_type(1); ?>
		])
	//addmenu(menu=["User Data",,,175,1,"",style2,,"top",effect,,,,,,,,,,,,
<?php
#echo nav_item($sys_nav[''][]); # view/edit
?>
//	,"View/Edit Users","",,,1
//	,"Add User","",,,1
//	,"Search Users","",,,1
//		])		
		
	addmenu(menu=["Form",,,175,1,"",style2,,"top",effect,,,,,,,,,,,,
<?php
echo nav_item($sys_nav['udm'][0]); # view
echo nav_item($sys_nav['udm'][1]); # add ?>
	,"Form Data","show-menu=UDMs",,,1
<?php
echo nav_item($sys_nav['udm'][2]); # search
?>
		])		
	
	addmenu(menu=["UDMs",,,175,1,"",style2,,"top",effect,,,,,,,,,,,,
<?php
echo nav_udms();
?>
		])

	addmenu(menu=["rss",,,175,1,"",style2,,"top",effect,,,,,,,,,,,,
<?php
echo nav_item($sys_nav[45][0]); #View RSS Feeds
echo nav_item($sys_nav[45][1]); # Add RSS Feed
echo nav_item($sys_nav[45][3]); # RSS Aggragator
?>
		])
	

	addmenu(menu=["Docs",,,175,1,"",style2,,"top",effect,,,,,,,,,,,,
<?php
echo nav_item($sys_nav['content'][5]); # View Documents
echo nav_item($sys_nav['content'][8]); # Upload Documents
echo nav_item($sys_nav['content'][6]); # View Images
echo nav_item($sys_nav['content'][8]); # Upload Images
?>
		])


	addmenu(menu=["Content Tools",,,175,1,"",style2,,"top",effect,,,,,,,,,,,,
<?php
#echo nav_item($sys_nav[''][]); # view/edit
?>
	,"Comments","comments.php?action=list",,,1
	,"Page Redirection","redirect.php?action=list",,,1
	,"Hot Words","hotwords.php?action=list",,,1
	,"User Added Content","article_list.php?&class=9",,,1
	,"Other Content Tools","module_header_list.php?modid=19",,,1
	

		])		

	addmenu(menu=["Help",,,175,1,"",style2,,"top",effect,,,,,,,,,,,,
<?php
//echo nav_item($sys_nav[''][]); # 
?>
	,"Help","http://www.radicaldesigns.org/manual.pdf",,,1
	,"HTML Tips","html.html",,,1
	,"About","",,,1
		])
		
	addmenu(menu=["Settings",,,175,1,"",style2,,"top",effect,,,,,,,,,,,,
<?php
echo nav_item($sys_nav['system'][1]); # System Permisssions
echo nav_item($sys_nav['system'][0]); # System Users
echo nav_item($sys_nav['system'][2]); # System Settings
?>
,"Modules Settings","show-menu=Module Settings",,,1
<?php
echo nav_item($sys_nav['system'][3]); # Setup Wizard
echo nav_item($sys_nav['system'][4]); # Reset Cache
echo nav_item($sys_nav['system'][5]); # Logout
?>
		])

	addmenu(menu=["Module Settings",,,175,1,"",style2,,"top",effect,,,,,,,,,,,,
<?php
echo nav_item($sys_nav['module'][0]); # intro
echo nav_item($sys_nav['module'][1]); # intro add
echo nav_item($sys_nav['module'][2]); # module add
echo nav_item($sys_nav['module'][3]); # module list
?>
		])	

	
dumpmenus()

</script>
<script language="JavaScript" src="../Connections/mmenu.js" type=text/javascript></script>

<script language="JavaScript">
var menuOne = document.getElementById('menu1');
menuOne.style.height='28px';

for ( var i = 0; i < 7; i++ ) {
		var hsepHnd = document.getElementById('hsep'+i);
		var helHnd = document.getElementById('hel'+i);
		hsepHnd.style.height='28px';
		helHnd.style.height='28px';
}
</script>

<?php

function no_wh($text) {
	$t = str_replace('','&nbsp;',$text);
	$output =  '<nobr>'.$t.'</nobr>';
	return $output;
}

function nav_item($var=NULL) {
	global $userper;
	
	if (!$var) {
			$output =",\"\",\"\",,,1 ";
	} elseif ($var['nav']){
		$output = ",\"".$var['nav']."\",\"".$var['link']."\",,,1 ";
	} else {
		$output = ",\"".$var['name']."\",\"".$var['link']."\",,,1 ";
	}
	if ($var['per']) {
		if ($userper[$var['per']]) {
			return $output;
		}
	} else {
		return $output;
	}
}

function nav_item_basic($name,$link,$per=NULL) {
	global $userper;
	
	$output = ",\"".$name."\",\"".$link."\",,,1 ";
	if ($per) {
		if ($userper[$per]) {
			return $output;
		}
	} else {
		return $output;
	}
}

function nav_udm_layout($id,$name) {
	$output = ",\"".$name."\",\"modinput4_data.php?modin=".$id."\",,,1 ";
	return $output;
}

function nav_item_udm($id) {
	global $dbcon, $userper;
	$sql = "select m.perid, u.name from userdata_fields u left join modules m on u.id = m.userdatamodid where u.id = $id";
	$R=$dbcon->Execute($sql) or DIE($sql.$dbcon->ErrorMsg());
	$output =nav_udm_layout($id,$R->Fields("name"));
	if ($R->Fields("perid")) {
		if ($userper[$R->Fields("perid")]) {
			return $output;
		}
	} else {
		return $output;
	}
}

function nav_udms() {
	global $dbcon;
    $output = '';
	$R=$dbcon->CacheExecute("SELECT id, name FROM userdata_fields WHERE id >= 50 ORDER BY name")
                    or die("Couldn't fetch UDM nav information: " . $dbcon->ErrorMsg());
	while (!$R->EOF) {
		$output .= nav_item_udm($R->Fields("id"));
		$R->MoveNext();
	}
	return $output;
}

function nav_mod_type($type) {
	global $dbcon;
    $output = '';
	$sql ="SELECT i.id, i.name FROM userdata_fields i, modules m WHERE i.id = m.userdatamodid AND m.module_type=" . $dbcon->qstr($type) . " AND m.userdatamodid > 49 ORDER BY name";
	$R=$dbcon->CacheExecute($sql) or die('Error in nov_mod_tpe: ' . $dbcon->ErrorMsg());
	while (!$R->EOF) {
		$output .= nav_item_udm($R->Fields("id"));
		$R->MoveNext();
	}
	return $output;
}


function nav_section($name,$section,$per=NULL) {
	global $userper;

	$output = ",\"".no_wh($name)."\",\"show-menu=".$section."\",\"\",\"".$section."\",1\n";
	//$output = ",".$output;
	if ($per) {
		if ($userper[$per]) {
			return $output;
		}
	} else {
		return $output;
	}
}

function nav_mod_type_check($type) {
	global $dbcon;
	$C=$dbcon->CacheExecute("SELECT id FROM modules WHERE module_type=" . $dbcon->qstr($type) . " AND userdatamodid > 49")
                    or die("Couldn't check module type: " . $dbcon->ErrorMsg());
	if ($C->Fields("id")) {
		$R=$dbcon->CacheExecute("SELECT name FROM module_type WHERE id=" . $dbcon->qstr($type))
                    or die("Couldn't fetch module type information: " . $dbcon->ErrorMsg());
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

<?php
echo nav_section('Content','Content',''); # 
echo nav_section('Docs and Images','Docs',''); # 
echo nav_section('Tools','Modules',''); # 
echo nav_section('Forms','Form',''); # 
echo nav_section('Navigation','Nav',''); # 
echo nav_section('Site Template','Template',''); # 
echo nav_section('Settings','Settings',''); # 
echo nav_section('Help&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;','Help',''); # 
?>

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
echo nav_section('RSS','rss',''); # rss menu
echo nav_section('Content Tools','Content Tools',''); # rss menu
?>
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
echo nav_item($sys_nav[1][0]); # calendar
echo nav_item($sys_nav[8][1]); # gallery
echo nav_section('Advocacy Modules','Advocacy',''); # 
echo nav_section('Email Lists','Email',''); # 
echo nav_section('Directories','Dir',''); # 
echo nav_section('Boards','Boards',''); # 
echo nav_mod_type_check(12); 
echo nav_mod_type_check(1); 
echo nav_item_basic('Volunteer','vol_list.php',''); # vol
echo nav_item_basic('Media Sign In','modinput4_data.php?modin=7',''); # media sign in
echo nav_item($sys_nav[4][0]); # faq
echo nav_item_basic('Tell A Friend','module_header_list.php?modid=22',''); # Tell a friend
echo nav_item_basic('Contact Us','module_header_list.php?modid=17','');# contact us
#echo nav_item_basic('','','');echo nav_section('','',''); # 
echo nav_item($sys_nav[41][0]); # quotes
?>
		])
		
	addmenu(menu=["Advocacy",,,175,1,"",style2,,"top",effect,,,,,,,,,,,,
<?php
echo nav_item($sys_nav[21][0]); # web actions
echo nav_item($sys_nav[7][0]); # vpetitons
echo nav_item_udm(1); #endorsements
echo nav_mod_type(11);
 ?>

		])
	addmenu(menu=["Boards",,,175,1,"",style2,,"top",effect,,,,,,,,,,,,
<?php
echo nav_item_udm(10); #ride board
echo nav_item_udm(11); #hosuign board
echo nav_mod_type(10); ?>
		])
		
	addmenu(menu=["Email",,,175,1,"",style2,,"top",effect,,,,,,,,,,,,
<?php
echo nav_item($sys_nav[9][1]); # amp
echo nav_item($sys_nav[9][0]); # phplist
?>
		])
		
	addmenu(menu=["Dir",,,175,1,"",style2,,"top",effect,,,,,,,,,,,,
<?php

echo nav_item($sys_nav[11][0]); # 
echo nav_item_udm(2); #groups
echo nav_item_udm(6); #speakers
echo nav_item_udm(5); #trainers
echo nav_mod_type(9);
?>
		])

	addmenu(menu=["Other",,,175,1,"",style2,,"top",effect,,,,,,,,,,,,
<?php
echo nav_mod_type(12); 
?>
		])
	addmenu(menu=["Custom",,,175,1,"",style2,,"top",effect,,,,,,,,,,,,
<?php
echo nav_mod_type(1); 
?>
		])
		
	addmenu(menu=["Form",,,175,1,"",style2,,"top",effect,,,,,,,,,,,,
<?php
echo nav_item($sys_nav['udm'][0]); # view
echo nav_item($sys_nav['udm'][1]); # add
echo nav_section('Form Data','UDMs',''); #  
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
echo nav_item_basic('Comments','comments.php?action=list','');# 
echo nav_item_basic('Page Redirection','redirect.php?action=list','');# 
echo nav_item_basic('Hot Words','hotwords.php?action=list','');# 
echo nav_item_basic('User Added Content','article_list.php?&class=9','');# 
echo nav_item_basic('Other Content Tools','module_header_list.php?modid=19','');# 
?>
		])		

	addmenu(menu=["Help",,,175,1,"",style2,,"top",effect,,,,,,,,,,,,
<?php
echo nav_item_basic('Help','http://www.radicaldesigns.org/manual.pdf','');# 
echo nav_item_basic('HTML Tips','html.html','');# 
echo nav_item_basic('About','','');# 
?>
		])
		
	addmenu(menu=["Settings",,,175,1,"",style2,,"top",effect,,,,,,,,,,,,
<?php
echo nav_item($sys_nav['system'][1]); # System Permisssions
echo nav_item($sys_nav['system'][0]); # System Users
echo nav_item($sys_nav['system'][2]); # System Settings
echo nav_section('Modules Settings','Module Settings',''); #  
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

<?php
function no_wh($text) {
	$t = str_replace('','&nbsp;',$text);
	echo '<nobr>'.$t.'</nobr>';
}


?>
<SCRIPT language=JavaScript>
menunum=0;menus=new Array();_d=document;function addmenu(){menunum++;menus[menunum]=menu;}function dumpmenus(){mt="<scr"+"ipt language=javascript>";for(a=1;a<menus.length;a++){mt+=" menu"+a+"=menus["+a+"];"}mt+="<\/scr"+"ipt>";_d.write(mt)}
//Please leave the above line intact. The above also needs to be enabled if it not already enabled unless this file is part of a multi pack.



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
"black",				// Menu Item Separator Color
]

style2=[			// style1 is an array of properties. You can have as many property arrays as you need. This means that menus can have their own style.
"black",				// Mouse Off Font Color
"#cccccc",			// Mouse Off Background Color
"",			// Mouse On Font Color
"#cccccc",			// Mouse On Background Color
"black",			// Menu Border Color 
12,					// Font Size in pixels
"normal",			// Font Style (italic or normal)
"bold",				// Font Weight (bold or normal)
"Arial",	// Font Name
5,					// Menu Item Padding
"arrow.gif",		// Sub Menu Image (Leave this blank if not needed)
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
62,					// Menu Top - The Top position of the menu in pixels
0,				// Menu Left - The Left position of the menu in pixels
,					// Menu Width - Menus width in pixels
0,					// Menu Border Width 
"center",					// Screen Position - here you can use "center;left;right;middle;top;bottom" or a combination of "center:middle"
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
,"<?php no_wh('Modules')?>","show-menu=Modules","","Modules",1
,"<?php no_wh('Navigation')?>","show-menu=Nav","","Navigation",1
,"<?php no_wh('User Data')?>","show-menu=User Data","","User Data",1
,"<?php no_wh('Site Template')?>","show-menu=Template","","Template",1
,"<?php no_wh('Settings')?>","show-menu=Settings","","Settings",1
,"<?php no_wh('Help')?>","show-menu=Help","","Help",1

])


	addmenu(menu=["Content",
	,
	,
	190,
	1,
	"",
	style2,
	,"top"
	,effect,
	,
	,
	,
	,
	,
	,
	,
	,
	,
	,
	,
	,"View Edit Site Content","articlelist.php",,,1
	,"Add Site Content","article_edit.php",,,1
	,"","",,,1
	,"View/Edit Home page Content","article_list.php?&class=2",,,1
	,"Add Home Page Content","article_fpedit.php",,,1
	,"","",,,1
	,"View/Edit Sections","edittypes.php",,,1
	,"Add Section","type_edit.php",,,1
	,"Add Class","class.php",,,1
	,"RSS","show-menu=rss",,,1
	,"Content Tools","show-menu=Content Tools",,,1

		])
	
	
	addmenu(menu=["Nav",
	,
	,
	175,
	1,
	"",
	style2,
	,"top"
	,effect,
	,
	,
	,
	,
	,
	,
	,
	,
	,
	,
	,
	
	,"Default Navigation Layout","module_nav_edit.php?id=1",,,1
	,"Homepage Navigation Layout","module_nav_edit.php?id=2",,,1
	,"View Basic Navigation Files","nav_list.php?nons=1",,,1
	,"View All Navigation Files","nav_list.php",,,1
	,"Add Basic Navigation File","nav_minedit.php",,,1
	,"Add Dynamic Navigation File","nav_edit.php",,,1
			
		])
	
	
	addmenu(menu=["Template",
	,
	,
	175,
	1,
	"",
	style2,
	,"top"
	,effect,
	,
	,
	,
	,
	,
	,
	,
	,
	,
	,
	,
	
	,"View/Edit Design Template","template.php?action=list",,,1
	,"Add Design Template","template.php",,,1
	,"Edit Standard CSS","css_edit.php",,,1
	,"Edit Custom CSS","css_list.php",,,1

		])
	
	
	addmenu(menu=["Modules",
	,
	,
	175,
	1,
	"",
	style2,
	,"top"
	,effect,
	,
	,
	,
	,
	,
	,
	,
	,
	,
	,
	,
	
	,"Advocacy Modules","show-menu=Advocacy",,,1
	,"Email Tools","lists/admin",,,1
	,"Calendar","calendar_gxlist.php",,,1
	,"Directories","show-menu=Dir",,,1
	,"Boards","show-menu=Boards",,,1
	,"User Data Modules","show-menu=UDMs",,,1
	,"Photo Gallery","photo_list.php",,,1
	,"Other Modules","show-menu=Other",,,1
	," ","",,,1
	,"View/Edit Forms","modinput4_list.php",,,1
	,"Add New Form","modinput4_new.php",,,1
	,"Modules Settings","show-menu=Module Settings",,,1

		])
		
	addmenu(menu=["Advocacy",
	,
	,
	175,
	1,
	"",
	style2,
	,"top"
	,effect,
	,
	,
	,
	,
	,
	,
	,
	,
	,
	,
	,
	
	,"Web Actions","sendfax_list.php",,,1
	,"Petitions","petition_list.php",,,1
	,"Endorsements","modinput4_data.php?modin=1",,,1

		])
	addmenu(menu=["Boards",
	,
	,
	175,
	1,
	"",
	style2,
	,"top"
	,effect,
	,
	,
	,
	,
	,
	,
	,
	,
	,
	,
	,
	
	,"Ride Board","modinput4_data.php?modin=10",,,1
	,"Housing Board","modinput4_data.php?modin=11",,,1

		])

	addmenu(menu=["UDMs",
	,
	,
	175,
	1,
	"",
	style2,
	,"top"
	,effect,
	,
	,
	,
	,
	,
	,
	,
	,
	,
	,
	,
	
	,"Media Sign In","modinput4_data.php?modin=7",,,1
	,"","",,,1
	,"","",,,1
	,"","",,,1
	,"","",,,1
		])
		
	addmenu(menu=["Other",
	,
	,
	175,
	1,
	"",
	style2,
	,"top"
	,effect,
	,
	,
	,
	,
	,
	,
	,
	,
	,
	,
	,
	
	,"FAQs","faq_list.php",,,1
	,"Forums","",,,1

		])
		
	addmenu(menu=["Dir",
	,
	,
	175,
	1,
	"",
	style2,
	,"top"
	,effect,
	,
	,
	,
	,
	,
	,
	,
	,
	,
	,
	,
	
	,"Links","link_list.php",,,1
	,"Local Groups","modinput4_data.php?modin=2",,,1
	,"Speakers","modinput4_data.php?modin=6",,,1
	,"Trainers","modinput4_data.php?modin=5",,,1
		])
	addmenu(menu=["User Data",
	,
	,
	175,
	1,
	"",
	style2,
	,"top"
	,effect,
	,
	,
	,
	,
	,
	,
	,
	,
	,
	,
	,
	
	,"View/Edit Users","",,,1
	,"Add User","",,,1
	,"Search Users","",,,1


		])		
		
		
		
	addmenu(menu=["Module Settings",
	,
	,
	175,
	1,
	"",
	style2,
	,"top"
	,effect,
	,
	,
	,
	,
	,
	,
	,
	,
	,
	,
	,
	
	,"View Module Intro Text","moduletext_list.php",,,1
	,"Add Module Intro Text","moduletext_edit.php",,,1
	,"Add Module","module_edit.php",,,1
	,"Edit Module Settings","module_list.php",,,1


		])	
		addmenu(menu=["rss",
	,
	,
	175,
	1,
	"",
	style2,
	,"bottom"
	,effect,
	,
	,
	,
	,
	,
	,
	,
	,
	,
	,
	,
	
	,"View RSS Feeds","rssfeed.php?action=list",,,1
	,"Add RSS Feed","rssfeed.php",,,1
	,"RSS Aggragator","feeds_view.php?action=list",,,1


		])
	
	addmenu(menu=["Docs",
	,
	,
	175,
	1,
	"",
	style2,
	,"top"
	,effect,
	,
	,
	,
	,
	,
	,
	,
	,
	,
	,
	,
	
	,"View Documents","docdir.php",,,1
	,"Upload Documents","doc_upload.php",,,1
	,"View Images","imgdir.php",,,1
	,"Upload Images","imgup.php",,,1

		])
		
	addmenu(menu=["Content Tools",
	,
	,
	175,
	1,
	"",
	style2,
	,"top"
	,effect,
	,
	,
	,
	,
	,
	,
	,
	,
	,
	,
	,
	
	,"Comments","comments.php",,,1
	,"Page Redirection","redirect.php?action=list",,,1
	,"Hot Words","hotwords.php",,,1
	,"RSS","show-menu=rss",,,1
	,"User Added Content","article_list.php?&class=9",,,1
	,"Site Map","",,,1
	,"Search","",,,1

		])		



	addmenu(menu=["Help",
	,
	,
	175,
	1,
	"",
	style2,
	,"top"
	,effect,
	,
	,
	,
	,
	,
	,
	,
	,
	,
	,
	,
	
	,"Help","http://www.radicaldesigns.org/manual.pdf",,,1
	,"HTML Tips","html.html",,,1
	,"About","",,,1

		])
		
			addmenu(menu=["Settings",
	,
	,
	175,
	1,
	"",
	style2,
	,"top"
	,effect,
	,
	,
	,
	,
	,
	,
	,
	,
	,
	,
	,
	
	,"System Permisssions","permissions_list.php",,,1
	,"System Users","user_list.php",,,1
	,"System Settings","sysvar.php",,,1
	,"Setup Wizard","wizard_setup.php",,,1
	,"Reset Cahce","flushcache.php",,,1
	,"Logout","logout.php",,,1
		])
	
dumpmenus()
</script>
<SCRIPT language=JavaScript src="../Connections/mmenu.js" type=text/javascript></SCRIPT>


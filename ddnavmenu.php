<?php 
/*********************
06-04-2003  v3.01
Module:  Template Include
Description: drop down navigation element,
Usage::copy into custom and include in design template 
To Do: 

*********************/ 
global $MX_top;

 $navtop=$dbcon->CacheExecute("SELECT id, type  FROM articletype WHERE parent = $MX_top and usenav =1 order by textorder asc")or DIE($dbcon->ErrorMsg()); ?>

<SCRIPT language=JavaScript>
menunum=0;menus=new Array();_d=document;function addmenu(){menunum++;menus[menunum]=menu;}function dumpmenus(){mt="<scr"+"ipt language=javascript>";for(a=1;a<menus.length;a++){mt+=" menu"+a+"=menus["+a+"];"}mt+="<\/scr"+"ipt>";_d.write(mt)}
//Please leave the above line intact. The above also needs to be enabled if it not already enabled unless this file is part of a multi pack.



////////////////////////////////////
// Editable properties START here //
////////////////////////////////////

// Special effect string for IE5.5 or above please visit http://www.milonic.co.uk/menu/filters_sample.php for more filters
effect = "Fade(duration=0.2);Alpha(style=0,opacity=88);Shadow(color='#777777', Direction=135, Strength=5)"


timegap=500			// The time delay for menus to remain visible
followspeed=5		// Follow Scrolling speed
followrate=40		// Follow Scrolling Rate
suboffset_top=4;	// Sub menu offset Top position 
suboffset_left=6;	// Sub menu offset Left position
closeOnClick = true

style1=[			// style1 is an array of properties. You can have as many property arrays as you need. This means that menus can have their own style.
"navy",				// Mouse Off Font Color
"#CCCC99",			// Mouse Off Background Color
"#990000",			// Mouse On Font Color
"FFFFFF",			// Mouse On Background Color
"000000",			// Menu Border Color 
10,					// Font Size in pixels
"normal",			// Font Style (italic or normal)
"bold",				// Font Weight (bold or normal)
"Arial",	// Font Name
4,					// Menu Item Padding
"arrow.gif",		// Sub Menu Image (Leave this blank if not needed)
,					// 3D Border & Separator bar
"66ffff",			// 3D High Color
"000099",			// 3D Low Color
"Purple",			// Current Page Item Font Color (leave this blank to disable)
"pink",				// Current Page Item Background Color (leave this blank to disable)
"",		// Top Bar image (Leave this blank to disable)
"ffffff",			// Menu Header Font Color (Leave blank if headers are not needed)
"000099",			// Menu Header Background Color (Leave blank if headers are not needed)
"black",				// Menu Item Separator Color
]


addmenu(menu=[		// This is the array that contains your menu properties and details
"mainmenu",			// Menu Name - This is needed in order for the menu to be called
70,					// Menu Top - The Top position of the menu in pixels
16,				// Menu Left - The Left position of the menu in pixels
,					// Menu Width - Menus width in pixels
0,					// Menu Border Width 
"",					// Screen Position - here you can use "center;left;right;middle;top;bottom" or a combination of "center:middle"
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
,					// Open the Menus OnClick - leave blank for OnMouseover (1=on/0=off)
,					// ID of the div you want to hide on MouseOver (useful for hiding form elements)
,					// Background image for menu when BGColor set to transparent.
,					// Scrollable Menu
,					// Reserved for future use
<?php while (!$navtop->EOF)
   { ?>
,"<?php echo $navtop->Fields("type")?>&nbsp;&nbsp;","<?php $navcheck=$dbcon->CacheExecute("SELECT id  FROM articletype WHERE parent = ".$navtop->Fields("id")." and usenav =1 order by  textorder asc")or DIE($dbcon->ErrorMsg());
if ($navcheck->RecordCount() != 0) {
echo "show-menu=".$navtop->Fields("id")."\",\"section.php?id=".$navtop->Fields("id")."\"";}
else {
echo "section.php?id=".$navtop->Fields("id")."\",\"\"";}?>,"<?php echo $navtop->Fields("type")?>",1

<?php  $navtop->MoveNext();}?>
])

<?php $navtop2=$dbcon->CacheExecute("SELECT id  FROM articletype WHERE parent = 1 and usenav =1 order by  textorder asc")or DIE($dbcon->ErrorMsg());?>
<?php while (!$navtop2->EOF)   { ?>
<?php $navtop3=$dbcon->CacheExecute("SELECT id, type  FROM articletype WHERE parent = ".$navtop2->Fields("id")." and usenav =1 order by  textorder asc")or DIE($dbcon->ErrorMsg());
if ($navtop3->RecordCount() != 0) {
?>
	addmenu(menu=["<?php echo $navtop2->Fields("id")?>",
	,,120,1,"",style1,,"left",effect,,,,,,,,,,,,
<?php while (!$navtop3->EOF)   { ?>	
	,"<?php echo $navtop3->Fields("type")?>","section.php?id=<?php echo $navtop3->Fields("id")?>",,,1
	<?php  $navtop3->MoveNext();}?>
	])
	
	<?php } $navtop2->MoveNext();}?>

dumpmenus()
</script>
<SCRIPT language=JavaScript src="scripts/mmenu.js" type=text/javascript></SCRIPT>

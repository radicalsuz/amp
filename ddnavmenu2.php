<?php 
/*********************
06-04-2003  v3.01
Module:  Template Include
Description: drop down navigation element,
Usage::copy into custom and include in design template 
To Do: 

*********************/ 

 $navtop=$dbcon->CacheExecute("SELECT id, type, image, useimage  FROM articletype WHERE parent = 2 and usenav =1")or DIE($dbcon->ErrorMsg()); ?>
<SCRIPT language=JavaScript src="scripts/milo_menu_src.js" type=text/javascript></SCRIPT>
<SCRIPT language=JavaScript>
_menuCloseDelay=500           // The time delay for menus to remain visible on mouse out
_menuOpenDelay=150            // The time delay before menus open on mouse over
_followSpeed=5                // Follow scrolling speed
_followRate=50                // Follow scrolling Rate
_subOffsetTop=5               // Sub menu top offset
_subOffsetLeft=-10            // Sub menu left offset
_scrollAmount=3               // Only needed for Netscape 4.x
_scrollDelay=20               // Only needed for Netcsape 4.x



with(AllImagesStyle=new mm_style()){
bordercolor="#000000";
borderstyle="solid";
padding=3;
fontstyle="normal";
fontweight="normal";
}


with(menuStyle=new mm_style()){
onbgcolor="#CD9933";
oncolor="#ffffff";
offbgcolor="#CD9933";
offcolor="#515151";
bordercolor="#296488";
borderstyle="solid";
borderwidth=1;
separatorcolor="#2D729D";
separatorsize="1";
padding=10;
fontsize="75%";
fontstyle="normal";
fontfamily="Verdana, Tahoma, Arial";
pagecolor="black";
pagebgcolor="#82B6D7";
headercolor="#000000";
headerbgcolor="#ffffff";
subimagepadding="20";
overfilter="Fade(duration=0.01);Alpha(opacity=90);Shadow(color='#777777', Direction=135, Strength=1)";
}

with(milonic=new menuname("Main Menu")){
style=AllImagesStyle;
top=134;
left=190;
alwaysvisible=1;
orientation="horizontal";
<?php while (!$navtop->EOF)   { ?>
aI("showmenu=<?php echo $navtop->Fields("id")?>;<?php if ($navtop->Fields("useimage") != 1) { ?>text=<?php echo $navtop->Fields("type")?>;<?php } ; ?>
<?php if ($navtop->Fields("image") != (NULL)) { ?>image=<?php echo $navtop->Fields("image"); ?>;<?php } ?>
url=section.php?id=<?php echo $navtop->Fields("id")?>;status=<?php echo $navtop->Fields("type")?>;");

<?php  $navtop->MoveNext();}?>
}

<?php $navtop2=$dbcon->CacheExecute("SELECT id  FROM articletype WHERE parent = 2 and usenav =1")or DIE($dbcon->ErrorMsg());?>
<?php while (!$navtop2->EOF)   { ?>
<?php $navtop3=$dbcon->CacheExecute("SELECT id, type, image FROM articletype WHERE parent = ".$navtop2->Fields("id")." and usenav =1")or DIE($dbcon->ErrorMsg());
if ($navtop3->RecordCount() != 0) {
?>
	with(milonic=new menuname("<?php echo $navtop2->Fields("id")?>")){
style=menuStyle;
<?php while (!$navtop3->EOF)   { ?>
aI("showmenu=<?php echo $navtop3->Fields("id")?>;<?php if ($navtop3->Fields("useimage") != 1) { ?>text=<?php echo $navtop3->Fields("type")?>;<?php } ; ?>
<?php if ($navtop3->Fields("image") != (NULL)) { ?>image=<?php echo $navtop3->Fields("image"); ?>;<?php } ?>
url=section.php?id=<?php echo $navtop3->Fields("id")?>;status=<?php echo $navtop3->Fields("type")?>;");
	<?php  $navtop3->MoveNext();}?>

	}
	
	<?php } $navtop2->MoveNext();}?>
drawMenus();	
	
</script>

<?php

$menu_header='
<STYLE type ="text/css">
    .floater{ position: absolute; background-color: '.$menu_bg_color.'; width: 200; 
                border: solid black 1px; text-align: left; padding: 4px; }
    TABLE#menu td.boxcontents {
                font-family: Verdana, Arial, Helvetica, sans-serif; background-color: '.$menu_bg_color.';
                    font-size: 10px; font-weight: normal; color: '.$menu_txt_color.'; padding-bottom: 5px;}
    .nav {  font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 11px; font-weight: normal; 
            color: '.$menu_txt_color.';}
    a:hover.nav {  font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 11px; 
                    font-weight: normal; color: '.$menu_txt_color_hover.';  text-decoration : none; }
</STYLE>
<!-- this prevents \'events fall through the menu\' bug in win/ie --><!--[if gte IE 5]>
<STYLE type=text/css>TABLE#menu DIV {
	BACKGROUND-COLOR: '.$menu_bg_bgcolor.'
}
</STYLE>
<![endif]-->
<table border="0" cellspacing="0" id="menu" cellpadding="3" width="145">';


function DivMenu_showmenu(&$menuset) {
    $menu_base='<tr>
    <td style="border: solid black 1px" class="boxcontents" 
    onmouseover="show_menu(this, this.id.substring(5)); " id="mrow_%s"><img align=right src="img/point_r_wt.gif" id="mptr_%s"> 
    <a href="%s"><b>%s</b> </a></td></tr><tr><td><img src="img/s.gif" width="5" height="3"></td></tr>'."\n";

    $output = "";

    $currentset = array();
    foreach ($menuset as $testtype=>$typeinfo) {
        $currentset[$typeinfo['parent']][$testtype]=$typeinfo;
    }
    $baseset=$currentset['1'];

    foreach ($baseset as $menu_id=> $menu_def) {
        $linktext=(isset($menu_def['link'])?$menu_def['link']: "article.php?list=type&type=".$menu_id);
        $action = sprintf ($menu_base, $menu_id, $menu_id, $linktext, $menu_def['type']);
        $output .= $action;
    }
    unset ($currentset['1']);

    foreach ($currentset as $onekey=>$oneset) {
        
        $output .= DivMenu_showsub($onekey, $oneset);
    }

    return $output;
}

function DivMenu_showsub($typeid,&$menuset) {
    $menu_folder_start="<DIV ID=\"%s\" CLASS=\"floater\" STYLE=\"display: none; width:250\"><span class=\"nav\">";
    $menu_folder_end="</span></DIV>\n";
    $menu_entry="<a href=\"%s\">%s</a><BR><img src=\"img/s.gif\" width=\"2\" height=\"4\"><BR>";
    $output=sprintf($menu_folder_start,("divmenu_".$typeid));
    foreach ($menuset as $menu_id=>$menu_def) {
        $linktext=(isset($menu_def['link'])?$menu_def['link']: "article.php?list=type&type=".$menu_id);
        $output.=sprintf($menu_entry, $linktext, $menu_def['type'])."\n";
    }
    $output.=$menu_folder_end;

    return $output;
    
}

$menu_footer='
</TABLE>
<SCRIPT type="text/javascript">
<!--
  document.onclick = new Function("show_menu(null)")
  
  function getPos(el,sProp) {
      var iPos = 0
      while (el != null) {
	  iPos += el["offset" + sProp]
	  el = el.offsetParent
      }
      return iPos
  }
  
  var current_menu = null;
  var current_mptr = null;
  function show_menu(el,menuid) {
      if (menu=document.getElementById(("divmenu_"+menuid))) {
      alert (menu.style.display);
	  menu.style.display="block";
	  menu.style.pixelLeft = getPos(el,"Left") + el.offsetWidth + 2
	  menu.style.pixelTop = getPos(el,"Top")
      }
      else {
        alert (menuid."is the id");
      }
      if (mptr=document.getElementById("mptr_"+menuid)) {
	mptr.src="img/point_r_rd.gif"
      }
      if ((menu != current_menu) && (current_menu)) {
	  current_menu.style.display="none"
      }
      if ((mptr != current_mptr) && (current_mptr)) {
	  current_mptr.src="img/point_r_wt.gif"
      }
      current_menu = menu
      current_mptr = mptr
  }
  -->
</SCRIPT>';

?>

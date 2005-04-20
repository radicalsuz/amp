<?php

$menu_header = '<!-- this prevents \'events fall through the menu\' bug in win/ie --><!--[if gte IE 5]>
<STYLE type=text/css>UL#menu DIV {
	BACKGROUND-COLOR: #ffffff
}
</STYLE>
<![endif]-->


<UL id=menu>
';


function CSSMenu_showmenu($typeid, &$menuset){
    $menu_entry="<li><a href=\"article.php?list=type&type=%s\">%s</a></li>\r\n";
    $menu_folder="<li><a href=\"article.php?list=type&type=%s\" class=\"menufolder\">%s</a>\r\n<ul>\r\n     ";
    $menu_folder_end="</ul></li>\r\n";
    $output = "";

    $currentset = array();
    foreach ($menuset as $testtype=>$typeinfo) {
        if ($typeinfo['parent']==$typeid) {
            $currentset[]=$testtype;
        }
    }
    $numtabs=count($currentset);

    if ($numtabs==0) {
        $output .= sprintf($menu_entry, $typeid, $menuset[$typeid]['type']);
    } else {
        $output .= sprintf ($menu_folder, $typeid,  $menuset[$typeid]['type']);
        foreach ($currentset as $currenttype) {
            $output.= CSSMenu_showmenu($currenttype, $menuset);
        }
        $output .= $menu_folder_end;
        
    }
    return $output;

}


$menu_footer="
</UL>

<SCRIPT type=text/javascript>



/*
	list menu script by Brothercake (http://www.brothercake.com/)
	you may use the code providing this message remains intact
*/

//trigger initialiser
function menuInitTrigger(menuTrigger)
{

	//null menu object
	var menuMenu = null;

	//moz needs this         //mac/ie5 needs this
	if(menuTrigger.firstChild && menuTrigger.firstChild.nextSibling)
	{
		//get menu object
		menuMenu = menuTrigger.childNodes[2];
	}




	//bind menu mouse-opener
	menuTrigger.onmouseover = function()
	{
		this.menuShowMenu();
	}

	//bind menu mouse-closer
	menuTrigger.onmouseout = function(e)
	{
		this.menuHideMenu(e);
	}


	//menu opening function
	menuTrigger.menuShowMenu = function()
	{

		//rollover
		menuTrigger.firstChild.style.backgroundColor = '".$menu_bg_color_hover."';
		menuTrigger.firstChild.style.color = '".$menu_txt_color_hover."';

		//if trigger has menu
		if(menuMenu != null)
		{
			//tweak top position
			menuMenu.style.marginTop = (0-menuTrigger.offsetHeight)+'px';

			//show menu
			menuMenu.style.visibility = 'visible';

		}

	}



	//menu mouse-closing function
	menuTrigger.menuHideMenu = function(e)
	{
		if(!e) { e = window.event; }

		//if event came from outside current trigger branch
		if(!menuTrigger.contains(e.relatedTarget || e.toElement))
		{
			//rollout
			menuTrigger.firstChild.style.backgroundColor = '".$menu_bg_color."';
			menuTrigger.firstChild.style.color = '".$menu_txt_color."';

			//if trigger has menu
			if(menuMenu != null)
			{
				//hide menu
				menuMenu.style.visibility = 'hidden';
			}
		}
	}




	//prototyped contains method
	//obviously not necessary for win/ie5, and errs in mac/ie5 anyway
	//not actually necessary for O7 either .. but I'm superstitious
	if(!(menu.macie||menu.winie))
	{
		//contains method by jkd (http://www.jasonkarldavis.com/)
		menuTrigger.contains = function(node)
		{
			if (node == null) { return false; }
			if (node == this) { return true; }
			else { return this.contains(node.parentNode); }
		}
	}


}












//navbar object
function menuNavbar(ulTree)
{

	//object
	menu.nav = this;

	//ul tree
	menu.tree = ulTree;

	//invalidate markup so it works in win/ie
	if(menu.winie)
	{
		//get inner HTML
		menu.html = menu.tree.innerHTML;

		//replace <li> with <div> to prevent excess margins
		//sorry - I know this invalidates the DOM
		//but invalid markup is the only language it understands ...
		menu.html = menu.html.replace(/<([\/]?)li/ig,'<$1div');

		//write back to tree
		menu.tree.innerHTML = menu.html;
	}

	//get trigger elements
 	(menu.xdom) ? menu.tagNames = 'li' : (menu.winie) ? menu.tagNames = 'DIV' : menu.tagNames = 'LI';
 	menu.listItems = menu.tree.getElementsByTagName(menu.tagNames);
 	menu.listLen = menu.listItems.length;

 	//initialise
 	for(i=0; i<menu.listLen; i++)
 	{
 		menuInitTrigger(menu.listItems[i]);
 	}



}







//menu tree
var tree = null;

//menu object
var menu = new Object;

//identify support level
menu.ua = navigator.userAgent.toLowerCase();
menu.op6 = (menu.ua.indexOf('opera 6')!=-1||menu.ua.indexOf('opera/6')!=-1)?true:false;
menu.dom = (typeof document.getElementById!='undefined'&&typeof document.createElement!='undefined'&&!menu.op6)?true:false;

//identify win and mac versions of ie
menu.winie = (menu.dom&&typeof document.all!='undefined'&&typeof window.opera=='undefined')?true:false;
menu.macie = (menu.winie&&menu.ua.indexOf('mac')!=-1)?true:false;if(menu.macie){menu.winie=false;}

//identify which DOM were in
//this test may not be 100% reliable ...
menu.xdom = (menu.dom&&typeof document.write=='undefined')?true:false;


//initiate
window.onload = function()
{

	//if browser is supported
	if(menu.dom)
	{

		//tree object
		tree = document.getElementById('menu');

		if(tree != null)
		{
			//create navbar object
			menu.nav = new menuNavbar(tree)
		}

	}


}

</SCRIPT>
";
?>

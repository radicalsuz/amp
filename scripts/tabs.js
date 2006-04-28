var Tabs_registeredItems = Array();
var Tabs_registeredItems_ClassNames = Array();
var Tabs_currentWidth = 0;

function Tabs_register( item ) {

	window.Tabs_registeredItems[ window.Tabs_registeredItems.length ] = item;
	window.Tabs_registeredItems_ClassNames[ window.Tabs_registeredItems_ClassNames.length ] = item.className;
	
}

function Tabs_clear() {
	for (n=0;n<window.Tabs_registeredItems.length;n++){
		window.Tabs_registeredItems[n].className= window.Tabs_registeredItems_ClassNames[n];
	}
}

function Tabs_highlight( item, highlight_class ) {
	if (!highlight_class>'') highlight_class='current_tab';
	Tabs_clear();
	Tabs_register(item);
 	item.className = item.className + ' ' + highlight_class;	
}


var Tabs_registeredItems = Array();
var Tabs_registeredItems_ClassNames = Array();
var Tabs_currentWidth = 0;

function Tabs_register( item ) {

	window.Tabs_registeredItems[ window.Tabs_registeredItems.length ] = item;
	window.Tabs_registeredItems_ClassNames[ window.Tabs_registeredItems_ClassNames.length ] = item.className;
	
}

function Tabs_mirror( item ) {
	item_mirror = document.getElementById( item.id + '_mirror' );
	if (item_mirror) {
		return item_mirror;
	}
}

function Tabs_clear() {
	for (n=0;n<window.Tabs_registeredItems.length;n++){
		window.Tabs_registeredItems[n].className= window.Tabs_registeredItems_ClassNames[n];
		item_mirror = Tabs_mirror( window.Tabs_registeredItems[n] );
		if (item_mirror) {
			item_mirror.className = window.Tabs_registeredItems_ClassNames[n];
		}
	}
}

function Tabs_highlight( item, highlight_class ) {
	if (!highlight_class>'') highlight_class='current_tab';
	Tabs_clear();
	Tabs_register(item);
 	item.className = item.className + ' ' + highlight_class;	
	item_mirror = Tabs_mirror( window.Tabs_registeredItems[n] );
	if (item_mirror) {
		item_mirror.className = item_mirror.className + ' ' + highlight_class;
	}
	
}

function Tabs_highlight_mirror( item_mirror ){
	target_item = document.getElementById( item_mirror.id.substring(0,5));
	return Tabs_highlight( target_item );
}


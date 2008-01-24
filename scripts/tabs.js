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

function AMP_simple_tabs( ) {
    return {
        triggers : new Array( ),
        segments : new Array( ),
        segment_count: 0,
        add: function( element, trigger  ) {
            this.segments[ this.segment_count ] = $( element );
            this.triggers[ this.segment_count ] = $( trigger );
            Event.observe( $( trigger ), 'click', function( ){ this.show( $( element ) ) }, this ) ;
            ++this.segment_count;
            },
        show: function( trigger ) {
            key = this.triggers.indexOf( trigger );
            //this.segments.each( function( item ) { item.addClassName( 'AMPComponent_hidden');});
			to_hide = this.segments.without( this.segments[ key ] );
            to_hide.each( function( item ) { if (item.visible()) new Effect.SwitchOff( item );});
            
            //this.segments[ key ].removeClassName( 'AMPComponent_hidden');
            if ( !this.segments[key].visible()) new Effect.Appear( this.segments[ key ] ) ;
            this.triggers.each( function( item ) { item.removeClassName( 'active');});
            trigger.addClassName( 'active');
        },
        collapse: function( ) {
            this.segments.each( function( item ) { if (item.visible()) new Effect.SwitchOff( item );});
            this.triggers.each( function( item ) { item.removeClassName( 'active');});
        }

    };

}



function nav_locations_order_update( sortable ) {
	new_order = Sortable.serialize( sortable.id, { name: "location_id"} );
	tracker_element = "order_tracker_" + sortable.id;
	$( tracker_element ).value = new_order;
	return true;
}

function nav_location_add( form ) {
	//save_fields = Array( 'navid', 'badge_id', 'position' );
	save_fields = form.elements;
	save_count = save_fields.length;
	for( i=0; i < save_count; ++i ) {
		current_field = save_fields[i].name;
		if (save_fields[i].type != 'submit' ) {
			newitem = document.createElement('input');
			newitem.type = 'text';
			newitem.value = form.elements[ current_field ].value;
			newitem.name = current_field + '[]';
			document.forms['nav_layouts'].appendChild( newitem );
		}
	}
	form.reset();

	return false;
}


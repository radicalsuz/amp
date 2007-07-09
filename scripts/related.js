var related_count = 0;
var Related =  {
	form: new Hash( ),
	fields: new Hash( )
}

function related_add( form, fieldset, form_id ) {
	field_count = fieldset.length;
	window.related_count++;
	Related.form[form_id].push( form );
	Related.fields[form_id] = Array( );

	for( i=0; i < field_count; ++i ) {
		current_field = fieldset[i];
		newitem = document.createElement('input');
		newitem.type = 'text';
		newitem.value = form.elements[ current_field ].value;
		newitem.name = current_field + '['+window.related_count+']';

		Related.fields[form_id][ window.related_count ].push( newitem.name ) ;
		form.appendChild( newitem );
	}

	return false;
}

function related_delete( related_index, form_id ) {
	field_count = Related.fields[form_id ][related_index].length;
	for( i=0; i < field_count; ++i ) {
		current_field = Related.fields[form_id][ related_index ][i];
		Related.forms[ form_id ].elements[ current_field ].remove( );
	}

}

function related_describe( form, fieldset, badge_id, form_id, prefix ) {
	field_count = fieldset.length;
	params = '';
	delimiter = '&';
	for( i=0; i < field_count; ++i ) {
		current_field = fieldset[i];
		if ( prefix ) {
			param_field = fieldset[i].substring( prefix.length )
		}
		params = params + param_field + '=' + form.elements[current_field].value + delimiter; 
		if ( form.elements[current_field].type == 'select') {
			form.elements[current_field ].selectedIndex = '';
		} else {
			form.elements[current_field ].value = '';
		}
	}
	if ( !badge_id ) return;

	params = params + 'badge='  + badge_id + delimiter;
	params = params + 'format=xml' + delimiter;
	params = params + 'related_index=' + window.related_count + delimiter;
	params = params + 'modin='  + form_id;

    new Ajax.Updater ( 
            'related_items_' + form_id,
            '/badge_widget.php',
             {
                 method: 'get',
                 parameters: params,
				 insertion: Insertion.Bottom
//                 onLoading: AMP_ajax_signal,
//                 onSuccess: AMP_ajax_clear
             }
	);


}

function related_delete_button( ) {
	document.create( 'input')
}

function AMP_ajax_signal( ) {
    InsertionAfter( $( 'add_div' ), '<div id="ajax_signal" style="text-align:center;padding:1em;"><img src="/img/ajax-loader.gif"></div>' );
}

function AMP_ajax_clear( ) {
    setTimeout( '$( "ajax_signal").addClassName( "hidden");', 3200 );
}


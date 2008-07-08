var related_count = 0;
/*
var Related =  {
	form: new Hash( ),
	fields: new Hash( )
}
*/

function related_add( form, fieldset, form_id ) {
	field_count = fieldset.length;
	window.related_count++;
	//Related.form[form_id].push( form );
	//Related.fields[form_id] = Array( );

  field_container = document.createElement('div');
  field_container.id = 'form_' + form_id + '_related_custom_fields_' + window.related_count;
	for( i=0; i < field_count; ++i ) {
		current_field = fieldset[i];
		newitem = document.createElement('input');
		newitem.type = 'hidden';
		newitem.value = form.elements[ current_field ].value;
		newitem.name = current_field + '['+window.related_count+']';

		//Related.fields[form_id][ window.related_count ].push( newitem.name ) ;
		field_container.appendChild( newitem );
	}
  form.appendChild( field_container );

	return false;
}

function related_delete( related_index, form_id ) {
  $('form_' + form_id + '_related_custom_fields_'+related_index).remove();
}

function related_describe( form, fieldset, badge_id, form_id, prefix ) {
	field_count = fieldset.length;
	params = '';
	delimiter = '&';
	for( i=0; i < field_count; ++i ) {
		current_field = fieldset[i];
		if ( prefix ) {
			param_field = fieldset[i].substring( prefix.length )
		} else {
			param_field = fieldset[i]
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

function URLEncode (clearString) {
  var output = '';
  var x = 0;
  clearString = clearString.toString();
  var regex = /(^[a-zA-Z0-9_.]*)/;
  while (x < clearString.length) {
    var match = regex.exec(clearString.substr(x));
    if (match != null && match.length > 1 && match[1] != '') {
    	output += match[1];
      x += match[1].length;
    } else {
      if (clearString[x] == ' ')
        output += '+';
      else {
        var charCode = clearString.charCodeAt(x);
        var hexVal = charCode.toString(16);
        output += '%' + ( hexVal.length < 2 ? '0' : '' ) + hexVal.toUpperCase();
      }
      x++;
    }
  }
  return output;
}

var RelatedForm = function() {
	var self = {
		display_container_id:  "related_form_",
		display_block_container_id: "_related_fields_",
		fieldset: Array(),
		create: function( form, fieldset, related_form_id, report_badge_id, field_prefix ) {
			self.form = form;
			fieldset.each( function(field_name) { self.add_field( field_name ); } );
			self.badge_id = report_badge_id;
			self.related_form_id = related_form_id;
			self.field_prefix = field_prefix;
			self.related_count = 0;
			self.display_container_id = self.display_container_id + related_form_id;
			self.display_block_container_id = related_form_id + self.display_block_container_id;

			if(!$( self.display_container_id )) {
				display_container = document.createElement('div');
			 	display_container.id = self.display_container_id;	
				Element.insert( form, display_container );
			}
			Event.observe( form, 'submit', function(e){ self.prepare_submit(); return false;} );
			return self;
		},
		add_field: function( field_name ) {
			self.fieldset[ self.fieldset.length ] = field_name;
			self.field_count = self.fieldset.length;
		},
		copy_to_hidden: function() {
			if( !self.fieldset.any( function(field_name) { return $F( self.form.elements[ field_name ] ); } )) return false;
			self.related_count++;
			self.display_block_container = document.createElement('div');
			self.display_block_container.id = self.display_block_container_id + self.related_count;
			self.fieldset.each( function(field_name) {
				new_hidden_field = document.createElement('input');
				new_hidden_field.type = 'hidden';
				new_hidden_field.name = self.related_form_id + '[' + field_name + '][' + self.related_count + ']';
				new_hidden_field.value = $F( self.form.elements[ field_name ] );
				if(!new_hidden_field.value) return;

				self.display_block_container.appendChild( new_hidden_field );
			} );
			$( self.display_container_id).insert( self.display_block_container );
			return true;
		},
		gather_values: function() {
			var value_set = Array();
			self.fieldset.each( function(field_name) {
				new_value = Form.Element.serialize( self.form.elements[ field_name ] );
				if (self.field_prefix)  new_value = new_value.substring( self.field_prefix.length );
				value_set.push( new_value );
			} );
			return value_set;
		},
		clear_values: function() {
			self.fieldset.each( function(field_name) {
				Form.Element.clear( self.form.elements[ field_name ] );
			} );
		},
		get_badge_display: function() {
			params = self.gather_values();
			params.push( 'badge=' + self.badge_id, 'format=xml', 'related_index=' + self.related_count, 'modin='+self.related_form_id, 'container='+self.display_block_container.id	);
			new Ajax.Updater( self.display_block_container, '/badge_widget.php', { method: 'get', parameters: params.join('&'), insertion: 'bottom', onSuccess: function(){ self.clear_values() } } );
		},
		prepare_submit: function() {
			self.save();
			self.form.elements[ self.related_form_id ].remove();
		},
		save: function() {
			if(self.copy_to_hidden()) {
				self.get_badge_display();
			}
		}
	};
	return self;
}();

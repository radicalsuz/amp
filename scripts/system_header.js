function getCookie(name)
{
    var dc = document.cookie;
    var prefix = name + "=";
    var begin = dc.indexOf("; " + prefix);
    if (begin == -1)
    {
        begin = dc.indexOf(prefix);
        if (begin != 0) return null;
    }
    else
    {
        begin += 2;
    }
    var end = document.cookie.indexOf(";", begin);
    if (end == -1)
    {
        end = dc.length;
    }
    return unescape(dc.substring(begin + prefix.length, end));
}
  
function setCookie(name, value, expires, path, domain, secure)
{
    document.cookie= name + "=" + escape(value) +
        ((expires) ? "; expires=" + expires.toGMTString() : "") +
        ((path) ? "; path=" + path : "") +
        ((domain) ? "; domain=" + domain : "") +
        ((secure) ? "; secure" : "");
}

function setPointer(theRow, theRowNum, theAction, theDefaultColor, thePointerColor, theMarkColor)
{
    var theCells = null;

    // 1. Pointer and mark feature are disabled or the browser can't get the
    //    row -> exits
    if ((thePointerColor == '' && theMarkColor == '')
        || typeof(theRow.style) == 'undefined') {
        return false;
    }

    // 2. Gets the current row and exits if the browser can't get it
    if (typeof(document.getElementsByTagName) != 'undefined') {
        theCells = theRow.getElementsByTagName('td');
    }
    else if (typeof(theRow.cells) != 'undefined') {
        theCells = theRow.cells;
    }
    else {
        return false;
    }

    // 3. Gets the current color...
    var rowCellsCnt  = theCells.length;
    var domDetect    = null;
    var currentColor = null;
    var newColor     = null;
    // 3.1 ... with DOM compatible browsers except Opera that does not return
    //         valid values with "getAttribute"
    if (typeof(window.opera) == 'undefined'
        && typeof(theCells[0].getAttribute) != 'undefined') {
        currentColor = theCells[0].getAttribute('bgcolor');
        domDetect    = true;
    }
    // 3.2 ... with other browsers
    else {
        currentColor = theCells[0].style.backgroundColor;
        domDetect    = false;
    } // end 3

    // 3.3 ... Opera changes colors set via HTML to rgb(r,g,b) format so fix it
    if (currentColor.indexOf("rgb") >= 0)
    {
        var rgbStr = currentColor.slice(currentColor.indexOf('(') + 1,
                                     currentColor.indexOf(')'));
        var rgbValues = rgbStr.split(",");
        currentColor = "#";
        var hexChars = "0123456789ABCDEF";
        for (var i = 0; i < 3; i++)
        {
            var v = rgbValues[i].valueOf();
            currentColor += hexChars.charAt(v/16) + hexChars.charAt(v%16);
        }
    }

    // 4. Defines the new color
    // 4.1 Current color is the default one
    if (currentColor == ''
        || currentColor.toLowerCase() == theDefaultColor.toLowerCase()) {
        if (theAction == 'over' && thePointerColor != '') {
            newColor              = thePointerColor;
        }
        else if (theAction == 'click' && theMarkColor != '') {
            newColor              = theMarkColor;
            marked_row[theRowNum] = true;
            // Garvin: deactivated onclick marking of the checkbox because it's also executed
            // when an action (like edit/delete) on a single item is performed. Then the checkbox
            // would get deactived, even though we need it activated. Maybe there is a way
            // to detect if the row was clicked, and not an item therein...
            // document.getElementById('id_rows_to_delete' + theRowNum).checked = true;
        }
    }
    // 4.1.2 Current color is the pointer one
    else if (currentColor.toLowerCase() == thePointerColor.toLowerCase()
             && (typeof(marked_row[theRowNum]) == 'undefined' || !marked_row[theRowNum])) {
        if (theAction == 'out') {
            newColor              = theDefaultColor;
        }
        else if (theAction == 'click' && theMarkColor != '') {
            newColor              = theMarkColor;
            marked_row[theRowNum] = true;
            // document.getElementById('id_rows_to_delete' + theRowNum).checked = true;
        }
    }
    // 4.1.3 Current color is the marker one
    else if (currentColor.toLowerCase() == theMarkColor.toLowerCase()) {
        if (theAction == 'click') {
            newColor              = (thePointerColor != '')
                                  ? thePointerColor
                                  : theDefaultColor;
            marked_row[theRowNum] = (typeof(marked_row[theRowNum]) == 'undefined' || !marked_row[theRowNum])
                                  ? true
                                  : null;
            // document.getElementById('id_rows_to_delete' + theRowNum).checked = false;
        }
    } // end 4

    // 5. Sets the new color...
    if (newColor) {
        var c = null;
        // 5.1 ... with DOM compatible browsers except Opera
        if (domDetect) {
            for (c = 0; c < rowCellsCnt; c++) {
                theCells[c].setAttribute('bgcolor', newColor, 0);
            } // end for
        }
        // 5.2 ... with other browsers
        else {
            for (c = 0; c < rowCellsCnt; c++) {
                theCells[c].style.backgroundColor = newColor;
            }
        }
    } // end 5

    return true;
} // end of the 'setPointer()' function



function deleteCookie(name)
{
    if (getCookie(name))
    {
        document.cookie = name + "=" + 
           "; expires=Thu, 01-Jan-70 00:00:01 GMT";
    }
}



function changex(which) {
    document.getElementById('standard').style.display = 'none';
document.getElementById('basic').style.display = 'none'; 
    document.getElementById(which).style.display = 'block';
	
    }

function hideClass(theclass, objtype) {
	if (!objtype>'') {objtype='div';}
	var objset=document.getElementsByTagName(objtype);
	for (i=0;i<objset.length; i++) {
		if (objset.item(i).className == theclass){
			objset.item(i).style.display = 'none';
		}
	}
	
}

function showClass(theclass, objtype) {
	if (!objtype>'') {objtype='div';}
	var objset=document.getElementsByTagName(objtype);
	for (i=0;i<objset.length; i++) {
		if (objset.item(i).className == theclass){
			objset.item(i).style.display = 'block';
		}
	}
}

function change_any(which, whatkind) {
	if (whatkind!='') {hideClass(whatkind, '');}
	target = document.getElementById(which);
	if (!target) return false;
	if(document.getElementById(which).style.display == 'block' ) {
		document.getElementById(which).style.display = 'none';
	} else {
		document.getElementById(which).style.display = 'block';
	}
}

function change_form_block(which) {
    var setting = document.getElementById(which).style.display;
    if ( setting == 'block' ) {
        var parentDiv = document.getElementById(which + "_parent");
        parentDiv.className = 'fieldset';
        document.getElementById(which).style.display = 'none';
        document.getElementById( "arrow_" + which ).style.display = 'block';
        document.getElementById( "arrow_" + which ).src = 'images/arrow-right.gif';
    } else {
        var parentDiv = document.getElementById(which + "_parent");
        parentDiv.className = 'fieldset fieldextra';
        document.getElementById(which).style.display = 'block';
        document.getElementById( "arrow_" + which ).style.display = 'none';
    }
}

function change_all_blocks( setting ) {
	if (!setting>'') {setting='block';}
	var block_set=document.getElementsByTagName('table');
	for (i=0;i<block_set.length; i++) {
		if ( block_set.item(i).className == 'form_hider' ) {
			var parentDiv = document.getElementById(block_set.item(i).id + "_parent");
			//summary = summary + parentDiv.id + " : " + block_set.item(i).style.display + " vs " + setting + "\n";
			if ( block_set.item(i).style.display != setting ) {
				change_form_block( block_set.item(i).id );
			}
		}
	}
}

function showUploadWindow (parentform, calledfield, dtype, handler) {
    alert( 'Sorry, this upload mechanism has been disabled for security reasons');
    //url  = 'http://'+location.host+'/upload_popup.php?pform='+parentform+'&pfield='+calledfield;
    //if (dtype) url = url + '&doctype='+dtype;
    //if (handler) url = url + '&handler='+handler;
    //hWnd = window.open( url, 'recordWindow', 'height=175,width=300,scrollbars=no,menubar=no,toolbar=no,resizeable=no,location=no,status=no' );
}

function AMP_flash_signal( ) {
    $( 'AMP_flash').hide( );
    $( 'AMP_flash').update( '<div id="ajax_signal" style="text-align:center;padding:1em;"><img src="/img/ajax-loader.gif"></div>' );
    Effect.SlideDown( 'AMP_flash');
}

function AMP_permission_update( ) {
    new Ajax.Updater ( 
            'AMP_flash',
            '/system/permission.php',
            {
                method: 'post',
                parameters: 'action=update',
                onLoading: AMP_flash_signal,
                onSuccess: AMP_flash_clear
            }
        );
}

function clear_AMP_cache( ){
    new Ajax.Updater ( 
            'AMP_flash',
            '/system/cache.php',
             {
                 method: 'post',
                 parameters: 'action=flush',//'&source_url='+window.url.href,
                 onLoading: AMP_flash_signal,
                 onSuccess: AMP_flash_clear
             }
        );
}

function AMP_flash_clear( ) {
    setTimeout( 'new Effect.BlindUp( "AMP_flash")', 3200 );
}

function AMP_show_panel( panel_id ) {
    $( panel_id ).show( );
    new Effect.ScrollTo( panel_id, { offset: -50 } ); 
    new Effect.Highlight( panel_id, { duration: 3 } );
    new Effect.Pulsate( panel_id, { from: 0.3, pulses: 3, duration: 2 } );
    return false;
}

function AMP_change_list_order( list_id, item_id, offset_to_new_location ) {
    //only works for offsets of 1 and -1, will only switch 2 elements
    original_set = Sortable.sequence( list_id );
    old_location = original_set.indexOf( item_id );
    set_length = original_set.length;
    new_location = old_location + offset_to_new_location;

    if ( ( new_location < 0 ) || ( new_location > set_length )) {
             return false;
    }
    tmp = original_set[ new_location ];
    original_set[ new_location ] = original_set[ old_location ];
    original_set[old_location] = tmp;

    Sortable.setSequence( list_id, original_set );

}

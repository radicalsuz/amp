function ElementCopier ( formname, start_qty ) {
    this.ElementSets = new Array(); //Holds pointers to form elements

    //the number of sets being displayed on the form
    this.set_qty = start_qty;
    this.formRef = document.forms[ formname ]
    this.formtable = window.findFormTable( formname );
    this.dup_elements=new Array();

    this.DuplicateElementSet = DuplicateElementSet;
    this.defineElement = defineElement;
    this.addElement = addElement;
    this.addImage = addImage;
    this.addButton = addButton;
    this.addHeader = addHeader;
    this.addStatic = addStatic;
    this.addTextArea = addTextArea;
    this.addLabel = addLabel;
    this.isSpan = isSpan;
    this.findSetbyRow = findSetbyRow;
	this.setFormTable = setFormTable;

    this.getInstanceName = getInstanceName;
    this.findDupElementIndex = findDupElementIndex;
    this.populateSet = populateSet;
    this.makenew = makenew;
    this.CopySelect = CopySelect;
    this.BuildSelect = BuildSelect;
    this.SaveSet = SaveSet;
    this.MoveSet = MoveSet;
    this.RemoveSet = RemoveSet;
    this.RemoveCurrentSet = RemoveCurrentSet;
    //this.ValidateItems = ValidateItems;
    //this.event = event;
    this.pushValue = pushValue;
    this.pullValue = pullValue;
	this.singleRow = false;
	this.labelColumn = true;
	this.table_id = false;
	this.startRowOffset = -1;
	this.cssElementClassName = false;

}

function addElement( name, element_type ) {
    newitem = document.createElement('input');
    newitem.type = element_type;
    newitem.name = name;

    return newitem;
}

function addTextArea ( name, txt_rows, txt_cols ) {
    newitem = document.createElement('textarea');
    if (txt_rows) newitem.rows = txt_rows;
    if (txt_cols) newitem.cols = txt_cols;
    newitem.name = name;

    return newitem;
}

function addImage (name, imgsrc) {
    newimage = document.createElement('image');
    newimage.setAttribute('src', imgsrc);
    return newimage;
}

function addHeader ( label ){
    newheader = document.createElement('span');
    newheader.setAttribute ( 'class', 'udm_header' );
    newheader.className = 'udm_header';
    newheader.appendChild( document.createTextNode( label ));
    return newheader;
}

function addStatic ( label ){
    newheader = document.createElement( 'span' );
    newheader.className = 'udm_label';
    newheader.innerHTML = label;
    return newheader;
}
function addButton (name, caption, action) {
    newbutton = this.addElement( name, 'button');
    newbutton.value = caption;
    if (action){
        event (newbutton, 'onclick', action);
    }
    return newbutton;
}

function getInstanceName( set_qty, elementDef ) {
    return elementDef.name + '[' + set_qty + ']';
}


function makenew( elementdef ) {
   // var model_name = elementdef.name + '[' + ( this.set_qty - 1 ) + ']';
    var instance_name = this.getInstanceName( this.set_qty, elementdef ); 

    switch (elementdef.type) {
        case 'button':
            newitem = this.addButton(instance_name, elementdef.label, elementdef.action);
            break;
        case 'image':
            newitem = this.addImage(instance_name, elementdef.label);
            break;
        case 'header':
            newitem = this.addHeader( elementdef.values );
            break;
        case 'static':
            newitem = this.addStatic( elementdef.values );
            break;
        case 'select':
            if ( this.set_qty > 1 ) {
                newitem = this.CopySelect( instance_name,  elementdef.values );
            } else {
            	newitem = this.BuildSelect (instance_name, elementdef );
			}

            break;
        case 'textarea':
            newitem = this.addTextArea( instance_name, 20, 50 ); 
            break;
        default:
            newitem = this.addElement( instance_name, elementdef.type );
    }
	if ( this.cssElementClassName && !newitem.className ){
		newitem.className = this.cssElementClassName;
	}

    return newitem;
}

function defineElement( name, type, label, values, action, source, size, required, attr ) {
    element_def = new elementDefinition();
    element_def.name = name;
    element_def.type = type;
    element_def.label = label;
    element_def.action = action;
    element_def.values = values;
    element_def.attr = attr;
    /*
    element_def.source = source;
    element_def.required = required;
    element_def.size = size;
    */
    this.dup_elements[this.dup_elements.length] = element_def;
}

function elementDefinition() {
    var name;
    var type;
    var label;
    var action;
    var attr;
    /*
    var source;
    var values;
    var required;
    var size;
    */
}


function DuplicateElementSet ( which, startRow ) {
    which.set_qty++;
    which.ElementSets[which.set_qty]=new Array();
    which.ElementSets[which.set_qty]['elements']=new Array();

    if (!startRow) startRow = which.formtable.tBodies[0].rows.length+which.startRowOffset;
    which.ElementSets[which.set_qty]['start_row'] = startRow;
	if (which.singleRow) {
		new_single_row = which.formtable.tBodies[0].insertRow( startRow );
	} else {
		new_single_row = false;
	}
    
    for (i=0; i<which.dup_elements.length; i++) {
        if( !new_single_row ){
			 newrow=which.formtable.tBodies[0].insertRow( startRow+i  );
		} else {
			newrow = new_single_row;
		}
		if (which.labelColumn) {
			newcell = newrow.insertCell(newrow.cells.length);
			which.addLabel( newcell, which.dup_elements[i] );
			if ( !which.isSpan( which.dup_elements[i].type )) {
				newrow.appendChild( newcell );
				newinput = newrow.insertCell( newrow.cells.length );
			} else {
				newinput = newcell;
				newinput.setAttribute( 'colspan', 2 );
				newinput.colSpan = 2;
			}
		} else {
			if (which.dup_elements[i].type == 'hidden') {
				newinput = which.formRef;
			} else {
				newinput = newrow.insertCell( newrow.cells.length );
			}
		}
        which.ElementSets[which.set_qty]['elements'][i] = which.makenew( which.dup_elements[i] );
        if ( which.ElementSets[ which.set_qty ]['elements'][ i ] ) {
            newinput.appendChild(  which.ElementSets[which.set_qty]['elements'][i] );
        }
        //newrow.appendChild( newinput );

    }
}

function restoreSet( which, startElementName, dataFieldSet, dataNameSet ) {
    //DuplicateElementSet( which, parentRow( which.formRef.elements[ startElementName ]).rowIndex ); 
    DuplicateElementSet( which ); 
    which.populateSet( which.set_qty, dataFieldSet, dataNameSet );
}

function addLabel( newcell, element_def ) {
    if (element_def.type == 'button') return false;
    if (element_def.label == "" ) return false;

    labelspan =  document.createElement('SPAN'); 
    labelspan.className = 'form_label_col';
    labelspan.innerHTML = element_def.label;
    newcell.appendChild( labelspan );
    if ( element_def.type == 'textarea' ) {
        labelspan.className = 'form_span_col';
        newcell.appendChild( document.createElement( 'br' ) );
    }
}

function isSpan( element_type ) {
    if ( element_type == 'button' ) return true; 
    if ( element_type == 'header' ) return true; 
    if ( element_type == 'static' ) return true; 
    if ( element_type == 'textarea' ) return true; 
    return false;
}

function getNewElement( el_index ) {
    new_item = this.ElementSets[this.set_qty]['elements'][el_index];
    //new_item.name = new_item.name;
}

function SaveSet (set_index) { //This is A Javascript Function
    //wont work in IE for rows generated by script
    //used to commit the first row, which serves as a template
    this.ElementSets[set_index]['elements']=new Array();
    for (i=0; i<this.dup_elements.length; i++) {
        this.ElementSets[set_index]['elements'][i]=this.formRef.elements[ this.dup_elements[i].name +'['+set_index+']'];
    }
    
}

function MoveSet(source_set,target_set) {
    if ( target_set <= 0 ) return false;
    for (i=0; i<this.dup_elements.length; i++) {
        this.pushValue( this.pullValue( this.ElementSets[source_set]['elements'][i] ), this.ElementSets[target_set]['elements'][i] );
    }
}	

function pullValue( elementRef ) {
    if (elementRef.type == "select") {
        return elementRef.selectedIndex;
    } else {
        return elementRef.value;
    }

}

function pushValue( pvalue, elementRef ) {
    if (elementRef.type == "select") {
        elementRef.selectedIndex = pvalue;
    } else {
        elementRef.value = pvalue;
    }
    /*
    if (this.dup_elements[i].type == "select") {
        this.ElementSets[target_set]['elements'][i].selectedIndex=this.ElementSets[source_set]['elements'][i].selectedIndex;
    } else {
        this.ElementSets[target_set]['elements'][i].value = this.ElementSets[source_set]['elements'][i].value;
    }
    */
}

/*
function ValidateItems () {

    //sets an indicator flag for which values should be kept
    var newrow=this.formtable.tBodies[0].appendChild(document.createElement('tr'));

    for (n=1; n<=this.set_qty; n++) {
        var newflag=document.createElement('input');
        newflag.type='hidden';
        newflag.name='valid_row['+n+']';
        newflag.value=1;
        newrow.appendChild(newflag);
    }
}
*/

function RemoveCurrentSet( elementRef ) {
    row_index = parentRow( elementRef ).rowIndex;
    set_index = this.findSetbyRow( row_index );
    this.RemoveSet( set_index );
}

function RemoveSet( set_index ) {
    if (this.set_qty<=0)  return false;

    for (n=set_index; n<this.set_qty; n++) {
        this.MoveSet( (parseInt(n)+1), n);
    }
    
    startrow = this.ElementSets[this.set_qty]['start_row'];
    this.set_qty=this.set_qty-1;
    //startrow = this.set_qty*this.dup_elements.length;
	if ( this.singleRow ) {
		this.formtable.deleteRow( startrow );
	} else {
		for (n=this.dup_elements.length-1; n>=0; n--) {
			this.formtable.deleteRow(startrow + n);
		}
	}
	for (n=0; n<this.dup_elements.length; n++) {
		if (this.dup_elements[n].type == 'hidden') {
			nameIndex = this.findDupElementIndex( this.dup_elements[n].name );
			elementRef = this.ElementSets[set_index]['elements'][ nameIndex ];
			this.pushValue( 0, elementRef );
		} 
	}
        
        /*
    } else {
        for (n=0; n<this.dup_elements.length; n++) {
            if (this.dup_elements[n].type == 'select') {
                this.dup_elements[n].selectedIndex = 0;
            } else {
                this.dup_elements[n].value = '';
            }
        }
        this.formRef.elements[ (this.dup_elements[0].name+'[1]') ].focus;
    }
    */
} 

//To Ensure IE Compliance for assigning onClick action
function event(elem,handler,funct) {//This is A Javascript Function

    if(document.all) {
        elem[handler] = new Function(funct);
    } else {
        elem.setAttribute(handler,funct);
    }
}

function populateSet( set_index, dataSetValues, dataSetNames ) {
    for ( k=0; k<dataSetNames.length; k++ ) {
        nameIndex = this.findDupElementIndex( dataSetNames[k] );
        if ( nameIndex === false ) continue;
        elementRef = this.ElementSets[ set_index ][ 'elements' ][nameIndex]
        this.pushValue( dataSetValues[ k ], elementRef );
    }
}

function findDupElementIndex( elementName ) {
    for ( whichelement =0; whichelement < this.dup_elements.length; whichelement++ ) {
        if ( this.dup_elements[ whichelement ].name == elementName ) return whichelement;
    }
    return false;
}


function CopySelect(newname, modelvalues) {
    var newselect=document.createElement('select');
    newselect.name = newname;
    for (n=0; n<modelvalues.length; n++) {
        newselect.options[n] = new Option( modelvalues[n].text, modelvalues[n].value );
    }

    /*
    for (n=0; n<model.options.length; n++) {
        newselect.options[n] = new Option(model.options[n].text, model.options[n].value);
    }
    */
    
    return(newselect);
}

function BuildSelect (newname, element_def) {
    var newselect=document.createElement('select');
    newselect.name = newname;
    for (n=0; n<element_def.values.length; n++) {
        newselect.options[n] = element_def.values[n];
    }
    return newselect;
}

function findSetbyRow( row_index ) {
    if (this.set_qty <= 0) return false;
	if (this.singleRow) {
		return row_index - this.ElementSets[ 1 ]['start_row'] + 1;
	}
    for (k = 1; k<=this.set_qty; k++) {
        if ( (row_index  > this.ElementSets[ k ]['start_row']) && row_index < parseInt(this.ElementSets[ k ]['start_row'] + this.dup_elements.length )) {
            return k;
        }
    }
    return 0;
}

function findFormTable ( formname ) {
    table_set = document.getElementsByTagName('TABLE');
    for (n=0; n<table_set.length; n++) {
        if (hasParent(table_set[n], document.forms[formname])) {
            return table_set[n];
        }
    }
    return false;
}

function setFormTable( table_id ) {
	candidate_table = document.getElementById( table_id );
	if (candidate_table){
		this.formtable = candidate_table;
	}
}

function hasParent( testchild, desired_parent ) {
    if (testchild.parentNode == desired_parent) return true;
    if (testchild.parentNode == document ) return false;
    return hasParent(testchild.parentNode, desired_parent);
}

function parentRow( elementRef ) {
    if ( elementRef.parentNode.tagName == 'BODY' ) {
        return false;
    }
    if ( elementRef.parentNode.tagName == 'TR' ) {
        return elementRef.parentNode;
    }
    return parentRow( elementRef.parentNode );
}

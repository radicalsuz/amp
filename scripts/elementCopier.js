function ElementCopier ( formname, start_qty ) {
    this.ElementSets =new Array(); //Holds pointers to form elements

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
    this.makenew = makenew;
    this.CopySelect = CopySelect;
    this.SaveSet = SaveSet;
    this.MoveSet = MoveSet;
    this.RemoveSet = RemoveSet;
    this.ValidateItems = ValidateItems;
    this.event = event;


}

function addElement( name, element_type ) {
    newitem = document.createElement('input');
    newitem.type = element_type;
    newitem.name = name;

    return newitem;
}

function addImage (name, imgsrc) {
    newimage = document.createElement('image');
    newimage.setAttribute('src', imgsrc);
}

function addButton (name, caption, action) {
    newbutton = this.addElement( name, 'button');
    newbutton.value = caption;
    if (action){
        event (newbutton, 'onClick', action);
    }
    return newbutton;
}


function makenew( elementdef ) {
    var model_name = elementdef.name;
    elementdef.name = elementdef.name+'['+this.set_qty+']';

    switch (elementdef.type) {
        case 'button':
            newitem = this.addButton(elementdef.name, elementdef.label, elementdef.action);
            break;
        case 'image':
            newitem = this.addImage(elementdef.name, elementdef.label);
            break;
        case 'select':
            newitem = this.CopySelect( this.formRef.elements[model_name+'['+(this.set_qty-1)+']'] );
            break;
        default:
            newitem = this.addElement( elementdef.name, elementdef.type );
    }

    return newitem;
}

function defineElement( name, type, label, action, source ) {
    element_def = new elementDefinition();
    element_def.name = name;
    element_def.type = type;
    element_def.label = label;
    element_def.action = action;
    element_def.source = source;
    this.dup_elements[this.dup_elements.length] = element_def;
}

function elementDefinition() {
    var name;
    var type;
    var label;
    var action;
    var source;
}


function DuplicateElementSet ( which ){
    which.set_qty++;
    which.ElementSets[which.set_qty]=new Array();
    
    for (i=0; i<which.dup_elements.length; i++) {
        //newrow=which.formtable.tBodies[0].appendChild(document.createElement('tr'));
        newrow=which.formtable.tBodies[0].insertRow( which.formtable.rows.length - 1 );
        which.ElementSets[which.set_qty][i] = which.makenew( which.dup_elements[i] );
        newlabel = newrow.insertCell(newrow.cells.length);
        labeltext =  document.createTextNode(which.dup_elements[i].label);
        newlabel.appendChild( labeltext );
        newinput = newrow.insertCell( newrow.cells.length );
        newinput.appendChild(  which.ElementSets[which.set_qty][i] );
        //newrow.appendChild( newinput );

    }
}

function getNewElement( el_index ) {
    new_item = this.ElementSets[this.set_qty][el_index];
    //new_item.name = new_item.name;
}

function SaveSet (rowindex) { //This is A Javascript Function
    //wont work in IE for rows generated by script
    //used to commit the first row, which serves as a template
    this.ElementSets[rowindex]=new Array();
    for (i=0; i<this.dup_elements.length; i++) {
        this.ElementSets[rowindex][i]=this.formRef.elements[ this.dup_elements[i].name +'['+rowindex+']'];
    }
    
}
//This is A Javascript Function
//Which Moves values *from* one set of select boxes *to* another

function MoveSet(from,to) {
    for (i=0; i<this.dup_elements.length; i++) {
        if (this.dup_elements[i].type == "select") {
            this.ElementSets[to][i].selectedIndex=this.ElementSets[from][i].selectedIndex;
        } else {
            this.ElementSets[to][i].value = this.ElementSets[from][i].value;
        }
    }
}	

function ValidateItems () { //Javascript Function

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

function RemoveSet(which) { //This is A Javascript Function
    if (this.set_qty>=1) {
        for (n=which; n<this.set_qty; n++) {
            MoveRow(n+1, n);
        }
        
        this.set_qty=this.set_qty-1;
        startrow = this.set_qty*this.dup_elements.length;
        for (n=0; n<this.dup_elements.length; n++) {
            this.formtable.deleteRow(startrow + n);
        }
        
    } else {
        for (n=0; n<this.dup_elements.length; n++) {
            if (this.dup_elements[n].type == 'select') {
                this.dup_elements[n].selectedIndex = 0;
            } else {
                this.dup_elements[n].value = '';
            }
        }
        this.formRef.elements[this.dup_elements[0].name+'[1]'].focus;
    }
} 

//This is A Javascript Function
//To Ensure IE Compliance for assigning onClick action
function event(elem,handler,funct) {//This is A Javascript Function

    if(document.all) {
        elem[handler] = new Function(funct);
    } else {
        elem.setAttribute(handler,funct);
    }
}

function CopySelect(model) {
    var newselect=document.createElement('select');
    newselect.name = model.name +'['+ this.set_qty +']';
    for (n=0; n<selbox.options.length; n++) {
        newselect.options[n] = new Option(model.options[n].text, model.options[n].value);
    }
    
    return(newselect);
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

function hasParent(testchild, desired_parent) {
    if (testchild.parentNode == desired_parent) return true;
    if (testchild.parentNode == document ) return false;
    return hasParent(testchild.parentNode, desired_parent);
}

function ElementSwapper ( satformname ) {
    this.ElementSets = new Array();

    this.addSwapElement = addSwapElement;
    this.addSwapSet = addSwapSet;
    this.swapSetOn = swapSetOn;
    this.swapSetOff = swapSetOff;
    this.getSwapSet = getSwapSet;
    this.ActivateSwap = ActivateSwap;
    this.swapForm = document.forms[ satformname ];
    this.visibleStyle = "table-row";
    this.hideStyle = 'none';

}

function addSwapElement ( name, setname ) {
    updateset = this.getSwapSet ( setname );
    if (updateset  && this.swapForm.elements[ name ]) {
        updateset[ updateset.length ] = this.swapForm.elements[ name ];
    }
}


function getSwapSet ( setname ) {
    for (i=0; i<this.ElementSets.length; i++ ) {
        if (this.ElementSets[i].name == setname) {
            return this.ElementSets[i];
        }
    }
    return false;
}

function addSwapSet ( setname ) {
    newset = this.ElementSets[ this.ElementSets.length ] = Array();
    newset.name = setname;
}

function swapSetOn ( givenSet ) {
    if (!givenSet.length) return false;
    for (i=0; i<givenSet.length; i++) {
        givenSet[i].parentNode.parentNode.style.display = this.visibleStyle;
    }
}

function swapSetOff ( givenSet ) {
    if (!givenSet.length) return false;
    for (i=0; i<givenSet.length; i++) {
        givenSet[i].parentNode.parentNode.style.display = this.hideStyle;
    }
}

function ActivateSwap ( swapper, setname ) {
    for (n=0; n<swapper.ElementSets.length; n++) {
        swapper.swapSetOff (swapper.ElementSets[n]);
    }
    if ( setname ) {
        swapper.swapSetOn ( swapper.getSwapSet( setname ) );
    }
}

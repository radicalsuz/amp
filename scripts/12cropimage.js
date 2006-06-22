/****************************************************************************
DHTML library from DHTMLCentral.com
*   Copyright (C) 2001 Thomas Brattli 2001
*   This script was released at DHTMLCentral.com
*   Visit for more great scripts!
*   This may be used and changed freely as long as this msg is intact!
*   We will also appreciate any links you could give us.
*
*   Made by Thomas Brattli 2001
***************************************************************************/

//Browsercheck (needed) ***************
function lib_bwcheck(){
  this.ver=navigator.appVersion
  this.agent=navigator.userAgent
  this.dom=document.getElementById?1:0
  this.opera5=this.agent.indexOf("Opera 5")>-1
  this.ie5=(this.ver.indexOf("MSIE 5")>-1 && this.dom && !this.opera5)?1:0;
  this.ie6=(this.ver.indexOf("MSIE 6")>-1 && this.dom && !this.opera5)?1:0;
  this.ie4=(document.all && !this.dom && !this.opera5)?1:0;
  this.ie=this.ie4||this.ie5||this.ie6
  this.mac=this.agent.indexOf("Mac")>-1
  this.ns6=(this.dom && parseInt(this.ver) >= 5) ?1:0;
  this.ns4=(document.layers && !this.dom)?1:0;
  this.bw=(this.ie6||this.ie5||this.ie4||this.ns4||this.ns6||this.opera5)
  return this
}
bw=new lib_bwcheck() //Browsercheck object

//Debug function ******************
function lib_message(txt){alert(txt); return false}

//Lib objects  ********************
function lib_obj(obj,nest){
  if(!bw.bw) return lib_message('Old browser')
  nest=(!nest) ? "":'document.'+nest+'.'
  this.evnt=bw.dom? document.getElementById(obj):
    bw.ie4?document.all[obj]:bw.ns4?eval(nest+"document.layers." +obj):0;
  if(!this.evnt) return lib_message('The layer does not exist ('+obj+')'
    +'- \nIf you are using Netscape please check the nesting of your tags!')
  this.css=bw.dom||bw.ie4?this.evnt.style:this.evnt;
  this.ref=bw.dom||bw.ie4?document:this.css.document;
  this.x=parseInt(this.css.left)||this.css.pixelLeft||this.evnt.offsetLeft||0;
  this.y=parseInt(this.css.top)||this.css.pixelTop||this.evnt.offsetTop||0
  this.w=this.evnt.offsetWidth||this.css.clip.width||
    this.ref.width||this.css.pixelWidth||0;
  this.h=this.evnt.offsetHeight||this.css.clip.height||
    this.ref.height||this.css.pixelHeight||0
  this.c=0 //Clip values
  if((bw.dom || bw.ie4) && this.css.clip) {
  this.c=this.css.clip; this.c=this.c.slice(5,this.c.length-1);
  this.c=this.c.split(' ');
  for(var i=0;i<4;i++){this.c[i]=parseInt(this.c[i])}
  }
  this.ct=this.css.clip.top||this.c[0]||0;
  this.cr=this.css.clip.right||this.c[1]||this.w||0
  this.cb=this.css.clip.bottom||this.c[2]||this.h||0;
  this.cl=this.css.clip.left||this.c[3]||0
  this.obj = obj + "Object"; eval(this.obj + "=this")
  return this
}

//Moving object to **************
lib_obj.prototype.moveIt = function(x,y){
  this.x=x;this.y=y; this.css.left=x;this.css.top=y
}

//Clipping object to ******
lib_obj.prototype.clipTo = function(t,r,b,l,setwidth){
  this.ct=t; this.cr=r; this.cb=b; this.cl=l
  if(bw.ns4){
    this.css.clip.top=t;this.css.clip.right=r
    this.css.clip.bottom=b;this.css.clip.left=l
  }else{
    if(t<0)t=0;if(r<0)r=0;if(b<0)b=0;if(b<0)b=0
    this.css.clip="rect("+t+","+r+","+b+","+l+")";
    if(setwidth){this.css.pixelWidth=this.css.width=r;
    this.css.pixelHeight=this.css.height=b}
  }
}

//Drag drop functions start *******************
dd_is_active=0; dd_obj=0; dd_mobj=0
function lib_dd(){
  dd_is_active=1
  if(bw.ns4){
    document.captureEvents(Event.MOUSEMOVE|Event.MOUSEDOWN|Event.MOUSEUP)
  }
  document.onmousemove=lib_dd_move;
  document.onmousedown=lib_dd_down
  document.onmouseup=lib_dd_up
}
lib_obj.prototype.dragdrop = function(obj){
  if(!dd_is_active) lib_dd()
  this.evnt.onmouseover=new Function("lib_dd_over("+this.obj+")")
  this.evnt.onmouseout=new Function("dd_mobj=0")
  if(obj) this.ddobj=obj
}
lib_obj.prototype.nodragdrop = function(){
  this.evnt.onmouseover=""; this.evnt.onmouseout=""
  dd_obj=0; dd_mobj=0
}
//Drag drop event functions
function lib_dd_over(obj){dd_mobj=obj}
function lib_dd_up(e){dd_obj=0}
function lib_dd_down(e){ //Mousedown
  if(dd_mobj){
    x=(bw.ns4 || bw.ns6)?e.pageX:event.x||event.clientX
    y=(bw.ns4 || bw.ns6)?e.pageY:event.y||event.clientY
    dd_obj=dd_mobj
    dd_obj.clX=x-dd_obj.x;
    dd_obj.clY=y-dd_obj.y
  }
}
function lib_dd_move(e,y,rresize){ //Mousemove
  x=(bw.ns4 || bw.ns6)?e.pageX:event.x||event.clientX
  y=(bw.ns4 || bw.ns6)?e.pageY:event.y||event.clientY
  if(dd_obj){
    nx=x-dd_obj.clX; ny=y-dd_obj.clY
    if(dd_obj.ddobj) dd_obj.ddobj.moveIt(nx,ny)
    else dd_obj.moveIt(nx,ny)
  }
  if(!bw.ns4) return false
}
//Drag drop functions end *************


    function CropInterface( layer_id ){
        this.crop_obj=new lib_obj( layer_id );
        this.crop_obj.dragdrop()
        this.image = false;
        this.image_filename = false;

        this.Check = cropCheck;
        this.Stop = stopZoom;
        this.Bigger = Bigger;
        this.Smaller = Smaller;

        this.setImage = setImage;
        this.setRatio = setRatio;

        this.display_ratio = 1;
        this.window_ratio = 1;
        this.crop_min_width = 50;
        this.crop_min_height = 50;

        this.zoomtimer = null;
    }

    function setImage( image_name, image_filename ){
        if ( imageRef = document.images[ image_name ] ) {
            this.image = imageRef;
            this.image_filename = image_filename;
            layer_obj = document.getElementById( 'cropDiv');
            this.crop_obj.x = this.image.x;
            this.crop_obj.y = this.image.y;
            layer_obj.style.left = this.image.x;
            layer_obj.style.top  = this.image.y;
        }
    }

    function setRatio( ratio ){
        this.display_ratio=ratio;
        this.crop_min_height = 50/ratio;
        this.crop_min_width = 50/ratio;
    }

	function cropCheck(crA, formname ){
	   if (!((((this.crop_obj.x + this.crop_obj.cr)-this.image.x ) <= this.image.width )&&(((this.crop_obj.y + this.crop_obj.cb)- this.image.y ) <= this.image.height )&&(this.crop_obj.x >= this.image.x )&&(this.crop_obj.y >= this.image.y))) {
	        alert('The selection has to be completely on the image');
            return false;
       }
       formRef = document.forms[formname];
       if (  formname >= ''){
           formRef.elements['start_x'].value = this.crop_obj.x - this.image.x;
           formRef.elements['start_y'].value = this.crop_obj.y - this.image.y;
           formRef.elements['width'].value = this.crop_obj.cr;
           formRef.elements['height'].value = this.crop_obj.cb;
           return true;
       }
        var url = 'image.php?action=crop&filename='+ this.image_filename +'&class=original&width='+ this.crop_obj.cr + '&height='+ this.crop_obj.cb + '&start_x='+( this.crop_obj.x - this.image.x )+'&start_y='+( this.crop_obj.y - this.image.y );
        if (crA == 'pre'){
           window.open( url, 'prevWin', ('width=' + this.crop_obj.cr + ',height=' + this.crop_obj.cb));
            prompt( 'Stuff', url );
        } else {
           location.href=url;
           return true;
        }
    }

    function stopZoom() {
       clearTimeout(this.zoomtimer);
    }

    function Bigger( ){
       if (  this.crop_obj == undefined ) {
           return window.cropper.Bigger( );
       }
       if (( ( this.crop_obj.x + this.crop_obj.cr - this.image.x ) < ( this.image.width )) && ( ( this.crop_obj.y + this.crop_obj.cb - this.image.y ) < ( this.image.height ))){
           cW = this.crop_obj.cr + 1;
           cH = parseInt( this.window_ratio * cW);
           this.crop_obj.clipTo(0,cW,cH,0,1);
           this.zoomtimer = setTimeout( Bigger, 10 );
       }

    }

    function Smaller( ){
       if (  this.crop_obj == undefined ) {
           return window.cropper.Smaller( );
       }
       if (( this.crop_obj.cr > this.crop_min_width ) && ( this.crop_obj.cb > this.crop_min_height )) {
			cW = this.crop_obj.cr - 1;
			cH = parseInt(this.window_ratio * cW);
			this.crop_obj.clipTo(0,cW,cH,0,1);
            this.zoomtimer = setTimeout( this.Smaller, 10);
       }

	}


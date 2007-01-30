/*
Anarchy Media Player 1.6.5
http://an-archos.com/anarchy-media-player
Makes any mp3, Flash flv, Quicktime mov, mp4, m4v, m4a, m4b and 3gp as well as wmv, avi and asf links playable directly on your webpage while optionally hiding the download link. 
Based on a hack of the excellent Del.icio.us mp3 Playtagger javascript (http://del.icio.us/help/playtagger) as used in Taragana's Del.icio.us mp3 Player Plugin (http://blog.taragana.com/index.php/archive/taraganas-delicious-mp3-player-wordpress-plugin/) 
and using Jeroen Wijering's Flv Player (http://www.jeroenwijering.com/?item=Flash_Video_Player) with Tradebit modifications (http://www.tradebit.com), (http://www.jeroenwijering.com/?item=Flash_Video_Player) and WP Audio Player mp3 player (http://www.1pixelout.net/code/audio-player-wordpress-plugin). Flash embeds use Geoff Stearns' excellent standards compliant Flash detection and embedding JavaScript (see http://blog.deconcept.com/swfobject/ for usage).
distributed under GNU General Public License.

For non-WP pages call script in <HEAD>:
<script type="text/javascript" src="http://PATH TO PLAYER DIRECTORY/anarchy_media/anarchy.js"></script>
*/
// Configure plugin options below

var anarchy_url = '/scripts/anarchy' // http address for the anarchy-media folder (no trailing slash).
var accepted_domains=new Array("") 	// OPTIONAL - Restrict script use to your domains. Add root domain name (minus 'http' or 'www') in quotes, add extra domains in quotes and separated by comma.
var viddownloadLink = 'none'	// Download link for flv and wmv links: One of 'none' (to turn downloading off) or 'inline' to display the link. ***Use $qtkiosk for qt***.

// MP3 Flash player options
var playerloop = 'no'		// Loop the music ... yes or no?
var mp3downloadLink = 'none'	// Download for mp3 links: One of 'none' (to turn downloading off) or 'inline' to display the link.

// Hex colours for the MP3 Flash Player (minus the #)
var playerbg ='DDDDDD'				// Background colour
var playerleftbg = 'BBBBBB'			// Left background colour
var playerrightbg = 'BBBBBB'		// Right background colour
var playerrightbghover = '666666'	// Right background colour (hover)
var playerlefticon = '000000'		// Left icon colour
var playerrighticon = '000000'		// Right icon colour
var playerrighticonhover = 'FFFFFF'	// Right icon colour (hover)
var playertext = '333333'			// Text colour
var playerslider = '666666'			// Slider colour
var playertrack = '999999'			// Loader bar colour
var playerloader = '666666'			// Progress track colour
var playerborder = '333333'			// Progress track border colour

// Flash video player options
var flvwidth = '400' 	// Width of the flv player
var flvheight = '320'	// Height of the flv player (allow 20px for controller)
var flvfullscreen = 'true' // Show fullscreen button, true or false (no auto return on Safari, double click in IE6)

//Quicktime player options
var qtloop = 'false'	// Loop Quicktime movies: true or false.
var qtwidth = '400'		// Width of your Quicktime player
var qtheight = '316'	// Height of your Quicktime player (allow 16px for controller)
var qtkiosk = 'false'	// Allow downloads, false = yes, true = no.
// Required Quicktime version - To set the minimum version higher than 6 go to Quicktime player section below and edit (quicktime.ver6) on or around line 236.

//WMV player options
var wmvwidth = '400'	// Width of your WMV player
var wmvheight = '372'	// Height of your WMV player (allow 45px for WMV controller or 16px if QT player - ignored by WinIE)

// CSS styles
var mp3playerstyle = 'vertical-align:bottom; margin:10px 0 5px 2px;'	// Flash mp3 player css style
var mp3imgmargin = '0.5em 0.5em -4px 5px'		// Mp3 button image css margins
var vidplayerstyle = ''	// Video player css style
var vidimgmargin = '0'		// Video image placeholder css margins

/* ------------------ End configuration options --------------------- */

/* --------------------- Domain Check ----------------------- */
//Lite protection only, you can also use .htaccss if you're paranoid - see http://evolt.org/node/60180
var domaincheck=document.location.href //retrieve the current URL of user browser
var accepted_ok=false //set acess to false by default

if (domaincheck.indexOf("http")!=-1){ //if this is a http request
for (r=0;r<accepted_domains.length;r++){
if (domaincheck.indexOf(accepted_domains[r])!=-1){ //if a match is found
accepted_ok=true //set access to true, and break out of loop
break
}
}
}
else
accepted_ok=true

if (!accepted_ok){
alert("You\'re not allowed to directly link to this .js file on our server!")
history.back(-1)
}

/* --------------------- Flash MP3 audio player ----------------------- */
if(typeof(Anarchy) == 'undefined') Anarchy = {}
Anarchy.Mp3 = {
	playimg: null,
	player: null,
	go: function() {
		var all = document.getElementsByTagName('a')
		for (var i = 0, o; o = all[i]; i++) {
			if(o.href.match(/\.mp3$/i)) {
				o.style.display = mp3downloadLink
				var img = document.createElement('img')
				img.src = anarchy_url+'/images/audio_mp3_play.gif'; img.title = 'Click to listen'
				img.style.margin = mp3imgmargin
				img.style.cursor = 'pointer'
				img.onclick = Anarchy.Mp3.makeToggle(img, o.href)
				o.parentNode.insertBefore(img, o)
	}}},
	toggle: function(img, url) {
		if (Anarchy.Mp3.playimg == img) Anarchy.Mp3.destroy()
		else {
			if (Anarchy.Mp3.playimg) Anarchy.Mp3.destroy()
			img.src = anarchy_url+'/images/audio_mp3_stop.gif'; Anarchy.Mp3.playimg = img;
			Anarchy.Mp3.player = document.createElement('span')
			Anarchy.Mp3.player.innerHTML = '<br /><object style="'+mp3playerstyle+'" classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000"' +
			'codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0"' +
			'width="290" height="24" id="player" align="middle">' +
			'<param name="wmode" value="transparent" />' +
			'<param name="allowScriptAccess" value="sameDomain" />' +
			'<param name="flashVars" value="bg=0x'+playerbg+'&amp;leftbg=0x'+playerleftbg+'&amp;rightbg=0x'+playerrightbg+'&amp;rightbghover=0x'+playerrightbghover+'&amp;lefticon=0x'+playerlefticon+'&amp;righticon=0x'+playerrighticon+'&amp;righticonhover=0x'+playerrighticonhover+'&amp;text=0x'+playertext+'&amp;slider=0x'+playerslider+'&amp;track=0x'+playertrack+'&amp;loader=0x'+playerloader+'&amp;border=0x'+playerborder+'&amp;autostart=yes&amp;loop='+playerloop+'&amp;soundFile='+url+'" />' +
			'<param name="movie" value="'+anarchy_url+'/player.swf" /><param name="quality" value="high" />' +
			'<embed style="'+mp3playerstyle+'" src="'+anarchy_url+'/player.swf" flashVars="bg=0x'+playerbg+'&amp;leftbg=0x'+playerleftbg+'&amp;rightbg=0x'+playerrightbg+'&amp;rightbghover=0x'+playerrightbghover+'&amp;lefticon=0x'+playerlefticon+'&amp;righticon=0x'+playerrighticon+'&amp;righticonhover=0x'+playerrighticonhover+'&amp;text=0x'+playertext+'&amp;slider=0x'+playerslider+'&amp;track=0x'+playertrack+'&amp;loader=0x'+playerloader+'&amp;border=0x'+playerborder+'&amp;autostart=yes&amp;loop='+playerloop+'&amp;soundFile='+url+'" '+
			'quality="high" wmode="transparent" width="290" height="24" name="player"' +
			'align="middle" allowScriptAccess="sameDomain" type="application/x-shockwave-flash"' +
			' pluginspage="http://www.macromedia.com/go/getflashplayer" /></object><br />'
			img.parentNode.insertBefore(Anarchy.Mp3.player, img.nextSibling)
	}},
	destroy: function() {
		Anarchy.Mp3.playimg.src = anarchy_url+'/images/audio_mp3_play.gif'; Anarchy.Mp3.playimg = null
		Anarchy.Mp3.player.removeChild(Anarchy.Mp3.player.firstChild); Anarchy.Mp3.player.parentNode.removeChild(Anarchy.Mp3.player); Anarchy.Mp3.player = null
	},
	makeToggle: function(img, url) { return function(){ Anarchy.Mp3.toggle(img, url) }}
}

/* ----------------- Flash flv video player ----------------------- */

if(typeof(Anarchy) == 'undefined') Anarchy = {}
Anarchy.FLV = {
	go: function() {
		var all = document.getElementsByTagName('a')
		for (var i = 0, o; o = all[i]; i++) {
			if(o.href.match(/\.flv$/i)) {
			o.style.display = viddownloadLink
			url = o.href
			var flvplayer = document.createElement('span')
			flvplayer.innerHTML = '<object style="'+vidplayerstyle+'" type="application/x-shockwave-flash" wmode="transparent" data="'+anarchy_url+'/flvplayer.swf?click='+anarchy_url+'/images/flvplaybutton.jpg&file='+url+'&showfsbutton='+flvfullscreen+'" height="'+flvheight+'" width="'+flvwidth+'">' +
			'<param name="movie" value="'+anarchy_url+'/flvplayer.swf?click='+anarchy_url+'/images/flvplaybutton.jpg&file='+url+'&showfsbutton='+flvfullscreen+'"> <param name="wmode" value="transparent">' +
			'<embed src="'+anarchy_url+'/flvplayer.swf?file='+url+'&click='+anarchy_url+'/images/flvplaybutton.jpg&&showfsbutton='+flvfullscreen+'" ' + 
			'style="'+vidplayerstyle+'" ' +
			'width="'+flvwidth+'" height="'+flvheight+'" name="flvplayer" align="middle" ' + 
			'play="true" loop="false" quality="high" allowScriptAccess="sameDomain" ' +
			'type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer">' + 
			'</embed></object>'
			o.parentNode.insertBefore(flvplayer, o)
	}}}}

/* ----------------------- QUICKTIME DETECT --------------------------- 
All code by Ryan Parman, unless otherwise noted.
(c) 1997-2003, Ryan Parman
http://www.skyzyx.com
Distributed according to SkyGPL 2.1, http://www.skyzyx.com/license/
--------------------------------------------------------------------- */
var quicktime=new Object();
// Set some base values
quicktime.installed=false;
quicktime.version='0.0';
if (navigator.plugins && navigator.plugins.length) {
	for (x=0; x<navigator.plugins.length; x++) {
		if (navigator.plugins[x].name.indexOf('QuickTime Plug-in') != -1) {
			quicktime.installed=true;
			quicktime.version=navigator.plugins[x].name.split('QuickTime Plug-in ')[1].split(' ')[0];
			break;
		}
	}
}
else if (window.ActiveXObject) {
	try {
		oQTime=new ActiveXObject('QuickTimeCheckObject.QuickTimeCheck.1');
		if (oQTime) {
			quicktime.installed=oQTime.IsQuickTimeAvailable(0);			
			quicktime.version=parseInt(oQTime.QuickTimeVersion.toString(16).substring(0,3))/100;
		}
	}
	catch(e) {}
}
quicktime.ver2=(quicktime.installed && parseInt(quicktime.version) >= 2) ? true:false;
quicktime.ver3=(quicktime.installed && parseInt(quicktime.version) >= 3) ? true:false;
quicktime.ver4=(quicktime.installed && parseInt(quicktime.version) >= 4) ? true:false;
quicktime.ver5=(quicktime.installed && parseInt(quicktime.version) >= 5) ? true:false;
quicktime.ver6=(quicktime.installed && parseInt(quicktime.version) >= 6) ? true:false;
quicktime.ver7=(quicktime.installed && parseInt(quicktime.version) >= 7) ? true:false;
quicktime.ver8=(quicktime.installed && parseInt(quicktime.version) >= 8) ? true:false;
quicktime.ver9=(quicktime.installed && parseInt(quicktime.version) >= 9) ? true:false;

/* ----------------------- Quicktime player ------------------------ */

if(typeof(Anarchy) == 'undefined') Anarchy = {}
Anarchy.MOV = {
	playimg: null,
	player: null,
	go: function() {
		var all = document.getElementsByTagName('a')
		for (var i = 0, o; o = all[i]; i++) {
			if(o.href.match(/\.mov$|\.mp4$|\.m4v$|\.m4b$|\.3gp$/i)) {
				o.style.display = viddownloadLink
				var img = document.createElement('img')
				img.src = anarchy_url+'/images/vid_play.gif'; img.title = 'Click to play video'
				img.style.margin = vidimgmargin
				img.style.padding = '0px'
				img.style.cursor = 'pointer'
				img.height = qtheight
				img.width = qtwidth
				img.onclick = Anarchy.MOV.makeToggle(img, o.href)
				o.parentNode.insertBefore(img, o)
	}}},
	toggle: function(img, url) {
		if (Anarchy.MOV.playimg == img) Anarchy.MOV.destroy()
		else {
			if (Anarchy.MOV.playimg) Anarchy.MOV.destroy()
			img.src = anarchy_url+'/images/vid_play.gif'
			img.style.display = 'none'; Anarchy.MOV.playimg = img;
			Anarchy.MOV.player = document.createElement('p')
			if (quicktime.ver6) {
			Anarchy.MOV.player.innerHTML = '<embed src="'+url+'" width="'+qtwidth+'" height="'+qtheight+'" loop="'+qtloop+'" autoplay="true" controller="true" border="0" style="'+vidplayerstyle+'" type="video/quicktime" kioskmode="'+qtkiosk+'" scale="tofit"></embed>'
          img.parentNode.insertBefore(Anarchy.MOV.player, img.nextSibling)
          }
		else
			Anarchy.MOV.player.innerHTML = '<a href="http://www.apple.com/quicktime/download/" target="_blank"><img src="'+anarchy_url+'/images/getqt.jpg"></a>'
          img.parentNode.insertBefore(Anarchy.MOV.player, img.nextSibling)
	}},
	destroy: function() {
	},
	makeToggle: function(img, url) { return function(){ Anarchy.MOV.toggle(img, url) }}
}

/* --------------------- MPEG 4 Audio Quicktime player ---------------------- */

if(typeof(Anarchy) == 'undefined') Anarchy = {}
Anarchy.M4a = {
	playimg: null,
	player: null,
	go: function() {
		var all = document.getElementsByTagName('a')
		for (var i = 0, o; o = all[i]; i++) {
			if(o.href.match(/\.m4a$/i)) {
				o.style.display = mp3downloadLink
				var img = document.createElement('img')
				img.src = anarchy_url+'/images/audio_mp4_play.gif'; img.title = 'Click to listen'
				img.style.margin = mp3imgmargin
				img.style.cursor = 'pointer'
				img.onclick = Anarchy.M4a.makeToggle(img, o.href)
				o.parentNode.insertBefore(img, o)
	}}},
	toggle: function(img, url) {
		if (Anarchy.M4a.playimg == img) Anarchy.M4a.destroy()
		else {
			if (Anarchy.M4a.playimg) Anarchy.M4a.destroy()
			img.src = anarchy_url+'/images/audio_mp4_stop.gif'; Anarchy.M4a.playimg = img;
			Anarchy.M4a.player = document.createElement('p')
			if (quicktime.ver6) {
			Anarchy.M4a.player.innerHTML = '<embed src="'+url+'" width="160" height="16" loop="'+qtloop+'" autoplay="true" controller="true" border="0" type="video/quicktime" kioskmode="'+qtkiosk+'" ></embed>'
          img.parentNode.insertBefore(Anarchy.M4a.player, img.nextSibling)
          }
		else
			Anarchy.M4a.player.innerHTML = '<a href="http://www.apple.com/quicktime/download/" target="_blank"><img src="'+anarchy_url+'/images/getqt.jpg"></a>'
          img.parentNode.insertBefore(Anarchy.M4a.player, img.nextSibling)
	}},
	destroy: function() {
		Anarchy.M4a.playimg.src = anarchy_url+'/images/audio_mp4_play.gif'; Anarchy.M4a.playimg = null
		Anarchy.M4a.player.removeChild(Anarchy.M4a.player.firstChild); Anarchy.M4a.player.parentNode.removeChild(Anarchy.M4a.player); Anarchy.M4a.player = null
	},
	makeToggle: function(img, url) { return function(){ Anarchy.M4a.toggle(img, url) }}
}

/* ----------------------- WMV player -------------------------- */

if(typeof(Anarchy) == 'undefined') Anarchy = {}
Anarchy.WMV = {
	playimg: null,
	player: null,
	go: function() {
		var all = document.getElementsByTagName('a')
		for (var i = 0, o; o = all[i]; i++) {
			if(o.href.match(/\.asf$|\.avi$|\.wmv$/i)) {
				o.style.display = viddownloadLink
				var img = document.createElement('img')
				img.src = anarchy_url+'/images/vid_play.gif'; img.title = 'Click to play video'
				img.style.margin = '0px'
				img.style.padding = '0px'
				img.style.cursor = 'pointer'
				img.height = qtheight
				img.width = qtwidth
				img.onclick = Anarchy.WMV.makeToggle(img, o.href)
				o.parentNode.insertBefore(img, o)
	}}},
	toggle: function(img, url) {
		if (Anarchy.WMV.playimg == img) Anarchy.WMV.destroy()
		else {
			  if (Anarchy.WMV.playimg) Anarchy.WMV.destroy()
			  img.src = anarchy_url+'/images/vid_play.gif'
			  img.style.display = 'none'; Anarchy.WMV.playimg = img;
			  Anarchy.WMV.player = document.createElement('span')
			  if(navigator.userAgent.indexOf('Mac') != -1) {
			  Anarchy.WMV.player.innerHTML = '<embed src="'+url+'" width="'+qtwidth+'" height="'+qtheight+'" loop="'+qtloop+'" autoplay="true" controller="true" border="0" style="'+vidplayerstyle+'" type="video/quicktime" kioskmode="'+qtkiosk+'" scale="tofit" pluginspage="http://www.apple.com/quicktime/download/"></embed>'
			  img.parentNode.insertBefore(Anarchy.WMV.player, img.nextSibling)
			  } else {
			  if (navigator.plugins && navigator.plugins.length) {
			  Anarchy.WMV.player.innerHTML = '<embed type="application/x-mplayer2" src="'+url+'" ' +
			  'showcontrols="1" ShowStatusBar="1" autostart="1" displaySize="4"' +
			  'pluginspage="http://www.microsoft.com/Windows/Downloads/Contents/Products/MediaPlayer/"' +
			  'width="'+wmvwidth+'" height="'+wmvheight+'">' +
			  '</embed>'
			  img.parentNode.insertBefore(Anarchy.WMV.player, img.nextSibling)
			  } else {
				Anarchy.WMV.player.innerHTML = '<object classid="CLSID:6BF52A52-394A-11d3-B153-00C04F79FAA6" width="'+wmvwidth+'" height="'+wmvheight+'" id="player"> ' +
			  '<param name="url" value="'+url+'" /> ' +
			  '<param name="autoStart" value="true" /> ' +
			  '<param name="stretchToFit" value="True" /> ' +
			  '<param name="showControls" value="true" /> ' +
			  '<param name="ShowStatusBar" value="true" /> ' +
			  '<embed type="application/x-mplayer2" src="'+url+'" ' +
			  'showcontrols="1" ShowStatusBar="1" autostart="1" displaySize="4"' +
			  'pluginspage="http://www.microsoft.com/Windows/Downloads/Contents/Products/MediaPlayer/"' +
			  'width="'+wmvwidth+'" height="'+wmvheight+'">' +
			  '</embed>'
			  '</object>'
			  img.parentNode.insertBefore(Anarchy.WMV.player, img.nextSibling)
			  }}
	}},
	destroy: function() {
		Anarchy.WMV.playimg.src = anarchy_url+'/images/vid_play.gif'
		Anarchy.WMV.playimg.style.display = 'inline'; Anarchy.WMV.playimg = null
		Anarchy.WMV.player.removeChild(Anarchy.WMV.player.firstChild); 
		Anarchy.WMV.player.parentNode.removeChild(Anarchy.WMV.player); 
		Anarchy.WMV.player = null
	},
	makeToggle: function(img, url) { return function(){ Anarchy.WMV.toggle(img, url) }}
}

/* ----------------- Trigger players onload ----------------------- */

Anarchy.addLoadEvent = function(f) { var old = window.onload
	if (typeof old != 'function') window.onload = f
	else { window.onload = function() { old(); f() }}
}

Anarchy.addLoadEvent(Anarchy.Mp3.go)
Anarchy.addLoadEvent(Anarchy.FLV.go)
Anarchy.addLoadEvent(Anarchy.MOV.go)
Anarchy.addLoadEvent(Anarchy.M4a.go)
Anarchy.addLoadEvent(Anarchy.WMV.go)

/* ----------------- Start Flash SWF Embeds ----------------------- 
 * SWFObject v1.4.4: Flash Player detection and embed - http://blog.deconcept.com/swfobject/
 *
 * SWFObject is (c) 2006 Geoff Stearns and is released under the MIT License:
 * http://www.opensource.org/licenses/mit-license.php
 *
 * **SWFObject is the SWF embed script formerly known as FlashObject. The name was changed for
 *   legal reasons.
 ------------------------------------------------------------------- */
if(typeof deconcept == "undefined") var deconcept = new Object();
if(typeof deconcept.util == "undefined") deconcept.util = new Object();
if(typeof deconcept.SWFObjectUtil == "undefined") deconcept.SWFObjectUtil = new Object();
deconcept.SWFObject = function(swf, id, w, h, ver, c, useExpressInstall, quality, xiRedirectUrl, redirectUrl, detectKey){
	if (!document.getElementById) { return; }
	this.DETECT_KEY = detectKey ? detectKey : 'detectflash';
	this.skipDetect = deconcept.util.getRequestParameter(this.DETECT_KEY);
	this.params = new Object();
	this.variables = new Object();
	this.attributes = new Array();
	if(swf) { this.setAttribute('swf', swf); }
	if(id) { this.setAttribute('id', id); }
	if(w) { this.setAttribute('width', w); }
	if(h) { this.setAttribute('height', h); }
	if(ver) { this.setAttribute('version', new deconcept.PlayerVersion(ver.toString().split("."))); }
	this.installedVer = deconcept.SWFObjectUtil.getPlayerVersion();
	if(c) { this.addParam('bgcolor', c); }
	var q = quality ? quality : 'high';
	this.addParam('quality', q);
	this.setAttribute('useExpressInstall', useExpressInstall);
	this.setAttribute('doExpressInstall', false);
	var xir = (xiRedirectUrl) ? xiRedirectUrl : window.location;
	this.setAttribute('xiRedirectUrl', xir);
	this.setAttribute('redirectUrl', '');
	if(redirectUrl) { this.setAttribute('redirectUrl', redirectUrl); }
}
deconcept.SWFObject.prototype = {
	setAttribute: function(name, value){
		this.attributes[name] = value;
	},
	getAttribute: function(name){
		return this.attributes[name];
	},
	addParam: function(name, value){
		this.params[name] = value;
	},
	getParams: function(){
		return this.params;
	},
	addVariable: function(name, value){
		this.variables[name] = value;
	},
	getVariable: function(name){
		return this.variables[name];
	},
	getVariables: function(){
		return this.variables;
	},
	getVariablePairs: function(){
		var variablePairs = new Array();
		var key;
		var variables = this.getVariables();
		for(key in variables){
			variablePairs.push(key +"="+ variables[key]);
		}
		return variablePairs;
	},
	getSWFHTML: function() {
		var swfNode = "";
		if (navigator.plugins && navigator.mimeTypes && navigator.mimeTypes.length) { // netscape plugin architecture
			if (this.getAttribute("doExpressInstall")) { this.addVariable("MMplayerType", "PlugIn"); }
			swfNode = '<embed type="application/x-shockwave-flash" src="'+ this.getAttribute('swf') +'" width="'+ this.getAttribute('width') +'" height="'+ this.getAttribute('height') +'"';
			swfNode += ' id="'+ this.getAttribute('id') +'" name="'+ this.getAttribute('id') +'" ';
			var params = this.getParams();
			 for(var key in params){ swfNode += [key] +'="'+ params[key] +'" '; }
			var pairs = this.getVariablePairs().join("&");
			 if (pairs.length > 0){ swfNode += 'flashvars="'+ pairs +'"'; }
			swfNode += '/>';
		} else { // PC IE
			if (this.getAttribute("doExpressInstall")) { this.addVariable("MMplayerType", "ActiveX"); }
			swfNode = '<object id="'+ this.getAttribute('id') +'" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="'+ this.getAttribute('width') +'" height="'+ this.getAttribute('height') +'">';
			swfNode += '<param name="movie" value="'+ this.getAttribute('swf') +'" />';
			var params = this.getParams();
			for(var key in params) {
			 swfNode += '<param name="'+ key +'" value="'+ params[key] +'" />';
			}
			var pairs = this.getVariablePairs().join("&");
			if(pairs.length > 0) {swfNode += '<param name="flashvars" value="'+ pairs +'" />';}
			swfNode += "</object>";
		}
		return swfNode;
	},
	write: function(elementId){
		if(this.getAttribute('useExpressInstall')) {
			// check to see if we need to do an express install
			var expressInstallReqVer = new deconcept.PlayerVersion([6,0,65]);
			if (this.installedVer.versionIsValid(expressInstallReqVer) && !this.installedVer.versionIsValid(this.getAttribute('version'))) {
				this.setAttribute('doExpressInstall', true);
				this.addVariable("MMredirectURL", escape(this.getAttribute('xiRedirectUrl')));
				document.title = document.title.slice(0, 47) + " - Flash Player Installation";
				this.addVariable("MMdoctitle", document.title);
			}
		}
		if(this.skipDetect || this.getAttribute('doExpressInstall') || this.installedVer.versionIsValid(this.getAttribute('version'))){
			var n = (typeof elementId == 'string') ? document.getElementById(elementId) : elementId;
			n.innerHTML = this.getSWFHTML();
			return true;
		}else{
			if(this.getAttribute('redirectUrl') != "") {
				document.location.replace(this.getAttribute('redirectUrl'));
			}
		}
		return false;
	}
}

/* ---- detection functions ---- */
deconcept.SWFObjectUtil.getPlayerVersion = function(){
	var PlayerVersion = new deconcept.PlayerVersion([0,0,0]);
	if(navigator.plugins && navigator.mimeTypes.length){
		var x = navigator.plugins["Shockwave Flash"];
		if(x && x.description) {
			PlayerVersion = new deconcept.PlayerVersion(x.description.replace(/([a-zA-Z]|\s)+/, "").replace(/(\s+r|\s+b[0-9]+)/, ".").split("."));
		}
	}else{
		// do minor version lookup in IE, but avoid fp6 crashing issues
		// see http://blog.deconcept.com/2006/01/11/getvariable-setvariable-crash-internet-explorer-flash-6/
		try{
			var axo = new ActiveXObject("ShockwaveFlash.ShockwaveFlash.7");
		}catch(e){
			try {
				var axo = new ActiveXObject("ShockwaveFlash.ShockwaveFlash.6");
				PlayerVersion = new deconcept.PlayerVersion([6,0,21]);
				axo.AllowScriptAccess = "always"; // throws if player version < 6.0.47 (thanks to Michael Williams @ Adobe for this code)
			} catch(e) {
				if (PlayerVersion.major == 6) {
					return PlayerVersion;
				}
			}
			try {
				axo = new ActiveXObject("ShockwaveFlash.ShockwaveFlash");
			} catch(e) {}
		}
		if (axo != null) {
			PlayerVersion = new deconcept.PlayerVersion(axo.GetVariable("$version").split(" ")[1].split(","));
		}
	}
	return PlayerVersion;
}
deconcept.PlayerVersion = function(arrVersion){
	this.major = arrVersion[0] != null ? parseInt(arrVersion[0]) : 0;
	this.minor = arrVersion[1] != null ? parseInt(arrVersion[1]) : 0;
	this.rev = arrVersion[2] != null ? parseInt(arrVersion[2]) : 0;
}
deconcept.PlayerVersion.prototype.versionIsValid = function(fv){
	if(this.major < fv.major) return false;
	if(this.major > fv.major) return true;
	if(this.minor < fv.minor) return false;
	if(this.minor > fv.minor) return true;
	if(this.rev < fv.rev) return false;
	return true;
}
/* ---- get value of query string param ---- */
deconcept.util = {
	getRequestParameter: function(param) {
		var q = document.location.search || document.location.hash;
		if(q) {
			var pairs = q.substring(1).split("&");
			for (var i=0; i < pairs.length; i++) {
				if (pairs[i].substring(0, pairs[i].indexOf("=")) == param) {
					return pairs[i].substring((pairs[i].indexOf("=")+1));
				}
			}
		}
		return "";
	}
}
/* fix for video streaming bug */
deconcept.SWFObjectUtil.cleanupSWFs = function() {
	if (window.opera || !document.all) return;
	var objects = document.getElementsByTagName("OBJECT");
	for (var i=0; i < objects.length; i++) {
		objects[i].style.display = 'none';
		for (var x in objects[i]) {
			if (typeof objects[i][x] == 'function') {
				objects[i][x] = function(){};
			}
		}
	}
}
// fixes bug in fp9 see http://blog.deconcept.com/2006/07/28/swfobject-143-released/
deconcept.SWFObjectUtil.prepUnload = function() {
	__flash_unloadHandler = function(){};
	__flash_savedUnloadHandler = function(){};
	if (typeof window.onunload == 'function') {
		var oldUnload = window.onunload;
		window.onunload = function() {
			deconcept.SWFObjectUtil.cleanupSWFs();
			oldUnload();
		}
	} else {
		window.onunload = deconcept.SWFObjectUtil.cleanupSWFs;
	}
}
if (typeof window.onbeforeunload == 'function') {
	var oldBeforeUnload = window.onbeforeunload;
	window.onbeforeunload = function() {
		deconcept.SWFObjectUtil.prepUnload();
		oldBeforeUnload();
	}
} else {
	window.onbeforeunload = deconcept.SWFObjectUtil.prepUnload;
}
/* add Array.push if needed (ie5) */
if (Array.prototype.push == null) { Array.prototype.push = function(item) { this[this.length] = item; return this.length; }}

/* add some aliases for ease of use/backwards compatibility */
var getQueryParamValue = deconcept.util.getRequestParameter;
var FlashObject = deconcept.SWFObject; // for legacy support
var SWFObject = deconcept.SWFObject;

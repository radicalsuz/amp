/*********
* Javascript for file upload demo
* Copyright (C) Tomas Larsson 2006
* http://tomas.epineer.se/

* Licence:
* The contents of this file are subject to the Mozilla Public
* License Version 1.1 (the "License"); you may not use this file
* except in compliance with the License. You may obtain a copy of
* the License at http://www.mozilla.org/MPL/
* 
* Software distributed under this License is distributed on an "AS
* IS" basis, WITHOUT WARRANTY OF ANY KIND, either express or
* implied. See the License for the specific language governing
* rights and limitations under the License.
*/
var uploads_in_progress = 0;
var sids = {};

function beginUpload(ul,sid) {		
	ul.form.submit();
	sids[ul.name] = sid;
	uploads_in_progress = uploads_in_progress + 1;
	var pb = $(ul.name + "_progress");
	Element.show(pb.parentNode);
	new Ajax.PeriodicalUpdater({},'fileprogress.php',{'decay': 2,'frequency' : 0.5,'method': 'post','parameters': 'sid=' + sid,'onSuccess' : function(request){updateProgress(pb,request)},'onFailure':function(request){updateFailure(pb,request)}})
}

function updateProgress(pb,req) {
	var percent = parseInt(req.responseText);
	if(!percent) percent = 0;
	pb.style.width = percent + "%";
	if(percent >= 100) {
		var inp_id = pb.id.replace("_progress","");
		if(sids[inp_id]) {
			uploads_in_progress = uploads_in_progress - 1;
			var inp = $(inp_id);
			if(inp) {
				inp.value = sids[inp_id];
			}
		}
		Element.hide(pb.parentNode);
		sids[inp_id] = false;
	}
}

function updateFailure(pb,req) {
	var mes = req.responseText;
	pb.style.width=0;
	alert(mes);
	uploads_in_progress = uploads_in_progress - 1;
}

function submitUpload(frm) {
	if(uploads_in_progress > 0) {
		alert("File upload in progress. Please wait until upload finishes and try again.");
	} else {
		frm.submit();
	}
}
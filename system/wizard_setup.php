<?php
$mod_name = "system";

require("Connections/freedomrising.php");
$buildform = new BuildForm;

function addsecart($name,$desc,$type) {
	global $dbcon;
	$sql = "insert into articles(title,test,type,class,publish) values ('$name','desc','$type','8','1')";
	$insert=$dbcon->Execute($sql) or DIE($dbcon->ErrorMsg());
}

function addsec($name, $desc,$order) {
	global $dbcon;
	$name = addslashes($name);
	$desc = addslashes($desc);
	$sql = "insert into articletype (usenav,type,description,up,parent,listtype,header,textorder) values ('1','$name','$desc','20','1','1','1','$order')";
	$insert=$dbcon->Execute($sql) or DIE($dbcon->ErrorMsg());
	$getid=$dbcon->Execute("select id from articletype order by id desc limit 1") or DIE($dbcon->ErrorMsg());
	addsecart($name, $desc, $getid->Fields("id"));
	$nav = "<td> <a href=\"section.php?id=".$getid->Fields("id")."\" class=nav>".$name."</a></td>";
	return $nav;
}

if ($_POST['MM_insert']) {

	$stripit= substr(trim($basepath), -1); 
	if ($stripit != "/") { $basepath = $basepath."/";}
	$websitename = addslashes($websitename);
	$metadescription = addslashes($metadescription);
	$sql = "update sysvar set websitename= '$websitename' ,basepath = '$basepath', metadescription = '$metadescription', emfrom = '$emfrom', emfaq = '$emfaq' where id=1 ";
	$updateit=$dbcon->Execute($sql) or DIE($dbcon->ErrorMsg());
	if ($_POST["section1"]) { $navigation .= addsec($_POST["section1"], $_POST["desc_section1"],1);  }
	if ($_POST["section2"]) { $navigation .= addsec($_POST["section2"], $_POST["desc_section2"],2);  }
	if ($_POST["section3"]) { $navigation .= addsec($_POST["section3"], $_POST["desc_section3"],3);  }
	if ($_POST["section4"]) { $navigation .= addsec($_POST["section4"], $_POST["desc_section4"],4);  }
	if ($_POST["section5"]) { $navigation .= addsec($_POST["section5"], $_POST["desc_section5"],5);  }
	if ($_POST["section6"]) { $navigation .= addsec($_POST["section6"], $_POST["desc_section6"],6);  }
	if ($_POST["section7"]) { $navigation .= addsec($_POST["section7"], $_POST["desc_section7"],7);  }
	if ($_POST["section8"]) { $navigation .= addsec($_POST["section8"], $_POST["desc_section8"],8);  }
	
	$fnav = "<table width = \"100%\"><tr>".$navigation."</tr></table>";
	
	$htmlt=$dbcon->Execute("select header2 from template where id =1 ") or DIE($dbcon->ErrorMsg());
	$htmltemplate = $htmlt->Fields("header2");
	$htmltemplate = str_replace("Header Image", $websitename, $htmltemplate);
	$htmltemplate = str_replace("navigation", $fnav, $htmltemplate);
	$sql = "update template set header2 = '$htmltemplate' where id=1 ";
	$updateit=$dbcon->Execute($sql) or DIE($dbcon->ErrorMsg());
	redirect("articlelist.php");
}

//declare form objects
$rec_id = & new Input('hidden', 'MM_recordId', $_GET[id]);

//build form
$html  = "<h2>AMP SETUP WIZARD</h2>"; 
$html .= $buildform->start_table('wizard');
$html .= $buildform->add_header('Site Information');

$html .= addfield('websitename','Web Site Name','text');
$html .= addfield('basepath','Website URL','text');
$html .= addfield('metadescription','Meta Description','textarea');
$html .= addfield('emfrom','System email sent from this address:','text');
$html .= addfield('emfaq','System Adminsitrators Email Address','text');

$html .= $buildform->add_header('Top Level Sections');
$html .= addfield('section1','Section Name','text');
$html .= addfield('desc_section1','Intro Text','textarea');
$html .= addfield('section2','Section Name','text');
$html .= addfield('desc_section2','Intro Text','textarea');
$html .= addfield('section3','Section Name','text');
$html .= addfield('desc_section3','Intro Text','textarea');
$html .= addfield('section4','Section Name','text');
$html .= addfield('desc_section5','Intro Text','textarea');
$html .= addfield('section6','Section Name','text');
$html .= addfield('desc_section7','Intro Text','textarea');
$html .= addfield('section7','Section Name','text');
$html .= addfield('desc_section7','Intro Text','textarea');
$html .= addfield('section8','Section Name','text');
$html .= addfield('desc_section8','Intro Text','textarea');

//$html .= $buildform->add_colspan('', $object);
$html .= $buildform->add_content($buildform->add_btn());
$html .= $buildform->end_table();
$form = & new Form();
$form->set_contents($html);

include ("header.php");

echo $form->fetch();

include ("footer.php");
?>

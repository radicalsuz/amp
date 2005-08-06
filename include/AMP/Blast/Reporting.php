<?php
include_once ('AMP/Charts/charts.php');

$blast_ID = $_REQUEST['blast_ID'];

function get_count($blast_ID,$status) {
	global $dbcon;
	$sql="select message_ID from messages_to_contacts where blast_ID = $blast_ID and Status= '$status' ";
	$status=$dbcon->Execute($sql)or DIE($sql.$dbcon->ErrorMsg());
	return $status->RecordCount();
}

function build_row($label,$value) {
	$out = "<tr class=name><td width='20%' >$label: </td><td>$value</td></tr>";
	return $out;
}


function blast_details($blast_ID) {
	global $dbcon;	
	$call=$dbcon->Execute("select * from blast where blast_ID = $blast_ID ")or DIE('26'.$dbcon->ErrorMsg());	

	$out .= "<table>";
	$out .= build_row('Subject', $call->Fields("subject"));
	$out .= build_row('Blast Type', $call->Fields("blast_type"));
	if ($call->Fields("from_email")) {
		$out .= build_row('From Email', $call->Fields("from_email"));
	}
	if ($call->Fields("from_name")) {
		$out .= build_row('From Name', $call->Fields("from_name"));
	}
	if ($call->Fields("reply_to_address")) {
		$out .= build_row('Reply To Address', $call->Fields("reply_to_address"));
	}
	if ($call->Fields("sendformat")) {
		$out .= build_row('Send Format', $call->Fields("sendformat"));
	}
	if ($call->Fields("message_email_text")) {
		$out .= build_row('Text Message', $call->Fields("message_email_text"));
	}
	if ($call->Fields("message_email_html")) {
		$out .= build_row('HTML Message', $call->Fields("message_email_html"));
	}
	if ($call->Fields("message_sms")) {
		$out .= build_row('SMS Message', $call->Fields("message_sms"));
	}
	$out .= build_row('Embargoed Till', $call->Fields("embargo"));
	$out .= build_row('Send End Time', $call->Fields("send_start_time"));
	$out .= build_row('Send Start Time', $call->Fields("send_end_time"));
	$out .= "</table>";

	return $out;
}

function open_rate($blast_ID) {
	global $dbcon;	
	$ct   =  $dbcon->Execute("select Count(message_ID) from messages_to_contacts where blast_ID = $blast_ID and status='Done' ")or DIE(__LINE__.$dbcon->ErrorMsg());
	$open =  $dbcon->Execute("select Count(message_ID) from messages_to_contacts where blast_ID = $blast_ID and status='Done' and viewed > 1  ")or DIE(__LINE__.$dbcon->ErrorMsg());


	$out .= "<table>";
	
	$out .= build_row('HTML Messages Sent', $ct->Fields("Count(message_ID)"));
	$out .= build_row('<nobr>HTML Messages Opened</nobr>', $open->Fields("Count(message_ID)"));
	$out .= build_row('Open Rate', (($open->Fields("Count(message_ID)")/$ct->Fields("Count(message_ID)")*100))."%");

	$out .= "</table>";
	return $out;
}


function blast_report($blast_ID) {
	global $dbcon;	
	$list=$dbcon->Execute("select message_ID from messages_to_contacts where blast_ID = $blast_ID ")or DIE('line 23'.$dbcon->ErrorMsg());

	$out .= "<table>";
	
	$out .= build_row('# of total message', $list->RecordCount());
	$out .= build_row('# of New', get_count($blast_ID,'New'));
	$out .= build_row('# of Loaded', get_count($blast_ID,'Loaded'));
	$out .= build_row('# of Currently Sending', get_count($blast_ID,'Sending'));
	$out .= build_row('# of Sent', get_count($blast_ID,'Done'));
	$out .= build_row('# of Bounced', get_count($blast_ID,'Bounced'));
	#$out .= build_row('# of Pending', get_count($blast_ID,'Pending'));
	#$out .= build_row('# of Paused', get_count($blast_ID,'Paused'));
		#$out .= build_row('# of In-Progress', get_count($blast_ID,'In-Progress'));
	$out .= build_row('# of Server Failure', get_count($blast_ID,'Server Failure'));
	$out .= build_row('# of Bad Address', get_count($blast_ID,'Bad Address'));
	#$out .= build_row('# of Send Failed', get_count($blast_ID,'Failed'));

	#$out .= build_row('# of Failed', get_count($blast_ID,'Failed'));
	#$out .= build_row('# of Delayed', get_count($blast_ID,'Delayed'));
	#$out .= build_row('# of Testing', get_count($blast_ID,'Testing'));
	
	$out .= "</table>";
	return $out;
}	

function delivery_chart($blast_ID) {
	
	$chart[ 'canvas_bg' ] = array ( 'width'=>400, 'height'=>300, 'color'=>"666666" );
	$chart[ 'chart_bg' ] = array ( 'positive_color'=>"ffffff", 'positive_alpha'=>20, 'negative_color'=>"ff0000",  'negative_alpha'=>10);
	$chart[ 'chart_data' ] = array ( array ( "New", "Pending", "Sending","Done","In-Progress","Bad Address","Bounced","Server Failure","Delayed","Testing" ), array ( get_count($blast_ID,'New'), get_count($blast_ID,'Pending'), get_count($blast_ID,'Sending'), get_count($blast_ID,'Done'),get_count($blast_ID,'In-Progress'),get_count($blast_ID,'Bad Address'),get_count($blast_ID,'Bounced'),get_count($blast_ID,'Server Failure'),get_count($blast_ID,'Delayed'),get_count($blast_ID,'Testing') ));
	$chart[ 'chart_grid' ] = array ( 'alpha'=>10, 'color'=>"000000", 'horizontal_thickness'=>1, 'vertical_thickness'=>0, 'horizontal_skip'=>0, 'vertical_skip'=>0 );
	$chart[ 'chart_type' ] = "pie";
	$chart[ 'chart_value' ] = array ( 'color'=>"ffffff", 'alpha'=>90, 'font'=>"arial", 'bold'=>true, 'size'=>10, 'position'=>"inside", 'prefix'=>"", 'suffix'=>"", 'decimals'=>0, 'separator'=>"", 'as_percentage'=>true );
	
	$chart[ 'draw_text' ] = array (	array ( 'color'=>"000000", 'alpha'=>10, 'font'=>"arial", 'rotation'=>0, 'bold'=>true, 'size'=>30, 'x'=>0, 'y'=>140, 'width'=>400, 'height'=>150, 'text'=>"|||||||||||||||||||||||||||||||||||||||||||||||", 'h_align'=>"center", 'v_align'=>"bottom" )) ;
	
	$chart[ 'legend_bg' ] = array ( 'bg_color'=>"ffffff", 'bg_alpha'=>10, 'border_color'=>"000000", 'border_alpha'=>0, 'border_thickness'=>0 ); 
	$chart[ 'legend_label' ] = array ( 'layout'=>"horizontal", 'bullet'=>"circle", 'font'=>"arial", 'bold'=>true, 'size'=>13, 'color'=>"ffffff", 'alpha'=>85 ); 
	
	$chart[ 'series_color' ] = array ( "4d4d4d","ddaa41", "88dd11", "4e62dd", "ff8811","FF0000" , "5a4b6e" ,"FFFF00", "000066" ,"CCCCCC" ); 
	$chart[ 'series_explode' ] = array ( 20, 0, 50 );
	
	DrawChart ( $chart );

}
/*  
function sms_carrier_failures($blast_ID) {
	global $dbcon;
	$out .= "<P class=banner>Server Failures by Carrier</p><table class=name>";
	$sql = "SELECT distinct u.Phone_Provider from messages_to_contacts m, userdata.u where  m.user_ID = u.id and m.blast_ID = $blast_ID and m.status = 'Server Failure'";
	$car=$dbcon->Execute($sql)or DIE($sql.$dbcon->ErrorMsg());
	if ($car->RecordCount() > 0) {
		while (!$car->EOF) {
			$sql = "select m.message_ID from messages_to_contacts m, userdata.u where  m.user_ID = u.id and m.blast_ID = $blast_ID and m.status = 'Server Failure' and u.Phone_Provider = '".$car->Fields("Phone_Provider")."'";
			$carc=$dbcon->Execute($sql)or DIE($sql.$dbcon->ErrorMsg());
			$out .= "<tr><td>".$car->Fields("Carrier")."</td><td>".$carc->RecordCount()."</td></tr>";
			$car->MoveNext();
	}
	$out .= "</table>";
	return $out;

}	
 */
?>

<?php

/**********************************

processor.php
 
used to run a php process over a long time
and give the user progressive feedback as to the progress
and allow the user to pause and resume the processing.
 
This works best if the specified 'process_function'
does a flush frequently so that you can see the progress.
For that to work, gzip page compression must be turned off.
Example apache directives:

output_buffering Off
#php_flag output_handler
php_flag zlib.output_compression Off

**********************************/

#session_name("processor");
session_start();
require_once('AMP/BaseDB.php');
include_once("glog.php");
glog_set_level(LOG_DEBUG);
glog_set_file(AMP_LOCAL_PATH . "/custom/blast.log");
#ini_set('max_execution_time', 1000000);
if (!isset($chunk_size))
	{$chunk_size = 100;}      // how many items to process at a time

if (!isset($refresh_delay))
	{$refresh_delay = 0; } // value of 1000 is one second

if (!isset($process_function))
	{$process_function = 'test_process';}

if (isset($count_function) && !isset($_SESSION['max_count']))
	{$_SESSION['max_count'] = $count_function();}


$action = isset($_REQUEST['processor_action']) ? $_REQUEST['processor_action'] : '';

# allow setup function to do the initial configuration
# do not continue further until setup function is happy
#if (isset($setup_function)) {
#	if ($setup_function() == false)
#		return;
#}

if ($action == '') {
	do_frameset();
}
elseif ($action == 'view_control_frame') {
	do_controls();
}
elseif ($action == 'start') {
	do_start();
}
elseif ($action == 'pause') {
	do_pause();
}
elseif ($action == 'resume') {
	do_resume();
}
#elseif ($action == 'reset') {
#   session_destroy();
#	refresh('');
#}
elseif ($action == 'run') {
	do_run();
}
elseif ($action == 'test') {
	echo "blah";
}
elseif ($action == 'none') {
	echo "";
}

return;

##############################################################

function do_frameset() {
	?>
	<html>
	<head><title>Mail Blast</title></head>
	<frameset cols="160,*">
		<frame name="controls" src="<?=$_SERVER['PHP_SELF']?>?processor_action=view_control_frame">
		<frame name="display" src="<?=$_SERVER['PHP_SELF']?>?processor_action=none">
	</frameset><noframes></noframes>
	</html>
	<?php
}

function do_controls() {
	global $controls_function;
	
	$url = $_SERVER['PHP_SELF'];

	function action($url, $cmd, $target) {return "<a target=$target href=\"$url?processor_action=$cmd\">$cmd</a>";}
	?>
	<html><body>
	<table width="100%" bgcolor="#DDDDDD" cellpadding=0 cellspacing=0 border=0><tr><td>
	<table width="100%" cellpadding=6 cellspacing=2>
		<tr>
			<td bgcolor="#EEEEEE" align=center><?=action($url,'start','display')?></td>
		<!--	<td bgcolor="#EEEEEE" align=center><?=action($url,'reset','_parent')?></td> -->
		</tr>
		<tr>
			<td bgcolor="#EEEEEE" align=center><?=action($url,'pause','display')?></td>
		</tr><tr>
			<td bgcolor="#EEEEEE" align=center><?=action($url,'resume','display')?></td>
		</tr>
	</table>
	</td></tr></table>
	<p>
	<?php
		if (isset($controls_function))
			$controls_function();
	?>
    <br><br><br>
	Advanced options:<p>
	<FORM name="startoffsetform" ACTION="<?=$url?>" METHOD=GET target="display">
	<a href="javascript:document.startoffsetform.submit()">start from</a>
	<INPUT TYPE="text" size="5" NAME="start_offset" VALUE="0">
	<INPUT TYPE="hidden" NAME="processor_action" VALUE="start">

	</body></html>
	<?php
}

function do_start() {
	$_SESSION['active'] = true;
	$_SESSION['done'] = false;
	$_SESSION['offset'] = isset($_REQUEST['start_offset']) ? $_REQUEST['start_offset'] : 0;
	$_SESSION['max_count'] = null;

	refresh("?processor_action=run");
}

function do_run() {
	global $refresh_delay;

	if ($_SESSION['active'] == false) {
		echo "Sessions are not enabled. Sorry, this can't work without 'em";
		return;
	}
	elseif($_SESSION['done'] == true) {
		do_progress();
		echo "Finished " . (((int)($_SESSION['offset']))) ." of $_SESSION[max_count] records.";
		if (isset($_SESSION['done-msg']))
			echo "<br>" . $_SESSION['done-msg'];
		return;
	}

	### DO HEADER ###
	
	echo "<html><head><script language=\"JavaScript\">\n";
	if($refresh_delay == 0)
		echo "function MyTimedelay(){ location.reload(); }";
	else {
		echo "
			function MyReload() { 
				location.reload();
			}
			function MyTimedelay() {
				setTimeout(\"MyReload()\",$refresh_delay);
			}
		";
	}
	echo "</script></head>";

	### DO BODY ###

	echo "<body onLoad=\"MyTimedelay()\">";

	# many browsers cache the first couple hundred bytes, so we add some dummy data
	#print("<!--\n");
	#for($i=0;$i<1000;$i++) {
	#	print(md5(random()) . "\n");
	#}
	#print("-->\n");
	print(str_repeat(' ',5000) . "\n");
	flush("\n");

	# show progress bar
	if (isset($_SESSION['max_count']))
		do_progress();

	global $process_function;
	global $chunk_size;
	if (isset($_SESSION['offset'])) {
		$start_offset = $_SESSION['offset'];
		$status = $process_function($_SESSION['offset'], $chunk_size);
		if ($status == 'success') {
			$end_offset = $_SESSION['offset'];
			if ($start_offset == $end_offset)
				die("dying: $process_function did not advance the offset!");
		}
		else {
			$_SESSION['done'] = true;
			$_SESSION['done-msg'] = $status;
		}
	}
	echo "</body>";
}

function do_pause() {
	echo "<html><body>";
	do_progress();
	echo "<br><br><b>PAUSED</b></body></html>";
}

function do_resume() {
	refresh("?processor_action=run");	
}

function do_progress() {
	if (isset($_SESSION['max_count'])) {
		$sofar = $_SESSION['offset'];
		if ($sofar < 0) $sofar = 0;
		$percent = round( ( 100.00 * $sofar ) / $_SESSION['max_count'] );
		echo "<b>Progress: $sofar ($percent%)</b><br>"; 
		echo "<table cellpadding=0 cellspacing=4 bgcolor=grey border=0><tr>";
		echo "<td bgcolor=blue><img height=16 width=1><img border=0 height=1 width=". $percent*2 ."></td>";
		echo "<td><img height=1 width=". ((int)(200 - $percent*2)) ."></td>";
		echo "</tr></table>";
	}
	else {
		echo "<b>Progress: $sofar</b><br>";
	}
}

# note that this only works for mysql
# postgresql has the args switched
function limit($offset, $length) {
	return "LIMIT " . (0 + $offset) . ", " . (0 + $length);
}

// must be called before any output
function refresh($args) {
	if (isset($_SERVER['HTTPS']) && $_SERVER["HTTPS"] == "on")
		$protocal = "https";
	else
		$protocal = "http";

	header("Location: $protocal://".$_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . $args);
}

########################################################
## testing functions 
## used for stand alone unit-testing
########################################################

# this is a test function used if a process_function is not set.
# return false when processing should stop.

function test_process(&$offset, $chunk_size) {
	$_SESSION['max_count'] = 800;

	$i = $offset;
	$stop = $i + $chunk_size;

	while($i<$stop) {
		if ($i > 799) return false;
		usleep(100000);
		$data = "$i -- ". date("D M j G:i:s T Y");
		glog($data);
		echo "<br>$data\n";
		$offset = $i+1;
		flush();
		$i++;
	}
	return 'success';
}


<h1>The PHPlist community</h1>
<p><b>Latest Version</b><br/>
Please make sure you are using the latest version when submitting a bugreport.<br/>
<?
ini_set("user_agent","PHPlist version ".VERSION);
ini_set("default_socket_timeout",5);
if ($fp = @fopen ("http://www.phplist.com/files/LATESTVERSION","r")) {
	$latestversion = fgets ($fp);
  $latestversion = preg_replace("/[^\.\d]/","",$latestversion);
  $v = VERSION;
  $v = str_replace("-dev","",$v);
  if (!strcmp($v,$latestversion)) {
  	print "<font color=green size=2>Congratulations, you are using the latest version</font>";
  } else {
  	print "<font color=green size=2>You are not using the latest version</font>";
  	print "<br/>Your version: <b>".$v."</b>";
  	print "<br/>Latest version: <b>".$latestversion."</b>  ";
    print '<a href="http://www.phplist.com/files/changelog">View what has changed</a>&nbsp;&nbsp;';
    print '<a href="http://www.phplist.com/files/phplist-'.$latestversion.'.tgz">Download</a>';
	}
} else {
	print "<br/>Check for the latest version: <a href=http://www.phplist.com/files>here</a>";
}
?>
<p>PHPlist started early 2000 as a small application for the
<a href="http://www.nationaltheatre.org.uk" target="_blank">National Theatre</a>. Over time it has
grown into a fairly comprehensive Customer Relationship Management system and the 
number of sites using it has grown rapidly. Even though the codebase is primarily
maintained by one person, it is starting to become very complex, and ensuring the
quality will require the input of many other people.</p>
<p>In order to avoid clogging up the mailbox of the developers, you are kindly
requested not to send queries directly to <a href="http://tincan.co.uk" target="_blank">Tincan</a>, but
instead to use other methods of communication available. Not only does this free up
time to continue development, but it also creates a history of questions, that can be
used by new users to get acquainted with the system</a>.</p>
<p>To facilitate the PHPlist community several options are available:
<ul>
<li>The <a href="http://www.phplist.com/forums/" target="_blank">Forums</a></li>
<li>The <a href="#lists">Mailinglist</a></li>
<li>The <a href="#bugtrack">Bug Tracker</a></li>
</ul>
</p><hr>
<h1>What you can do to help</h1>
<p>If you are a <b>regular user of PHPlist</b> and you think you have cracked most of it's issues
you can help out by answering the questions of other users.</p>
<p>If you are <b>new to PHPlist</b> and you are having problems with setting it up to work for
your site, you can help by trying to find the solution in the above locations first, before
immediately posting a "it does not work" message. Often the problems you may have are related
to the environment your PHPlist installation is running in. Only having one developer for 
PHPlist has the disadvantage that the system cannot be tested thoroughly on every platform 
and every version of PHP.</p>
<h1>Other things you can do to help</h1>
<ul>
<li><p>If you think PHPlist is a great help for you, why not help to let other people know about
it's existence. You probably made quite an effort to find it and to decide to use if after
having compared it to other similar appliations, so you could help other people benefit 
from your experience.</p>

<p>To do so, you can <?=PageLink2("vote","Vote")?> for PHPlist, or write reviews on the
sites that list applications. You can also tell other people you know about it.
</li>
<li><p>You can <b>Translate</b> PHPlist into your language and submit the translation. I hope to
improve internationalisation, but for now, you simply need to translate the file <i>english.inc</i>.</p>
</li>
<li>
<p>You can <b>Try out</b> all the different features of PHPlist and check whether they work ok for you.
Please post your findings on the <a href="http://www.phplist.com/forums/" target="_blank">Forums</a>.</p></li>
<li>
<p>You can use PHPlist for your paying clients (if you are a web-outfit for example) and convince them
that the system is a great tool to achieve their goals. Then if they want some changes
you can <b>commission new features</b> that are paid for by your clients. If you want to know
how much it would be to add features to PHPlist, <a href="mailto:phplist@tincan.co.uk?subject=request for quote to change PHPlist">get in touch</a>.
Most of the new features of PHPlist are added by request from paying clients. This will benefit 
you for paying a small price to achieve your aims, it will benefit the community for getting new
features, and it will benefit the developers for getting paid for some of the work on PHPlist :-)</p></li>
<li><p>If you use PHPlist regularly and you have a <b>fairly large amount of subscribers</b> (1000+), we are
interested in your system specification, and send-statistics. By default PHPlist will send
statistics to <a href="mailto:phplist-stats@tincan.co.uk">phplist-stats@tincan.co.uk</a>, but it will
not send system details. If you want to help out making things work better, it would be great
if you could tell us your system specs, and leave the default of the stats to go to the above address.
The address is just a drop, it is not being read by people, but we will analyse it to figure out
how well PHPlist is performing.</p></li>
</ul>

<hr>
<p><b><a name="lists"></a>Mailinglist</b><br/>
To receive support, sign up for the phplist-users mailinglist:
<ul>
<li>Sign up by sending an email to <a href="mailto:phplist-users-subscribe@tincan.co.uk">phplist-users-subscribe@tincan.co.uk</a>
<li>Send your emails to <a href="mailto:phplist-users@tincan.co.uk">phplist-users@tincan.co.uk</a>
<li>Unsubscribe by sending an email to <a href="mailto:phplist-users-unsubscribe@tincan.co.uk">phplist-users-unsubscribe@tincan.co.uk</a>
<li>To check the mailinglist archives go <a href="http://lists.cupboard.org/archive/tincan.co.uk" target="_blank">here</a>
</ul>
</p>
<p><b><a name="bugtrack"></a>Bugtrack</b><br/>
To report a bug, go to <a href="http://mantis.tincan.co.uk/" target="_blank">http://bugtrack.tincan.co.uk</a>
and create an account for yourself. You will get a password by email.<br/>
You can then enter the "mantis" system and submit a bugreport.</p>
<p>Your system details are:</p>
<ul>
<li>PHP version: <?=phpversion()?></li>
<li>Webserver: <?=getenv("SERVER_SOFTWARE")?></li>
<li>Website: <a href="http://<?=getConfig("website")."$pageroot"?>"><?=getConfig("website")."$pageroot"?></a></li>
<li>Mysql Info: <?=mysql_get_server_info();?></li>
<li>PHP Modules:<br/><ul>
<?
$le = get_loaded_extensions();
foreach($le as $module) {
    print "<LI>$module\n";
}
?>
</ul></li>
</ul>
<p>You can also use this system to request new features.</p>
<p>Please note, emails not using this system, or the phplist-users mailinglist will be ignored.</p>

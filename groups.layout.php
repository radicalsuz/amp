<span class ="eventtitle"><a <?php 
 if (($groups->Fields("WebPage") != ($null)) and ($groups->Fields("WebPage") != ("http://")))  
 {echo "href=\"".$groups->Fields("WebPage")."\"";}?> class ="eventtitle" target="_blank"><?php echo $groups->Fields("Organization")?></a></span><br>
<?php if (($groups->Fields("City")) && ($groups->Fields("state"))) {?>
	 <span class="eventsubtitle"><?php echo $groups->Fields("City")?>, <?php  if  ($groups->Fields("state") ==53) {echo $groups->Fields("Country") ;} else { 
	 echo $groups->Fields("state");}?></span><br><?php }?>
	 
<?php if (($groups->Fields("FirstName") !=($null))  or ($groups->Fields("LastName") !=($null))) { ?>
<span class="bodygrey"><?php echo $groups->Fields("FirstName")?>&nbsp;<?php echo $groups->Fields("LastName")?></span><br><?php } ?>
<?php if (($groups->Fields("EmailAddress") !=($null)) ) { ?>
<span class="bodygrey"><a href="mailto:<?php echo $groups->Fields("EmailAddress")?>"><?php echo $groups->Fields("EmailAddress")?></a></span><br><?php } ?>
<?php if (($groups->Fields("Phone") !=($null)) ) { ?>
<span class="bodygrey"><?php echo $groups->Fields("Phone")?></span><br><?php } ?>
<?php if ($groups->Fields("field1") != ($null)) { ?>
<span class="text"><?php echo converttext($groups->Fields("field1")); ?></span><br><?php }?>
<br>

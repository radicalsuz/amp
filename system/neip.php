<?php
$modid = 101;
 require("Connections/freedomrising.php");  
  ob_start();
 if ( ((isset($MM_update)) && (isset($MM_recordId)) ) or (isset($MM_insert)) or ((isset($MM_delete)) && (isset($MM_recordId))) )  {
 
    $MM_editTable  = "neip";
       $MM_editColumn = "id";
    $MM_recordId = $MM_recordId ;
	 $MM_editRedirectUrl = "neip.php?action=list";
	 
	    $MM_fieldsStr ="baseline_indicator_component|value|baseline_findings_description|value|baseline_calculate|value|baseline_time_range|value|baseline_notes|value|ua_unit_of_analysis|value|ua_data_source|value|ua_attributes|value|ua_measurement|value|ua_geographic|value|ua_frequency|value|ua_storage|value|dc_unit_of_analysis|value|dc_data_source|value|dc_contact|value|dc_correspondence|value|dc_cost|value|dc_time|value|dc_results|value|dp_component|value|dp_calculate|value|dp_task_description|value|dp_expertise|value|dp_crunching_log|value|dp_time|value|dp_results|value|da_component|value|da_initial_findings|value|da_updated_reults|value|da_description|value|action_additional_components|value|action_community_outreach|value|action_groups|value|action_implications|value|question|value";
   $MM_columnsStr ="baseline_indicator_component|',none,''|baseline_findings_description|',none,''|baseline_calculate|',none,''|baseline_time_range|',none,''|baseline_notes|',none,''|ua_unit_of_analysis|',none,''|ua_data_source|',none,''|ua_attributes|',none,''|ua_measurement|',none,''|ua_geographic|',none,''|ua_frequency|',none,''|ua_storage|',none,''|dc_unit_of_analysis|',none,''|dc_data_source|',none,''|dc_contact|',none,''|dc_correspondence|',none,''|dc_cost|',none,''|dc_time|',none,''|dc_results|',none,''|dp_component|',none,''|dp_calculate|',none,''|dp_task_description|',none,''|dp_expertise|',none,''|dp_crunching_log|',none,''|dp_time|',none,''|dp_results|',none,''|da_component|',none,''|da_initial_findings|',none,''|da_updated_reults|',none,''|da_description|',none,''|action_additional_components|',none,''|action_community_outreach|',none,''|action_groups|',none,''|action_implications|',none,''|question|',none,''";
     require ("../Connections/insetstuff.php");
  require ("../Connections/dataactions.php");
  ob_end_flush();

}

$Recordset1__MMColParam = '90000000000';
if (isset($_GET["id"]))
	  {$Recordset1__MMColParam = $_GET["id"];}
    	$Recordset1=$dbcon->Execute("SELECT * FROM neip WHERE id = $Recordset1__MMColParam ") or DIE($dbcon->ErrorMsg());

?>
<?php include ("header.php"); 

if ($_GET[action] != 'list') { ?>?>

<form name="form1" method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
  <table width="100%" border="0" cellspacing="0" cellpadding="0">
    <tr valign="top"> 
      <td colspan="2"><strong>1) Research Question: </strong></td>
    </tr>
    <tr valign="top"> 
      <td> What is our question? </td>
      <td><textarea name="question" rows="6" wrap="VIRTUAL" style="width:400;"><?php echo $Recordset1->Fields("question");?></textarea></td>
    </tr>
    <tr valign="top"> 
      <td colspan="2"><br> <strong>2) Baseline Research Findings (From 2002 Indicators 
        Report):</strong></td>
    </tr>
    <tr valign="top"> 
      <td>Indicator Component (include comparisons to data in geographic areas 
        beyond WO)</td>
      <td><textarea name="baseline_indicator_component" rows="6" wrap="VIRTUAL" id="textarea" style="width:400;"><?php echo $Recordset1->Fields("baseline_indicator_component");?></textarea></td>
    </tr>
    <tr valign="top"> 
      <td>Findings Description &amp; Location of Information in 2002 Indicators 
        Report</td>
      <td><textarea name="baseline_findings_description" rows="6" wrap="VIRTUAL" id="baseline_findings_description" style="width:400;"><?php echo $Recordset1->Fields("baseline_findings_description");?></textarea></td>
    </tr>
    <tr valign="top"> 
      <td>How to Calculate the Indicator (in Units of Analysis Used)</td>
      <td><textarea name="baseline_calculate" rows="6" wrap="VIRTUAL" id="baseline_calculate" style="width:400;"><?php echo $Recordset1->Fields("baseline_calculate");?></textarea></td>
    </tr>
    <tr valign="top"> 
      <td>Time Range used for Initial Report</td>
      <td><textarea name="baseline_time_range" rows="6" wrap="VIRTUAL" style="width:400;"><?php echo $Recordset1->Fields("baseline_time_range");?></textarea></td>
    </tr>
    <tr valign="top"> 
      <td>Additional Notes</td>
      <td><textarea name="baseline_notes" rows="6" wrap="VIRTUAL" style="width:400;"><?php echo $Recordset1->Fields("baseline_notes");?></textarea></td>
    </tr>
    <tr valign="top"> 
      <td colspan="2"><br> <strong>3.) Units of Analysis: How are we measuring 
        this information? </strong></td>
    </tr>
    <tr valign="top"> 
      <td>Unit of Analysis</td>
      <td><textarea name="ua_unit_of_analysis" rows="6" wrap="VIRTUAL" style="width:400;"><?php echo $Recordset1->Fields("ua_unit_of_analysis");?></textarea></td>
    </tr>
    <tr valign="top"> 
	
      <td>Data Source(Who&#8217;s doing the measuring?)</td>
      <td><textarea name="ua_data_source" rows="6" wrap="VIRTUAL" style="width:400;"><?php echo $Recordset1->Fields("ua_data_source");?></textarea></td>
    </tr>
    <tr valign="top"> 
      <td>Attributes (What&#8217;s Measured?)</td>
      <td><textarea name="ua_attributes" rows="6" wrap="VIRTUAL" style="width:400;"><?php echo $Recordset1->Fields("ua_attributes");?></textarea></td>
    </tr>
    <tr valign="top"> 
      <td>Units of Measurement(How is it measured?)</td>
      <td><textarea name="ua_measurement" rows="6" wrap="VIRTUAL" style="width:400;"><?php echo $Recordset1->Fields("ua_measurement");?></textarea></td>
    </tr>
    <tr valign="top"> 
      <td>Geographic Level /Boundaries (Where&#8217;s it Measured?)</td>
      <td><textarea name="ua_geographic" rows="6" wrap="VIRTUAL" style="width:400;"><?php echo $Recordset1->Fields("ua_geographic");?></textarea></td>
    </tr>
    <tr valign="top"> 
      <td>Frequency of Collection</td>
      <td><textarea name="ua_frequency" rows="6" wrap="VIRTUAL" style="width:400;"><?php echo $Recordset1->Fields("ua_frequency");?></textarea></td>
    </tr>
    <tr valign="top"> 
      <td>Storage/Reporting Format</td>
      <td><textarea name="ua_storage" rows="6" wrap="VIRTUAL" style="width:400;"><?php echo $Recordset1->Fields("ua_storage");?></textarea></td>
    </tr>
    <tr valign="top"> 
      <td colspan="2"><p><br>
          <strong>4.) Data Collection: How and where do we obtain this information? 
          </strong></p></td>
    </tr>
    <tr valign="top"> 
      <td>Unit of Analysis</td>
      <td><textarea name="dc_unit_of_analysis" rows="6" wrap="VIRTUAL" style="width:400;"><?php echo $Recordset1->Fields("dc_unit_of_analysis");?></textarea></td>
    </tr>
    <tr valign="top"> 
      <td>Data Source</td>
      <td><textarea name="dc_data_source" rows="6" wrap="VIRTUAL" style="width:400;"><?php echo $Recordset1->Fields("dc_data_source");?></textarea></td>
    </tr>
    <tr valign="top"> 
      <td>Contact Information</td>
      <td><textarea name="dc_contact" rows="6" wrap="VIRTUAL" style="width:400;"><?php echo $Recordset1->Fields("dc_contact");?></textarea></td>
    </tr>
    <tr valign="top"> 
      <td>Correspondence Log (Date &amp; Content of Communication w/ Data Source)</td>
      <td><textarea name="dc_correspondence" rows="6" wrap="VIRTUAL" style="width:400;"><?php echo $Recordset1->Fields("dc_correspondence");?></textarea></td>
    </tr>
    <tr valign="top"> 
      <td>Cost of Obtaining/Notes on Data Access</td>
      <td><textarea name="dc_cost" rows="6" wrap="VIRTUAL" style="width:400;"><?php echo $Recordset1->Fields("dc_cost");?></textarea></td>
    </tr>
    <tr valign="top"> 
      <td>Time Required to Complete Task</td>
      <td><textarea name="dc_time" rows="6" wrap="VIRTUAL" style="width:400;"><?php echo $Recordset1->Fields("dc_time");?></textarea></td>
    </tr>
    <tr valign="top"> 
      <td>RESULTS</td>
      <td><textarea name="dc_results" rows="6" wrap="VIRTUAL" style="width:400;"><?php echo $Recordset1->Fields("dc_results");?></textarea></td>
    </tr>
    <tr valign="top"> 
      <td colspan="2"><br>
        <strong>5) Data Processing: How do we &#8220;crunch&#8221; this information 
        to get the updated indicator we need? </strong></td>
    </tr>
    <tr valign="top"> 
      <td>Indicator Component</td>
      <td><textarea name="dp_component" rows="6" wrap="VIRTUAL" style="width:400;"><?php echo $Recordset1->Fields("dp_component");?></textarea></td>
    </tr>
    <tr valign="top"> 
      <td>How to Calculate this Indicator Component (in Units of Analysis Used)</td>

      <td><textarea name="dp_calculate" rows="6" wrap="VIRTUAL" style="width:400;"><?php echo $Recordset1->Fields("dp_calculate");?></textarea></td>
    </tr>
    <tr valign="top"> 
      <td>Processing Task Description</td>
      <td><textarea name="dp_task_description" rows="6" wrap="VIRTUAL" style="width:400;"><?php echo $Recordset1->Fields("dp_task_description");?></textarea></td>
    </tr>
    <tr valign="top"> 
      <td>Expertise and/or Software Required</td>
      <td><textarea name="dp_expertise" rows="6" wrap="VIRTUAL" style="width:400;"><?php echo $Recordset1->Fields("dp_expertise");?></textarea></td>
    </tr>
    <tr valign="top"> 
      <td>Crunching Log (Who &amp; when data processing took place)</td>
      <td><textarea name="dp_crunching_log" rows="6" wrap="VIRTUAL" style="width:400;"><?php echo $Recordset1->Fields("dp_crunching_log");?></textarea></td>
    </tr>
    <tr valign="top"> 
      <td>Time Required to Complete Task </td>
      <td><textarea name="dp_time" rows="6" wrap="VIRTUAL" style="width:400;"><?php echo $Recordset1->Fields("dp_time");?></textarea></td>
    </tr>
    <tr valign="top"> 
      <td>RESULTS</td>
      <td><textarea name="dp_results" rows="6" wrap="VIRTUAL" style="width:400;"><?php echo $Recordset1->Fields("dp_results");?></textarea></td>
    </tr>
    <tr valign="top"> 
      <td colspan="2"><br>
        <strong>6) Data Analysis: How does the updated indicator compare to the 
        initial indicator (has it increased/decreased/stayed the same)? What does 
        this mean for the community? </strong></td>
    </tr>

    <tr valign="top"> 
      <td>Indicator component</td>
      <td><textarea name="da_component" rows="6" wrap="VIRTUAL" style="width:400;"><?php echo $Recordset1->Fields("da_component");?></textarea></td>
    </tr>
    <tr valign="top"> 
      <td>Initial Indicator Findings (From 2002 EIP Report)</td>
      <td><textarea name="da_initial_findings" rows="6" wrap="VIRTUAL" style="width:400;"><?php echo $Recordset1->Fields("da_initial_findings");?></textarea></td>
    </tr>
    <tr valign="top"> 
      <td>Updated Indicator Results (From 2002-2003)</td>
      <td><textarea name="da_updated_reults" rows="6" wrap="VIRTUAL" style="width:400;"><?php echo $Recordset1->Fields("da_updated_reults");?></textarea></td>
    </tr>
    <tr valign="top"> 
      <td>Description and Implications of Change </td>
      <td><textarea name="da_description" rows="6" wrap="VIRTUAL" style="width:400;"><?php echo $Recordset1->Fields("da_description");?></textarea></td>
    </tr>
    <tr valign="top"> 
      <td colspan="2"><br>
        <strong>7) Translating Data into Action: How can we make this information 
        accessible and user-friendly to community folks? How can the information 
        about changes in the indicator be used for advocacy/organizing/education? 
        </strong> </td>
    </tr>
    <tr valign="top"> 
      <td>Additional Indicator Components that may be Useful</td>
      <td><textarea name="action_additional_components" rows="6" wrap="VIRTUAL" style="width:400;"><?php echo $Recordset1->Fields("action_additional_components");?></textarea></td>
    </tr>
    <tr valign="top"> 
      <td>Ideas for Community Outreach/Education Related to Indicator</td>
      <td><textarea name="action_community_outreach" rows="6" wrap="VIRTUAL" style="width:400;"><?php echo $Recordset1->Fields("action_community_outreach");?></textarea></td>
    </tr>
    <tr valign="top"> 
      <td>Other groups that may be interested in results</td>
      <td><textarea name="action_groups" rows="6" wrap="VIRTUAL" style="width:400;"><?php echo $Recordset1->Fields("action_groups");?></textarea></td>
    </tr>
    <tr valign="top"> 
      <td>Implications for use in Indicator Trainings, Campaigns, EIP Base-Building, 
        etc.</td>
      <td><textarea name="action_implications" rows="6" wrap="VIRTUAL" style="width:400;"><?php echo $Recordset1->Fields("action_implications");?></textarea></td>
    </tr>
  </table>
                <input type="submit" name="Submit" value="Save Changes">
                <input name="MM_delete" type="submit" value="Delete Record" onclick="return confirmSubmit('Are you sure you want to DELETE this record?')">
        
        
        <?php if (!$_GET["id"]) { ?>
        <input type="hidden" name="MM_insert" value="true">
        <?php 		}
		else { ?>
        <input type="hidden" name="MM_update" value="true">
        <?php } ?>
        <input type="hidden" name="MM_recordId" value="<?php echo $_GET["id"]; ?>">
</form>
<?php } if ($_GET[action] == 'list') { 

    	$Recordset1=$dbcon->Execute("SELECT id, question FROM neip  ") or DIE($dbcon->ErrorMsg());
?>
<h2>Indicators</h2>
<table width="100%">
  <tr>
    <td>ID</td>
    <td>Question</td>
    <td></td>
  </tr>
  <?php while (!$Recordset1->EOF) {?>
  <tr>
    <td><?php echo $Recordset1->Fields("id");?></td>
    <td><?php echo $Recordset1->Fields("question");?></td>
    <td><a href="neip.php?id=<?php echo $Recordset1->Fields("id");?>">edit</a></td>
  </tr>
    <?php $Recordset1->MoveNext();  }?>
</table>
<?php 
}
include ("footer.php"); ?>
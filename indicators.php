<?php
$modid =101;
$mod_id = 100 ;
include("sysfiles.php");
include("header.php"); 


$Recordset1=$dbcon->Execute("SELECT * FROM neip WHERE id = $_GET[id] ") or DIE($dbcon->ErrorMsg());
?>

<table width="100%" border="0" cellspacing="4" cellpadding="0" class="text">
    <tr valign="top"> 
      <td colspan="2"><strong>1) Research Question: </strong></td>
    </tr>
    <tr valign="top"> 
      <td colspan="2"> <?php echo $Recordset1->Fields("question");?>        </textarea></td>
    </tr>
    <tr valign="top"> 
      <td colspan="2"><br> <strong>2) Baseline Research Findings (From 2002 Indicators 
        Report):</strong></td>
    </tr>
    <tr valign="top"> 
      <td width="34%">Indicator Component (include comparisons to data in geographic areas 
        beyond WO)</td>
      <td width="66%"><?php echo $Recordset1->Fields("baseline_indicator_component");?></textarea></td>
    </tr>
    <tr valign="top"> 
      <td>Findings Description &amp; Location of Information in 2002 Indicators 
        Report</td>
      <td><?php echo $Recordset1->Fields("baseline_findings_description");?></textarea></td>
    </tr>
    <tr valign="top"> 
      <td>How to Calculate the Indicator (in Units of Analysis Used)</td>
      <td><?php echo $Recordset1->Fields("baseline_calculate");?></textarea></td>
    </tr>
    <tr valign="top"> 
      <td>Time Range used for Initial Report</td>
      <td><?php echo $Recordset1->Fields("baseline_time_range");?></textarea></td>
    </tr>
    <tr valign="top"> 
      <td>Additional Notes</td>
      <td><?php echo $Recordset1->Fields("baseline_notes");?></textarea></td>
    </tr>
    <tr valign="top"> 
      <td colspan="2"><br> <strong>3.) Units of Analysis: How are we measuring 
        this information? </strong></td>
    </tr>
    <tr valign="top"> 
      <td>Unit of Analysis</td>
      <td><?php echo $Recordset1->Fields("ua_unit_of_analysis");?></textarea></td>
    </tr>
    <tr valign="top"> 
	
      <td>Data Source(Who&#8217;s doing the measuring?)</td>
      <td><?php echo $Recordset1->Fields("ua_data_source");?></textarea></td>
    </tr>
    <tr valign="top"> 
      <td>Attributes (What&#8217;s Measured?)</td>
      <td><?php echo $Recordset1->Fields("ua_attributes");?></textarea></td>
    </tr>
    <tr valign="top"> 
      <td>Units of Measurement(How is it measured?)</td>
      <td><?php echo $Recordset1->Fields("ua_measurement");?></textarea></td>
    </tr>
    <tr valign="top"> 
      <td>Geographic Level /Boundaries (Where&#8217;s it Measured?)</td>
      <td><?php echo $Recordset1->Fields("ua_geographic");?></textarea></td>
    </tr>
    <tr valign="top"> 
      <td>Frequency of Collection</td>
      <td><?php echo $Recordset1->Fields("ua_frequency");?></textarea></td>
    </tr>
    <tr valign="top"> 
      <td>Storage/Reporting Format</td>
      <td><?php echo $Recordset1->Fields("ua_storage");?></textarea></td>
    </tr>
    <tr valign="top"> 
      <td colspan="2"><p><br>
          <strong>4.) Data Collection: How and where do we obtain this information? 
          </strong></p></td>
    </tr>
    <tr valign="top"> 
      <td>Unit of Analysis</td>
      <td><?php echo $Recordset1->Fields("dc_unit_of_analysis");?></textarea></td>
    </tr>
    <tr valign="top"> 
      <td>Data Source</td>
      <td><?php echo $Recordset1->Fields("dc_data_source");?></textarea></td>
    </tr>
    <tr valign="top"> 
      <td>Contact Information</td>
      <td><?php echo $Recordset1->Fields("dc_contact");?></textarea></td>
    </tr>
    <tr valign="top"> 
      <td>Correspondence Log (Date &amp; Content of Communication w/ Data Source)</td>
      <td><?php echo $Recordset1->Fields("dc_correspondence");?></textarea></td>
    </tr>
    <tr valign="top"> 
      <td>Cost of Obtaining/Notes on Data Access</td>
      <td><?php echo $Recordset1->Fields("dc_cost");?></textarea></td>
    </tr>
    <tr valign="top"> 
      <td>Time Required to Complete Task</td>
      <td><?php echo $Recordset1->Fields("dc_time");?></textarea></td>
    </tr>
    <tr valign="top"> 
      <td>RESULTS</td>
      <td><?php echo $Recordset1->Fields("dc_results");?></textarea></td>
    </tr>
    <tr valign="top"> 
      <td colspan="2"><br>
        <strong>5) Data Processing: How do we &#8220;crunch&#8221; this information 
        to get the updated indicator we need? </strong></td>
    </tr>
    <tr valign="top"> 
      <td>Indicator Component</td>
      <td><?php echo $Recordset1->Fields("dp_component");?></textarea></td>
    </tr>
    <tr valign="top"> 
      <td>How to Calculate this Indicator Component (in Units of Analysis Used)</td>

      <td><?php echo $Recordset1->Fields("dp_calculate");?></textarea></td>
    </tr>
    <tr valign="top"> 
      <td>Processing Task Description</td>
      <td><?php echo $Recordset1->Fields("dp_task_description");?></textarea></td>
    </tr>
    <tr valign="top"> 
      <td>Expertise and/or Software Required</td>
      <td><?php echo $Recordset1->Fields("dp_expertise");?></textarea></td>
    </tr>
    <tr valign="top"> 
      <td>Crunching Log (Who &amp; when data processing took place)</td>
      <td><?php echo $Recordset1->Fields("dp_crunching_log");?></textarea></td>
    </tr>
    <tr valign="top"> 
      <td>Time Required to Complete Task </td>
      <td><?php echo $Recordset1->Fields("dp_time");?></textarea></td>
    </tr>
    <tr valign="top"> 
      <td>RESULTS</td>
      <td><?php echo $Recordset1->Fields("dp_results");?></textarea></td>
    </tr>
    <tr valign="top"> 
      <td colspan="2"><br>
        <strong>6) Data Analysis: How does the updated indicator compare to the 
        initial indicator (has it increased/decreased/stayed the same)? What does 
        this mean for the community? </strong></td>
    </tr>

    <tr valign="top"> 
      <td>Indicator component</td>
      <td><?php echo $Recordset1->Fields("da_component");?></textarea></td>
    </tr>
    <tr valign="top"> 
      <td>Initial Indicator Findings (From 2002 EIP Report)</td>
      <td><?php echo $Recordset1->Fields("da_initial_findings");?></textarea></td>
    </tr>
    <tr valign="top"> 
      <td>Updated Indicator Results (From 2002-2003)</td>
      <td><?php echo $Recordset1->Fields("da_updated_reults");?></textarea></td>
    </tr>
    <tr valign="top"> 
      <td>Description and Implications of Change </td>
      <td><?php echo $Recordset1->Fields("da_description");?></textarea></td>
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
      <td><?php echo $Recordset1->Fields("action_additional_components");?></textarea></td>
    </tr>
    <tr valign="top"> 
      <td>Ideas for Community Outreach/Education Related to Indicator</td>
      <td><?php echo $Recordset1->Fields("action_community_outreach");?></textarea></td>
    </tr>
    <tr valign="top"> 
      <td>Other groups that may be interested in results</td>
      <td><?php echo $Recordset1->Fields("action_groups");?></textarea></td>
    </tr>
    <tr valign="top"> 
      <td>Implications for use in Indicator Trainings, Campaigns, EIP Base-Building, 
        etc.</td>
      <td><?php echo $Recordset1->Fields("action_implications");?></textarea></td>
    </tr>
  </table>
  <?php include("footer.php");?>

{*
 * $Revision: 1.18 $
 * If you want to customize this file, do not edit it directly since future upgrades
 * may overwrite it.  Instead, copy it into a new directory called "local" and edit that
 * version.  Gallery will look for that file first and use it if it exists.
 *}
<div class="gbBlock gcBackground1">
  <h2> {g->text text="FFMPEG Settings"} </h2>
</div>

{if isset($status.saved)}
<div class="gbBlock"><h2 class="giSuccess">
  {g->text text="Settings saved successfully"}
</h2></div>
{/if}

<div class="gbBlock">
  <p class="giDescription">
    {g->text text="FFMPEG is a graphics toolkit that can be used to process video files that you upload to Gallery.  You must install the FFMPEG binary on your server, then enter the path to it in the text box below.  If you're on a Unix machine, don't forget to make the binary executable (<i>chmod 755 ffmpeg</i> in the right directory should do it)"}
  </p>

  {g->text text="Path to FFMPEG:"}
  <input type="text" size="40" name="{g->formVar var="form[path]"}" value="{$form.path}"
    id='giFormPath' autocomplete="off"/>
  {g->autoComplete element="giFormPath"}
    {g->url arg1="view=core.SimpleCallback" arg2="command=lookupFiles" arg3="prefix=__VALUE__"
      forJavascript="true"}
  {/g->autoComplete}

  {if isset($form.error.path.missing)}
  <div class="giError">
    {g->text text="You must enter a path to your FFMPEG binary"}
  </div>
  {/if}
  {if isset($form.error.path.testError)}
  <div class="giError">
    {g->text text="The path you entered doesn't contain a valid FFMPEG binary. Use the 'test' button to check where the error is."}
  </div>
  {/if}
  {if isset($form.error.path.badPath)}
  <div class="giError">
    {g->text text="The path you entered isn't a valid path to a <b>ffmpeg</b> binary."}
  </div>
  {/if}
  {if isset($form.error.path.notExecutable)}
  <div class="giError">
    {g->text text="The <b>ffmpeg</b> binary is not executable.  To fix it, run <b>chmod 755 %s</b> in a shell." arg1=$form.path}
  </div>
  {/if}
</div>

<div class="gbBlock gcBackground1">
  <input type="submit" class="inputTypeSubmit"
   name="{g->formVar var="form[action][save]"}" value="{g->text text="Save Settings"}"/>
  <input type="submit" class="inputTypeSubmit"
   name="{g->formVar var="form[action][test]"}" value="{g->text text="Test Settings"}"/>
  {if $AdminFfmpeg.isConfigure}
    <input type="submit" class="inputTypeSubmit"
     name="{g->formVar var="form[action][cancel]"}" value="{g->text text="Cancel"}"/>
  {else}
    <input type="submit" class="inputTypeSubmit"
     name="{g->formVar var="form[action][reset]"}" value="{g->text text="Reset"}"/>
  {/if}
</div>

{if !empty($AdminFfmpeg.tests)}
<div class="gbBlock">
  <h3> {g->text text="FFMPEG binary test results"} </h3>

  <table class="gbDataTable"><tr>
    <th> {g->text text="Binary Name"} </th>
    <th> {g->text text="Pass/Fail"} </th>
  </tr>
  {foreach from=$AdminFfmpeg.tests item=test}
    <tr class="{cycle values="gbEven,gbOdd"}">
      <td>
	{$test.name}
      </td><td>
	{if ($test.success)}
	  <div class="giSuccess">
	    {g->text text="Passed"}
	  </div>
	{else}
	  <div class="giError">
	    {g->text text="Failed"}
	  </div>
	  {if ! empty($test.message)}
	    {g->text text="Error messages:"}
	    <br/>
	    {foreach from=$test.message item=line}
	    <pre>{$line}</pre>
	    {/foreach}
	  {/if}
	{/if}
      </td>
    </tr>
  {/foreach}
  </table>
</div>

{if $AdminFfmpeg.mimeTypes}
<div class="gbBlock">
  <h3> {g->text text="Supported MIME Types"} </h3>

  <p class="giDescription">
    {g->text text="The FFMPEG module can support files with the following MIME types"}
  </p>
  <p class="giDescription">
  {foreach from=$AdminFfmpeg.mimeTypes item=mimeType}
    {$mimeType}<br/>
  {/foreach}
   </p>
</div>
{/if}

{if ($AdminFfmpeg.failCount > 0)}
<div class="gbBlock">
  <h3>
    {g->text one="Debug output (%d failed test)" many="Debug output (%d failed tests)"
	     count=$AdminFfmpeg.failCount arg1=$AdminFfmpeg.failCount}
    <span id="AdminFfmpeg_trace-toggle"
     class="giBlockToggle gcBackground1 gcBorder2" style="border-width: 1px"
     onclick="BlockToggle('AdminFfmpeg_debugSnippet', 'AdminFfmpeg_trace-toggle')">+</span>
  </h3>
  <p class="giDescription">
    {g->text text="We gathered this debug output while testing your Ffmpeg installation.  If you read through this carefully you may discover the reason why Ffmpeg failed the tests."}
  </p>
  <pre id="AdminFfmpeg_debugSnippet" class="gcBackground1 gcBorder2"
   style="display: none; border-width: 1px; border-style: dotted; padding: 4px">
    {$AdminFfmpeg.debugSnippet}
  </pre>
</div>
{/if}
{/if}

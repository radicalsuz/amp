{*
 * $Revision: 1.3 $
 * If you want to customize this file, do not edit it directly since future upgrades
 * may overwrite it.  Instead, copy it into a new directory called "local" and edit that
 * version.  Gallery will look for that file first and use it if it exists.
 *}
<div class="gbBlock gcBackground1">
  <h2>
    {g->text text="Captcha Settings"}
  </h2>
</div>

{if isset($status.saved)}
<div class="gbBlock">
  <h2 class="giSuccess">
    {g->text text="Settings saved successfully"}
  </h2>
</div>
{/if}

<div class="gbBlock">
  <h3> {g->text text="High Security - Always On"} </h3>
  <p class="giDescription">
    {g->text text="Modules such as User Registration will always require the Captcha value to be entered before proceeding."}
  </p>
</div>

<div class="gbBlock">
  <h3> {g->text text="Medium Security - Failed Attempts"} </h3>
  <p class="giDescription">
    {g->text text="Users are not required to pass the Captcha test unless they have failed validation or user input at least this many times.  After that, they have to enter the Captcha value to log in, or perform certain other secured actions."}
  </p>

  {g->text text="Failed attempts:"}
  <select name="{g->formVar var="form[failedAttemptThreshold]"}">
    {html_options values=$CaptchaSiteAdmin.failedAttemptThresholdList selected=$form.failedAttemptThreshold output=$CaptchaSiteAdmin.failedAttemptThresholdList}
  </select>
</div>

<div class="gbBlock">
  <h3> {g->text text="Low Security - Always Off"} </h3>
  <p class="giDescription">
    {g->text text="Most modules rely solely on the user being authenticated by Gallery and require no special Captcha interaction."}
  </p>
</div>

<div class="gbBlock gcBackground1">
  <input type="submit" name="{g->formVar var="form[action][save]"}" value="{g->text text="Save Settings"}"/>
  <input type="submit" name="{g->formVar var="form[action][cancel]"}" value="{g->text text="Cancel"}"/>
</div>

{*
 * $Revision: 1.9 $
 * If you want to customize this file, do not edit it directly since future upgrades
 * may overwrite it.  Instead, copy it into a new directory called "local" and edit that
 * version.  Gallery will look for that file first and use it if it exists.
 *}
<div class="gbBlock gcBackground1">
  <h2> {g->text text="Registration successful"} </h2>
</div>

<div class="gbBlock">
{if isset($status.registeredUser) || isset($status.registeredUserNoEmail)}
  <p class="giDescription">
    {g->text text="Your registration was successful."}
  </p>
  <p class="giDescription">
    {if isset($status.registeredUserNoEmail)}
      {g->text text="Your registration will be processed and your account activated soon."}
    {else}
      {g->text text="You will shortly receive an email containing a link. You have to click this link to confirm and activate your account."}
	{g->text text="This procedure is necessary to prevent account abuse."}
    {/if}
  </p>
{elseif isset($status.activatedUser)}

  <p class="giDescription">
      {g->text text="Your registration was successful and your account has been activated."}
  </p>
  <p class="giDescription">
      {g->text text="You can now"}
      <a href="{g->url arg1="view=core.UserAdmin" arg2="subView=core.UserLogin"}">
	{g->text text="login"}
      </a>
      {g->text text="to your account with your username and password."}
  </p>
{else}

  <p class="giDescription" style="font-weight: bold">
    {g->text text="This page can only be called once."}
  </p>
  <p class="giDescription">
    {capture name=loginLink}{strip}
      <a href="{g->url arg1="view=core.UserAdmin" arg2="subView=core.UserLogin"}">
	{g->text text="login"}
      </a>
    {/strip}{/capture}
    {g->text text="This page has been requested before and can only be called once. Probable you have hit the Reload button. Please proceed to %s." arg1=$smarty.capture.loginLink}
  </p>

{/if}
</div>

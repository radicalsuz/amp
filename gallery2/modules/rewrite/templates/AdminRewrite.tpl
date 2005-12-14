{*
 * $Revision: 1.15 $
 * If you want to customize this file, do not edit it directly since future upgrades
 * may overwrite it.  Instead, copy it into a new directory called "local" and edit that
 * version.  Gallery will look for that file first and use it if it exists.
 *}
<div class="gbBlock gcBackground1">
  <h2> {g->text text="URL Rewrite Administration"} </h2>
</div>

{if !empty($status)}
<div class="gbBlock"><h2 class="giSuccess">
  {if isset($status.rulesSaved)}
    {g->text text="Successfully saved URL styles"}
  {/if}
  {if isset($status.setupSaved)}
    {g->text text="Successfully saved the configuration"}
  {/if}
</h2></div>
{/if}

{if !empty($form.error)}
<div class="gbBlock">
  <h2 class="giError"> {g->text text="An error occured while trying to save your settings:"} </h3>

  <div class="giError">
  {if isset($form.error.cantWrite.gallery)}
    {g->text text="Cannot write to the Gallery .htaccess file"}
  {/if}

  {if isset($form.error.cantWrite.embedded)}
    {g->text text="Cannot write to the embedded .htaccess file"}
  {/if}

  {if isset($form.error.dupe)}
    {g->text text="Duplicate URL patterns."}
  {/if}

  {if isset($form.error.empty)}
    {g->text text="Empty URL pattern."}
  {/if}

  {if isset($form.error.badKeyword)}
    {g->text text="Bad keyword."}
  {/if}
  </div>
</div>
{/if}

<div class="gbTabBar">
  {if ($AdminRewrite.mode == 'rules')}
    <span class="giSelected o"><span>
      {g->text text="Rules"}
    </span></span>
  {else}
    <span class="o"><span>
      <a href="{g->url arg1="view=core.SiteAdmin" arg2="subView=rewrite.AdminRewrite"
       arg3="mode=rules"}">{g->text text="Rules"}</a>
    </span></span>
  {/if}

  {if ($AdminRewrite.mode == 'setup')}
    <span class="giSelected o"><span>
      {g->text text="Setup"}
    </span></span>
  {else}
    <span class="o"><span>
      <a href="{g->url arg1="view=core.SiteAdmin" arg2="subView=rewrite.AdminRewrite"
       arg3="mode=setup"}">{g->text text="Setup"}</a>
    </span></span>
  {/if}

  {if ($AdminRewrite.mode == 'test')}
    <span class="giSelected o"><span>
      {g->text text="Test"}
    </span></span>
  {else}
    <span class="o"><span>
      <a href="{g->url arg1="view=core.SiteAdmin" arg2="subView=rewrite.AdminRewrite"
       arg3="mode=test"}">{g->text text="Test"}</a>
    </span></span>
  {/if}
</div>

{if $AdminRewrite.mode == 'rules'}
<div class="gbBlock">
  <p class="giDescription">
      {g->text text="Short URLs are compiled out of predefined keywords. Modules may provide additional keywords. Keywords are escaped with % (eg: %itemId%)."}
  </p>

  <table class="gbDataTable">
  {assign var="group" value=""}
  {foreach from=$AdminRewrite.shortUrls item=module key=moduleId}
  {if !empty($group)}
    <tr><td> &nbsp; </td></tr>
  {/if}
  {assign var="group" value=$module.name}
  <tr><th colspan="6"><h2>{$module.name}</h2></th></tr>
  <tr>
    <th colspan="2" style="text-align: center;"> {g->text text="Active"} </th>
    <th> {g->text text="Help"} </th>
    <th> {g->text text="View"} </th>
    <th> {g->text text="URL Pattern"} </th>
    <th> {g->text text="Additional Keywords"} </th>
  </tr>

  {foreach from=$form.shortUrls.$moduleId item=rule key=index}
  {cycle values="gbEven,gbOdd" assign="rowClass"}
  <tr class="{$rowClass}">
    <td>
      {if isset($form.error.dupe[$rule.pattern]) || isset($form.error.empty.$moduleId.$index)}
	<img src="{g->url href="modules/core/data/module-inactive.gif"}" width="13" height="13"
	       alt="{g->text text="Status: Error"}" />
      {elseif isset($rule.active)}
	<img src="{g->url href="modules/core/data/module-active.gif"}" width="13" height="13"
	     alt="{g->text text="Status: Active"}" />
      {else}
	<img src="{g->url href="modules/core/data/module-install.gif"}" width="13" height="13"
	     alt="{g->text text="Status: Not Active"}" />
      {/if}
    </td>
    <td>
      <input type="checkbox" name="{g->formVar var="form[shortUrls][$moduleId][$index][active]"}" {if isset($rule.active)}checked="checked"{/if}/>
      {if isset($rule.match)}
	<input type="hidden" name="{g->formVar var="form[shortUrls][$moduleId][$index][match]}" value="{$rule.match}" />
      {/if}
    </td>
    <td style="text-align: center;">
      <span id="shortUrls-{$moduleId}-{$index}-toggle"
	    class="giBlockToggle gcBackground1 gcBorder2"
	    style="border-width: 1px;"
	    onclick="BlockToggle('shortUrls-{$moduleId}-{$index}-help', 'shortUrls-{$moduleId}-{$index}-toggle', 'table-row')">+</span>
    </td>
    <td>
      {$AdminRewrite.shortUrls.$moduleId.rules.$index.name|default:""}
    </td>
    <td>
      {if isset($AdminRewrite.shortUrls.$moduleId.rules.$index.locked)}
	{$rule.pattern}
	<input type="hidden" name="{g->formVar var="form[shortUrls][$moduleId][$index][pattern]"}" value="{$rule.pattern}" />
      {else}
	<input type="text" size="40" name="{g->formVar var="form[shortUrls][$moduleId][$index][pattern]"}" value="{$rule.pattern}" />
      {/if}
    </td>
    <td>
      {foreach from=$AdminRewrite.shortUrls.$moduleId.rules.$index.keywords key=keyword item=prefs}
	%{$keyword}%
      {/foreach}
    </td>
  </tr>
  <tr class="{$rowClass}" style="display: none;" id="shortUrls-{$moduleId}-{$index}-help">
    <td colspan="2">
      &nbsp;
    </td>
    <td colspan="4">
      <b>{g->text text="Help"}</b><br/>
      {if isset($AdminRewrite.shortUrls.$moduleId.rules.$index.help)}
	{$AdminRewrite.shortUrls.$moduleId.rules.$index.help}
      {else}
	<i>{g->text text="No help available"}</i>
      {/if}<br/><br/>
      <b>{g->text text="Keywords"}</b><br/>
      {assign var="hasKeywordHelp" value=false}
      {foreach from=$AdminRewrite.shortUrls.$moduleId.rules.$index.keywords key=keyword item=prefs}
	{if isset($prefs.help)}
	  %{$keyword}% : {$prefs.help}<br/>
	  {assign var="hasKeywordHelp" value=true}
	{/if}
      {/foreach}
      {if !$hasKeywordHelp}
	<i>{g->text text="No keyword help available"}</i>
      {/if}
    </td>
  </tr>
  {/foreach}

  {/foreach}
  </table>
</div>

<div class="gbBlock gcBackground1">
  <input type="submit"class="inputTypeSubmit" name="{g->formVar var="form[action][saveRules]"}" value="{g->text text="Save"}"/>
</div>
{/if}

{if $AdminRewrite.mode == 'setup'}
<div class="gbBlock">
  <h3> {g->text text="Approved referers"} </h3>

  <p class="giDescription">
    {g->text text="Some rules only apply if the referer (the site that linked to the item) is something other than Gallery itself. Hosts in the list below will be treated as friendly referers."}<br/>
  </p>

  <table class="gbDataTable"><tr>
    <td><input type="text" name="{g->formVar var="form[dummy]"}" size="60" value="{$AdminRewrite.serverName}" disabled/></td>
  {counter start=0 assign="i"}
  {foreach from=$form.allow item=host}
  </tr><tr>
    <td><input type="text" name="{g->formVar var="form[allow][$i]"}" size="60" value="{$host}"/></td>
    {counter print=false}
  {/foreach}
  </tr><tr>
    <td><input type="text" name="{g->formVar var="form[allow][$i]"}" size="60"/></td>
  </tr><tr>
    <td><input type="text" name="{g->formVar var="form[allow][`$i+1`]"}" size="60"/></td>
  </tr><tr>
    <td><input type="text" name="{g->formVar var="form[allow][`$i+2`]"}" size="60"/></td>
  </tr></table>
</div>

{if $AdminRewrite.isEmbedded}
<div class="gbBlock">
  <h3> {g->text text="Embedded Setup"} </h3>

  <p class="giDescription">
    {g->text text="For URL Rewrite to work in an embedded environment you need to set up an extra htaccess file to hold the mod_rewrite rules."}
  </p>

  <input type="hidden" name="{g->formVar var="form[embedded][save]"}" value="true">
  <table class="gbDataTable"><tr>
    <td>
      {g->text text="Htaccess path:"}
    </td><td>
      <input type="text" size="60" name="{g->formVar var="form[embedded][htaccessPath]"}" value="{$form.embedded.htaccessPath}"/>
      {if isset($form.error.invalidDir)}
	<div class="giError">
	  {g->text text="Invalid directory."}
	</div>
      {/if}
    </td>
  </tr><tr>
    <td>
      {g->text text="Public path:"}
    </td><td>
      {$AdminRewrite.host}<input type="text" size="40" name="{g->formVar var="form[embedded][publicPath]"}" value="{$form.embedded.publicPath}"/>
      {if isset($form.error.invalidPath)}
	<div class="giError">
	  {g->text text="Invalid path."}
	</div>
      {/if}
    </td>
  </tr></table>
</div>
{/if}

<div class="gbBlock gcBackground1">
  <input type="submit" class="inputTypeSubmit" name="{g->formVar var="form[action][saveSetup]"}" value="{g->text text="Save"}"/>
</div>
{/if}

{if $AdminRewrite.mode == 'test'}
<div class="gbBlock">
  <table><tr>
    <td>
      <h3> {g->text text="Apache mod_rewrite"} </h3>

      {capture name=mod_rewrite_anchor}
      <a href="http://httpd.apache.org/docs/mod/mod_rewrite.html">mod_rewrite</a>
      {/capture}
      <p class="giDescription">
        {g->text text="In order for this Gallery module to work you need %s enabled with your Apache server." arg1=$smarty.capture.mod_rewrite_anchor}
      </p>
    </td>
    <td style="float: right; vertical-align: top;">
      {if $AdminRewrite.apacheCode == REWRITE_STATUS_OK}
        <h3 class="giSuccess"> {g->text text="Success"} </h3>
      {else}
        <h3 class="giWarning"> {g->text text="Warning"} </h3>
      {/if}
    </td>
  {if $AdminRewrite.apacheCode != REWRITE_STATUS_OK}
  </tr><tr>
    <td colspan="2">
      <div class="gbBlock">
        <h3> {g->text text="Test mod_rewrite manually"} </h3>

        {capture name="setup_link"}"{g->url arg1="view=core.SiteAdmin" arg2="subView=rewrite.SetupRewrite"}"{/capture}
        <p class="giDescription">
        {g->text text="Go to the <a href=%s>Setup</a> page where you will be able to further probe mod_rewrite." arg1=$smarty.capture.setup_link arg2="</a>"}
        </p>
      </div>
      
      <div class="gbBlock">
        <input type="submit" class="inputTypeSubmit"
          name="{g->formVar var="form[action][test]"}" value="{g->text text="Test Webserver Again"}"/>
      </div>
    </td>
  {/if}
  </tr><tr>
    <td>
      <h3> {g->text text="Gallery .htaccess file"} </h3>
      
      <p class="giDescription">
        {g->text text="Gallery's URL rewriting works by creating a new file in your gallery directory called <b>.htaccess</b> which contains rules for how short urls should be interpreted."}
      </p>
    </td>
    <td style="float: right; vertical-align: top;">
      {if $AdminRewrite.htaccessCode == REWRITE_STATUS_HTACCESS_READY}
        <h2 class="giSuccess"> {g->text text="Success"} </h2>
      {else}
        <h2 class="giError"> {g->text text="Error"} </h2>
      {/if}
    </td>
  {if $AdminRewrite.htaccessCode != REWRITE_STATUS_HTACCESS_READY}
  </tr><tr>
    <td colspan="2">
      <div class="gbBlock">
        {if $AdminRewrite.htaccessCode == REWRITE_STATUS_HTACCESS_MISSING}
        <h3> {g->text text="Please create a file in your Gallery directory named .htaccess"} </h3>
        
        <pre class="giDescription">touch {$AdminRewrite.htaccessPath}<br/>chmod 666 {$AdminRewrite.htaccessPath}</pre>
        {/if}
        
        {if $AdminRewrite.htaccessCode == REWRITE_STATUS_HTACCESS_CANT_READ}
        <h3> {g->text text="Please make sure Gallery can read the existing .htaccess file"} </h3>
        
        <pre class="giDescription">chmod 666 {$AdminRewrite.htaccessPath}</pre>
        {/if}
        
        {if $AdminRewrite.htaccessCode == REWRITE_STATUS_HTACCESS_CANT_WRITE}
        <h3> {g->text text="Please make sure Gallery can write to the existing .htaccess file"} </h3>
        
        <pre class="giDescription">chmod 666 {$AdminRewrite.htaccessPath}</pre>
        {/if}
      </div>
      
      <div class="gbBlock">
        <input type="submit" class="inputTypeSubmit"
          name="{g->formVar var="form[action][test]"}" value="{g->text text="Test .htaccess File Again"}"/>
      </div>
    </td>
  {/if}
  {if $AdminRewrite.isEmbedded}
  </tr><tr>
    <td>
      <h3> {g->text text="Embedded .htaccess file"} </h3>
      
      <p class="giDescription">
        {g->text text="You need a <b>.htaccess</b> file in the embedded access point directory."}
      </p>
    </td>
    <td style="float: right; vertical-align: top;">
      {if $AdminRewrite.embeddedCode == REWRITE_STATUS_HTACCESS_READY}
        <h2 class="giSuccess"> {g->text text="Success"} </h2>
      {else}
        <h2 class="giError"> {g->text text="Error"} </h2>
      {/if}
    </td>
  {if $AdminRewrite.embeddedCode != REWRITE_STATUS_HTACCESS_READY}
  </tr><tr>
    <td colspan="2">
      <div class="gbBlock">
        {if $AdminRewrite.embeddedCode == REWRITE_STATUS_HTACCESS_MISSING}
        <h3> {g->text text="Please create a file in your Gallery directory named .htaccess"} </h3>
        
        <pre class="giDescription">touch {$AdminRewrite.embeddedPath}<br/>chmod 666 {$AdminRewrite.embeddedPath}</pre>
        {/if}
        
        {if $AdminRewrite.embeddedCode == REWRITE_STATUS_HTACCESS_CANT_READ}
        <h3> {g->text text="Please make sure Gallery can read the existing .htaccess file"} </h3>
        
        <pre class="giDescription">chmod 666 {$AdminRewrite.embeddedPath}</pre>
        {/if}
        
        {if $AdminRewrite.embeddedCode == REWRITE_STATUS_HTACCESS_CANT_WRITE}
        <h3> {g->text text="Please make sure Gallery can write to the existing .htaccess file"} </h3>
        
        <pre class="giDescription">chmod 666 {$AdminRewrite.embeddedPath}</pre>
        {/if}
      </div>
      
      <div class="gbBlock">
        <input type="submit" class="inputTypeSubmit"
          name="{g->formVar var="form[action][test]"}" value="{g->text text="Test .htaccess File Again"}"/>
      </div>
    </td>
  {/if}
  </tr><tr>
    <td>
      <h3> {g->text text="Embedded .htaccess file is up to date"} </h3>
      
      <p class="giDescription">
        {g->text text="This checks if the content in your embedded .htaccess file is equal to the standalone version."}
      </p>
    </td>
    <td style="float: right; vertical-align: top;">
      {if $AdminRewrite.embeddedSync == REWRITE_STATUS_OK}
        <h2 class="giSuccess"> {g->text text="Success"} </h2>
      {else}
        <h2 class="giError"> {g->text text="Error"} </h2>
      {/if}
    </td>
  {if $AdminRewrite.embeddedSync != REWRITE_STATUS_OK}
  </tr><tr>
    <td colspan="2">
      <div class="gbBlock">
        <h3> {g->text text="Please update your rules while in embedded mode. Hit the Save button should be sufficient."} </h3>
      </div>
      
      <div class="gbBlock">
        <input type="submit" class="inputTypeSubmit"
          name="{g->formVar var="form[action][test]"}" value="{g->text text="Test .htaccess Files Again"}"/>
      </div>
    </td>
  {/if}
  {/if}
  </tr></table>
</div>
{/if}

{*
 * $Revision: 1.16 $
 * If you want to customize this file, do not edit it directly since future upgrades
 * may overwrite it.  Instead, copy it into a new directory called "local" and edit that
 * version.  Gallery will look for that file first and use it if it exists.
 *}
<div class="gbBlock gcBackground1">
  <h2> {g->text text="URL Rewrite System Checks"} </h2>
</div>

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
      {if $SetupRewrite.apacheCode == REWRITE_STATUS_OK}
        <h3 class="giSuccess"> {g->text text="Success"} </h3>
      {else}
        <h3 class="giWarning"> {g->text text="Warning"} </h3>
      {/if}
    </td>
  {if $SetupRewrite.apacheCode != REWRITE_STATUS_OK}
  </tr><tr>
    <td colspan="2">
      {if $SetupRewrite.apacheCode == REWRITE_STATUS_APACHE_UNABLE_TO_TEST}
      <div class="gbBlock">
        <h3> {g->text text="Custom Gallery directory test setup"} </h3>

        <p class="giDescription">
          {g->text text="Gallery tries to test mod_rewrite in action. For this to work you need to edit each of these two files accordingly:"}
        </p>

        <p class="giDescription">
          <b>{$SetupRewrite.customFile}</b><br/>
          {g->text text="Line 6:"} {$SetupRewrite.customLine}
        </p>

        <p class="giDescription">
          <b>{$SetupRewrite.customFileNoOptions}</b><br/>
          {g->text text="Line 6:"} {$SetupRewrite.customLineNoOptions}
        </p>
      </div>
      {/if}

      {if $SetupRewrite.apacheCode == REWRITE_STATUS_MULTISITE}
        <h3 class="giWarning"> {g->text text="Multisite setup"} </h3>

        <p class="giDescription">
          {g->text text="Gallery tries to test mod_rewrite in action. This does not work with multisite since Gallery lacks the complete codebase."}
        </p>

	<p class="giDescription">
	  <b>{g->text text="The tests below will only show if mod_rewrite works for your Gallery codebase. If you experience broken links chances are that mod_rewrite does not work."}</b>
	</p>
      </div>
      {/if}

      <div class="gbBlock">
        <h3> {g->text text="Test mod_rewrite manually"} </h3>

        <p class="giDescription">
        {g->text text="For whatever reason, Gallery did not detect a working mod_rewrite setup. If you are confident that mod_rewrite does work you may override the automatic detection. Please, run these two tests to see for yourself."}
        </p>

        <table class="gbDataTable"><tr>
          <th> {g->text text="Works"} </th>
          <th> {g->text text="Test"} </th>
        </tr><tr>
          <td style="text-align: center;">
            <input type="checkbox" name="{g->formVar var="form[force][test1]"}"/>
          </td>
          <td>
            <a href="{$SetupRewrite.test1}">{g->text text="Test mod_rewrite"}</a>
          </td>
        </tr><tr>
          <td style="text-align: center;">
            <input type="checkbox" name="{g->formVar var="form[force][test2]"}"/>
          </td>
          <td>
            <a href="{$SetupRewrite.test2}">{g->text text="Test mod_rewrite with Options directive"}</a>
          </td>
        </tr></table>

        <p class="giDescription">
          {g->text text="If one of the two tests gives you a page with the text PASS_REWRITE you are good to go."}
        </p>

        <input type="submit" class="inputTypeSubmit"
          name="{g->formVar var="form[action][force]"}" value="{g->text text="Save"}"/>
      </div>

      {include file="gallery:modules/rewrite/templates/Troubleshooting.tpl"}

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
      {if $SetupRewrite.htaccessCode == REWRITE_STATUS_HTACCESS_READY}
        <h2 class="giSuccess"> {g->text text="Success"} </h2>
      {else}
        <h2 class="giError"> {g->text text="Error"} </h2>
      {/if}
    </td>
  {if $SetupRewrite.htaccessCode != REWRITE_STATUS_HTACCESS_READY}
  </tr><tr>
    <td colspan="2">
      <div class="gbBlock">
        {if $SetupRewrite.htaccessCode == REWRITE_STATUS_HTACCESS_MISSING}
        <h3> {g->text text="Please create a file in your Gallery directory named .htaccess"} </h3>

        <pre class="giDescription">touch {$SetupRewrite.htaccessPath}<br/>chmod 666 {$SetupRewrite.htaccessPath}</pre>
        {/if}

        {if $SetupRewrite.htaccessCode == REWRITE_STATUS_HTACCESS_CANT_READ}
        <h3> {g->text text="Please make sure Gallery can read the existing .htaccess file"} </h3>

        <pre class="giDescription">chmod 666 {$SetupRewrite.htaccessPath}</pre>
        {/if}

        {if $SetupRewrite.htaccessCode == REWRITE_STATUS_HTACCESS_CANT_WRITE}
        <h3> {g->text text="Please make sure Gallery can write to the existing .htaccess file"} </h3>

        <pre class="giDescription">chmod 666 {$SetupRewrite.htaccessPath}</pre>
        {/if}
      </div>

      <div class="gbBlock">
        <input type="submit" class="inputTypeSubmit"
          name="{g->formVar var="form[action][test]"}" value="{g->text text="Test .htaccess File Again"}"/>
      </div>
    </td>
  {/if}
  </tr></table>
</div>

<div class="gbBlock gcBackground1">
  <input type="submit" class="inputTypeSubmit"
   name="{g->formVar var="form[action][done]"}" value="{g->text text="Done"}"/>
</div>

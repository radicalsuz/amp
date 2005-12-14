{*
 * $Revision: 1.5 $
 * If you want to customize this file, do not edit it directly since future upgrades
 * may overwrite it.  Instead, copy it into a new directory called "local" and edit that
 * version.  Gallery will look for that file first and use it if it exists.
 *}
<div class="gbBlock">
  <h3> {g->text text="Troubleshooting"} </h3>
  
  <p class="giDescription">
    <span id="question-0-toggle"
	    class="giBlockToggle gcBackground1 gcBorder2"
	    style="border-width: 1px;"
	    onclick="BlockToggle('question-0-help', 'question-0-toggle', 'inline')">+</span>
    <b>{g->text text="How can I check if mod_rewrite is loaded?"}</b><br/>
    <span id="question-0-help" style="display: none;">
      {capture name=url}{g->url href="lib/support/phpinfo.php"}{/capture}
      {g->text text="Go to the <a href=\"%s\">Gallery phpinfo page</a> and look for Loaded Modules. You should see mod_rewrite in the list if it's loaded." arg1=$smarty.capture.url}
    </span>
  </p>
  
  <p class="giDescription">
    <span id="question-1-toggle"
	    class="giBlockToggle gcBackground1 gcBorder2"
	    style="border-width: 1px;"
	    onclick="BlockToggle('question-1-help', 'question-1-toggle', 'inline')">+</span>
    <b>{g->text text="I know mod_rewrite is loaded, why is Gallery telling me it's not working?"}</b><br/>
    <span id="question-1-help" style="display: none;">
      {g->text text="If you are the server admin make sure the Gallery directory has the proper AllowOverride rights. Gallery needs to be able to override FileInfo and Options. Put this at the end of your Apache configuration:"}<br/>
      &lt;Directory /my/gallery2&gt;<br/>
      &nbsp; &nbsp; AllowOverride FileInfo Options<br/>
      &lt;/Directory&gt;<br/><br/>
    </span>
  </p>
  
  <p class="giDescription">
    <span id="question-2-toggle"
	    class="giBlockToggle gcBackground1 gcBorder2"
	    style="border-width: 1px;"
	    onclick="BlockToggle('question-2-help', 'question-2-toggle', 'inline')">+</span>
    <b>{g->text text="My Gallery is password protected using Apache mod_auth. I know mod_rewrite works, why doesnt Gallery detect this?"}</b><br/>
    <span id="question-2-help" style="display: none;">
      {g->text text="Gallery tries to fetch a page from your server and most likely Gallery gets an unauthorized access error. In order to fix this you need to allow requests from the server IP. If you are paranoid you could narrow it down to requests to the gallery2/modules/rewrite/data directory."}
    </span>
  </p>
</div>

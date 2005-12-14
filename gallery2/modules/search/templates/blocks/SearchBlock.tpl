{*
 * $Revision: 1.3 $
 * If you want to customize this file, do not edit it directly since future upgrades
 * may overwrite it.  Instead, copy it into a new directory called "local" and edit that
 * version.  Gallery will look for that file first and use it if it exists.
 *}
{if !isset($showAdvancedLink)} {assign var="showAdvancedLink" value="true"} {/if}

{g->addToTrailer}
<script type="text/javascript">
  // <![CDATA[
  var search_SearchBlock_searchDefault = '{g->text text="Search the Gallery"}';
  var search_SearchBlock_input = document.getElementById('search_SearchBlock').searchCriteria;
  function search_SearchBlock_checkForm() {ldelim}
    var sc = search_SearchBlock_input.value;
    if (sc == searchDefault || sc == '') {ldelim}
      alert('{g->text text="Please enter keywords to search."}');
      return false;
    {rdelim} else {ldelim}
      document.getElementById('search_SearchBlock').submit();
    {rdelim}
  {rdelim}

  function search_SearchBlock_focus() {ldelim}
    if (search_SearchBlock_input.value == search_SearchBlock_searchDefault) {ldelim}
      search_SearchBlock_input.value = '';
    {rdelim}
  {rdelim}

  function search_SearchBlock_blur() {ldelim}
    if (search_SearchBlock_input.value == '') {ldelim}
      search_SearchBlock_input.value = search_SearchBlock_searchDefault;
    {rdelim}
  {rdelim}
  // ]]>
</script>
{/g->addToTrailer}

<div class="{$class}">
  <form id="search_SearchBlock" action="{g->url}" method="post" onsubmit="return checkForm()">
    <div>
      {g->hiddenFormVars}
      <input type="hidden" name="{g->formVar var="view"}" value="search.SearchScan"/>
      <input type="hidden" name="{g->formVar var="form[formName]"}" value="search_SearchBlock"/>
      <input type="text" id="searchCriteria" size="18"
	     name="{g->formVar var="form[searchCriteria]"}"
	     value="{g->text text="Search the Gallery"}"
	     onfocus="search_SearchBlock_focus()"
	     onblur="search_SearchBlock_blur()"
	     class="textbox"/>
      <input type="hidden" name="{g->formVar var="form[useDefaultSettings]"}" value="1" />
    </div>
    {if $showAdvancedLink}
    <div>
      <a href="{g->url arg1="view=search.SearchScan" arg2="form[useDefaultSettings]=1"
		       arg3="return=1"}"
	 class="{g->linkId view="search.SearchScan"} advanced">{g->text text="Advanced Search"}</a>
    </div>
    {/if}
  </form>
</div>


{*
 * $Revision: 1.4 $
 * If you want to customize this file, do not edit it directly since future upgrades
 * may overwrite it.  Instead, copy it into a new directory called "local" and edit that
 * version.  Gallery will look for that file first and use it if it exists.
 *}
{g->callback type="albumselect.LoadAlbumData" albumTree=true}

{if isset($block.albumselect)}
<div class="{$class}">
  <div class="dtree">
    {assign var="params" value=$block.albumselect.LoadAlbumData.params}
    {assign var="albumTree" value=$block.albumselect.LoadAlbumData.albumTreeName}
    {if $params.treeExpandCollapse and !$params.treeCloseSameLevel}
      <p>
	<a href="javascript: {$albumTree}.openAll()"
	 onclick="this.blur()">{g->text text="Expand"}</a>
	|
	<a href="javascript: {$albumTree}.closeAll()"
	 onclick="this.blur()">{g->text text="Collapse"}</a>
      </p>
    {/if}

    <script type="text/javascript">
      // <![CDATA[
      var {$albumTree} = new dTree('{$albumTree}');
      {$albumTree}.icon = {ldelim}
	  root            : '{g->url href="modules/albumselect/images/base.gif"}',
	  folder          : '{g->url href="modules/albumselect/images/folder.gif"}',
	  folderOpen      : '{g->url href="modules/albumselect/images/imgfolder.gif"}',
	  node            : '{g->url href="modules/albumselect/images/imgfolder.gif"}',
	  empty           : '{g->url href="modules/albumselect/images/empty.gif"}',
	  line            : '{g->url href="modules/albumselect/images/line.gif"}',
	  join            : '{g->url href="modules/albumselect/images/join.gif"}',
	  joinBottom      : '{g->url href="modules/albumselect/images/joinbottom.gif"}',
	  plus            : '{g->url href="modules/albumselect/images/plus.gif"}',
	  plusBottom      : '{g->url href="modules/albumselect/images/plusbottom.gif"}',
	  minus           : '{g->url href="modules/albumselect/images/minus.gif"}',
	  minusBottom     : '{g->url href="modules/albumselect/images/minusbottom.gif"}',
	  nlPlus          : '{g->url href="modules/albumselect/images/nolines_plus.gif"}',
	  nlMinus         : '{g->url href="modules/albumselect/images/nolines_minus.gif"}'
      {rdelim};
      {$albumTree}.config.useLines = {if $params.treeLines}true{else}false{/if};
      {$albumTree}.config.useIcons = {if $params.treeIcons}true{else}false{/if};
      {$albumTree}.config.useCookies = {if $params.treeCookies}true{else}false{/if};
      {$albumTree}.config.closeSameLevel = {if $params.treeCloseSameLevel}true{else}false{/if};
      {$albumTree}.add(0, -1, " {$block.albumselect.LoadAlbumData.titles.root|markup:strip}",
		    '{g->url}');
      {foreach from=$block.albumselect.LoadAlbumData.tree item=node}
	{assign var="title" value=$block.albumselect.LoadAlbumData.titles[$node.id]|markup:strip}
	{$albumTree}.add({$node.nodeId}, {$node.parentNode}, "{$title}",
		      '{g->url arg1="view=core.ShowItem" arg2="itemId=`$node.id`"}');
      {/foreach}
      document.write({$albumTree});
      // ]]>
    </script>
  </div>
</div>
{/if}

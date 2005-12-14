{*
 * $Revision: 1.4 $
 * If you want to customize this file, do not edit it directly since future upgrades
 * may overwrite it.  Instead, copy it into a new directory called "local" and edit that
 * version.  Gallery will look for that file first and use it if it exists.
 *}
{g->callback type="albumselect.LoadAlbumData"}

{if isset($block.albumselect)}
<div class="{$class}">
  <select onchange="{literal}if (this.value) { var newLocation = this.value; this.options[0].selected = true; location.href = newLocation; }{/literal}">
    <option value="">
      {g->text text="&laquo; Jump to Album &raquo;"}
    </option>
    {foreach from=$block.albumselect.LoadAlbumData.tree item=node}
      {assign var="title" value=$block.albumselect.LoadAlbumData.titles[$node.id]}
      <option value="{g->url arg1="view=core.ShowItem" arg2="itemId=`$node.id`"}">
	{$title|markup:strip|entitytruncate:20|indent:$node.depth:"-- "}
      </option>
    {/foreach}
  </select>
</div>
{/if}

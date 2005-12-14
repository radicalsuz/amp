{*
 * $Revision: 1.3 $
 * If you want to customize this file, do not edit it directly since future upgrades
 * may overwrite it.  Instead, copy it into a new directory called "local" and edit that
 * version.  Gallery will look for that file first and use it if it exists.
 *}
<script type="text/javascript">
// <![CDATA[
{if $theme.imageCount==1}
var data_iw = new Array(1); data_iw[0] = {$theme.imageWidths};
var data_ih = new Array(1); data_ih[0] = {$theme.imageHeights};
{else}
var data_iw = new Array({$theme.imageWidths});
var data_ih = new Array({$theme.imageHeights});
{/if}
var data_count = data_iw.length, data_name = '{$theme.item.id}',
    data_view = {$theme.viewIndex|default:-1},
    album_showtext = '{g->text text="Show Details" forJavascript=true}',
    album_hidetext = '{g->text text="Hide Details" forJavascript=true}',
    album_showlinks = '{g->text text="Show Item Links" forJavascript=true}',
    album_hidelinks = '{g->text text="Hide Item Links" forJavascript=true}',
    item_details = '{g->text text="Item Details" forJavascript=true}';
// ]]>
</script>
<script type="text/javascript" src="{$theme.themeUrl}/hybrid.js"></script>
<style type="text/css">
#gsAlbumContent td.t {ldelim} width: {$theme.columnWidthPct}%; {rdelim}
</style>

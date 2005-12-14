{*
 * $Revision: 1.9 $
 * If you want to customize this file, do not edit it directly since future upgrades
 * may overwrite it.  Instead, copy it into a new directory called "local" and edit that
 * version.  Gallery will look for that file first and use it if it exists.
 *}
<div id="gsContent" class="gcBorder1">
  <div class="gbBlock gcBackground1">
    <h2> {g->text text="Image Frame Samples"} </h2>
  </div>

  <div>
  {foreach from=$ImageFrameSample.list key=frame item=name}
    <div style="float:left;margin:6px">
    {$name}<br/>
    {g->container type="imageframe.ImageFrame" frame=$frame}
      {if isset($ImageFrameSample.item)}
	{g->image item=$ImageFrameSample.item image=$ImageFrameSample.thumb
		  maxSize=150 id="%ID%" class="%CLASS%"}
      {else}
	<img src="{g->url href="modules/imageframe/images/sample.jpg"}"
	     width="150" height="126" id="%ID%" class="%CLASS%"/>
      {/if}
    {/g->container}
    </div>
  {/foreach}
  </div>
  <div class="gbBlock" style="clear: both">&nbsp;</div>
</div>

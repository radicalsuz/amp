{*
 * $Revision: 1.4 $
 * If you want to customize this file, do not edit it directly since future upgrades
 * may overwrite it.  Instead, copy it into a new directory called "local" and edit that
 * version.  Gallery will look for that file first and use it if it exists.
 *}
{if isset($ImageFrameData.data[$frame])}
  {assign var="data" value=$ImageFrameData.data[$frame]}
{/if}
{counter name="ImageFrame_counter" assign="IF_count"}
{assign var="objectId" value="IFid`$IF_count`"}
{if !isset($data) || $data.type=='style'}
  {$content|replace:"%ID%":$objectId|replace:"%CLASS%":"ImageFrame_`$frame`"}
{elseif $data.type=='image'}
  <table class="ImageFrame_{$frame}" border="0" cellspacing="0" cellpadding="0">
  {if !empty($data.imageTT) || !empty($data.imageTL) || !empty($data.imageTR) ||
      !empty($data.imageTTL) || !empty($data.imageTTR)}
    <tr>
    <td class="TL"></td>
    {if $data.wHL}<td class="TTL"></td>{/if}
    <td class="TT"{if $data.wHL or $data.wHR}
     style="width:expression((document.getElementById('{$objectId}').width-{$data.wHL+$data.wHR})+'px')"
    {/if}><div class="H"></div></td>
    {if $data.wHR}<td class="TTR"></td>{/if}
    <td class="TR"></td>
    </tr>
  {/if}
  <tr>
  {if $data.hVT}<td class="LLT"></td>{else}<td class="LL"{if $data.hVT or $data.hVB}
   style="height:expression((document.getElementById('{$objectId}').height-{$data.hVT+$data.hVB})+'px')"
   {/if}><div class="V">&nbsp;</div></td>{/if}
  <td rowspan="{$data.rowspan}" colspan="{$data.colspan}" class="IMG"
  >{$content|replace:"%ID%":$objectId|replace:"%CLASS%":"ImageFrame_image"}</td>
  {if $data.hVT}<td class="RRT"></td>{else}<td class="RR"{if $data.hVT or $data.hVB}
   style="height:expression((document.getElementById('{$objectId}').height-{$data.hVT+$data.hVB})+'px')"
   {/if}><div class="V">&nbsp;</div></td>{/if}
  </tr>
  {if $data.hVT}
    <tr>
    <td class="LL"{if $data.hVT or $data.hVB}
     style="height:expression((document.getElementById('{$objectId}').height-{$data.hVT+$data.hVB})+'px')"
    {/if}><div class="V">&nbsp;</div></td>
    <td class="RR"{if $data.hVT or $data.hVB}
     style="height:expression((document.getElementById('{$objectId}').height-{$data.hVT+$data.hVB})+'px')"
    {/if}><div class="V">&nbsp;</div></td>
    </tr>
  {/if}
  {if $data.hVB}
    <tr>
    <td class="LLB"></td>
    <td class="RRB"></td>
    </tr>
  {/if}
  {if !empty($data.imageBB) || !empty($data.imageBL) || !empty($data.imageBR) ||
      !empty($data.imageBBL) || !empty($data.imageBBR)}
    <tr>
    <td class="BL"></td>
    {if $data.wHL}<td class="BBL"></td>{/if}
    <td class="BB"{if $data.wHL or $data.wHR}
     style="width:expression((document.getElementById('{$objectId}').width-{$data.wHL+$data.wHR})+'px')"
    {/if}><div class="H"></div></td>
    {if $data.wHR}<td class="BBR"></td>{/if}
    <td class="BR"></td>
    </tr>
  {/if}
  </table>
{/if}

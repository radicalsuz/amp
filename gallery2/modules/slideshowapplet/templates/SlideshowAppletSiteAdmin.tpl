{*
 * $Revision: 1.1 $
 * If you want to customize this file, do not edit it directly since future upgrades
 * may overwrite it.  Instead, copy it into a new directory called "local" and edit that
 * version.  Gallery will look for that file first and use it if it exists.
 *}
<div class="gbBlock gcBackground1">
  <h2> {g->text text="Slideshow Applet Settings"} </h2>
</div>

{if isset($status.saved)}
<div class="gbBlock"><h2 class="giSuccess">
  {g->text text="Settings saved successfully"}
</h2></div>
{/if}

<div class="gbBlock">
  <input type="checkbox" id="cbRecursive"{if $form.slideshowRecursive} checked="checked"{/if}
   name="{g->formVar var="form[slideshowRecursive]"}"/>
  <label for="cbRecursive">
    {g->text text="Include the pictures inside sub-albums in slideshows"}
  </label>
  <br />

  <label for="tfLimit">
    {g->text text="Limit the slideshow to: "}
  </label>
  <input type="text" size="6" id="tfLimit" name="{g->formVar var="form[slideshowMaxPictures]"}" value="{$form.slideshowMaxPictures}"/>
  pictures (0 for no limit)

  {if isset($form.error.limit)}
  <div class="giError">
    {g->text text="The limit must be a positive number"}
  </div>
  {/if}
</div>

<div class="gbBlock gcBackground1">
  <input type="submit" class="inputTypeSubmit"
   name="{g->formVar var="form[action][save]"}" value="{g->text text="Save"}"/>
  <input type="submit" class="inputTypeSubmit"
   name="{g->formVar var="form[action][reset]"}" value="{g->text text="Reset"}"/>
</div>

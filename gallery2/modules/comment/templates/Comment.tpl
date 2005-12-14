{*
 * $Revision: 1.24 $
 * If you want to customize this file, do not edit it directly since future upgrades
 * may overwrite it.  Instead, copy it into a new directory called "local" and edit that
 * version.  Gallery will look for that file first and use it if it exists.
 *}
<h3>
  {$comment.subject|markup}
</h3>

{if $can.edit}
<span class="edit">
  <a href="{g->url arg1="view=comment.EditComment" arg2="itemId=`$item.id`"
                   arg3="commentId=`$comment.id`" arg4="return=true"}">
      {g->text text="edit"}
  </a>
</span>
{/if}

{if $can.delete}
<span class="delete">
  <a href="{g->url arg1="view=comment.DeleteComment" arg2="itemId=`$item.id`"
                   arg3="commentId=`$comment.id`" arg4="return=true"}">
    {g->text text="delete"}
  </a>
</span>
{/if}

{if isset($truncate)}
{assign var="truncated" value=$comment.comment|markup|entitytruncate:$truncate}
{/if}

{if isset($truncate) && ($truncated != $comment.comment)}
  <a id="comment-more-toggle-{$comment.id}"
      onclick="document.getElementById('comment-truncated-{$comment.id}').style.display='none';
               document.getElementById('comment-full-{$comment.id}').style.display='block';
               document.getElementById('comment-more-toggle-{$comment.id}').style.display='none';
               document.getElementById('comment-less-toggle-{$comment.id}').style.display='inline'; "
      >{g->text text="show full"}</a>
  <a id="comment-less-toggle-{$comment.id}"
      onclick="document.getElementById('comment-truncated-{$comment.id}').style.display='block';
               document.getElementById('comment-full-{$comment.id}').style.display='none';
               document.getElementById('comment-more-toggle-{$comment.id}').style.display='inline';
               document.getElementById('comment-less-toggle-{$comment.id}').style.display='none'; "
      style="display: none">{g->text text="show summary"}</a>

<p id="comment-truncated-{$comment.id}" class="comment">
  {$comment.comment|markup|entitytruncate:$truncate}
</p>

<p id="comment-full-{$comment.id}" class="comment" style="display: none">
  {$comment.comment|markup}
</p>
{else}
<p class="comment">
  {$comment.comment|markup}
</p>
{/if}

<p class="info">
  {capture name="date"}{g->date timestamp=$comment.date style="datetime"}{/capture}
  {if $can.edit}
    {g->text text="Posted by %s on %s (%s)"
             arg1=$user.fullName|default:$user.userName
             arg2=$smarty.capture.date
             arg3=$comment.host}

  {else}
    {g->text text="Posted by %s on %s"
             arg1=$user.fullName|default:$user.userName
             arg2=$smarty.capture.date}
  {/if}
</p>

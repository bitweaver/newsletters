<a class="pagetitle" href="{$smarty.const.NEWSLETTERS_PKG_URL}admin/send.php">{tr}Send newsletters{/tr}</a>
<br /><br />

{if $emited eq 'y'}
	{tr}The newsletter was sent to {$sent} email addresses{/tr}<br /><br />
{/if}
{if $presend eq 'y'}
<div class="wikibody">{$subject}</div>
<div class="wikibody">{$data}</div>

{tr}This newsletter will be sent to {$subscribers} email addresses.{/tr}

<form method="post" action="{$smarty.const.NEWSLETTERS_PKG_URL}admin/send.php">
<input type="hidden" name="nl_id" value="{$nl_id|escape}" />
<input type="hidden" name="subject" value="{$subject|escape}" />
<input type="hidden" name="data" value="{$data|escape}" />
<input type="submit" name="send" value="{tr}send{/tr}" />
<input type="submit" name="preview" value="{tr}cancel{/tr}" />
</form>
{else}

<h2>{tr}Sent editions{/tr}</h2>
<table class="find">
<tr><td>{tr}Find{/tr}</td>
   <td>
   <form method="get" action="{$smarty.const.NEWSLETTERS_PKG_URL}admin/send.php">
     <input type="text" name="find" value="{$find|escape}" />
     <input type="submit" value="{tr}find{/tr}" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
   </form>
   </td>
</tr>
</table>

<table>
<tr>
<th><a href="{$smarty.const.NEWSLETTERS_PKG_URL}admin/send.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}">{tr}name{/tr}</a></th>
<th><a href="{$smarty.const.NEWSLETTERS_PKG_URL}admin/send.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'subject_desc'}subject_asc{else}subject_desc{/if}">{tr}subject{/tr}</a></th>
<th><a href="{$smarty.const.NEWSLETTERS_PKG_URL}admin/send.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'users_desc'}users_asc{else}users_desc{/if}">{tr}users{/tr}</a></th>
<th><a href="{$smarty.const.NEWSLETTERS_PKG_URL}admin/send.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'sent_desc'}sent_asc{else}sent_desc{/if}">{tr}sent{/tr}</a></th>
<th>{tr}action{/tr}</th>
</tr>
{cycle values="even,odd" print=false}
{foreach from=$editions key=edId item=ed}
<tr class="{cycle}">
<td>{$ed.title}</td>
<td>{$ed.subject}</td>
<td>{$ed.users}</td>
<td>{$ed.sent|bit_short_datetime}</td>
<td>
   <a href="{$smarty.const.NEWSLETTERS_PKG_URL}admin/send.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$ed.edition_id}">{tr}remove{/tr}</a>
   <a href="{$smarty.const.NEWSLETTERS_PKG_URL}admin/send.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;edition_id={$ed.edition_id}">{tr}use{/tr}</a>
</td>
</tr>
{/foreach}
</table>

<div class="pagination">
{if $prev_offset >= 0}
[<a href="{$smarty.const.NEWSLETTERS_PKG_URL}admin/send.php?find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a href="{$smarty.const.NEWSLETTERS_PKG_URL}admin/send.php?find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
{/if}
{if $direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
<a href="{$smarty.const.NEWSLETTERS_PKG_URL}admin/send.php?find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>

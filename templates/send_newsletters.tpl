<a class="pagetitle" href="{$gTikiLoc.NEWSLETTERS_PKG_URL}send.php">{tr}Send newsletters{/tr}</a>
<br /><br />

{if $emited eq 'y'}
	{tr}The newsletter was sent to {$sent} email addresses{/tr}<br /><br />
{/if}
{if $presend eq 'y'}
<div class="wikibody">{$subject}</div>
<div class="wikibody">{$data}</div>

{tr}This newsletter will be sent to {$subscribers} email addresses.{/tr}

<form method="post" action="{$gTikiLoc.NEWSLETTERS_PKG_URL}send.php">
<input type="hidden" name="nl_id" value="{$nl_id|escape}" />
<input type="hidden" name="subject" value="{$subject|escape}" />
<input type="hidden" name="data" value="{$data|escape}" />
<input type="submit" name="send" value="{tr}send{/tr}" />
<input type="submit" name="preview" value="{tr}cancel{/tr}" />
</form>
{else}
{if $preview eq 'y'}
<br />
<div class="wikibody">{$info.subject}</div>
<div class="wikibody">{$parsed}</div>
{/if}

<h2>{tr}Prepare a newsletter to be sent{/tr}</h2>
<form action="{$gTikiLoc.NEWSLETTERS_PKG_URL}send.php" method="post" id='editpageform'>
<table class="panel">
<tr><td>{tr}Subject{/tr}:</td><td><input type="text" maxlength="250" size="40" name="subject" value="{$info.subject|escape}" /></td></tr>
<tr><td>{tr}Newsletter{/tr}:</td><td>
<select name="nl_id">
{section loop=$newsletters name=ix}
<option value="{$newsletters[ix].nl_id|escape}" {if $newsletters[ix].nl_id eq $nl_id}selected="selected"{/if}>{$newsletters[ix].name}</option>
{/section}
</select>
</td></tr>
{if $tiki_p_use_content_templates eq 'y'}
<tr><td>{tr}Apply template{/tr}</td><td>
<select name="template_id" onchange="javascript:document.getElementById('editpageform').submit();">
<option value="0">{tr}none{/tr}</option>
{section name=ix loop=$templates}
<option value="{$templates[ix].template_id|escape}">{$templates[ix].name}</option>
{/section}
</select>
</td></tr>
{/if}
<tr><td>{tr}Data{/tr}:</td><td><textarea name="data" rows="25" cols="60">{$info.data|escape}</textarea></td></tr>
<tr class="panelsubmitrow"><td colspan="2">
<input type="submit" name="preview" value="{tr}Preview{/tr}" />&nbsp;<input type="submit" name="save" value="{tr}Send Newsletters{/tr}" />
</td></tr>
</table>
</form>
{/if}

<h2>{tr}Sent editions{/tr}</h2>
<table class="find">
<tr><td>{tr}Find{/tr}</td>
   <td>
   <form method="get" action="{$gTikiLoc.NEWSLETTERS_PKG_URL}send.php">
     <input type="text" name="find" value="{$find|escape}" />
     <input type="submit" value="{tr}find{/tr}" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
   </form>
   </td>
</tr>
</table>

<table>
<tr>
<th><a href="{$gTikiLoc.NEWSLETTERS_PKG_URL}send.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}">{tr}name{/tr}</a></th>
<th><a href="{$gTikiLoc.NEWSLETTERS_PKG_URL}send.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'subject_desc'}subject_asc{else}subject_desc{/if}">{tr}subject{/tr}</a></th>
<th><a href="{$gTikiLoc.NEWSLETTERS_PKG_URL}send.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'users_desc'}users_asc{else}users_desc{/if}">{tr}users{/tr}</a></th>
<th><a href="{$gTikiLoc.NEWSLETTERS_PKG_URL}send.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'sent_desc'}sent_asc{else}sent_desc{/if}">{tr}sent{/tr}</a></th>
<th>{tr}action{/tr}</th>
</tr>
{cycle values="even,odd" print=false}
{section name=user loop=$channels}
<tr class="{cycle}">
<td>{$channels[user].name}</td>
<td>{$channels[user].subject}</td>
<td>{$channels[user].users}</td>
<td>{$channels[user].sent|tiki_short_datetime}</td>
<td>
   <a href="{$gTikiLoc.NEWSLETTERS_PKG_URL}send.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$channels[user].edition_id}">{tr}remove{/tr}</a>
   <a href="{$gTikiLoc.NEWSLETTERS_PKG_URL}send.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;edition_id={$channels[user].edition_id}">{tr}use{/tr}</a>
</td>
</tr>
{/section}
</table>

<div class="pagination">
{if $prev_offset >= 0}
[<a href="{$gTikiLoc.NEWSLETTERS_PKG_URL}send.php?find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a href="{$gTikiLoc.NEWSLETTERS_PKG_URL}send.php?find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
{/if}
{if $direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
<a href="{$gTikiLoc.NEWSLETTERS_PKG_URL}send.php?find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>

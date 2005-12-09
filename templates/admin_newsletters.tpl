<a class="pagetitle" href="{$smarty.const.NEWSLETTERS_PKG_URL}admin/index.php">{tr}Admin newsletters{/tr}</a>
  
{if $gBitSystemPrefs.feature_help eq 'y'}
<a href="http://bitweaver.org/wiki/PackageNewsletters" target="help" title="{tr}Tikiwiki.org help{/tr}: {tr}Newsletters{/tr}"><img class="icon" src="{$smarty.const.IMG_PKG_URL}icons/help.gif" alt="help" /></a>
{/if}

{if $gBitSystemPrefs.feature_view_tpl eq 'y'}
<a href="{$smarty.const.THEMES_PKG_URL}edit_templates.php?template=templates/admin_newsletters.tpl" target="help" title="{tr}View tpl{/tr}: {tr}admin newsletters tpl{/tr}"><img class="icon" src="{$smarty.const.IMG_PKG_URL}icons/info.gif" alt="edit tpl" /></a>
{/if}
<br /><br />

<a href="{$smarty.const.NEWSLETTERS_PKG_URL}index.php">{tr}list newsletters{/tr}</a>
<a href="{$smarty.const.NEWSLETTERS_PKG_URL}send.php">{tr}send newsletters{/tr}</a>
<br /><br />

<h2>{tr}Create/edit newsletters{/tr}</h2>
{if $individual eq 'y'}
<a href="{$smarty.const.KERNEL_PKG_URL}object_permissions.php?objectName=newsletter%20{$info.name}&amp;object_type=newsletter&amp;permType=newsletters&amp;object_id={$info.nl_id}">{tr}There are individual permissions set for this newsletter{/tr}</a><br /><br />
{/if}

<form action="{$smarty.const.NEWSLETTERS_PKG_URL}admin/index.php" method="post">
<input type="hidden" name="nl_id" value="{$info.nl_id|escape}" />
<table class="panel">
<tr><td>{tr}Name{/tr}:</td><td><input type="text" name="name" value="{$info.name|escape}" /></td></tr>
<tr><td>{tr}Description{/tr}:</td><td><textarea name="description" rows="4" cols="40">{$info.description|escape}</textarea></td></tr>
<tr><td>{tr}Users can subscribe/unsubscribe to this list{/tr}</td><td>
<input type="checkbox" name="allow_user_sub" {if $info.allow_user_sub eq 'y'}checked="checked"{/if} /></td></tr>
<tr><td>{tr}Users can subscribe any email address{/tr}</td><td>
<input type="checkbox" name="allow_any_sub" {if $info.allow_any_sub eq 'y'}checked="checked"{/if} /></td></tr>
<tr><td>{tr}Add unsubscribe instructions to each newsletter{/tr}</td><td>
<input type="checkbox" name="unsub_msg" {if $info.unsub_msg eq 'y'}checked="checked"{/if} /></td></tr>
<tr><td>{tr}Validate email addresses{/tr}</td><td>
<input type="checkbox" name="validate_addr" {if $info.validate_addr eq 'y'}checked="checked"{/if} /></td></tr>
<tr class="panelsubmitrow"><td colspan="2"><input type="submit" name="save" value="{tr}Save{/tr}" /></td></tr>
</table>
</form>

<h2>{tr}Newsletters{/tr}</h2>
<table class="find">
<tr><td>{tr}Find{/tr}</td>
   <td>
   <form method="get" action="{$smarty.const.NEWSLETTERS_PKG_URL}admin/index.php">
     <input type="text" name="find" value="{$find|escape}" />
     <input type="submit" value="{tr}find{/tr}" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
   </form>
   </td>
</tr>
</table>

<table class="panel">
<tr>
<th><a href="{$smarty.const.NEWSLETTERS_PKG_URL}admin/index.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'nl_id_desc'}nl_id_asc{else}nl_id_desc{/if}">{tr}ID{/tr}</a></th>
<th><a href="{$smarty.const.NEWSLETTERS_PKG_URL}admin/index.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}">{tr}name{/tr}</a></th>
<th><a href="{$smarty.const.NEWSLETTERS_PKG_URL}admin/index.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'description_desc'}description_asc{else}description_desc{/if}">{tr}description{/tr}</a></th>
<th><a href="{$smarty.const.NEWSLETTERS_PKG_URL}admin/index.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'users_desc'}users_asc{else}users_desc{/if}">{tr}users{/tr}</a></th>
<th><a href="{$smarty.const.NEWSLETTERS_PKG_URL}admin/index.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'editions_desc'}editions_asc{else}editions_desc{/if}">{tr}editions{/tr}</a></th>
<th><a href="{$smarty.const.NEWSLETTERS_PKG_URL}admin/index.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'last_sent_desc'}last_sent_asc{else}last_sent_desc{/if}">{tr}last sent{/tr}</a></th>
<th>{tr}action{/tr}</th>
</tr>
{cycle values="even,odd" print=false}
{section name=user loop=$channels}
<tr class="{cycle}">
<td>{$channels[user].nl_id}</td>
<td>{$channels[user].name}</td>
<td>{$channels[user].description}</td>
<td>{$channels[user].users} ({$channels[user].confirmed})</td>
<td>{$channels[user].editions}</td>
<td>{$channels[user].last_sent|bit_short_datetime}</td>
<td>
   <a href="{$smarty.const.NEWSLETTERS_PKG_URL}admin/index.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$channels[user].nl_id}">{tr}remove{/tr}</a>
   <a href="{$smarty.const.NEWSLETTERS_PKG_URL}admin/index.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;nl_id={$channels[user].nl_id}">{tr}edit{/tr}</a>
   <a href="{$smarty.const.NEWSLETTERS_PKG_URL}admin/admin_newsletter_subscriptions.php?nl_id={$channels[user].nl_id}">{tr}subscriptions{/tr}</a>
   {if $channels[user].individual eq 'y'}({/if}<a href="{$smarty.const.KERNEL_PKG_URL}object_permissions.php?objectName=newsletter%20{$channels[user].name}&amp;object_type=newsletter&amp;permType=newsletters&amp;object_id={$channels[user].nl_id}">{tr}perms{/tr}</a>{if $channels[user].individual eq 'y'}){/if}
</td>
</tr>
{/section}
</table>

<div class="pagination">
{if $prev_offset >= 0}
[<a href="{$smarty.const.NEWSLETTERS_PKG_URL}admin/index.php?find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a href="{$smarty.const.NEWSLETTERS_PKG_URL}admin/index.php?find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
{/if}
{if $direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
<a href="{$smarty.const.NEWSLETTERS_PKG_URL}admin/index.php?find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>

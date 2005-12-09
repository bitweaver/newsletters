<a href="{$smarty.const.NEWSLETTERS_PKG_URL}index.php" class="pagetitle">{tr}Newsletters{/tr}</a><br /><br />
{if $subscribed eq 'y'}
{tr}Thanks for your subscription. You will receive an email soon to confirm your subscription. No newsletters will be sent to you until the subscription is confirmed.{/tr}<br /><br />
{/if}
{if $unsub eq 'y'}
{tr}Your email address was removed from the list of subscriptors.{/tr}<br /><br />
{/if}

{if $confirm eq 'y'}
<table class="panel">
<caption>{tr}Subscription confirmed!{/tr}</caption>
</tr>
<tr>
  <td>{tr}Name{/tr}:</td>
  <td>{$nl_info.name}</td>
</tr>
<tr>
  <td>{tr}Description{/tr}:</td>
  <td>{$nl_info.description}</td>
</tr>
</table>
{/if}

{if $subscribe eq 'y'}
<form method="post" action="{$smarty.const.NEWSLETTERS_PKG_URL}index.php">
<input type="hidden" name="nl_id" value="{$nl_id|escape}" />
<table class="panel">
<caption>{tr}Subscribe to newsletter{/tr}</caption>
<tr>
  <td>{tr}Name{/tr}:</td>
  <td>{$nl_info.name}</td>
</tr>
<tr>
  <td>{tr}Description{/tr}:</td>
  <td>{$nl_info.description}</td>
</tr>
{if ($nl_info.allow_user_sub eq 'y') or ($bitweaver.orgi_p_admin_newsletters eq 'y')}
{if $bitweaver.orgi_p_subscribe_email eq 'y'}
<tr>
  <td>{tr}Email:{/tr}</td>
  <td><input type="text" name="email" value="{$email|escape}" /></td>
</tr>
{else}
  <input type="hidden" name="email" value="{$email|escape}" />
{/if}
<tr class="panelsubmitrow">
  <td colspan="2"><input type="submit" name="subscribe" value="{tr}Subscribe{/tr}" /></td>
</tr>
{/if}
</table>
</form>
{/if}

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

<table>
<tr>
<th><a href="{$smarty.const.NEWSLETTERS_PKG_URL}admin/index.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}">{tr}name{/tr}</a></th>
<th><a href="{$smarty.const.NEWSLETTERS_PKG_URL}admin/index.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'description_desc'}description_asc{else}description_desc{/if}">{tr}description{/tr}</a></th>
</tr>
{cycle values="even,odd" print=false}
{section name=user loop=$channels}
{if $channels.individual ne 'y' or $channels.individual_bitweaver.orgi_p_subscribe_newsletters eq 'y'}
<tr class="{cycle}">
<td><a href="{$smarty.const.NEWSLETTERS_PKG_URL}index.php?nl_id={$channels[user].nl_id}&amp;info=1">{$channels[user].name}</a></td>
<td>{$channels[user].description}</td>
</tr>
{/if}
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
<a href="{$smarty.const.NEWSLETTERS_PKG_URL}admin/index.php?find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>

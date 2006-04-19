<div class="floaticon">{bithelp}</div>

<div class="admin newsletters">
	<div class="header">
		<h1>{tr}Admin newsletter subscriptions{/tr}</h1>
	</div>

	<div class="body">
		{form}
			<input type="hidden" name="nl_id" value="{$nl_id|escape}" />

			<div class="row">
				{formlabel label="" for=""}
				{forminput}
					{formhelp note=""}
				{/forminput}
			</div>

			<div class="row submit">
			</div>
		{/form}

		{minifind}
	</div><!-- end .body -->
</div><!-- end .newsletters -->


<table class="panel">
	<caption>Newsletters</caption>
	<tr>
		<td>{tr}Name{/tr}:</td>
		<td>{$nl_info.name}</td>
	</tr>
	<tr>
		<td>{tr}Description{/tr}:</td>
		<td>{$nl_info.description}</td>
	</tr>
</table>

{* original code
<h2>{tr}Add a subscription newsletters{/tr}</h2>
<form action="{$smarty.const.NEWSLETTERS_PKG_URL}admin/admin_newsletter_subscriptions.php" method="post">
<input type="hidden" name="nl_id" value="{$nl_id|escape}" />
<table class="panel">
<tr><td>{tr}Email{/tr}:</td><td><input type="text" name="email" /></td></tr>
<tr class="panelsubmitrow"><td colspan="2"><input type="submit" name="save" value="{tr}Save{/tr}" /></td></tr>
</table>
</form>

<h2>{tr}Add all your site users to this newsletter (broadcast){/tr}</h2>
<a href="{$smarty.const.NEWSLETTERS_PKG_URL}admin/admin_newsletter_subscriptions.php?nl_id={$nl_id}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;find={$find}&amp;add_all=1">{tr}Add users{/tr}</a>

<h2>{tr}Subscriptions{/tr}</h2>
<table class="find">
<tr><td>{tr}Find{/tr}</td>
   <td>
   <form method="get" action="{$smarty.const.NEWSLETTERS_PKG_URL}admin/admin_newsletter_subscriptions.php">
     <input type="text" name="find" value="{$find|escape}" />
     <input type="submit" value="{tr}find{/tr}" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
   </form>
   </td>
</tr>
</table>

<table>
<tr>
<th><a href="{$smarty.const.NEWSLETTERS_PKG_URL}admin/admin_newsletter_subscriptions.php?nl_id={$nl_id}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'email_desc'}email_asc{else}email_desc{/if}">{tr}email{/tr}</a></th>
<th><a href="{$smarty.const.NEWSLETTERS_PKG_URL}admin/admin_newsletter_subscriptions.php?nl_id={$nl_id}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'valid_desc'}valid_asc{else}valid_desc{/if}">{tr}valid{/tr}</a></th>
<th><a href="{$smarty.const.NEWSLETTERS_PKG_URL}admin/admin_newsletter_subscriptions.php?nl_id={$nl_id}&amp;offset={$offset}&amp;sort_mode={if $sort_mode eq 'subscribed_desc'}subscribed_asc{else}subscribed_desc{/if}">{tr}subscribed{/tr}</a></th>
<th>{tr}action{/tr}</th>
</tr>
{cycle values="even,odd" print=false}
{section name=user loop=$channels}
<tr class="{cycle}">
<td>{$channels[user].email}</td>
<td>{$channels[user].valid}</td>
<td>{$channels[user].subscribed|tiki_short_datetime}</td>
<td>
   <a href="{$smarty.const.NEWSLETTERS_PKG_URL}admin/admin_newsletter_subscriptions.php?nl_id={$nl_id}&amp;offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$channels[user].nl_id}&amp;email={$channels[user].email}">{tr}remove{/tr}</a>
</td>
</tr>
{/section}
</table>

<div class="pagination">
{if $prev_offset >= 0}
[<a href="{$smarty.const.NEWSLETTERS_PKG_URL}admin/admin_newsletter_subscriptions.php?nl_id={$nl_id}&amp;find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;
{/if}

{tr}Page{/tr}: {$actual_page}/{$cant_pages}

{if $next_offset >= 0}
&nbsp;[<a href="{$smarty.const.NEWSLETTERS_PKG_URL}admin/admin_newsletter_subscriptions.php?nl_id={$nl_id}&amp;find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
{/if}
{if $site_direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
<a href="{$smarty.const.NEWSLETTERS_PKG_URL}admin/admin_newsletter_subscriptions.php?nl_id={$nl_id}&amp;find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>
*}

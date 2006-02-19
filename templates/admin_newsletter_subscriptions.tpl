<div class="floaticon">{bithelp}</div>

<div class="admin newsletters">
	<div class="header">
		<h1>{tr}Admin newsletter subscriptions{/tr}</h1>
	</div>

	<div class="body">
		{form}
			<input type="hidden" name="nl_id" value="{$nl_id|escape}" />

			<div class="row">
				{if $gContent->mInfo.validate_addr eq 'y'}
					{formfeedback warning="Validate Email is enabled, a confirmation email will be sent to every subsciber inviting them to the newsletter. <strong>Any duplicate or previously unsubscribed emails will *NOT* be re-subscribed using this method.</strong>"}
				{else}
					{formfeedback warning="Validate Email is disabled, emails will be subscribed but not validated by the users. <strong>Any duplicate or previously unsubscribed emails will *NOT* be re-subscribed using this method.</strong>"}
				{/if}
			</div>

			<div class="row">
				{formlabel label="New Subscribers" for=""}
				{forminput}
					<textarea cols="50" rows="5" name="new_subscribers" id="new_subscribers"></textarea>
					{formhelp note="Enter multiple email addresses on separate lines to import into the subscriber list"}
				{/forminput}
			</div>

			<div class="row submit">
				{forminput}
					<input type="submit" name="save" value="{tr}Subscribe{/tr}" />
				{/forminput}
			</div>
		{/form}

		{minifind}

		<table class="data">
			<caption>{tr}Subscriptions{/tr}</caption>
			<tr>
				<th>{smartlink ititle="Email" isort=email offset=$offset idefault=1}</th>
				<th>{smartlink ititle="Valid" isort=is_valid offset=$offset idefault=1}</th>
				<th>{smartlink ititle="Subscribed" isort=subscribed_date offset=$offset idefault=1}</th>
				<th>{smartlink ititle="Unsubscribed" isort=unsubscribe_date offset=$offset idefault=1}</th>
				<th>{tr}Action{/tr}</th>
			</tr>
			{foreach from=$subscribers item=sb}
				<tr class="{cycle values='odd,even'}">
					<td>{$sb.email}</td>
					<td>{$sb.is_valid}</td>
					<td>{$sb.subscribed_date|bit_short_datetime}</td>
					<td>{if $sb.unsubscribe_date ne NULL}{$sb.unsubscribe_date|bit_short_datetime}{/if}</td>
					<td><a href="{$smarty.const.NEWSLETTERS_PKG_URL}admin/admin_newsletter_subscriptions.php?remove=1&amp;nl_id={$nl_id}&amp;email={$sb.email}">{tr}remove{/tr}</a></td>
				</tr>
			{foreachelse}
				<tr class="norecords">
					<td colspan="2">{tr}No Records Found{/tr}</td>
				</tr>
			{/foreach}
		</table>
	</div><!-- end .body -->
</div><!-- end .newsletters -->


{* original code
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
{if $direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
<a href="{$smarty.const.NEWSLETTERS_PKG_URL}admin/admin_newsletter_subscriptions.php?nl_id={$nl_id}&amp;find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">
{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>
*}

{if !$newsletters || $gContent->isValid() || $smarty.request.new}
	{include file="bitpackage:newsletters/edit_newsletter.tpl"}
{else}
{strip}
<div class="floaticon">{bithelp}</div>

<div class="edit newsletters">
	<div class="header">
		<h1>{tr}Newsletter Settings{/tr}</h1>
	</div>

	<div class="body">

			{minifind}

			<table class="data">
				<caption>{tr}Newsletters{/tr}</caption>
				<tr>
					<th>{smartlink ititle="Name" isort=name offset=$offset}</th>
					<th>{smartlink ititle="Description" isort=description offset=$offset}</th>
					<th>{smartlink ititle="Created" isort=last_sent offset=$offset}</th>
					<th>{smartlink ititle="Last Sent" isort=created offset=$offset}</th>
					<th>{smartlink ititle="Unsubscribed" isort=users offset=$offset}</th>
					<th>{tr}Actions{/tr}</th>
				</tr>

				{foreach key=nlId from=$newsletters item=nl}
					<tr class="{cycle values='odd,even'}">
						<td><a href="{$smarty.const.NEWSLETTERS_PKG_URL}index.php?nl_id={$nl.nl_id}">{$nl.title}</a></td>
						<td>{$nl.data}</td>
						<td>{$nl.created|bit_short_date}</td>
						<td>{$nl.last_sent|bit_short_date}</td>
						<td><a href="{$smarty.const.NEWSLETTERS_PKG_URL}admin/admin_newsletter_subscriptions.php?nl_id={$nl.nl_id}">{$nl.unsub_count}({$nl.confirmed})</a></td>
						<td style="text-align:right;">
							<a href="{$smarty.const.NEWSLETTERS_PKG_URL}edition_edit.php?nl_id={$nl.nl_id}">{biticon ipackage=liberty iname=new iexplain="New Edition"}</a>
							<a href="{$smarty.const.NEWSLETTERS_PKG_URL}admin/admin_newsletters.php?&amp;nl_id={$nl.nl_id}">{biticon ipackage=liberty iname=edit iexplain=Edit}</a>
							{if $channels[user].individual eq 'y'}({/if}<a href="{$smarty.const.KERNEL_PKG_URL}object_permissions.php?objectName=newsletter%20{$nl.title}&amp;object_type={$smarty.const.BITNEWSLETTER_CONTENT_TYPE_GUID}&amp;permType=newsletters&amp;object_id={$nlId}">{biticon ipackage=liberty iname=permissions iexplain=Permissions}</a>{if $nl.individual eq 'y'}){/if}
							<a href="{$smarty.const.NEWSLETTERS_PKG_URL}admin/admin_newsletters.php?remove=1&amp;nl_id={$nl.nl_id}">{biticon ipackage=liberty iname=delete iexplain=Remove}</a>
						</td>
					</tr>
				{foreachelse}
					<tr class="norecords">
						<td colspan="7">{tr}No Records Found{/tr}</td>
					</tr>
				{/foreach}
			</table>

			{if $gBitUser->hasPermission('bit_p_create_newsletters')}
				<a href="{$smarty.server.php_self}?new=1">Create new newsletter</a>
			{/if}

			{pagination}
	</div><!-- end .body -->
</div><!-- end .___ -->
{/strip}
{/if}

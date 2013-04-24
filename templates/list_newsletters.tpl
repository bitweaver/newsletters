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

			<table class="table data">
				<caption>{tr}Newsletters{/tr}</caption>
				<tr>
					<th>{smartlink ititle="Name" isort=name offset=$offset}</th>
					<th>{smartlink ititle="Description" isort=description offset=$offset}</th>
					<th>{smartlink ititle="Created" isort=last_sent offset=$offset}</th>
					<th>{smartlink ititle="Last Sent" isort=created offset=$offset}</th>
					<th>{smartlink ititle="Unsubscribed" isort=users offset=$offset}</th>
					<th>{tr}Action{/tr}</th>
				</tr>

				{foreach key=nlId from=$newsletters item=nl}
					<tr class="{cycle values='odd,even'}">
						<td><a href="{$smarty.const.NEWSLETTERS_PKG_URL}index.php?nl_id={$nlId}">{$nl.title|default:"Untitled Newsletter"|escape}</a></td>
						<td>{$nl.data}</td>
						<td>{$nl.created|bit_short_date}</td>
						<td>{$nl.last_sent|bit_short_date}</td>
						<td><a href="{$smarty.const.NEWSLETTERS_PKG_URL}admin/admin_newsletter_subscriptions.php?nl_id={$nl.nl_id}"> {$nl.unsub_count|default:0}</a></td>
						<td style="text-align:right;">
							<a href="{$smarty.const.NEWSLETTERS_PKG_URL}edition_edit.php?nl_id={$nl.nl_id}">{booticon iname="icon-file" ipackage="icons" iexplain="New Edition"}</a>
							<a href="{$smarty.const.NEWSLETTERS_PKG_URL}newsletters.php?remove=1&amp;nl_id={$nl.nl_id}">{booticon iname="icon-trash" ipackage="icons" iexplain=Remove}</a>
							<a href="{$smarty.const.NEWSLETTERS_PKG_URL}newsletters.php?&amp;nl_id={$nl.nl_id}">{booticon iname="icon-edit" ipackage="icons" iexplain=Edit}</a>
						</td>
					</tr>
				{foreachelse}
					<tr class="norecords">
						<td colspan="7">{tr}No Records Found{/tr}</td>
					</tr>
				{/foreach}
			</table>

			{if $gBitUser->hasPermission('p_newsletters_create')}
				<a href="{$smarty.server.php_self}?new=1">Create new newsletter</a>
			{/if}

			{pagination}
	</div><!-- end .body -->
</div><!-- end .___ -->
{/strip}
{/if}

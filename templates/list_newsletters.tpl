{strip}
<div class="floaticon">{bithelp}</div>

<div class="edit newsletters">
	<div class="header">
		<h1>{tr}Newsletter Settings{/tr}</h1>
	</div>

	<div class="body">
		{if !$newsletters || $gContent->isValid() || $smarty.request.new}
			{form legend="Create / Edit Newsletters"}
				{if $individual eq 'y'}
					<a href="{$smarty.const.KERNEL_PKG_URL}object_permissions.php?objectName=newsletter%20{$gContent->mInfo.name}&amp;object_type=newsletter&amp;permType=newsletters&amp;object_id={$gContent->mInfo.nl_id}">{tr}There are individual permissions set for this newsletter{/tr}</a><br /><br />
				{/if}
				<input type="hidden" name="nl_id" value="{$gContent->mNlId}" />

				<div class="row">
					{formlabel label="Title" for="title"}
					{forminput}
						<input type="text" name="title" id="title" value="{$gContent->mInfo.title|escape}" />
						{formhelp note="Title of the newsletter."}
					{/forminput}
				</div>

				<div class="row">
					{formlabel label="Description" for="description"}
					{forminput}
						<textarea name="edit" rows="4" cols="40" id="description">{$gContent->mInfo.data|escape}</textarea>
						{formhelp note="Description of the newsletter, that users know what they are getting themselves into."}
					{/forminput}
				</div>

				<div class="row">
					{formlabel label="Users can Subscribe" for="allow_user_sub"}
					{forminput}
						<input type="checkbox" name="allow_user_sub" id="allow_user_sub" {if $gContent->mInfo.allow_user_sub eq 'y'}checked="checked"{/if} />
						{formhelp note="Users can subscribe to this list. Disabling this options means that you have to manually add users to the list."}
					{/forminput}
				</div>

				<div class="row">
					{formlabel label="Any e-mail Address" for="allow_any_sub"}
					{forminput}
						<input type="checkbox" name="allow_any_sub" id="allow_any_sub" {if $gContent->mInfo.allow_any_sub eq 'y'}checked="checked"{/if} />
						{formhelp note="Users may subscribe using any email address."}
					{/forminput}
				</div>

				<div class="row">
					{formlabel label="Append Un/Subscribe Instructions" for="unsub_msg"}
					{forminput}
						<input type="checkbox" name="unsub_msg" id="unsub_msg" {if $gContent->mInfo.unsub_msg eq 'y'}checked="checked"{/if} />
						{formhelp note="Append instructions on how to subscribe / unsubscribe to ever outgoing newsletter. This is only useful when users can un / subscribe to the list themselves."}
					{/forminput}
				</div>

				<div class="row">
					{formlabel label="Validate e-mail Addresses" for="validate_addr"}
					{forminput}
						<input type="checkbox" name="validate_addr" id="validate_addr" {if $gContent->mInfo.validate_addr eq 'y'}checked="checked"{/if} />
						{formhelp note="Validate all email addresses before they are added to the list. This might result in members not being added despite working email addresses."}
					{/forminput}
				</div>

				<div class="row submit">
					<input type="submit" name="cancel" value="{tr}Cancel{/tr}" />
					<input type="submit" name="save" value="{tr}Save{/tr}" />
				</div>
			{/form}

		{else}

			{minifind}

			<table class="data">
				<caption>{tr}Newsletters{/tr}</caption>
				<tr>
					<th>{smartlink ititle="Name" isort=name offset=$offset}</th>
					<th>{smartlink ititle="Description" isort=description offset=$offset}</th>
					<th>{smartlink ititle="Created" isort=last_sent offset=$offset}</th>
					<th>{smartlink ititle="Last Sent" isort=created offset=$offset}</th>
					<th>{smartlink ititle="Users Unsubscribed" isort=users offset=$offset}</th>
					<th>{tr}Action{/tr}</th>
				</tr>

				{foreach key=nlId from=$newsletters item=nl}
					<tr class="{cycle values='odd,even'}">
						<td><a href="{$smarty.const.NEWSLETTERS_PKG_URL}index.php?nl_id={$nlId}">{$nl.title}</a></td>
						<td>{$nl.data}</td>
						<td>{$nl.created|bit_short_date}</td>
						<td>{$nl.last_sent|bit_short_date}</td>
						<td style="text-align:right;"><a href="{$smarty.const.NEWSLETTERS_PKG_URL}admin/admin_newsletter_subscriptions.php?nl_id={$nlId}">{$nl.users|default:0} [ {$channels[user].confirmed|default:0} ]</a></td>
						<td style="text-align:right;">
							<a href="{$smarty.const.NEWSLETTERS_PKG_URL}edition_edit.php?nl_id={$nlId}">{biticon ipackage=liberty iname=new iexplain="New Edition"}</a>
							<a href="{$smarty.const.NEWSLETTERS_PKG_URL}newsletters.php?remove=1&amp;nl_id={$nlId}">{biticon ipackage=liberty iname=delete iexplain=Remove}</a>
							<a href="{$smarty.const.NEWSLETTERS_PKG_URL}newsletters.php?&amp;nl_id={$nlId}">{biticon ipackage=liberty iname=edit iexplain=Edit}</a>
							{if $channels[user].individual eq 'y'}({/if}<a href="{$smarty.const.KERNEL_PKG_URL}object_permissions.php?objectName=newsletter%20{$nl.title}&amp;object_type={$smarty.const.BITNEWSLETTER_CONTENT_TYPE_GUID}&amp;permType=newsletters&amp;object_id={$nlId}">{biticon ipackage=liberty iname=permissions iexplain=Permissions}</a>{if $nl.individual eq 'y'}){/if}
						</td>
					</tr>
				{foreachelse}
					<tr class="norecords">
						<td colspan="7">{tr}No Records Found{/tr}</td>
					</tr>
				{/foreach}
			</table>

			<a href="{$smarty.server.php_self}?new=1">Create new newsletter</a>

			{pagination}
		{/if}
	</div><!-- end .body -->
</div><!-- end .___ -->
{/strip}

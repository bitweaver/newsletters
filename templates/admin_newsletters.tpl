{strip}
<div class="floaticon">{bithelp}</div>

<<<<<<< admin_newsletters.tpl
<div class="edit ___">
	<div class="header">
		<h1>{tr}Netsletter Settings{/tr}</h1>
	</div>

	<div class="body">
		{if $individual eq 'y'}
			<a href="{$smarty.const.KERNEL_PKG_URL}object_permissions.php?objectName=newsletter%20{$gContent->mInfo.name}&amp;object_type=newsletter&amp;permType=newsletters&amp;object_id={$gContent->mInfo.nl_id}">{tr}There are individual permissions set for this newsletter{/tr}</a><br /><br />
		{/if}
		{form legend="Create / Edit Newsletters"}
			maybe we could have an option to autosubscribe users to a list when they register with the site.
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
				<input type="submit" name="save" value="{tr}Save{/tr}" />
			</div>
		{/form}

		{minifind}

		<table class="data">
			<caption>{tr}Newsletters{/tr}</caption>
			<tr>
				<th>{smartlink ititle="Name" isort=name offset=$offset}</th>
				<th>{smartlink ititle="Description" isort=description offset=$offset}</th>
				<th>{smartlink ititle="Created" isort=last_sent offset=$offset}</th>
				<th>{smartlink ititle="Last Sent" isort=created offset=$offset}</th>
				<th>{smartlink ititle="Users [ Confirmed ]" isort=users offset=$offset}</th>
				<th>{smartlink ititle="Editions" isort=editions offset=$offset}</th>
				<th>{tr}Action{/tr}</th>
			</tr>

			{foreach key=nlId from=$newsletters item=nl}
				<tr class="{cycle values='odd,even'}">
					<td>{$nl.title}</td>
					<td>{$nl.data}</td>
					<td>{$nl.created|bit_short_date}</td>
					<td>{$nl.last_sent|bit_short_date}</td>
					<td style="text-align:right;"><a href="{$smarty.const.NEWSLETTERS_PKG_URL}admin/admin_newsletter_subscriptions.php?nl_id={$nlId}">{$nl.users|default:0} [ {$channels[user].confirmed|default:0} ]</a></td>
					<td style="text-align:right;">{$nl.editions|default:0}</td>
					<td style="text-align:right;">
						<a href="{$smarty.const.NEWSLETTERS_PKG_URL}admin/index.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove={$nlId}">{biticon ipackage=liberty iname=delete iexplain=Remove}</a>
						<a href="{$smarty.const.NEWSLETTERS_PKG_URL}admin/index.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;nl_id={$nlId}">{biticon ipackage=liberty iname=edit iexplain=Edit}</a>
						{if $channels[user].individual eq 'y'}({/if}<a href="{$smarty.const.KERNEL_PKG_URL}object_permissions.php?objectName=newsletter%20{$nl.title}&amp;object_type={$smarty.const.BITNEWSLETTER_CONTENT_TYPE_GUID}&amp;permType=newsletters&amp;object_id={$nlId}">{biticon ipackage=liberty iname=permissions iexplain=Permissions}</a>{if $nl.individual eq 'y'}){/if}
					</td>
				</tr>
			{foreachelse}
				<tr class="norecords">
					<td colspan="7">{tr}No Records Found{/tr}</td>
				</tr>
			{/foreach}
		</table>

		{pagination}
	</div><!-- end .body -->
</div><!-- end .___ -->
{/strip}
=======
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
<a href="{$smarty.const.KERNEL_PKG_URL}object_permissions.php?objectName=newsletter%20{$gContent->mInfo.name}&amp;object_type=newsletter&amp;permType=newsletters&amp;object_id={$gContent->mInfo.nl_id}">{tr}There are individual permissions set for this newsletter{/tr}</a><br /><br />
{/if}

<form action="{$smarty.const.NEWSLETTERS_PKG_URL}admin/index.php" method="post">
<input type="hidden" name="nl_id" value="{$gContent->mInfo.nl_id|escape}" />
<table class="panel">
<tr><td>{tr}Name{/tr}:</td><td><input type="text" name="title" value="{$gContent->mInfo.title|escape}" /></td></tr>
<tr><td>{tr}Description{/tr}:</td><td><textarea name="edit" rows="4" cols="40">{$gContent->mInfo.data|escape:html}</textarea></td></tr>
<tr><td>{tr}Users can subscribe/unsubscribe to this list{/tr}</td><td>
<input type="checkbox" name="allow_user_sub" {if $gContent->mInfo.allow_user_sub eq 'y'}checked="checked"{/if} /></td></tr>
<tr><td>{tr}Users can subscribe any email address{/tr}</td><td>
<input type="checkbox" name="allow_any_sub" {if $gContent->mInfo.allow_any_sub eq 'y'}checked="checked"{/if} /></td></tr>
<tr><td>{tr}Add unsubscribe instructions to each newsletter{/tr}</td><td>
<input type="checkbox" name="unsub_msg" {if $gContent->mInfo.unsub_msg eq 'y'}checked="checked"{/if} /></td></tr>
<tr><td>{tr}Validate email addresses{/tr}</td><td>
<input type="checkbox" name="validate_addr" {if $gContent->mInfo.validate_addr eq 'y'}checked="checked"{/if} /></td></tr>
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
{foreach key=nlId from=$newsletters item=nl}
<tr class="{cycle}">
<td>{$nlIid}</td>
<td>{$nl.title}</td>
<td>{$nl.data}</td>
<td>{$nl.users} ({$channels[user].confirmed})</td>
<td>{$nl.editions}</td>
<td>{$nl.last_sent|bit_short_datetime}</td>
<td>
   <a href="{$smarty.const.NEWSLETTERS_PKG_URL}admin/index.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;remove=1&amp;nl_id={$nlId}">{tr}remove{/tr}</a>
   <a href="{$smarty.const.NEWSLETTERS_PKG_URL}admin/index.php?offset={$offset}&amp;sort_mode={$sort_mode}&amp;nl_id={$nlId}">{tr}edit{/tr}</a>
   <a href="{$smarty.const.NEWSLETTERS_PKG_URL}admin/admin_newsletter_subscriptions.php?nl_id={$nlId}">{tr}subscriptions{/tr}</a>
   {if $channels[user].individual eq 'y'}({/if}<a href="{$smarty.const.KERNEL_PKG_URL}object_permissions.php?objectName=newsletter%20{$nl.title}&amp;object_type={$smarty.const.BITNEWSLETTER_CONTENT_TYPE_GUID}&amp;permType=newsletters&amp;object_id={$nlId}">{tr}perms{/tr}</a>{if $nl.individual eq 'y'}){/if}
</td>
</tr>
{/foreach}
</table>

{pagination}
>>>>>>> 1.4

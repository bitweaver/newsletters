{if $subInfo || $gBitUser->hasPermission('p_newsletters_subscribe')}
{strip}
<div class="display newsletters">
	<div class="header">
		<h1>{tr}Mailing Subscriptions{/tr}</h1>
	</div>

	<div class="body">
	{formfeedback hash=$feedback}
	{form enctype="multipart/form-data" id="editpageform"}
		<input type="hidden" name="response_content_id" value="{$subInfo.content_id}" />
{if $smarty.request.c}
		<input type="hidden" name="c" value="{$smarty.request.c}" />
		<div class="control-group">
<em>
			{formlabel label="In Response To"}
			{forminput}
				{tr}{$subInfo.content_name}{/tr}: {$subInfo.title|escape}
			{/forminput}
</em>
		</div>
{/if}
		<div class="control-group">
			{formlabel label="User Information"}
			{forminput}
				{displayname hash=$subInfo}<br/>
				{$subInfo.email}
			{/forminput}
		</div>
		<div class="control-group">
			{formlabel label="Subscriptions"}
			{forminput}
				{foreach from=$newsletters key=nlId item=nl}
					{if $nl.allow_user_sub}<input type="checkbox" name="nl_content_id[]" value="{$nlId}" {if !$unsubs.$nlId && !$subInfo.unsubscribe_all}checked="checked"{/if}/>{/if} <a href="{$nl.display_url}"/>{$nl.title|escape}</a> <br/>
				{foreachelse}
					{tr}No newsletters were found{/tr}
				{/foreach}
			{/forminput}
		</div>
		<div class="control-group">
			{formlabel label="Permanent Unsubscribe"}
			{forminput}
				<input type="checkbox" name="unsubscribe_all" value="y" {if $subInfo.unsubscribe_all}checked="checked"{/if} /> {tr}Remove myself from all lists, and receive no further mailings from{/tr} {$gBitSystem->getConfig('siteTitle','this site')}.</br>
			{/forminput}
		</div>
		<div class="control-group submit">
			<input type="submit" class="btn btn-default" name="update" value="{tr}Update Subscriptions{/tr}" />
		</div>
	{/form}
	</div>	<!-- end .body -->
</div>	<!-- end .newsletters -->
{/strip}
{else}
	{include file="bitpackage:newsletters/list_newsletters.tpl"}
{/if}

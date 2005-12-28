{if $subInfo || $gBitUser->hasPermission('bit_p_subscribe_newsletters')}
{strip}
<div class="display newsletters">
	<div class="header">
		<h1>{tr}Mailing Subscriptions{/tr}</h1>
	</div>

	<div class="body">
	{formfeedback success=$success error=$gContent->mErrors}
	{form enctype="multipart/form-data" id="editpageform"}
		<input type="hidden" name="response_content_id" value="{$subInfo.content_id}" />
{if $smarty.request.sub}
		<input type="hidden" name="sub" value="{$smarty.request.sub}" />
		<div class="row">
<em>
			{formlabel label="In Response To"}
			{forminput}
				{tr}{$subInfo.content_description}{/tr}: {$subInfo.title}
			{/forminput}
</em>
		</div>
{/if}
		<div class="row">
			{formlabel label="User Information"}
			{forminput}
				{displayname hash=$subInfo}<br/>
				{$subInfo.email}
			{/forminput}
		</div>
		<div class="row">
			{formlabel label="Subscriptions"}
			{forminput}
				{foreach from=$newsletters key=nlId item=nl}
					<input type="checkbox" name="nl_content_id[]" value="{$nlId}" {if !$unsubs.$nlId}checked="checked"{/if}/> <a href="{$nl.display_url}"/>{$nl.title}</a> <br/>
				{foreachelse}
					{tr}No newsletters were found{/tr}
				{/foreach}
			{/forminput}
		</div>
		<div class="row">
			{formlabel label="Permanent Unsubscribe"}
			{forminput}
				<input type="checkbox" name="unsubscribe_all" value="y" {if $subInfo.unsubscribe_all}checked="checked"{/if} /> {tr}Remove myself from all lists, and receive no further mailings from{/tr} {$gBitSystem->getPreference('siteTitle','this site')}.</br>
			{/forminput}
		</div>
		<div class="row submit">
			<input type="submit" name="update" value="{tr}Update Subscriptions{/tr}" />
		</div>
	{/form}
	</div>	<!-- end .body -->
</div>	<!-- end .newsletters -->
{/strip}
{else}
	{include file="bitpackage:newsletters/list_newsletters.tpl"}
{/if}
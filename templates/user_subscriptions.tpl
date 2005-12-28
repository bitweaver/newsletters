{strip}
<div class="display newsletters">
	<div class="header">
		<h1>{tr}Mailing Subscriptions{/tr}</h1>
	</div>

	<div class="body">
	{if $subInfo}
	{formfeedback success=$success error=$gContent->mErrors}
	{form enctype="multipart/form-data" id="editpageform"}
		<input type="hidden" name="unsub_content_id" value="{$subInfo.content_id}" />
		<div class="row">
<em>
			{formlabel label="In Response To"}
			{forminput}
				{tr}{$subInfo.content_description}{/tr}: {$subInfo.title}
			{/forminput}
</em>
		</div>
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
					<input type="checkbox" name="nl_content_id[]" value="{$nlId}" {if !$unsubs.$nlId}checked="checked"{/if}/> {$nl.title} <br/>
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
	{else}
		<div class="row">
			{tr}The subscription URL is no longer valid.{/tr}
			{if $gBitUser->isRegistered()}

			{else}
				{include file="bitpackage:users/login_inc.tpl"}
			{/if}
	{/if}
	</div>	<!-- end .body -->
</div>	<!-- end .newsletters -->
{/strip}

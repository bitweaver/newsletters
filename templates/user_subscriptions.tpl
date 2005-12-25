{strip}
<div class="display newsletters">
	<div class="header">
		<h1>{tr}Mailing Subscriptions{/tr}</h1>
	</div>

	<div class="body">
	{if $subInfo}
		<div class="row">
			{formlabel label="Subscriptions"}
			{forminput}
				<input type="checkbox" name="nl_content_id" value="{$subInfo.nl_content_id}" /> {$subInfo.title} </br>
			{/forminput}
		</div>
		<div class="row">
			{formlabel label="Permanent Unsubscribe"}
			{forminput}
				<input type="checkbox" name="unsubscribe_all" value="{$subInfo.nl_content_id}" {if subInfo.unsubscribe_all}{/if} /> {tr}Remove myself from all lists, and receive no further mailings from{/tr} {$gBitSystem->getPreference('siteTitle','this site')}.</br>
			{/forminput}
		</div>
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

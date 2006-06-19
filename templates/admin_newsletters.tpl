{strip}
{form legend="Newsletter Features"}
	<input type="hidden" name="page" value="{$page}" />

	{if !$gBitSystem->getConfig( 'kernel_server_name' )}
		{formfeedback error="Server name is not defined!"}
		{tr}You must <a href="{$smarty.const.KERNEL_PKG_URL}admin/index.php?page=server">define the server name</a> for proper background operation of the mail script.{/tr}
	{/if}

	{foreach from=$formNewsletterFeatures key=item item=output}
		<div class="row">
			{formlabel label=`$output.label` for=$item}
			{forminput}
				<input type="text" name="{$item}" value="{$gBitSystem->getConfig($item,$output.default)}" id="{$item}" />
				{formhelp note=`$output.note` page=`$output.page`}
			{/forminput}
		</div>
	{/foreach}

	<div class="row submit">
		<input type="submit" name="featuresTabSubmit" value="{tr}Change preferences{/tr}" />
	</div>
{/form}
{/strip}

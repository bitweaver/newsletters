{strip}
{form}
	{legend legend="Newsletter Mail Server Settings"}
		<input type="hidden" name="page" value="{$page}" />
		{foreach from=$formNewsletterFeatures key=item item=output}
			<div class="form-group">
				{formlabel label=$output.label for=$item}
				{forminput}
					<input type="text" name="{$item|escape}" value="{$gBitSystem->getConfig($item,$output.default)|escape}"/>
					{formhelp note=$output.note page=$output.page}
				{/forminput}
			</div>
		{/foreach}

		<div class="form-group submit">
			<input type="submit" class="btn btn-default" name="apply" value="{tr}Change preferences{/tr}" />
		</div>
	{/legend}
{/form}
{/strip}

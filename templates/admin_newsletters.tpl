{strip}
{form}
	{jstabs}
		{jstab title="Newsletter Features"}
			{legend legend="Newsletter Features"}
				<input type="hidden" name="page" value="{$page}" />

				{foreach from=$formNewsletterFeatures key=item item=output}
					<div class="row">
						{formlabel label=`$output.label` for=$item}
						{forminput}
							<input type="text" name="{$item}" value="{$gBitSystem->getPreference($item,$output.default)}" id=$item />
							{formhelp note=`$output.note` page=`$output.page`}
						{/forminput}
					</div>
				{/foreach}

				<div class="row submit">
					<input type="submit" name="featuresTabSubmit" value="{tr}Change preferences{/tr}" />
				</div>
			{/legend}
		{/jstab}

	{/jstabs}
{/form}

{/strip}

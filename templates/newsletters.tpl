{strip}
<div class="display newsletters">
	<div class="header">
		<h1>{tr}Newsletters{/tr}</h1>
	</div>

	<div class="body">
		{formfeedback hash=$feedback}

		{if $gContent->isValid()}
			{if $confirm eq 'y'}
				{formfeedback success="Subscription Confirmed!"}
			{/if}
					<div class="row">
						{formlabel label="Name" for=""}
						{forminput}
							{$gContent->getTitle()}
						{/forminput}
					</div>

					<div class="row">
						{formlabel label="Description" for=""}
						{forminput}
							{$gContent->parseData()}
						{/forminput}
					</div>

				{if ($gContent->mInfo.allow_user_sub eq 'y') or $gBitUser->hasPermission( 'p_newsletters_subscribe' )}
					{form}
						<input type="hidden" name="nl_id" value="{$gContent->mNewsletterId}" />
						<div class="row">
							{formlabel label="Email" for=""}
							{forminput}
							{if $gBitUser->hasPermission( 'p_newsletters_subscribe_email' )}
								<input type="text" name="email" value="{$email|escape}" />
							{else}
								<input type="hidden" name="email" value="{$email|escape}" />
								{$email|escape}
							{/if}
							{/forminput}
						</div>
						<div class="row submit">
							{forminput}
								<input type="submit" name="cancel" value="{tr}Cancel{/tr}" />
								<input type="submit" name="subscribe" value="{tr}Subscribe{/tr}" />
							{/forminput}
						</div>
					{/form}
				{/if}
		{else}

			{minifind}

			<table class="data">
				<caption>{tr}Newsletters{/tr}</caption>
				<tr>
					<th>{smartlink ititle="Name" isort=name offset=$offset idefault=1}</th>
					<th>{smartlink ititle="Description" isort=descritpion offset=$offset}</th>
				</tr>
				{foreach from=$newsletters item=nl key=nlId}
					{if $newsletters.individual ne 'y' or $newsletters.individual_bit_p_subscribe_newsletters eq 'y'}
						<tr class="{cycle values='odd,even'}">
							<td><a href="{$smarty.const.NEWSLETTERS_PKG_URL}index.php?nl_id={$nl.nl_id}">{$nl.title|escape}</a></td>
							<td>{$nl.data}</td>
						</tr>
					{/if}
				{foreachelse}
					<tr class="norecords">
						<td colspan="2">{tr}No Records Found{/tr}</td>
					</tr>
				{/foreach}
			</table>
		{/if}

		{* haven't dealt with pagination yet *}
		{pagination}
	</div><!-- end .body -->
</div><!-- end .newsletters -->
{/strip}

{strip}
<div class="display newsletters">
	<div class="header">
		<h1>{tr}Newsletters{/tr}</h1>
	</div>

	<div class="body">
		{if $confirm eq 'y'}
			{formfeedback success="Subscription Confirmed!"}
		{/if}
		{formfeedback hash=$feedback}
		{if $subscribe eq 'y'}
			<h2>{tr}Subscribe to Newsletter{/tr}</h2>
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
			{if ($gContent->getField('allow_user_sub') eq 'y') or $gBitUser->hasPermission( 'p_newsletters_subscribe' )}
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
							<input type="submit" name="subscribe" value="{tr}Subscribe{/tr}" />
							<input type="submit" name="unsubscribe" value="{tr}Unsubscribe{/tr}" />
						{/forminput}
					</div>
				{/form}
			{/if}
		{/if}

		{minifind}

		<table class="data">
			<caption>{tr}Newsletters{/tr}</caption>
			<tr>
				<th>{smartlink ititle="Name" isort=name offset=$offset idefault=1}</th>
				<th>{smartlink ititle="Description" isort=descritpion offset=$offset idefault=1}</th>
				<th>{tr}Subscribe{/tr}</th>
				<th>{tr}Editions{/tr}</th>
			</tr>
			{foreach from=$newsletters item=nl}
				{if $newsletters.individual ne 'y' or $newsletters.individual_bit_p_subscribe_newsletters eq 'y'}
					<tr class="{cycle values='odd,even'}">
						<td>{$nl.title|escape}</td>
						<td>{$nl.data}</td>
						<td><a href="{$smarty.const.NEWSLETTERS_PKG_URL}index.php?nl_id={$nl.nl_id}&amp;info=1">{tr}Subscribe{/tr}</a></td>
						<td><a href="{$smarty.const.NEWSLETTERS_PKG_URL}edition.php?nl_id={$nl.nl_id}">{tr}Editions{/tr}</a></td>
					</tr>
				{/if}
			{foreachelse}
				<tr class="norecords">
					<td colspan="2">{tr}No Records Found{/tr}</td>
				</tr>
			{/foreach}
		</table>

		{* haven't dealt with pagination yet *}
		{pagination}
	</div><!-- end .body -->
</div><!-- end .newsletters -->
{/strip}

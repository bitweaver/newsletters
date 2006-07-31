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
							{$email|escape}
						{/if}
						{/forminput}
					</div>
					<div class="row submit">
						{forminput}
							<input type="submit" name="cancel" value="{tr}Cancel{/tr}" />
							{if $gContent->getField( 'subscribed_date' )}
								<input type="submit" name="unsubscribe" value="{tr}Unsubscribe{/tr}" />
							{else}
								<input type="submit" name="subscribe" value="{tr}Subscribe{/tr}" />
							{/if}
						{/forminput}
					</div>
				{/form}
			{/if}
		{else}

		{minifind}

		<ul class="data">
{*
			<caption>{tr}Newsletters{/tr}</caption>
			<tr>
				<th>{smartlink ititle="Name" isort=name offset=$offset idefault=1}</th>
				<th>{smartlink ititle="Description" isort=descritpion offset=$offset idefault=1}</th>
				<th>{tr}Subscribe{/tr}</th>
				<th>{tr}Editions{/tr}</th>
			</tr>
*}
			{foreach from=$newsletters item=nl key=nlId}
				{if $newsletters.individual ne 'y' or $newsletters.individual_bit_p_subscribe_newsletters eq 'y'}
					<li class="item {cycle values='odd,even'}">
						<div class="floaticon">
						{if $subs.$nlId}
							<strong>{biticon ipackage=liberty iname=success}{tr}Subscribed{/tr}: {$subs.$nlId.subscribed_date|bit_short_date}</strong><br/>
							<a href="{$smarty.const.NEWSLETTERS_PKG_URL}index.php?nl_id={$nl.nl_id}&amp;info=1">{tr}Unsubscribe{/tr}</a>
						{else}
							<a href="{$smarty.const.NEWSLETTERS_PKG_URL}index.php?nl_id={$nl.nl_id}&amp;info=1">{tr}Subscribe{/tr}</a>
						{/if}
						</div>
						<h1>{$nl.title|escape}</h1>
						{$nl.data}
						<div><a href="{$smarty.const.NEWSLETTERS_PKG_URL}edition.php?nl_id={$nl.nl_id}">{tr}Editions{/tr}</a></div>
					</li>
				{/if}
			{foreachelse}
				<li class="norecords">
					{tr}No Records Found{/tr}
				</li>
			{/foreach}
		</ul>

		{* haven't dealt with pagination yet *}
		{pagination}
		{/if}
	</div><!-- end .body -->
</div><!-- end .newsletters -->
{/strip}

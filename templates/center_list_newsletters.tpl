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

		{if count($newsletters) > $gBitSystem->getConfig('max_records',10)}
			{minifind}
		{/if}

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
				{if $newsletters.individual ne 'y' or $newsletters.individual_p_subscribe_newsletters eq 'y'}
					<li class="item {cycle values='odd,even'}">
						<div class="floaticon">
						{if $subs.$nlId.unsubscribe_all || $subs.$nlId.unsubscribe_date}
							<strong>{biticon ipackage="icons" iname="dialog-cancel" iexplain="Success" iforce="icon"}{tr}Unsubscribed{/tr}: {$subs.$nlId.unsubscribe_date|bit_short_date}</strong><br/>
							<a href="{$smarty.const.NEWSLETTERS_PKG_URL}sub.php?nl_id={$nl.nl_id}&amp;sub=1">{tr}Subscribe{/tr}</a>
						{elseif $subs.$nlId}
							<strong>{biticon ipackage="icons" iname="dialog-ok" iexplain="Success" iforce="icon"}{tr}Subscribed{/tr}: {$subs.$nlId.subscribed_date|bit_short_date}</strong><br/>
							<a href="{$smarty.const.NEWSLETTERS_PKG_URL}sub.php?nl_id={$nl.nl_id}&amp;unsub=1">{tr}Unsubscribe{/tr}</a>
						{else}
							<a href="{$smarty.const.NEWSLETTERS_PKG_URL}sub.php?nl_id={$nl.nl_id}&amp;sub=1">{tr}Subscribe{/tr}</a>
						{/if}
						{if $gBitUser->hasPermission('p_newsletters_create')}
							<a href="{$smarty.const.NEWSLETTERS_PKG_URL}edition_edit.php?nl_id={$nl.nl_id}">{biticon ipackage="icons" iname="document-new" iexplain="New Edition"}</a>
							<a href="{$smarty.const.NEWSLETTERS_PKG_URL}newsletters.php?&amp;nl_id={$nl.nl_id}">{biticon ipackage="icons" iname="accessories-text-editor" iexplain=Edit}</a>
							{if $channels[user].individual eq 'y'}({/if}<a href="{$smarty.const.KERNEL_PKG_URL}object_permissions.php?objectName=newsletter%20{$nl.title|escape}&amp;object_type={$smarty.const.BITNEWSLETTER_CONTENT_TYPE_GUID}&amp;permType=newsletters&amp;object_id={$nlId}">{biticon ipackage="icons" iname="emblem-shared" iexplain=Permissions}</a>{if $nl.individual eq 'y'}){/if}
							<a href="{$smarty.const.NEWSLETTERS_PKG_URL}newsletters.php?remove=1&amp;nl_id={$nl.nl_id}">{biticon ipackage="icons" iname="edit-delete" iexplain=Remove}</a>
						{/if}
						</div>
						<h1>{$nl.title|escape}</h1>
						{$nl.data}
						<ul class="data">
						{foreach from=$nl.editions item=ed key=edId}
							<li class="item"><h2><a href="{$ed.display_url}">{$ed.title}</a></h2><span class="date">{$ed.event_time|bit_short_date}</span></li>
						{foreachelse}
							<li class="norecords">{tr}No editions.{/tr}</li>
						{/foreach}
						</ul>
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
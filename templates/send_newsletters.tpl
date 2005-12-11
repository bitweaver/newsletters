{strip}
<div class="floaticon">{bithelp}</div>

<div class="edit newsletters">
	<div class="header">
		<h1>{tr}Send Newsletter{/tr}{if $gContent->isValid()}: {$gContent->getTitle()}{/if}</h1>
	</div>

	<div class="body">

{if $gContent->isValid()}
{form}
	<input type="hidden" name="edition_id" value="{$gContent->mEditionId|escape}" />

	{if $smarty.request.emited eq 'y'}
		{tr}The newsletter was sent to {$sent} email addresses{/tr}<br /><br />
	{elseif $smarty.request.preview}

		<div class="row submit">
			{forminput}
				<input type="submit" name="cancel" value="{tr}Cancel{/tr}" />
				<input type="submit" name="send" value="{tr}send{/tr}" />
			{/forminput}
		</div>
		{jstabs}
			{jstab title="Preview Newsletter"}
				{legend legend="Preview Newsletter"}
					{include file="bitpackage:newsletters/view_edition.tpl"}
				{/legend}
			{/jstab}
			{jstab title="Preview Recipient List"}
				{legend legend="Preview Recipient List"}
				<div class="row">
					{foreach from=$smarty.request.send_group item=groupId key=i}
						<input type="hidden" name="send_group[]" value="{$groupId}" />
					{/foreach}
					{formlabel label="Actual Recipients"}
					{forminput}
					<ol>
					{foreach from=$recipientList item=recipient key=email}
						<li>"{$recipient.login}" <{$email}></li>
					{/foreach}
					</ol>
					{/forminput}
				</div>
				{/legend}
			{/jstab}
		{/jstabs}

		</div>
	{else}
		<div class="row">
			{formlabel label="Groups"}
			{forminput}
			{tr}This newsletter will be sent to members of the checked groups below:{/tr}
			<ul class="data">
			{foreach from=$groupList item=group key=groupId }
				<li class="item"><input type="checkbox" name="send_group[]" value="{$groupId}" />{$group.group_name}</li>
			{/foreach}
			</ul>
			{/forminput}
		</div>
		<div class="row submit">
			{forminput}
				<input type="submit" name="preview" value="{tr}Preview{/tr}" />
			{/forminput}
		</div>
	{/if}
{/form}

{else}

<h2>{tr}Sent editions{/tr}</h2>

{include file="bitpackage:newsletters/list_editions_inc.tpl"}

{pagination}

{minifind}

{/if}


	</div>
</div>
{/strip}
{strip}
<div class="floaticon">{bithelp}</div>

<div class="edit newsletters">
	<div class="header">
		<h1>{tr}Send Newsletter{/tr}{if $gContent->isValid()}: {$gContent->getTitle()}{/if}</h1>
	</div>

	<div class="body">
		{if $feedback}
			<div class="row">
				{formfeedback hash=$feedback}
			</div>
		{/if}

		{if $gContent->isValid()}
			{form}
				<input type="hidden" name="edition_id" value="{$gContent->mEditionId|escape}" />

				{if $smarty.request.emited eq 'y'}
					{tr}The newsletter was sent to {$sent} email addresses{/tr}
				{elseif $smarty.request.preview}
					<input type="hidden" name="validated" value="{$validated}" />
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
												<li>{$recipient.login} &nbsp; &lt;{$email}&gt;</li>
											{/foreach}
										</ol>
									{/forminput}
								</div>
							{/legend}
						{/jstab}
					{/jstabs}

					<div class="row submit">
						<input type="submit" name="cancel" value="{tr}Cancel{/tr}" />
						<input type="submit" name="send" value="{tr}Send{/tr}" />
					</div>
				{else}
					{legend legend="Recipient Groups"}
						<div class="row">
							{formlabel label="Groups"}
							{forminput}
								{foreach from=$groupList item=group key=groupId }
									<label><input type="checkbox" name="send_group[]" value="{$groupId}" /> {$group.group_name}</label><br />
								{/foreach}
								{formhelp note="This newsletter will be sent to members of the checked groups."}
							{/forminput}
						</div>

						<div class="row">
							{formlabel label="Only send to validated emails"}
							{forminput}
								<input type="checkbox" name="validated" "checked" />
							{/forminput}
						</div>

						<div class="row submit">
							<input type="submit" name="preview" value="{tr}Preview{/tr}" />
						</div>
					{/legend}
				{/if}
			{/form}
		{else}
			{minifind}
			<h2>{tr}Sent editions{/tr}</h2>
			{include file="bitpackage:newsletters/list_editions_inc.tpl"}
			{pagination}
		{/if}
	</div>
</div>
{/strip}

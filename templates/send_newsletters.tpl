{strip}
<div class="floaticon">{bithelp}</div>

<div class="edit newsletters">
	<div class="header">
		<h1>{tr}Send Newsletter{/tr}{if $gContent->isValid()}: {$gContent->getTitle()}{/if}</h1>
	</div>

	<div class="body">
		{if $feedback}
			<div class="control-group">
				{formfeedback hash=$feedback}
			</div>
		{/if}

		{if $gContent->isValid()}
			{form}
				<input type="hidden" name="edition_id" value="{$gContent->mEditionId|escape}" />

				{if $smarty.request.emited eq 'y'}
					{tr}The newsletter was sent to {$sent} email addresses{/tr}
				{elseif $smarty.request.preview}
					<input type="hidden" name="validated" value="{$smarty.request.validated}" />
					<input type="hidden" name="test_mode" value="{$smarty.request.test_mode}" />
					{jstabs}
						{jstab title="Preview Newsletter"}
							{legend legend="Preview Newsletter"}
								{include file="bitpackage:newsletters/view_edition.tpl"}
							{/legend}
						{/jstab}

						{jstab title="Preview Recipient List"}
							{legend legend="Preview Recipient List"}
								<div class="control-group">
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

					<div class="control-group submit">
						<input type="submit" class="btn" name="cancel" value="{tr}Cancel{/tr}" />
						<input type="submit" class="btn" name="send" value="{tr}Send{/tr}" />
					</div>
				{else}
					{legend legend="Recipient Groups"}
						<div class="control-group">
							{formlabel label="Groups"}
							{forminput}
								{foreach from=$groupList item=group key=groupId }
									<label><input type="checkbox" name="send_group[]" value="{$groupId}" /> {$group.group_name}</label><br />
								{/foreach}
								{formhelp note="This newsletter will be sent to members of the checked groups."}
							{/forminput}
						</div>

						<div class="control-group">
							<label class="checkbox">
								<input type="checkbox" name="test_mode" />Send Test
								{formhelp note="This will enable you to send the newsletter to the same recipients again."}
							</label>
						</div>
{*
						<div class="control-group">
							{formlabel label="Only send to validated emails"}
							{forminput}
								<input type="checkbox" name="validated" checked="checked" />
							{/forminput}
						</div>
*}
						<div class="control-group submit">
							<input type="submit" class="btn" name="preview" value="{tr}Preview{/tr}" />
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
	</div><!-- end .body -->
</div>
{/strip}

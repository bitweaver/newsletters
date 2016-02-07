<div class="floaticon">{bithelp}</div>

<div class="edit newsletters">
	<div class="header">
		<h1>{if $gContent->isValid()}{tr}Edit Edition{/tr}: {$gContent->getTitle()}{else}{tr}Create New Edition{/tr}{/if}</h1>
	</div>

	{if $preview eq 'y'}
		<div class="preview">
			<div class="header">
				<h1>{$info.subject}</h1>
			</div>

			<div class="body">{$parsed}</div>
		</div>
	{/if}

	<div class="body">
		{formfeedback success=$success error=$gContent->mErrors}
		{form enctype="multipart/form-data" id="editpageform"}
			<input type="hidden" name="edition_id" value="{$gContent->mEditionId}" />
			{jstabs}
				{jstab title="Edition Body"}
					{legend legend="Edition Body"}
						<div class="form-group">
							{formlabel label="Newsletter" for="nl_content_id"}
							{forminput}
								<select name="nl_content_id" id="nl_content_id">
									{foreach from=$newsletters item=nl key=nlConId}
										<option value="{$nlConId}" {if $nl.con_id eq $nl_id}selected="selected"{/if}>{$nl.title|escape}</option>
									{/foreach}
								</select>
								{formhelp note="Pick the newsletter you want to post to."}
							{/forminput}
						</div>

						{if $gBitUser->hasPermission('p_use_content_templates') && $templates}
							<div class="form-group">
								{formlabel label="Template" for=""}
								{forminput}
									<select name="template_id" onchange="javascript:document.getElementById('editpageform').submit();">
										<option value="0">{tr}none{/tr}</option>
										{section name=ix loop=$templates}
											<option value="{$templates[ix].template_id|escape}">{$templates[ix].name}</option>
										{/section}
									</select>
									{formhelp note=""}
								{/forminput}
							</div>
						{/if}

						{include file="bitpackage:liberty/edit_services_inc.tpl" serviceFile="content_edit_mini_tpl"}

						<div class="form-group">
							{forminput label="checkbox"}
								<input type="checkbox" name="is_draft" id="draft" value="y" {if $pageInfo.is_draft eq 'y'}checked="checked"{/if} />Draft
								{formhelp note=""}
							{/forminput}
						</div>

						<div class="form-group">
							{formlabel label="Subject" for="subject"}
							{forminput}
								<input type="text" maxlength="250" size="40" name="title" id="subject" value="{$pageInfo.title|escape:html}" />
								{formhelp note="This will appear in the <strong>subject</strong> line of the email."}
							{/forminput}
						</div>

						<div class="form-group">
							{formlabel label="Reply-To" for="replyto"}
							{forminput}
								<input type="text" maxlength="250" size="40" name="reply_to" id="replyto" value="{$pageInfo.reply_to|default:$gBitSystem->getConfig('site_sender_email',$smarty.server.SERVER_ADMIN)|escape:html}" />
								{formhelp note="This is the email address to which any replies will be sent."}
							{/forminput}
						</div>
					{textarea edit=$pageInfo.data}
					{/legend}
				{/jstab}

				{include file="bitpackage:liberty/edit_services_inc.tpl" serviceFile="content_edit_tab_tpl"}

				{if $gBitUser->hasPermission('p_liberty_attach_attachments')}
					{jstab title="Attachments"}
						{legend legend="Attachments"}
							{include file="bitpackage:liberty/edit_storage.tpl"}
						{/legend}
					{/jstab}
				{/if}

			{/jstabs}

			<div class="form-group submit">
				<input type="submit" class="btn btn-default" name="cancel" value="{tr}Cancel{/tr}" />
				<input type="submit" class="btn btn-default" name="preview" value="{tr}Preview{/tr}" />
				<input type="submit" class="btn btn-default" name="save" value="{tr}Save Edition{/tr}" />
			</div>
		{/form}
	</div><!-- end .body -->
</div><!-- end .newsletters -->

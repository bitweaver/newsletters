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
						<div class="row">
							{formlabel label="Subject" for="subject"}
							{forminput}
								<input type="text" maxlength="250" size="40" name="title" value="{$gContent->getTitle()|escape}" />
								{formhelp note="This will appear in the <strong>subject</strong> line of the email."}
							{/forminput}
						</div>

						<div class="row">
							{formlabel label="Newsletter" for="nl_id"}
							{forminput}
								<select name="nl_id" id="nl_id">
									{foreach from=$newsletters item=nl key=nlId}
										<option value="{$nlId}" {if $nlId eq $nl_id}selected="selected"{/if}>{$nl.title}</option>
									{/foreach}
								</select>
								{formhelp note="Pick the newsletter you want to post to."}
							{/forminput}
						</div>

						{if $gBitUser->hasPermission( 'bit_p_use_content_templates' ) && $templates}
							<div class="row">
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

						{include file="bitpackage:liberty/edit_services_inc.tpl serviceFile=content_edit_mini_tpl}

						<div class="row">
							{formlabel label="Draft" for="draft"}
							{forminput}
								<input type="checkbox" name="is_draft" value="y" {if $gContent->getField('is_draft')}checked="checked"{/if}" />
								{formhelp note=""}
							{/forminput}
						</div>

						<div class="row">
							{formlabel label="Body" for="body"}
							{forminput}
								<textarea name="edit" rows="25" cols="50">{$gContent->getField('data')|escape}</textarea>
								{formhelp note=""}
							{/forminput}
						</div>
					{/legend}
				{/jstab}

				{include file="bitpackage:liberty/edit_services_inc.tpl serviceFile=content_edit_tab_tpl}

				{if $gBitUser->hasPermission('bit_p_content_attachments')}
					{jstab title="Attachments"}
						{legend legend="Attachments"}
							{include file="bitpackage:liberty/edit_storage.tpl"}
						{/legend}
					{/jstab}
				{/if}

			{/jstabs}

			<div class="row submit">
				<input type="submit" name="cancel" value="{tr}Cancel{/tr}" />
				<input type="submit" name="preview" value="{tr}Preview{/tr}" />
				<input type="submit" name="save" value="{tr}Save Edition{/tr}" />
			</div>
		{/form}
	</div><!-- end .body -->
</div><!-- end .newsletters -->

{* original code
<form action="{$smarty.const.NEWSLETTERS_PKG_URL}edition_edit.php" method="post" id='editpageform'>
<table class="panel">
<tr><td>{tr}Subject{/tr}:</td><td><input type="text" maxlength="250" size="40" name="title" value="{$info.subject|escape}" /></td></tr>
<tr><td>{tr}Newsletter{/tr}:</td><td>
<select name="nl_id">
{foreach from=$newsletters item=nl key=nlId}
<option value="{$nlId|escape}" {if $nlId eq $nl_id}selected="selected"{/if}>{$nl.title}</option>
{/foreach}
</select>
</td></tr>
{if $tiki_p_use_content_templates eq 'y'}
<tr><td>{tr}Apply template{/tr}</td><td>
<select name="template_id" onchange="javascript:document.getElementById('editpageform').submit();">
<option value="0">{tr}none{/tr}</option>
{section name=ix loop=$templates}
<option value="{$templates[ix].template_id|escape}">{$templates[ix].name}</option>
{/section}
</select>
</td></tr>
{/if}
<tr><td>{tr}Data{/tr}:</td><td><textarea name="edit" rows="25" cols="60">{$info.data|escape}</textarea></td></tr>
<tr class="panelsubmitrow"><td colspan="2">
<input type="submit" name="preview" value="{tr}Preview{/tr}" />&nbsp;<input type="submit" name="save" value="{tr}Save Edition{/tr}" />
</td></tr>
</table>
</form>
*}
<div class="floaticon">{bithelp}</div>

<div class="edit newsletters">
	<div class="header">
		<h1>{tr}Prepare a newsletter to be sent{/tr}</h1>
	</div>

	{if $preview eq 'y'}
		<div id="preview">
			<div class="header">
				<h1>{$info.subject}</h1>
			</div>

			<div class="body">{$parsed}</div>
		</div>
	{/if}

	<div class="body">
		{form legend="Newsletter" id="editpageform"}
			<div class="row">
				{formlabel label="Subject" for="subject"}
				{forminput}
					<input type="text" maxlength="250" size="40" name="title" value="{$info.subject|escape}" />
					{formhelp note=""}
				{/forminput}
			</div>

			<div class="row">
				{formlabel label="" for=""}
				{forminput}
					<select name="nl_id">
						{foreach from=$newsletters item=nl key=nlId}
							<option value="{$nlId|escape}" {if $nlId eq $nl_id}selected="selected"{/if}>{$nl.title}</option>
						{/foreach}
					</select>
					{formhelp note=""}
				{/forminput}
			</div>

			{if $tiki_p_use_content_templates eq 'y'}
				<div class="row">
					{formlabel label="" for=""}
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

			<div class="row submit">
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

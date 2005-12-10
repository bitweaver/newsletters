{if $preview eq 'y'}
<br />
<div class="wikibody">{$info.subject}</div>
<div class="wikibody">{$parsed}</div>
{/if}

<h2>{tr}Prepare a newsletter to be sent{/tr}</h2>
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


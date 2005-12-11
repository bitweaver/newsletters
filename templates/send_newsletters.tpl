{strip}
<div class="floaticon">{bithelp}</div>

<div class="edit newsletters">
	<div class="header">
		<h1>{tr}Send newsletters{/tr}</h1>
	</div>

	<div class="body">

{if $emited eq 'y'}
	{tr}The newsletter was sent to {$sent} email addresses{/tr}<br /><br />
{/if}
{if $presend eq 'y'}
<div class="wikibody">{$subject}</div>
<div class="wikibody">{$data}</div>

{tr}This newsletter will be sent to {$subscribers} email addresses.{/tr}

<form method="post" action="{$smarty.const.NEWSLETTERS_PKG_URL}admin/send.php">
<input type="hidden" name="nl_id" value="{$nl_id|escape}" />
<input type="hidden" name="subject" value="{$subject|escape}" />
<input type="hidden" name="data" value="{$data|escape}" />
<input type="submit" name="send" value="{tr}send{/tr}" />
<input type="submit" name="preview" value="{tr}cancel{/tr}" />
</form>
{else}

<h2>{tr}Sent editions{/tr}</h2>

{include file="bitpackage:newsletters/list_editions_inc.tpl"}

{pagination}

{minifind}

{/if}


	</div>
</div>
{/strip}
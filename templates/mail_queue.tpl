<h1>Queued Mails</h1>

{form}
{if $queue}
	<table class="data">
	<tr>
		<th class="item">{smartlink ititle="Email" isort="email" offset=$offset}</th>
		<th class="item">{smartlink ititle="Content Title" isort="title" offset=$offset}</th>
		<th class="item">{smartlink ititle="Newsletter" isort="newsletter" offset=$offset}</th>
		<th class="item">{smartlink ititle="Queue Date" isort="queue_date" offset=$offset}</th>
		<th class="item">{smartlink ititle="Sent Date" isort="sent_date" offset=$offset}</th>
		<th class="item"></th>
	</tr>
	{foreach from=$queue item=q key=qId}
		<tr class="item">
			<td>{$q.email}</td>
			<td><a href="{$smarty.const.BIT_ROOT_URL}?content_id={$q.content_id}">{$q.title|escape}</a></td>
			<td><a href="{$smarty.const.BIT_ROOT_URL}?content_id={$q.nl_content_id}">{$q.newsletter_title|escape}</a></td>
			<td>{$q.queue_date|bit_short_datetime}</a></td>
			<td>{if $q.sent_date}{$q.sent_date|bit_short_datetime}{/if}</td>
			<td><input type="checkbox" name="queue_id[]" value="{$qId}" /></td>
		</tr>
	{/foreach}
	</table>
{/if}

<div style="float:right">
<div class="row">
	With checked: 
	<select name="batch_command">
		<option></option>
		<option value="delete">{tr}Delete{/tr}</option>
		<option value="send">{tr}Send Immediately{/tr}</option>
	</select>
</div>

<div class="row input">
	<input type="submit" name="Submit" value="Submit"/>
</div>
</div>


{/form}

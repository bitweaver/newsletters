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
			<td><a href="{$smarty.const.BIT_ROOT_URL}?content_id={$q.content_id}">{$q.title}</a></td>
			<td><a href="{$smarty.const.BIT_ROOT_URL}?content_id={$q.nl_content_id}">{$q.newsletter_title}</a></td>
			<td>{$q.queue_date|bit_short_datetime}</a></td>
			<td>{$q.sent_date}</td>
			<td><input type="checkbox" name="queue_id[]" value="{$qId}" /></td>
		</tr>
	{/foreach}
	</table>
{/if}

<div style="float:right">
<div class="row">
	With checked: 
	<select>
		<option></option>
		<option>{tr}Delete{/tr}</option>
		<option>{tr}Send Immediately{/tr}</option>
	</select>
</div>

<div class="row input">
	<input type="submit" name="Submit" value="Submit"/>
</div>
</div>


{/form}

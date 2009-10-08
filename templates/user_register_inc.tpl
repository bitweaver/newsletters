<div class="row">
	{formlabel label="Subscriptions"}
	{forminput}
	{foreach from=$newsletters item='newsletter'}
		<input type="checkbox" name="unsubscribe[]" value="{$newsletter.nl_id}"/><strong>{$newsletter.title}</strong><br/>
	{/foreach}
	{formhelp note="Check the box to opt-out of the specified newsletter."}
	{/forminput}
</div>

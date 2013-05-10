{if $newsletters}
<input type="hidden" name="newsletter_optin" value="y"/>
<script type = "text/javascript">
{literal}
function unsubscribe_all(){

var checkboxes = document.getElementsByName('subscribe[]');
for (var i = 0; i < checkboxes.length; i++){
	if( checkboxes[i].checked == true ){
		checkboxes[i].checked = false;
	}
}

}
{/literal}
</script>

<div class="control-group">
	{formlabel label="Subscriptions"}
	{forminput}
	{foreach from=$newsletters item='newsletter'}
		<label class="checkbox"><input type="checkbox" checked="true" name="subscribe[]" onclick="document.getElementById('unsubscribeall').checked=false;" value="{$newsletter.nl_id}"/><strong>{$newsletter.title}</strong></label>
	{/foreach}
	{formhelp note="Uncheck the box to opt-out of the specified newsletter."}
	{if count($newsletters) > 1}
			<input type="checkbox" name="unsub_all" value="y" id="unsubscribeall" onclick="unsubscribe_all();"/><strong>Unsubscribe All</strong><br/>
		{formhelp note="Check this box to unsubscribe from all of the above newsletters."}
	{/if}
	{/forminput}
</div>
{/if}

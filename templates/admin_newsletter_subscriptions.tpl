<div class="floaticon">{bithelp}</div>

<div class="admin newsletters">
	<div class="header">
		<h1>{tr}Admin newsletter subscriptions{/tr}</h1>
	</div>

	<div class="body">
		{form}
			<input type="hidden" name="nl_id" value="{$nl_id|escape}" />

			<div class="control-group">
				{if $gContent->getField('validate_addr') eq 'y'}
					{formfeedback warning="Validate Email is enabled, a confirmation email will be sent to every subsciber inviting them to the newsletter. <strong>Any duplicate or previously unsubscribed emails will *NOT* be re-subscribed using this method.</strong>"}
				{else}
					{formfeedback warning="Validate Email is disabled, emails will be subscribed but not validated by the users. <strong>Any duplicate or previously unsubscribed emails will *NOT* be re-subscribed using this method.</strong>"}
				{/if}
			</div>

			<div class="control-group">
				{formlabel label="New Subscribers" for=""}
				{forminput}
					<textarea cols="50" rows="5" name="new_subscribers" id="new_subscribers"></textarea>
					{formhelp note="Enter multiple email addresses on separate lines to import into the subscriber list"}
				{/forminput}
			</div>

			<div class="control-group submit">
				{forminput}
					<input type="submit" class="btn btn-default" name="save" value="{tr}Subscribe{/tr}" />
				{/forminput}
			</div>
		{/form}

		{minifind}

		{form id="list"}
			<input type="hidden" name="nl_id" value="{$nl_id|escape}" />

			<table class="table data">
				<caption>{tr}Subscriptions{/tr}</caption>
				<tr>
					<th>{smartlink ititle="Email" isort=email offset=$offset idefault=1}</th>
					<th>{smartlink ititle="Valid" isort=is_valid offset=$offset idefault=1}</th>
					<th>{smartlink ititle="Subscribed" isort=subscribed_date offset=$offset idefault=1}</th>
					<th>{smartlink ititle="Unsubscribed" isort=unsubscribe_date offset=$offset idefault=1}</th>
					<th>{tr}Actions{/tr}</th>
				</tr>
				{section name=sb loop=$subscribers}
					<tr class="{cycle values='odd,even'}">
						<td>{$subscribers[sb].email}</td>
						<td>{$subscribers[sb].is_valid}</td>
						<td>{$subscribers[sb].subscribed_date|bit_short_datetime}</td>
						<td>{if $subscribers[sb].unsubscribe_date ne NULL}{$subscribers[sb].unsubscribe_date|bit_short_datetime}{/if}</td>
						<td><input type="checkbox" name="checked[]" value="{$subscribers[sb].email}" /></td>
					</tr>
				{sectionelse}
					<tr class="norecords">
						<td colspan="2">{tr}No Records Found{/tr}</td>
					</tr>
				{/section}
			</table>
			<div style="text-align:right;">
				<script type="text/javascript">//<![CDATA[
					// check / uncheck all.
					document.write("<label for=\"switcher\">{tr}Select All{/tr}</label> ");
					document.write("<input name=\"switcher\" id=\"switcher\" type=\"checkbox\" onclick=\"BitBase.switchCheckboxes(this.form.id,'checked[]','switcher')\" />");
				//]]></script>

				<br />

				<select name="submit_mult" onchange="this.form.submit();">
					<option value="" selected="selected">{tr}with checked{/tr}:</option>
						<option value="remove">{tr}remove{/tr}</option>
						<option value="unsubscribe">{tr}unsubscribe{/tr}</option>
						<option value="resubscribe">{tr}resubscribe{/tr}</option>
				</select>

				<script type="text/javascript">//<![CDATA[
				// Fake js to allow the use of the <noscript> tag (so non-js-users kenn still submit)
				//]]></script>

				<noscript>
					<div><input type="submit" class="btn btn-default" value="{tr}Submit{/tr}" /></div>
				</noscript>
			</div>
		{/form}
	</div><!-- end .body -->
</div><!-- end .newsletters -->

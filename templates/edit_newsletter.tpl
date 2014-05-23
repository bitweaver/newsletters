{if !$newsletters || $gContent->isValid() || $smarty.request.new}
{strip}
<div class="floaticon">{bithelp}</div>

<div class="edit newsletters">
	<div class="header">
		<h1>{tr}Newsletter Settings{/tr}</h1>
	</div>

	<div class="body">
			{form legend="Create / Edit Newsletters"}
				{if $individual eq 'y'}
					<a href="{$smarty.const.KERNEL_PKG_URL}object_permissions.php?objectName=newsletter%20{$gContent->mInfo.name}&amp;object_type=newsletter&amp;permType=newsletters&amp;object_id={$gContent->mInfo.nl_id}">{tr}There are individual permissions set for this newsletter{/tr}</a><br /><br />
				{/if}
				<input type="hidden" name="nl_id" value="{$gContent->mNewsletterId}" />

				<div class="control-group">
					{formlabel label="Title" for="title"}
					{forminput}
						<input type="text" name="title" id="title" value="{$gContent->mInfo.title|escape}" />
						{formhelp note="Title of the newsletter."}
					{/forminput}
				</div>

				<div class="control-group">
					{formlabel label="Description" for="description"}
					{forminput}
						<textarea name="edit" rows="4" cols="40" id="description">{$gContent->mInfo.data|escape}</textarea>
						{formhelp note="Description of the newsletter, that users know what they are getting themselves into."}
					{/forminput}
				</div>

				<div class="control-group">
					<label class="checkbox">
						<input type="checkbox" name="allow_user_sub" id="allow_user_sub" {if !$gContent->isValid() || $gContent->mInfo.allow_user_sub eq 'y'}checked="checked"{/if} />Users can Subscribe
						{formhelp note="Users can subscribe to this list. Disabling this options means that you have to manually add users to the list."}
					</label>
				</div>

				<div class="control-group">
					<label class="checkbox">
						<input type="checkbox" name="allow_any_sub" id="allow_any_sub" {if !$gContent->isValid() || $gContent->mInfo.allow_any_sub eq 'y'}checked="checked"{/if} />Any e-mail Address
						{formhelp note="Users may subscribe using any email address."}
					</label>
				</div>

				<div class="control-group">
					<label class="checkbox">
						<input type="checkbox" name="unsub_msg" id="unsub_msg" {if !$gContent->isValid() || $gContent->mInfo.unsub_msg eq 'y'}checked="checked"{/if} />Append Un/Subscribe Instructions
						{formhelp note="Append instructions on how to subscribe / unsubscribe to ever outgoing newsletter. This is only useful when users can un / subscribe to the list themselves."}
					</label>
				</div>

				<div class="control-group">
					<label class="checkbox">
						<input type="checkbox" name="validate_addr" id="validate_addr" {if $gContent->mInfo.validate_addr eq 'y'}checked="checked"{/if} />Validate e-mail Addresses
						{formhelp note="Validate all email addresses before they are added to the list. This might result in members not being added despite working email addresses."}
					</label>
				</div>

				<div class="control-group">
					<label class="checkbox">
						<input type="checkbox" name="registration_optin" id="registration_optin" {if $gContent->getPreference('registration_optin') eq 'y'}checked="checked"{/if} value="y"/>Registration Opt-In
						{formhelp note="List this newsletter on the registration page and allow user to join newsletter."}
					</label>
				</div>

				<div class="control-group submit">
					<input type="submit" class="btn btn-default" name="cancel" value="{tr}Cancel{/tr}" />
					<input type="submit" class="btn btn-default" name="save" value="{tr}Save{/tr}" />
				</div>
			{/form}

	</div><!-- end .body -->
</div><!-- end .___ -->
{/strip}
{else}
	{include file="bitpackage:newsletters/list_newsletters.tpl"}
{/if}

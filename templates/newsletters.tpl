{strip}
<div class="display newsletters">
	<div class="header">
		<h1>{tr}Newsletters{/tr}</h1>
	</div>

	<div class="body">
		{formfeedback hash=$feedback}

		{if $gContent->isValid()}
			{if $confirm eq 'y'}
				{formfeedback success="Subscription Confirmed!"}
			{/if}
					<div class="row">
						{formlabel label="Name" for=""}
						{forminput}
							{$gContent->getTitle()}
						{/forminput}
					</div>

					<div class="row">
						{formlabel label="Description" for=""}
						{forminput}
							{$gContent->parseData()}
						{/forminput}
					</div>

				{if ($gContent->mInfo.allow_user_sub eq 'y') or $gBitUser->hasPermission( 'bit_p_subscribe_newsletters' )}
					{form}
						<input type="hidden" name="nl_id" value="{$gContent->mNewsletterId}" />
						<div class="row">
							{formlabel label="Email" for=""}
							{forminput}
							{if $gBitUser->hasPermission( 'bit_p_subscribe_email' )}
								<input type="text" name="email" value="{$email|escape}" />
							{else}
								<input type="hidden" name="email" value="{$email|escape}" />
								{$email|escape}
							{/if}
							{/forminput}
						</div>
						<div class="row submit">
							{forminput}
								<input type="submit" name="cancel" value="{tr}Cancel{/tr}" />
								<input type="submit" name="subscribe" value="{tr}Subscribe{/tr}" />
							{/forminput}
						</div>
					{/form}
				{/if}
		{else}

			{minifind}

			<table class="data">
				<caption>{tr}Newsletters{/tr}</caption>
				<tr>
					<th>{smartlink ititle="Name" isort=name offset=$offset idefault=1}</th>
					<th>{smartlink ititle="Description" isort=descritpion offset=$offset}</th>
				</tr>
				{foreach from=$newsletters item=nl key=nlId}
					{if $newsletters.individual ne 'y' or $newsletters.individual_bit_p_subscribe_newsletters eq 'y'}
						<tr class="{cycle values='odd,even'}">
							<td><a href="{$smarty.const.NEWSLETTERS_PKG_URL}index.php?nl_id={$nl.nl_id}">{$nl.title|escape}</a></td>
							<td>{$nl.data}</td>
						</tr>
					{/if}
				{foreachelse}
					<tr class="norecords">
						<td colspan="2">{tr}No Records Found{/tr}</td>
					</tr>
				{/foreach}
			</table>
		{/if}

		{* haven't dealt with pagination yet *}
		{pagination}
	</div><!-- end .body -->
</div><!-- end .newsletters -->
{/strip}


{* original code
{if $subscribe eq 'y'}
<form method="post" action="{$smarty.const.NEWSLETTERS_PKG_URL}index.php">
<input type="hidden" name="nl_id" value="{$nl_id|escape}" />
<table class="panel">
<caption>{tr}Subscribe to newsletter{/tr}</caption>
<tr>
  <td>{tr}Name{/tr}:</td>
  <td>{$nl_info.name}</td>
</tr>
<tr>
  <td>{tr}Description{/tr}:</td>
  <td>{$nl_info.description}</td>
</tr>
{if ($nl_info.allow_user_sub eq 'y') or ($bit_p_admin_newsletters eq 'y')}
{if $bit_p_subscribe_email eq 'y'}
<tr>
  <td>{tr}Email:{/tr}</td>
  <td><input type="text" name="email" value="{$email|escape}" /></td>
</tr>
{else}
  <input type="hidden" name="email" value="{$email|escape}" />
{/if}
<tr class="panelsubmitrow">
  <td colspan="2"><input type="submit" name="subscribe" value="{tr}Subscribe{/tr}" /></td>
</tr>
{/if}
</table>
</form>
{/if}

<h2>{tr}Newsletters{/tr}</h2>
<table class="find">
<tr><td>{tr}Find{/tr}</td>
   <td>
   <form method="get" action="{$smarty.const.NEWSLETTERS_PKG_URL}admin/index.php">
     <input type="text" name="find" value="{$find|escape}" />
     <input type="submit" value="{tr}find{/tr}" name="search" />
     <input type="hidden" name="sort_mode" value="{$sort_mode|escape}" />
   </form>
   </td>
</tr>
</table>

<table>
<tr>
<th><a href="{$smarty.const.NEWSLETTERS_PKG_URL}admin/index.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'name_desc'}name_asc{else}name_desc{/if}">{tr}name{/tr}</a></th>
<th><a href="{$smarty.const.NEWSLETTERS_PKG_URL}admin/index.php?offset={$offset}&amp;sort_mode={if $sort_mode eq 'description_desc'}description_asc{else}description_desc{/if}">{tr}description{/tr}</a></th>
</tr>
{cycle values="even,odd" print=false}
{section name=user loop=$newsletters}
{if $newsletters.individual ne 'y' or $newsletters.individual_bit_p_subscribe_newsletters eq 'y'}
<tr class="{cycle}">
<td><a href="{$smarty.const.NEWSLETTERS_PKG_URL}index.php?nl_id={$nl.nl_id}&amp;info=1">{$nl.name}</a></td>
<td>{$nl.description}</td>
</tr>
{/if}
{/section}
</table>

<div class="pagination">
{if $prev_offset >= 0}
[<a href="{$smarty.const.NEWSLETTERS_PKG_URL}admin/index.php?find={$find}&amp;offset={$prev_offset}&amp;sort_mode={$sort_mode}">{tr}prev{/tr}</a>]&nbsp;
{/if}
{tr}Page{/tr}: {$actual_page}/{$cant_pages}
{if $next_offset >= 0}
&nbsp;[<a href="{$smarty.const.NEWSLETTERS_PKG_URL}admin/index.php?find={$find}&amp;offset={$next_offset}&amp;sort_mode={$sort_mode}">{tr}next{/tr}</a>]
{/if}
{if $direct_pagination eq 'y'}
<br />
{section loop=$cant_pages name=foo}
{assign var=selector_offset value=$smarty.section.foo.index|times:$maxRecords}
<a href="{$smarty.const.NEWSLETTERS_PKG_URL}admin/index.php?find={$find}&amp;offset={$selector_offset}&amp;sort_mode={$sort_mode}">{$smarty.section.foo.index_next}</a>&nbsp;
{/section}
{/if}
</div>
*}

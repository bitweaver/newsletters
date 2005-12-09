{strip}
<div class="display newsletters">
	<div class="header">
		<h1>{tr}Newsletters{/tr}</h1>
	</div>

	<div class="body">
		{formfeedback hash=$feedback}

		{if $confirm eq 'y'}
			{legend legend="Subscription Confirmed!"}
				<div class="row">
					{formlabel label="Name" for=""}
					{forminput}
						{$nl_info.name}
					{/forminput}
				</div>

				<div class="row">
					{formlabel label="Description" for=""}
					{forminput}
						{$nl_info.description}
					{/forminput}
				</div>
			{/legend}
		{/if}

		{if $subscribe eq 'y'}
			{form}
				<div class="row">
					{formlabel label="Name" for=""}
					{forminput}
						{$nl_info.name}
					{/forminput}
				</div>

				<div class="row">
					{formlabel label="Description" for=""}
					{forminput}
						{$nl_info.description}
					{/forminput}
				</div>

				{if ($nl_info.allow_user_sub eq 'y') or ($bitweaver.orgi_p_admin_newsletters eq 'y')}
					{if $bitweaver.orgi_p_subscribe_email eq 'y'}
						<div class="row">
							{formlabel label="Email" for=""}
							{forminput}
								{$email|escape}
							{/forminput}
						</div>
					{else}
						<input type="hidden" name="email" value="{$email|escape}" />
					{/if}
				{/if}
			{/form}
		{/if}

		{minifind}

		<table class="data">
			<caption>{tr}Newsletters{/tr}</caption>
			<tr>
				<th>{smartlink ititle="Name" isort=name offset=$offset idefault=1}</th>
				<th>{smartlink ititle="Descritpion" isort=descritpion offset=$offset}</th>
			</tr>
			{section name=user loop=$channels}
				{if $channels.individual ne 'y' or $channels.individual_bitweaver.orgi_p_subscribe_newsletters eq 'y'}
					<tr class="{cycle values='odd,even'}">
						<td><a href="{$smarty.const.NEWSLETTERS_PKG_URL}index.php?nl_id={$channels[user].nl_id}&amp;info=1">{$channels[user].name}</a></td>
						<td>{$channels[user].description}</td>
					</tr>
				{/if}
			{sectionelse}
				<tr class="norecords">
					<td colspan="2">{tr}No Records Found{/tr}</td>
				</tr>
			{/section}
		</table>

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
{if ($nl_info.allow_user_sub eq 'y') or ($bitweaver.orgi_p_admin_newsletters eq 'y')}
{if $bitweaver.orgi_p_subscribe_email eq 'y'}
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
{section name=user loop=$channels}
{if $channels.individual ne 'y' or $channels.individual_bitweaver.orgi_p_subscribe_newsletters eq 'y'}
<tr class="{cycle}">
<td><a href="{$smarty.const.NEWSLETTERS_PKG_URL}index.php?nl_id={$channels[user].nl_id}&amp;info=1">{$channels[user].name}</a></td>
<td>{$channels[user].description}</td>
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

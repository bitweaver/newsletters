{strip}
<ul>
	<li><a class="item" href="{$smarty.const.NEWSLETTERS_PKG_URL}index.php">{tr}List Newsletters{/tr}</a></li>
	{if $gBitUser->hasPermission( 'p_newsletters_create' )}
		<li><a class="item" href="{$smarty.const.NEWSLETTERS_PKG_URL}admin/admin_newsletters.php">{tr}Admin Newsletters{/tr}</a></li>
		<li><a class="item" href="{$smarty.const.NEWSLETTERS_PKG_URL}admin/admin_newsletters.php?new=1">{tr}Create Newsletter{/tr}</a></li>
	{/if}
	<li><a class="item" href="{$smarty.const.NEWSLETTERS_PKG_URL}edition.php">{tr}List Editions{/tr}</a></li>
	{if $gBitUser->hasPermission( 'p_newsletters_create_editions' )}
		<li><a class="item" href="{$smarty.const.NEWSLETTERS_PKG_URL}edition_edit.php">{tr}Create Edition{/tr}</a></li>
	{/if}
	{if $gBitUser->hasPermission( 'p_newsletters_admin' )}
		<li><a class="item" href="{$smarty.const.NEWSLETTERS_PKG_URL}admin/send.php">{tr}Send Newsletters{/tr}</a></li>
	{/if}
</ul>
{/strip}

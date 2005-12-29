{strip}
<ul>
	<li><a class="item" href="{$smarty.const.NEWSLETTERS_PKG_URL}index.php">{tr}List newsletters{/tr}</a></li>
	{if $gBitUser->hasPermission( 'bit_p_create_newsletters' )}
		<li><a class="item" href="{$smarty.const.NEWSLETTERS_PKG_URL}newsletters.php?new=1">{tr}Create Newsletter{/tr}</a></li>
	{/if}
	<li><a class="item" href="{$smarty.const.NEWSLETTERS_PKG_URL}edition.php">{tr}List editions{/tr}</a></li>
	{if $gBitUser->hasPermission( 'bit_p_create_editions' )}
		<li><a class="item" href="{$smarty.const.NEWSLETTERS_PKG_URL}edition_edit.php">{tr}Create Edition{/tr}</a></li>
	{/if}
	{if $gBitUser->hasPermission( 'bit_p_admin_newsletters' )}
		<li><a class="item" href="{$smarty.const.NEWSLETTERS_PKG_URL}admin/send.php">{tr}Send newsletters{/tr}</a></li>
	{/if}
</ul>
{/strip}

{strip}
<ul>
	<li><a class="item" href="{$smarty.const.NEWSLETTERS_PKG_URL}index.php">{booticon iname="icon-list" iexplain="List Newsletters" ilocation=menu}</a></li>
	{if $gBitUser->hasPermission( 'p_newsletters_create' )}
		<li><a class="item" href="{$smarty.const.NEWSLETTERS_PKG_URL}newsletters.php?new=1">{biticon iname="mail-message-new" iexplain="Create Newsletter" ilocation=menu}</a></li>
	{/if}
	<li><a class="item" href="{$smarty.const.NEWSLETTERS_PKG_URL}edition.php">{biticon iname="document-open" iexplain="List Editions" ilocation=menu}</a></li>
	{if $gBitUser->hasPermission( 'p_newsletters_create_editions' )}
		<li><a class="item" href="{$smarty.const.NEWSLETTERS_PKG_URL}edition_edit.php">{biticon iname="folder-new" iexplain="Create Edition" ilocation=menu}</a></li>
	{/if}
	{if $gBitUser->hasPermission( 'p_newsletters_admin' )}
		<li><a class="item" href="{$smarty.const.NEWSLETTERS_PKG_URL}admin/send.php">{biticon iname="mail-forward" iexplain="Send Newsletters" ilocation=menu}</a></li>
	{/if}
</ul>
{/strip}

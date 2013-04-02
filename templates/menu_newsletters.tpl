{strip}
<ul>
	<li><a class="item" href="{$smarty.const.NEWSLETTERS_PKG_URL}index.php">{booticon iname="icon-list" iexplain="List Newsletters" ilocation=menu}</a></li>
	{if $gBitUser->hasPermission( 'p_newsletters_create' )}
		<li><a class="item" href="{$smarty.const.NEWSLETTERS_PKG_URL}newsletters.php?new=1">{booticon iname="icon-file-alt" iexplain="Create Newsletter" ilocation=menu}</a></li>
	{/if}
	<li><a class="item" href="{$smarty.const.NEWSLETTERS_PKG_URL}edition.php">{booticon iname="icon-folder-open"   iexplain="List Editions" ilocation=menu}</a></li>
	{if $gBitUser->hasPermission( 'p_newsletters_create_editions' )}
		<li><a class="item" href="{$smarty.const.NEWSLETTERS_PKG_URL}edition_edit.php">{booticon iname="icon-edit" iexplain="Create Edition" ilocation=menu}</a></li>
	{/if}
	{if $gBitUser->hasPermission( 'p_newsletters_admin' )}
		<li><a class="item" href="{$smarty.const.NEWSLETTERS_PKG_URL}admin/send.php">{booticon iname="icon-envelope"   iexplain="Send Newsletters" ilocation=menu}</a></li>
	{/if}
</ul>
{/strip}

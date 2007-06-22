{strip}
<ul>
	<li><a class="item" href="{$smarty.const.NEWSLETTERS_PKG_URL}index.php">{biticon ipackage="icons" iname="format-justify-fill" iexplain="List Newsletters" iforce="icon"} {tr}List Newsletters{/tr}</a></li>
	{if $gBitUser->hasPermission( 'p_newsletters_create' )}
		<li><a class="item" href="{$smarty.const.NEWSLETTERS_PKG_URL}newsletters.php?new=1">{biticon ipackage="icons" iname="mail-message-new" iexplain="Create Newsletter" iforce="icon"} {tr}Create Newsletter{/tr}</a></li>
	{/if}
	<li><a class="item" href="{$smarty.const.NEWSLETTERS_PKG_URL}edition.php">{biticon ipackage="icons" iname="document-open" iexplain="List Editions" iforce="icon"} {tr}List Editions{/tr}</a></li>
	{if $gBitUser->hasPermission( 'p_newsletters_create_editions' )}
		<li><a class="item" href="{$smarty.const.NEWSLETTERS_PKG_URL}edition_edit.php">{biticon ipackage="icons" iname="folder-new" iexplain="Create Edition" iforce="icon"}{tr}Create Edition{/tr}</a></li>
	{/if}
	{if $gBitUser->hasPermission( 'p_newsletters_admin' )}
		<li><a class="item" href="{$smarty.const.NEWSLETTERS_PKG_URL}admin/send.php">{biticon ipackage="icons" iname="mail-forward" iexplain="Send Newsletters" iforce="icon"} {tr}Send Newsletters{/tr}</a></li>
	{/if}
</ul>
{/strip}

{strip}
<ul>
<li><a class="item" href="{$smarty.const.NEWSLETTERS_PKG_URL}index.php">{tr}List newsletters{/tr}</a></li>
{if $gBitUser->hasPermission( 'tiki_p_admin_newsletters' )}
	<li><a class="item" href="{$smarty.const.NEWSLETTERS_PKG_URL}admin/send.php">{tr}Send newsletters{/tr}</a></li>
	<li><a class="item" href="{$smarty.const.NEWSLETTERS_PKG_URL}admin/index.php">{tr}Admin newsletters{/tr}</a></li>
{/if}
</ul>
{/strip}

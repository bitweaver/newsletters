{if $gBitUser->hasPermission( 'bit_p_admin_newsletters' )}
<ul>
	<li><a class="item" href="{$smarty.const.KERNEL_PKG_URL}admin/index.php?page=newsletters">{tr}Newsletters Settings{/tr}</a></li>
	<li><a class="item" href="{$smarty.const.NEWSLETTERS_PKG_URL}admin/send.php">{tr}Send Newsletters{/tr}</a></li>
</ul>
{/if}

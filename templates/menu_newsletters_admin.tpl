{if $gBitUser->hasPermission( 'p_newsletters_admin' )}
<ul>
	<li><a class="item" href="{$smarty.const.KERNEL_PKG_URL}admin/index.php?page=newsletters">{tr}Newsletters Settings{/tr}</a></li>
	<li><a class="item" href="{$smarty.const.NEWSLETTERS_PKG_URL}admin/send.php">{tr}Send Newsletters{/tr}</a></li>
	<li><a class="item" href="{$smarty.const.NEWSLETTERS_PKG_URL}admin/mail_queue.php">{tr}Tend Mail Queue{/tr}</a></li>
</ul>
{/if}

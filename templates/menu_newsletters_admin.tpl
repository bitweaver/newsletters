{if $gBitUser->hasPermission( 'p_newsletters_admin' )}
<li class="dropdown-submenu">
    <a href="#" onclick="return(false);" tabindex="-1" class="sub-menu-root">{tr}{$smarty.const.NEWSLETTERS_PKG_NAME|capitalize}{/tr}</a>
	<ul class="dropdown-menu sub-menu">
		<li><a class="item" href="{$smarty.const.KERNEL_PKG_URL}admin/index.php?page=newsletters">{tr}Newsletter Settings{/tr}</a></li>
		<li><a class="item" href="{$smarty.const.NEWSLETTERS_PKG_URL}admin/send.php">{tr}Send Newsletters{/tr}</a></li>
		<li><a class="item" href="{$smarty.const.NEWSLETTERS_PKG_URL}admin/mail_queue.php">{tr}Tend Mail Queue{/tr}</a></li>
	</ul>
</li>
{/if}

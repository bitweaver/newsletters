{if $gBitUser->hasPermission( 'p_newsletters_admin' )}
{if $packageMenuTitle}<a href="#"> {tr}{$packageMenuTitle|capitalize}{/tr}</a>{/if}
<ul class="{$packageMenuClass}">
	<li><a class="item" href="{$smarty.const.KERNEL_PKG_URL}admin/index.php?page=newsletters">{tr}Newsletter{/tr}</a></li>
	<li><a class="item" href="{$smarty.const.NEWSLETTERS_PKG_URL}admin/send.php">{tr}Send Newsletters{/tr}</a></li>
	<li><a class="item" href="{$smarty.const.NEWSLETTERS_PKG_URL}admin/mail_queue.php">{tr}Tend Mail Queue{/tr}</a></li>
</ul>
{/if}

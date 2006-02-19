<div class="display newsletters">
	{if !$sending}
	<div class="floaticon">
		{include file="bitpackage:liberty/services_inc.tpl" serviceLocation='icon'}

		{if $gBitUser->hasPermission('bit_p_admin_newsletters')}
			<a href="{$smarty.const.NEWSLETTERS_PKG_URL}admin/send.php?edition_id={$gContent->mEditionId}">{biticon ipackage=liberty iname="mail_send" iexplain="email this post"}</a>
		{/if}
		{if $gContent->isOwner() || $gBitUser->hasPermission( 'bit_p_admin_newsletters' )}
			<a href="{$smarty.const.NEWSLETTERS_PKG_URL}edition_edit.php?edition_id={$gContent->mEditionId}">{biticon ipackage=liberty iname="edit" iexplain="edit"}</a>
			<a href="{$smarty.const.NEWSLETTERS_PKG_URL}edition.php?edition_id={$gContent->mEditionId}&amp;remove=1">{biticon ipackage=liberty iname="delete" iexplain="delete"}</a>
		{/if}
	</div>
	{/if}

	<div class="header">
		<h1>{$gContent->getTitle()}</h1>
		{if $gContent->mNewsletter}
			<h2>{$gContent->mNewsletter->getTitle()}</h2>
		{/if}
	</div>

	{include file="bitpackage:liberty/services_inc.tpl" serviceLocation='nav'}

	<div class="body">
		<div class="content">
			{$gContent->parseData()}
		</div> <!-- end .content -->
	</div> <!-- end .body -->
</div><!-- end .newsletters -->

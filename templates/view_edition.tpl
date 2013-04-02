<div class="display newsletters">
	{if !$sending}
	<div class="floaticon">
		{include file="bitpackage:liberty/services_inc.tpl" serviceLocation='icon'}

		{if $gBitUser->hasPermission('p_newsletters_admin')}
			<a href="{$smarty.const.NEWSLETTERS_PKG_URL}admin/send.php?edition_id={$gContent->mEditionId}">{booticon iname="icon-envelope"  ipackage="icons"  iexplain="email this post"}</a>
		{/if}
		{if $gContent->isOwner() || $gBitUser->hasPermission( 'p_newsletters_admin' )}
			<a href="{$smarty.const.NEWSLETTERS_PKG_URL}edition_edit.php?edition_id={$gContent->mEditionId}">{booticon iname="icon-edit" ipackage="icons" iexplain="edit"}</a>
			<a href="{$smarty.const.NEWSLETTERS_PKG_URL}edition.php?edition_id={$gContent->mEditionId}&amp;remove=1">{booticon iname="icon-trash" ipackage="icons" iexplain="delete"}</a>
		{/if}
	</div>
	{/if}

	<div class="header">
		{if $gContent->mNewsletter}
			<p>{$gContent->mNewsletter->getTitle()}</p>
		{/if}
		<h1>{$gContent->getTitle()}</h1>
	</div>

	{if !$sending}
		{include file="bitpackage:liberty/services_inc.tpl" serviceLocation='nav'}
	{/if}

	<div class="body">
		<div class="content">
			{$gContent->parseData()}
		</div> <!-- end .content -->
	</div> <!-- end .body -->

</div><!-- end .newsletters -->

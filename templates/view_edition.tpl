<div class="display newsletters">
	{if !$sending}
	<div class="floaticon">
		{include file="bitpackage:liberty/services_inc.tpl" serviceLocation='icon'}

		{if $gBitUser->hasPermission('p_newsletters_admin')}
			<a href="{$smarty.const.NEWSLETTERS_PKG_URL}admin/send.php?edition_id={$gContent->mEditionId}">{biticon ipackage="icons" iname="mail-forward" iexplain="email this post"}</a>
		{/if}
		{if $gContent->isOwner() || $gBitUser->hasPermission( 'bit_p_admin_newsletters' )}
			<a href="{$smarty.const.NEWSLETTERS_PKG_URL}edition_edit.php?edition_id={$gContent->mEditionId}">{biticon ipackage="icons" iname="accessories-text-editor" iexplain="edit"}</a>
			<a href="{$smarty.const.NEWSLETTERS_PKG_URL}edition.php?edition_id={$gContent->mEditionId}&amp;remove=1">{biticon ipackage="icons" iname="edit-delete" iexplain="delete"}</a>
		{/if}
	</div>
	{else}
		<div><small>{tr}This newsletter can be viewed on the web at{/tr} <a href="{$gContent->getDisplayUrl()}">{$gContent->getDisplayUrl()}</a></small><div>
	{/if}

	<div class="header">
		<h1>{$gContent->getTitle()}</h1>
		{if $gContent->mNewsletter}
			<p>{$gContent->mNewsletter->getTitle()}</p>
		{/if}
	</div>

	{include file="bitpackage:liberty/services_inc.tpl" serviceLocation='nav'}

	<div class="body">
		<div class="content">
			{$gContent->parseData()}
		</div> <!-- end .content -->
	</div> <!-- end .body -->

	{if $sending} tcode {$trackCode}
		{include file="bitpackage:newsletters/unsubscribe_inc.tpl"}
		<img src="{$smarty.const.NEWSLETTERS_PKG_URI}track.php?sub={$trackCode}" alt="" />
	{/if}

</div><!-- end .newsletters -->

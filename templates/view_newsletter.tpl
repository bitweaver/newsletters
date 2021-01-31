<div class="display newsletters">
	<div class="floaticon">
		{include file="bitpackage:liberty/services_inc.tpl" serviceLocation='icon'}

		{if $gContent->isOwner() || $gBitUser->hasPermission( 'p_newsletters_admin' )}
			<a href="{$smarty.const.NEWSLETTERS_PKG_URL}newsletters.php?nl_id={$gContent->mNewsletterId}">{booticon iname="icon-edit" ipackage="icons" iexplain="edit"}</a>
			<a href="{$smarty.const.NEWSLETTERS_PKG_URL}newsletters.php?nl_id={$gContent->mNewsletterId}&amp;remove=1">{booticon iname="icon-trash" ipackage="icons" iexplain="delete"}</a>
		{/if}

		{if $gBitUser->hasPermission( 'p_liberty_print' )}
			<a href="{$smarty.const.NEWSLETTERS_PKG_URL}print_blog_post.php?post_id={$post_id}">{booticon iname="icon-print"  ipackage="icons"  iexplain="print"}</a>
		{/if}
		<a href="{$smarty.const.NEWSLETTERS_PKG_URL}send_newsletter.php?post_id={$post_id}">{booticon iname="icon-envelope"  ipackage="icons"  iexplain="email this post"}</a>
	</div>

	<div class="header">
		<h1>{$gContent->getTitle()}</h1>
		{if $gContent->getField('description')}
			<p>{$gContent->getField('description')}</p>
		{/if}
	</div>

	{include file="bitpackage:liberty/services_inc.tpl" serviceLocation='nav'}

	<div class="body">
		<div class="content">
			{$gContent->getParsedData()}

			{include file="bitpackage:newsletters/list_editions_inc.tpl" editionList=$gContent->getEditions()}
		</div> <!-- end .content -->
	</div> <!-- end .body -->
</div> <!-- end .newsletters -->

<div class="display newsletters">
	<div class="floaticon">
		{include file="bitpackage:liberty/services_inc.tpl" serviceLocation='icon'}

		{if $gContent->isOwner() || $gBitUser->hasPermission( 'bit_p_admin_newsletters' )}
			<a href="{$smarty.const.NEWSLETTERS_PKG_URL}admin/index.php?nl_id={$gContent->mNewsletterId}">{biticon ipackage=liberty iname="edit" iexplain="edit"}</a>
			<a href="{$smarty.const.NEWSLETTERS_PKG_URL}admin/index.php?nl_id={$gContent->mNewsletterId}&amp;remove=1">{biticon ipackage=liberty iname="delete" iexplain="delete"}</a>
		{/if}

		{if $gBitUser->hasPermission( 'bit_p_print' )}
			<a href="{$smarty.const.NEWSLETTERS_PKG_URL}print_blog_post.php?post_id={$post_id}">{biticon ipackage=liberty iname="print" iexplain="print"}</a>
		{/if}
		<a href="{$smarty.const.NEWSLETTERS_PKG_URL}send_newsletter.php?post_id={$post_id}">{biticon ipackage=liberty iname="mail_send" iexplain="email this post"}</a>
	</div>

	<div class="header">
		<h1>{$gContent->getTitle()}</h1>
		{if $gContent->getField('description')}
			<h2>{$gContent->getField('description')}</h2>
		{/if}
	</div>

	{include file="bitpackage:liberty/services_inc.tpl" serviceLocation='nav'}

	<div class="body">
		<div class="content">
			{$gContent->parseData()}

			{include file="bitpackage:newsletters/list_editions_inc.tpl" editionList=$gContent->getEditions()}
		</div> <!-- end .content -->
	</div> <!-- end .body -->
</div> <!-- end .newsletters -->

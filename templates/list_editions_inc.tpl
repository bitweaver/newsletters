<ul class="clear data">
	{foreach from=$editionList key=editionId item=ed}
		<li class="item {cycle values='odd,even'} {$ed.content_type_guid}">
			<div class="floaticon">
				{if $gBitUser->hasPermission('bit_p_admin_newsletters')}
					<a href="{$smarty.const.NEWSLETTERS_PKG_URL}admin/send.php?edition_id={$ed.edition_id}">{biticon ipackage=liberty iname="mail_send" iexplain="Send"}</a>
			   {/if}
			</div>

			<h2><a href="{$ed.display_url}">{$ed.title|escape}</a></h2>
			<p>
				{$ed.data|truncate:200:'...'}
				<br />
				{tr}in{/tr} <a href="{$smarty.const.NEWSLETTERS_PKG_URL}?nl_content_id={$ed.nl_content_id}">{$ed.newsletter_title}</a>
			</p>
		</li>
	{foreachelse}
		<li class="item norecords">
			{tr}No editions{/tr}
		</li>
	{/foreach}
</ul>


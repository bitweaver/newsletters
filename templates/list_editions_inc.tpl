<ul class="clear data">
	{foreach from=$editionList key=editionId item=ed}
		<li class="item {cycle values='odd,even'} {$ed.content_type_guid}">
			<div class="floaticon">
				{if $gBitUser->hasPermission('p_newsletters_admin')}
					<a href="{$smarty.const.NEWSLETTERS_PKG_URL}admin/send.php?edition_id={$ed.edition_id}">{booticon iname="icon-envelope"  ipackage="icons"  iexplain="Send"}</a>
			   {/if}
			</div>

			<h2><a href="{$ed.display_url}">{$ed.title|default:"Untitled Edition"|escape}</a></h2>
		</li>
	{foreachelse}
		<li class="item norecords">
			{tr}No editions{/tr}
		</li>
	{/foreach}
</ul>

		<ul class="clear data">
			{foreach from=$editionList key=editionId item=ed}
				<li class="item {cycle values='odd,even'} {$ed.content_type_guid}">
					<div class="floaticon">
   <a href="{$smarty.const.NEWSLETTERS_PKG_URL}admin/send.php?edition_id={$ed.edition_id}">{biticon ipackage=liberty iname="mail_send" iexplain="Send"}</a>
					</div>
					<h2><a href="{$ed.display_url}">{$ed.title}</a></h2>
					{if $ed.newsletter_title}
					{tr}in{/tr} <a href="{$smarty.const.NEWSLETTERS_PKG_URL}?nl_id={$ed.nl_id}">{$ed.newsletter_title}</a></h3>
					{/if}

					{if $fisheye_list_description eq 'y'}
						<p>{$ed.data|truncate:200}</p>
					{/if}

					<div class="clear"></div>
				</li>
			{foreachelse}
				<li class="item norecords">
					{tr}No editions{/tr}
				</li>
			{/foreach}
		</ul>


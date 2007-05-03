
		<div class="subscriptioninfo">
		{include file="bitpackage:newsletters/unsubscribe_inc.tpl"}
		{if $url_code}
			<img src="{$smarty.const.NEWSLETTERS_PKG_URI}track.php?sub={$url_code}" alt="" />
		{/if}
		</div>


		{if $gBitSystem->isFeatureActive( 'site_bot_bar' )}
			<div id="footer">
				{include file="bitpackage:kernel/bot_bar.tpl"}
			</div>
		{/if}

	</div>


{include file="bitpackage:kernel/footer.tpl"}


	{include file="bitpackage:kernel/header.tpl"}

	<div id="header">
		{include file="bitpackage:kernel/top.tpl"}
		{if $gBitSystem->isFeatureActive( 'site_top_bar' )}
			{include file="bitpackage:kernel/top_bar.tpl"}
		{/if}
	</div>


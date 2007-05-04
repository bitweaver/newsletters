
	{include file="bitpackage:kernel/header.tpl"}

	<div id="header">
		{include file="bitpackage:kernel/top.tpl"}
		{if $gBitSystem->isFeatureActive( 'site_top_bar' )}
			{include file="bitpackage:kernel/top_bar.tpl"}
		{/if}
	</div>

	<div><small>{tr}If you have trouble reading this email,{/tr} <a href="{$gContent->getDisplayUrl()}">{tr}read it on the web{/tr}</a></small></div>


{strip}
	{if $gBitSystem->isFeatureActive( 'bidirectional_text' )}<div dir="rtl">{/if}

	{include file="bitpackage:newsletter/mail_edition_header.tpl"}

		<div id="wrapper">
			<div id="content">
				{include file=$mid}
			</div>
		</div>

	{include file="bitpackage:newsletter/mail_edition_footer.tpl"}

	{if $gBitSystem->isFeatureActive( 'bidirectional_text' )}</div>{/if}
{/strip}

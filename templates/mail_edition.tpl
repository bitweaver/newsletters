{strip}
	{if $gBitSystem->isFeatureActive( 'bidirectional_text' )}<div dir="rtl">{/if}

	{include file="bitpackage:newsletters/mail_edition_header.tpl"}

	{include file="bitpackage:newsletters/mail_edition_body.tpl"}

	{include file="bitpackage:newsletters/mail_edition_footer.tpl"}

	{if $gBitSystem->isFeatureActive( 'bidirectional_text' )}</div>{/if}
{/strip}

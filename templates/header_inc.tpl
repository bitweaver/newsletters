{* $Header: /cvsroot/bitweaver/_bit_newsletters/templates/header_inc.tpl,v 1.2 2007/01/06 06:22:12 spiderr Exp $ *}
{strip}
{* this can totally screw up commerce. need to activate this only when rendering a newsletter to be mailed
{if $gBitSystem->isPackageActive( 'newsletters' )}
	<base href="{$smarty.const.BIT_BASE_URI}" />
{/if}
*}
{/strip}

{* $Header: /cvsroot/bitweaver/_bit_newsletters/templates/header_inc.tpl,v 1.1.2.2 2006/07/22 00:09:20 hash9 Exp $ *}
{strip}
{if $gBitSystem->isPackageActive( 'newsletters' ) && $smarty.const.ACTIVE_PACKAGE == 'newsletters'}
	<base href="{$smarty.const.BIT_BASE_URI}" />
{/if}
{/strip}

<a class="menuoption" href="{$gTikiLoc.NEWSLETTERS_PKG_URL}index.php">{tr}List newsletters{/tr}</a>
{if $tiki_p_admin_newsletters eq 'y'}
	<a class="menuoption" href="{$gTikiLoc.NEWSLETTERS_PKG_URL}send.php">{tr}Send newsletters{/tr}</a>
	<a class="menuoption" href="{$gTikiLoc.NEWSLETTERS_PKG_URL}admin/index.php">{tr}Admin newsletters{/tr}</a>
{/if}

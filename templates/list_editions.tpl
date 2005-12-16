{strip}
<div class="listing newsletters">
	<div class="header">
		<h1>{tr}Newsletter Editions{/tr}</h1>
	</div>

	<div class="body">
		{minifind}

		{include file='bitpackage:newsletters/list_editions_inc.tpl'}

		{libertypagination page=$curPage numPages=$numPages find=$find}
	</div>	<!-- end .body -->
</div>	<!-- end .newsletters -->
{/strip}

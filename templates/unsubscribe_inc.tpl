
<a href="{$smarty.const.NEWSLETTERS_PKG_URI}?unsubscribe={$code}">{$smarty.const.NEWSLETTERS_PKG_URI}?sub={$url_code}</a></p>
<small>{tr}You have received this message because you are registered at{/tr} <a href="{$smarty.const.BIT_BASE_URI}">{$gBitSystem->getConfig('site_title', $smarty.server.HTTP_HOST)}</a>.<br />
{tr}You can always cancel your subscription using:{/tr} <a href="{$smarty.const.NEWSLETTERS_PKG_URI}sub.php?c={$url_code}">{$smarty.const.NEWSLETTERS_PKG_URI}sub.php?c={$url_code}</a></small>

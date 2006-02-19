{tr}Welcome to{/tr} {$gContent->mInfo.title} {tr}newsletter!{/tr}

{$gContent->mInfo.data}

{tr}This email address has been added to the list of subscribers.{/tr}

{tr}You can read{/tr} {$gContent->mInfo.title} {tr}online by visiting:{/tr}
{$smarty.const.NEWSLETTERS_PKG_URI}edition.php?nl_id={$gContent->mNewsletterId}

{tr}You can always cancel your subscription using:{/tr}
{$smarty.const.NEWSLETTERS_PKG_URI}?unsubscribe={$sub_code}

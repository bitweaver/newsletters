{tr}Welcome to{/tr} {$gContent->getTitle()} {tr}newsletter!{/tr}

{$gContent->getField('data')}

{tr}This email address has been added to the list of subscribers.{/tr}

{tr}You can read{/tr} {$gContent->getTitle()} {tr}online by visiting:{/tr}
{$smarty.const.NEWSLETTERS_PKG_URI}edition.php?nl_id={$gContent->mNewsletterId}

{tr}You can always cancel your subscription using:{/tr}
{$smarty.const.NEWSLETTERS_PKG_URI}?unsubscribe={$sub_code}

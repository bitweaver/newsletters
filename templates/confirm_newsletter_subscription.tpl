{tr}A request has been made to subscribe this email address to the newsletter:{/tr} {$gContent->getTitle()}

{$gContent->getField('data')}

{tr}You can read{/tr} {$gContent->getTitle()} {tr}online by visiting:{/tr}
{$smarty.const.NEWSLETTERS_PKG_URI}edition.php?nl_id={$gContent->mNewsletterId}

{tr}To confirm your subscription, please visit the following URL:{/tr}
{$smarty.const.NEWSLETTERS_PKG_URI}?sub={$sub_code}

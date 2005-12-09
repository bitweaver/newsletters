<?php


if (!isset($_REQUEST["nl_id"])) {
	$_REQUEST["nl_id"] = 0;
}

$gBitSmarty->assign('nl_id', $_REQUEST["nl_id"]);

	$gBitSmarty->assign('individual', 'n');
/*
	if ($userlib->object_has_one_permission($_REQUEST["nl_id"], 'newsletter')) {
		$gBitSmarty->assign('individual', 'y');

		if( !$gBitUser->isAdmin() ) {
			$perms = $userlib->get_permissions(0, -1, 'perm_name_desc', '', 'newsletters');

			foreach ($perms["data"] as $perm) {
				$perm_name = $perm["perm_name"];

				if ($userlib->object_has_permission($user, $_REQUEST["nl_id"], 'newsletter', $perm_name)) {
					$$perm_name = 'y';

					$gBitSmarty->assign("$perm_name", 'y');
				} else {
					$$perm_name = 'n';

					$gBitSmarty->assign("$perm_name", 'n');
				}
			}
		}
	}
*/



?>

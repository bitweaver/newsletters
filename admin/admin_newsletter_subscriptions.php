<?php

// $Header: /cvsroot/bitweaver/_bit_newsletters/admin/admin_newsletter_subscriptions.php,v 1.4 2006/04/20 16:24:47 squareing Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once( '../../bit_setup_inc.php' );
$gBitSystem->verifyPackage( 'newsletters' );
$gBitSystem->verifyPermission( 'p_admin_newsletters' );

require_once( NEWSLETTERS_PKG_PATH.'lookup_newsletter_inc.php' );

/*
if ($userlib->object_has_one_permission($_REQUEST["nl_id"], 'newsletter')) {
	$smarty->assign('individual', 'y');

	if ($bitweaver.orgi_p_admin != 'y') {
		$perms = $userlib->get_permissions(0, -1, 'perm_name_desc', '', 'newsletters');

		foreach ($perms["data"] as $perm) {
			$perm_name = $perm["perm_name"];

			if ($userlib->object_has_permission($user, $_REQUEST["nl_id"], 'newsletter', $perm_name)) {
				$$perm_name = 'y';

				$smarty->assign("$perm_name", 'y');
			} else {
				$$perm_name = 'n';

				$smarty->assign("$perm_name", 'n');
			}
		}
	}
}
*/

if (isset($_REQUEST["remove"])) {
	check_ticket('admin-nl-subsriptions');
	$nllib->remove_newsletter_subscription($_REQUEST["remove"], $_REQUEST["email"]);
}

if (isset($_REQUEST["add_all"])) {
	check_ticket('admin-nl-subsriptions');
	$nllib->add_all_users($_REQUEST["nl_id"]);
}

if (isset($_REQUEST["save"])) {
	check_ticket('admin-nl-subsriptions');
	$sid = $nllib->newsletter_subscribe($_REQUEST["nl_id"], $_REQUEST["email"]);
}

/*
$cat_type='newsletter';
$cat_objid = $_REQUEST["nl_id"];
include_once( CATEGORIES_PKG_PATH.'categorize_list_inc.php' );
*/

// Display the template
$gBitSystem->display( 'bitpackage:newsletters/admin_newsletter_subscriptions.tpl' );

?>

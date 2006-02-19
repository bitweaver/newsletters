<?php

// $Header: /cvsroot/bitweaver/_bit_newsletters/admin/admin_newsletter_subscriptions.php,v 1.3.2.2 2006/02/19 04:38:47 wolff_borg Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once( '../../bit_setup_inc.php' );
$gBitSystem->verifyPackage( 'newsletters' );
$gBitSystem->verifyPermission( 'bit_p_admin_newsletters' );

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

if( $gContent->isValid() ) {
	$nl_id = $_REQUEST['nl_id'];
	$gBitSmarty->assign( 'nl_id', $nl_id );

	if (isset($_REQUEST["remove"])) {
		$gContent->removeSubscription($_REQUEST["email"], FALSE, TRUE );
	}

	if (isset($_REQUEST["save"])) {
		$new_subs = preg_split("/[\s,]+/", $_REQUEST["new_subscribers"]);
		foreach($new_subs as $sub) {
			$sub = trim($sub);
			if (empty($sub))
				continue;
			$gContent->subscribe($sub, TRUE );
		}
	}

	$subscribers = $gContent->getAllSubscribers($nl_id);
	$gBitSmarty->assign( 'subscribers', $subscribers );
}
/*
$cat_type='newsletter';
$cat_objid = $_REQUEST["nl_id"];
include_once( CATEGORIES_PKG_PATH.'categorize_list_inc.php' );
*/

// Display the template
$gBitSystem->display( 'bitpackage:newsletters/admin_newsletter_subscriptions.tpl' );

?>

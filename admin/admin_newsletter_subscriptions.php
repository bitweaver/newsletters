<?php

// $Header: /cvsroot/bitweaver/_bit_newsletters/admin/admin_newsletter_subscriptions.php,v 1.2 2005/12/09 07:04:17 spiderr Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once( '../../bit_setup_inc.php' );

include_once( NEWSLETTERS_PKG_PATH.'nl_lib.php' );

if ($feature_newsletters != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_newsletters");

	$gBitSystem->display( 'error.tpl' );
	die;
}

if (!isset($_REQUEST["nl_id"])) {
	$smarty->assign('msg', tra("No newsletter indicated"));

	$gBitSystem->display( 'error.tpl' );
	die;
}

$smarty->assign('nl_id', $_REQUEST["nl_id"]);

$smarty->assign('individual', 'n');

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

if ($bitweaver.orgi_p_admin_newsletters != 'y') {
	$smarty->assign('msg', tra("You dont have permission to use this feature"));

	$gBitSystem->display( 'error.tpl' );
	die;
}

if ($_REQUEST["nl_id"]) {
	$info = $nllib->get_newsletter($_REQUEST["nl_id"]);
} else {
	$info = array();

	$info["name"] = '';
	$info["description"] = '';
	$info["allow_any_sub"] = 'n';
	$info["frequency"] = 7 * 24 * 60 * 60;
}

$smarty->assign('nl_info', $info);

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

if ( empty( $_REQUEST["sort_mode"] ) ) {
	$sort_mode = 'subscribed_desc';
} else {
	$sort_mode = $_REQUEST["sort_mode"];
}

if (!isset($_REQUEST["offset"])) {
	$offset = 0;
} else {
	$offset = $_REQUEST["offset"];
}

$smarty->assign_by_ref('offset', $offset);

if (isset($_REQUEST["find"])) {
	$find = $_REQUEST["find"];
} else {
	$find = '';
}

$smarty->assign('find', $find);

$smarty->assign_by_ref('sort_mode', $sort_mode);
$channels = $nllib->list_newsletter_subscriptions($_REQUEST["nl_id"], $offset, $maxRecords, $sort_mode, $find);

$cant_pages = ceil($channels["cant"] / $maxRecords);
$smarty->assign_by_ref('cant_pages', $cant_pages);
$smarty->assign('actual_page', 1 + ($offset / $maxRecords));

if ($channels["cant"] > ($offset + $maxRecords)) {
	$smarty->assign('next_offset', $offset + $maxRecords);
} else {
	$smarty->assign('next_offset', -1);
}

// If offset is > 0 then prev_offset
if ($offset > 0) {
	$smarty->assign('prev_offset', $offset - $maxRecords);
} else {
	$smarty->assign('prev_offset', -1);
}

$smarty->assign_by_ref('channels', $channels["data"]);

// Fill array with possible number of questions per page
$freqs = array();

for ($i = 0; $i < 90; $i++) {
	$aux["i"] = $i;

	$aux["t"] = $i * 24 * 60 * 60;
	$freqs[] = $aux;
}

$smarty->assign('freqs', $freqs);

/*
$cat_type='newsletter';
$cat_objid = $_REQUEST["nl_id"];
include_once( CATEGORIES_PKG_PATH.'categorize_list_inc.php' );
*/
ask_ticket('admin-nl-subsriptions');
// Display the template
$gBitSystem->display( 'bitweaver.orgipackage:newsletters/admin_newsletter_subscriptions.tpl');

?>

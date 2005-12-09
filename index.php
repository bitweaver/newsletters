<?php

// $Header: /cvsroot/bitweaver/_bit_newsletters/index.php,v 1.1 2005/12/09 06:59:54 bitweaver Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once( '../tiki_setup_inc.php' );

include_once( NEWSLETTERS_PKG_PATH.'nl_lib.php' );
include_once( WEBMAIL_PKG_PATH.'htmlMimeMail.php' );

if ($feature_newsletters != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_newsletters");

	$gTikiSystem->display( 'error.tpl' );
	die;
}

if (isset($_REQUEST["confirm_subscription"])) {
	check_ticket('newsletters');
	$conf = $nllib->confirm_subscription($_REQUEST["confirm_subscription"]);

	if ($conf) {
		$smarty->assign('confirm', 'y');

		$smarty->assign('nl_info', $conf);
	}
}

$smarty->assign('unsub', 'n');

if (isset($_REQUEST["unsubscribe"])) {
	check_ticket('newsletters');
	$conf = $nllib->unsubscribe($_REQUEST["unsubscribe"]);

	if ($conf) {
		$smarty->assign('unsub', 'y');

		$smarty->assign('nl_info', $conf);
	}
}

if (!$user && $tiki_p_subscribe_newsletters != 'y' && !isset($_REQUEST["confirm_subscription"])) {
	$smarty->assign('msg', tra("You must be logged in to subscribe to newsletters"));

	$gTikiSystem->display( 'error.tpl' );
	die;
}

if (!isset($_REQUEST["nl_id"])) {
	$_REQUEST["nl_id"] = 0;
}

$smarty->assign('nl_id', $_REQUEST["nl_id"]);

$smarty->assign('subscribe', 'n');
$smarty->assign('subscribed', 'n');

$foo = parse_url($_SERVER["REQUEST_URI"]);
$smarty->assign('url_subscribe', httpPrefix(). $foo["path"]);

if (isset($_REQUEST["nl_id"])) {
	$smarty->assign('individual', 'n');

	if ($userlib->object_has_one_permission($_REQUEST["nl_id"], 'newsletter')) {
		$smarty->assign('individual', 'y');

		if ($tiki_p_admin != 'y') {
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
}

if ($user) {
	$user_email = $tikilib->get_user_email($user);
} else {
	$user_email = '';
}

$smarty->assign('email', $user_email);

$smarty->assign('confirm', 'n');

if ($tiki_p_subscribe_newsletters == 'y') {
	if (isset($_REQUEST["subscribe"])) {
	check_ticket('newsletters');
		$smarty->assign('subscribed', 'y');

		if ($tiki_p_subscribe_email != 'y') {
			$_REQUEST["email"] = $userlib->get_user_email($user);
		}

		// Now subscribe the email address to the newsletter
		$nllib->newsletter_subscribe($_REQUEST["nl_id"], $_REQUEST["email"]);
	}
}

if (isset($_REQUEST["info"])) {
	$nl_info = $nllib->get_newsletter($_REQUEST["nl_id"]);

	$smarty->assign('nl_info', $nl_info);
	$smarty->assign('subscribe', 'y');
}
/* List newsletters */
if ( empty( $_REQUEST["sort_mode"] ) ) {
	$sort_mode = 'created_desc';
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
$channels = $nllib->list_newsletters($offset, $maxRecords, $sort_mode, $find);

for ($i = 0; $i < count($channels["data"]); $i++) {
	if ($userlib->object_has_one_permission($channels["data"][$i]["nl_id"], 'newsletters')) {
		$channels["data"][$i]["individual"] = 'y';

		if ($userlib->object_has_permission($user, $channels["data"][$i]["nl_id"], 'newsletter', 'tiki_p_subscribe_newsletters')) {
			$channels["data"][$i]["individual_tiki_p_subscribe_newsletters"] = 'y';
		} else {
			$channels["data"][$i]["individual_tiki_p_subscribe_newsletters"] = 'n';
		}

		if ($tiki_p_admin == 'y'
			|| $userlib->object_has_permission($user, $channels["data"][$i]["nl_id"], 'newsletter', 'tiki_p_admin_newsletters')) {
			$channels["data"][$i]["individual_tiki_p_subscribe_newsletters"] = 'y';
		}
	} else {
		$channels["data"][$i]["individual"] = 'n';
	}
}

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
ask_ticket('newsletters');

// Display the template
$gTikiSystem->display( 'tikipackage:newsletters/newsletters.tpl');

?>
